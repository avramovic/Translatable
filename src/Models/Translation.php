<?php

namespace Avram\Translatable\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    /**
     * @var string
     */
    protected $table = 'translations';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * @return \stdClass
     */
    protected function getDataAttribute()
    {
        if (empty($this->attributes['data'])) {
            return [];
        }

        $array = [];

        foreach (json_decode($this->attributes['data']) as $index=>$value) {
            $array[$index] = $value;
        }

        return $array;
    }

    /**
     * @param mixed $data
     */
    protected function setDataAttribute($data)
    {
        $this->attributes['data'] = json_encode($data);
    }

}
