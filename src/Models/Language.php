<?php

namespace Avram\Translatable\Models;

use Avram\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * @var string
     */
    protected $table = 'languages';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return integer
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @param string      $value
     *
     * @return Language
     */
    public static function getByLanguageCode($value)
    {
        $lang = static::where('code', $value)->first();
        if ($lang == null) {
            throw new \InvalidArgumentException("Unrecognized language: $value");
        }

        return $lang;
    }

    /**
     * @return Language
     */
    public static function getDefaultLanguage()
    {
        return static::orderBy('default', 'DESC')->orderBy('id', 'ASC')->first();
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->getDefault();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return (int)$this->order;
    }

    /**
     * @param integer $order
     */
    public function setOrder($order)
    {
        $this->order = (int)$order;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return (bool)$this->active;
    }

    public function isActive()
    {
        return $this->getActive();
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = (bool)$active;
    }

    /**
     * @return boolean
     */
    public function getDefault()
    {
        return (bool)$this->default;
    }

    /**
     * @param boolean $default
     */
    public function setDefault($default)
    {
        $this->default = (bool)$default;
    }

    /**
     * EVENTS
     */

    public static function boot()
    {
        /** @var Translatable $instance */
        $instance = app(Translatable::class);

        static::creating(function ($language) use ($instance) {
            $instance->clearLanguageCache();
        });

        static::updating(function ($language) use ($instance) {
            $instance->clearLanguageCache();
        });

        static::saving(function ($language) use ($instance) {
            $instance->clearLanguageCache();
        });

        static::deleting(function ($language) use ($instance) {
            $instance->clearLanguageCache();
        });
    }
}
