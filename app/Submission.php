<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{

    protected $fillable = [
        'title',
        'type',
        'description',
        'lang',
        'category_id',
        'release_date'
    ];

    /**
     * Establish a relationship between submission and submission_meta.
     *
     * @return mixed
     */
    public function meta()
    {
        return $this->hasMany('\App\SubmissionMeta', 'submission_id');
    }

    /**
     * Establish a relationship between submission and submission_rating.
     *
     * @return mixed
     */
    public function ratings()
    {
        return $this->hasMany('\App\SubmissionRating', 'submission_id');
    }

    /**
     * Retrieve an submission meta record.
     *
     * @return mixed
     */
    public function getAllMeta()
    {
        $meta = SubmissionMeta::where('submission_id', $this->id)
            ->get();
        if ($meta) return $meta;

        return false;
    }

    /**
     * Retrieve an submission meta record.
     *
     * @param      $key
     * @param bool $record
     * @return mixed
     */
    public function getMeta($key, $record = false)
    {
        $meta = SubmissionMeta::where("submission_id", $this->id)
            ->where("meta_key", $key)
            ->first();
        if ($meta) {
            if (!$record) return $meta->meta_value;

            return $meta;
        }

        return false;
    }

    /**
     * Set a meta value based on a key.
     * @param string $key
     * @param mixed  $value
     * @return boolean
     */
    public function setMeta($key, $value)
    {
        $meta = $this->getMeta($key, true);

        if ($meta && !is_integer($value) && empty($value)) {
            $meta->delete();

            return true;
        }

        if (!$meta) $meta = new SubmissionMeta;

        $meta->submission_id = $this->id;
        $meta->meta_key = $key;
        $meta->meta_value = $value;

        $meta->save();

        return true;
    }

    /**
     * Delete a specified meta key if it exists.
     *
     * @param $key
     * @return mixed
     */
    public function deleteMeta($key)
    {
        return SubmissionMeta::where('meta_key', '=', $key)->where('submission_id', '=', $this->id)->delete();
    }

    public function category()
    {
        $category = "None";
        if ($this->type == 'game') $category = config('site.categories')[$this->category_id];
        if ($this->type == 'resource') $category = config('site.resource_categories')[$this->category_id];
        return $category;
    }

    public function contents()
    {
        $content = $this->getMeta('content');
        if (!$content) return [];

        $indexes = explode(',', $this->getMeta('content'));
        $contents = [];
        foreach ($indexes as $index) {
            $contents[$index] = config('site.content')[$index];
        }
        return $contents;
    }

    public function languages()
    {
        $indexes = explode(',', $this->lang);
        $languages = [];
        foreach ($indexes as $index) {
            $languages[$index] = config('site.languages')[$index];
        }
        return $languages;
    }

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        return array_merge(['release_date'], parent::getDates());
    }

    public function media()
    {
        return $this->hasMany('App\SubmissionMedia');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function isPlayable()
    {
        return $this->getPlayableMediaPath() ? true : false;
    }

    public function getPlayableMediaPath()
    {
        $path = false;
        $media = $this->media()->where('type', 'exe')->get();
        foreach ($media as $m) {
            if (ends_with($m->file_path, '.1rc')) {
                $path = "/submissions/" . $this->id . "/media/" .  $m->id  . "/media";
                break;
            }
        }

        return $path;
    }

    public static function getTypes()
    {
        return [
            'game' => 'Game',
            'resource' => 'Resource',
            'tutorial' => 'Tutorial',
        ];
    }

    public function remove()
    {
        foreach ($this->meta as $meta) {
            $meta->delete();
        }

        foreach ($this->media as $media) {
            $media->remove();
        }

        $this->delete();
    }

}
