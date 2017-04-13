<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmissionMeta extends Model {

    protected $table = 'submission_meta';

    /**
     * The attributes to protect from mass assignment.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Establish a relationship between submission and submission_meta.
     * @return mixed
     */
    public function submission()
    {
        return $this->belongsTo('\App\Submission', 'submission_id');
    }


}