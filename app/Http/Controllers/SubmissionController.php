<?php

namespace App\Http\Controllers;

use App\Submission;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubmissionRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;

class SubmissionController extends Controller
{

    public function index(Request $request)
    {
        $editable = ($request->get('mine') == 1) ? '1' : '0';

        $sort_by = $request->get('sort_by', 'created_at');
        if (!in_array($sort_by, ['created_at', 'title', 'rating'])) $sort_by = 'created_at';

        $sort_order = $request->get('sort_order', 'created_at');
        if (!in_array($sort_order, ['asc', 'desc'])) $sort_order = 'desc';

        $type = $request->get('type', false);
        if ($type && !array_key_exists($type, Submission::getTypes())) $type = false;

        $categories = config('site.categories');
        if ($type == 'resource') $categories = config('site.resource_categories');

        $submissionRepository = Submission::query();
        if ($editable) $submissionRepository = Auth::user()->submissions();

        $search = $request->get('search', false);
        if ($search)
            $submissions = $submissionRepository->where('title', 'LIKE', '%' . $search . '%')->where('approved', 1);
        else
            $submissions = $submissionRepository->where('approved', 1);

        if ($type) $submissions = $submissions->where('type', $type);

        $category = intval($request->get('category', -1));
        if (!array_key_exists($category, $categories)) $category = -1;
        if ($category > -1) $submissions->where('category_id', $category);

        $submissions = collect($submissions->get());
        if ($sort_by == 'rating') {
            if ($sort_order == 'desc') {
                $submissions = $submissions->sortByDesc(function ($item) {
                    return count($item->ratings);
                });
            } else {
                $submissions = $submissions->sortBy(function ($item) {
                    return count($item->ratings);
                });
            }
        } else {
            if ($sort_order == 'desc') {
                $submissions = $submissions->sortByDesc($sort_by);
            } else {
                $submissions = $submissions->sortBy($sort_by);
            }
        }

        $page = intval($request->get('page', 1));
        $perPage = 20;
        $offset = ($page * $perPage) - $perPage;
        $paginator = new LengthAwarePaginator($submissions->slice($offset, $perPage, true), count($submissions), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return view('submission.list')->with([
            'categories' => $categories,
            'submissions' => $paginator,
            'editable' => $editable,
            'search' => $search,
            'category' => $category,
            'type' => $type,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order
        ]);
    }
    
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $submission = Submission::findOrFail($id);

        if ($submission->approved != 1 && (!$user || $user->access != 'admin')) {
        	// The submission is not approved and the user is not an admin so we need to check if this user has permission.
        	if (!$user || $submission->user_id != $user->id) {
        		// There is no current user or the current user does not own the submission.
        		App::abort(403, 'Unauthorized action.');
        	}
        }

        $media = $submission->media()->where('type', 'image')->get();
        $files = $submission->media()->where('type', '!=', 'image')->get();

        $currentPage = $request->get('page', 1);

        $disqus_sso_message = $disqus_sso_hmac = $disqus_sso_timestamp = false;

        if ($user) {
            $disqus_sso_user = [
                'id' => $user->id,
                'username' => $user->name,
                'email' => $user->email
            ];
            $disqus_sso_message = base64_encode(json_encode($disqus_sso_user));
            $disqus_sso_timestamp = time();
            $disqus_sso_hmac = hash_hmac('sha1', $disqus_sso_message . ' ' . $disqus_sso_timestamp, config('services.disqus.secret_key'));
        }

        return view('submission.show', compact('submission', 'media', 'files', 'currentPage', 'disqus_sso_message', 'disqus_sso_hmac', 'disqus_sso_timestamp'));
    }

    public function play($id)
    {
        $submission = Submission::findOrFail($id);

        $path = $submission->getPlayableMediaPath();

        if (!$path) {
            Flash::error('There is no playable media for this submission!');
            return redirect()->route('submission', $submission->id);
        }

        return view('submission.play')->with(['submission' => $submission, 'path' => $path]);
    }

