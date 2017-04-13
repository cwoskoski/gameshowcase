<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SubmissionMedia extends Model
{
    protected $fillable = [
        'type',
        'file_path',
    ];

    public function submission()
    {
        $this->belongsTo('App\Submission');
    }

    public function remove()
    {
        if (Storage::disk('s3')->has($this->file_path)) Storage::disk('s3')->delete($this->file_path);
        return $this->delete();
    }
}
