<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmissionRating extends Model {

    protected $table = 'submission_rating';

    /**
     * The attributes to protect from mass assignment.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Establish a relationship between submission and submission_rating.
     * @return mixed
     */
    public function submission()
    {
        return $this->belongsTo('\App\Submission', 'submission_id');
    }

    /**
     * Establish a relationship between submission_rating and user.
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('\App\User', 'user_id');
    }


}