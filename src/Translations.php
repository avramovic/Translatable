<?php namespace Avram\Translatable;

use Illuminate\Database\Eloquent\Model;

class Translations
{
    /** @var Model $model */
    protected $model;

    /** @var \stdClass */
    /** @var \stdClass */
    protected $default, $translated;

    /** @var boolean */
    protected $forceEmpty;

    /**
     * Translations constructor.
     *
     * @param Model      $model
     * @param \stdClass  $default
     * @param \stdClass  $translated
     * @param bool|false $forceEmpty
     */
    public function __construct(Model $model, $default, $translated, $forceEmpty = false)
    {
        $this->model      = $model;
        $this->default    = $default;
        $this->translated = $translated;
        $this->forceEmpty = $forceEmpty;
    }

    /**
     * @param string $field
     *
     * @return mixed|string
     */
    public function __get($field)
    {
        $phrase  = isset($this->translated->{$field}) ? $this->translated->{$field} : '';
        $default = isset($this->default->{$field}) ? $this->default->{$field} : '';
        if (empty($phrase)) {
            if ($this->forceEmpty) {
                return '';
            } else if (!empty($default)) {
                return $default;
            } else {
                return $this->model->getAttribute($field);
            }
        }

        return $phrase;
    }

}