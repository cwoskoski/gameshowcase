<?php

namespace App\Http\Controllers;

use App\Submission;
use App\SubmissionMedia;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class SubmissionMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($submissionId)
    {
        $media = Submission::find($submissionId)->media;
        view('submission.media.list', compact(array('media')));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($submissionId)
    {
        return $submissionId;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param $submissionId
     * @param $resourceId
     * @return Response
     */
    public function show($submissionId, $resourceId)
    {
        return "{$submissionId} - {$resourceId}";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $submissionId
     * @param      $resourceId
     * @return Response
     */
    public function edit($submissionId, $resourceId)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $submissionId, $resourceId)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $submissionId
     * @param      $resourceId
     * @return Response
     */
    public function destroy($submissionId, $resourceId)
    {
        $submission = Auth::user()->submissions()->findOrFail($submissionId);
        $resource = $submission->media()->find($resourceId);
        $resource->remove();

        return redirect('submission/edit/' . $submissionId);
    }

    public function media($submissionId, $resourceId)
    {
        $resource = SubmissionMedia::find($resourceId);

        $response = Response::make(
//            File::get($filePath),
            Storage::disk('s3')->get($resource->file_path),
            200
        );

        // Set the mime type for the response.
        // We now use the Image class for this also.


        if($resource->type == 'image') {
            $contentType = "image/png";
        } else {
            $contentType = "";
            $response->header(
                'Content-Disposition',
                'attachment; filename="'.$resource->file_path.'"'
            );
        }

        $response->header(
            'content-type',
            $contentType
        );

        return $response;
    }
}