    public function modify($id)
    {
        $submission = Auth::user()->submissions()->findOrFail($id);
        $media = $submission->media()->orderBy('type', 'desc')->get();

        return view('submission.edit', compact('submission', 'media'));
    }

    public function pick()
    {
        return view('submission.pick');
    }

    public function create($type)
    {
        return view('submission.create', compact(['type']));
    }

    /**
     * @param CreateSubmissionRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateSubmissionRequest $request)
    {
        $data = $request->all();
        if (isset($data["lang"])) $data["lang"] = implode(',', $data["lang"]);

        $submission = new Submission($data);
        Auth::user()->submissions()->save($submission);

        return redirect()->route('submission.edit', $submission->id);
    }

    public function edit($id)
    {
        set_time_limit(0);
        ini_set("MEMORY_LIMIT", "1024M");

        $submission = Auth::user()->submissions()->findOrFail($id);
        $input = Input::file('file');

        $success = false;
        $destinationPath = storage_path('app/media');
        $fileName = "";
        $type = false;

        $data = Input::all();
        if (isset($data["lang"])) $data["lang"] = implode(',', $data["lang"]);

        foreach ($data as $key => $value) {
            if (starts_with($key, 'meta_')) {
                $metaKey = str_replace('meta_', '', $key);
                if (empty($value) && !$submission->getMeta($metaKey)) continue;

                if ($metaKey == 'content') {
                    $value = implode(',', $value);
                }

                $submission->setMeta($metaKey, $value);
            }
        }

        if($input && $input->isValid()) {
            if (in_array($input->getClientOriginalExtension(), config('site.allowed_extensions.image'))) {
                $fileName = "image_submission_" . $id . "_" . $input->getClientOriginalName();
//                $success = $input->move($destinationPath, $fileName);
                $success = Storage::disk('s3')->put($fileName, file_get_contents($input->getRealPath()));
                $type = "image";
            } elseif ($submission->type == 'game' && in_array($input->getClientOriginalExtension(), config('site.allowed_extensions.exe'))) {
                $fileName = "file_submission_" . $id . "_" . $input->getClientOriginalName();
//                $success = $input->move($destinationPath, $fileName);
                $success = Storage::disk('s3')->put($fileName, file_get_contents($input->getRealPath()));
                $type = "exe";
            } elseif ($submission->type == 'resource' && in_array($input->getClientOriginalExtension(), config('site.allowed_extensions.resource')[$submission->category_id])) {
                $fileName = "resource_submission_" . $id . "_" . $input->getClientOriginalName();
    //                $success = $input->move($destinationPath, $fileName);
                $success = Storage::disk('s3')->put($fileName, file_get_contents($input->getRealPath()));
                $type = "resource";
            }

            if ($success) {
                $res = $submission->media()->create(['type' => $type, 'file_path' => $fileName]);
                return $res;
            } else {
                App::abort(415);
            }
        } elseif (!$submission->update($data)) {
            return Redirect::back()
                ->with('message', 'Something wrong happened while saving your submission.')
                ->withInput();
        }

        $submission->denied_for = null;
        $submission->save();

        Flash::success('The submission has been saved successfully.');
        return redirect()->route('submission.edit', $submission->id);
    }

    public function destroy($id)
    {
        $submission = Auth::user()->submissions()->findOrFail($id);
        $submission->remove();

        Flash::success('The submission has been removed successfully.');
        return redirect()->route('submission.mine');
    }

    public function rate($id)
    {
        $submission = Submission::findOrFail($id);
        $user = Auth::user();

        if ($submission->user->id == $user->id) {
            Flash::error('You cannot rate your own submissions!');
            return redirect()->route('submission', $submission->id);
        }

        $previous = $submission->ratings()->where('user_id', $user->id);
        if ($previous->count() > 0) {
            $previous->delete();
            Flash::info('Your rating has been removed successfully.');
            return redirect()->route('submission', $submission->id);
        }

        $submission->ratings()->create([
            'user_id' => $user->id
        ]);

        Flash::success('Your rating has been added successfully.');
        return redirect()->route('submission', $submission->id);
    }
}
