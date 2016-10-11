<?php

namespace Avram\Translatable\Traits;

use Avram\Translatable\Models\Language;
use Avram\Translatable\Models\Translation;
use Avram\Translatable\Translatable;
use Avram\Translatable\Translations;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait TranslatableTrait
{
    /**
     * @var array
     */
    protected $translated;

    /**
     * @return MorphOne
     */
    public function translations()
    {
        return $this->morphOne(Translation::class, 'model');
    }

    /**
     * @return \stdClass
     */
    public function getTranslations()
    {
        if (isset($this->translated)) {
            return $this->translated;
        }

        $translations = $this->translations;
        if ($translations == null) {
            $translations = new Translation;
        }

        return $this->translated = $translations->data;
    }

    /**
     * @param string|null $lang
     * @param bool        $forceEmpty
     *
     * @return Translations
     */
    public function translate($lang = null, $forceEmpty = false)
    {
        /** @var Translatable $instance */
        $instance = app(Translatable::class);
        if ($lang == null) {
            $lang = $instance->getLanguage();
        } else {
            $lang = $instance->getBy($lang);
        }

        $data        = $this->getTranslations();
        $translated  = isset($data[$lang->getId()]) ? $data[$lang->getId()] : null ;
        $defaultLang = $instance->getDefaultLanguage();
        $default     = isset($data[$defaultLang->getId()]) ? $data[$defaultLang->getId()] : null ;

        return new Translations($this, $default, $translated, $forceEmpty);
    }

    /**
     * @param string $field
     * @param mixed  $value
     * @param null   $lang
     *
     * @return bool
     */
    public function saveTranslation($field, $value, $lang = null)
    {
        /** @var Translatable $instance */
        $instance = app(Translatable::class);

        if ($lang == null) {
            $lang = $instance->getLanguage();
        } else {
            $lang = Language::getByLanguageCode($lang);
        }

        $langId = $lang->getId();

        $translations = $this->translations;
        if ($translations === null) {
            $data               = [];
            $data[$langId]      = new \stdClass;
            $translations       = new Translation();
            $translations->data = $data;
            $translations->save();
            $this->translations()->save($translations);
        } else {
            $data = $translations->data;
        }

        if (!isset($data[$langId])) {
            $data[$langId] = new \stdClass();
        }

        $data[$langId]->{$field} = $value;
        $translations->data      = $data;
        $translations->save();

        if ($langId == $instance->getDefaultLanguage()->getId()) {
            $this->{$field} = $value;
            $this->save();
        }

        return true;
    }

    /**
     * @param mixed $lang
     *
     * @return bool
     */
    public function getTranslationsForLanguage($lang = null)
    {
        /** @var Translatable $instance */
        $instance = app(Translatable::class);
        if ($lang == null) {
            $lang = $instance->getLanguage();
        } else {
            $lang = $instance->getBy($lang);
        }

        $langId       = $lang->getId();
        $translations = $this->translations;

        if ($translations === null) {
            return null;
        }

        $data = $translations->data;
        if (!isset($data[$langId])) {
            return null;
        }

        return $data[$langId];
    }

    /**
     * @param $what
     *
     * @return mixed|string
     */
    public function __get($what)
    {
        /** @var Translatable $instance */
        $instance = app(Translatable::class);

        if ($instance->autoTranslate) {
            if (isset($this->attributes[$what])) {
                if ($instance->hasValidLanguageSegment()) {
                    $lang = $instance->getLanguageSegment();
                    return $this->translate($lang)->{$what};
                } else {
                    return $this->translate($instance->getDefaultLanguage()->getCode())->{$what};
                }
            }
        }

        if ($instance->magicFields) {
            $whatArray = explode('_', $what);
            if (count($whatArray) >= 2) {
                $lang  = array_pop($whatArray);
                $field = implode('_', $whatArray);
                if ($instance->isValidLanguageSegment($lang)) {
                    return $this->translate($lang)->{$field};
                } elseif ($lang == 'i18n') {
                    $lang = $instance->hasValidLanguageSegment() ? $instance->getLanguageSegment() : $instance->getDefaultLanguage();
                    return $this->translate($lang)->{$field};
                }
            }
        }


        return parent::__get($what);
    }
}