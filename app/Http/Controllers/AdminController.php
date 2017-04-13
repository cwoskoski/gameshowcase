<?php

namespace App\Http\Controllers;

use App\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;

class AdminController extends Controller
{
    public function index()
    {
        $submissions = Submission::latest()->get();
        $editable = false;

        return view('admin.submission.list', compact(['submissions', 'editable']));
    }

    public function moderate()
    {
        $submissions = Submission::where('approved', 0)->whereNull('denied_for')->latest()->get();
        $editable = false;

        return view('admin.submission.list', compact(['submissions', 'editable']));
    }

    public function approve($id)
    {
        $submission = Submission::findOrFail($id);
        $downloadables = $submission->media()->where('type', 'exe')->orWhere('type', 'resource')->get();

        if($submission->type == 'tutorial' || count($downloadables) > 0) {
            $submission->approved = 1;
            $submission->save();
            return redirect()->route('submission', $id);
        }

        if ($submission->type == 'game') $types = config('site.allowed_extensions.exe');
        if ($submission->type == 'resource') $types = config('site.allowed_extensions.resource')[$submission->category_id];

        Flash::error('The '. $submission->type .' must contain at least one of the following types of files to be allowed: ' . implode(', ' , $types) . '.');
        return Redirect::back();
    }

    public function deny(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);

        $submission->denied_for = $request->get('comment');
        $submission->save();

        Flash::success('The submission has been denied successfully!');
        return 'The submission has been denied successfully!';
    }

    public function suspend($id)
    {
        $submission = Submission::findOrFail($id);
        $submission->approved = 0;
        $submission->save();

        Flash::success('The submission has been suspended successfully!');
        return redirect()->route('submission', $id);
    }
}