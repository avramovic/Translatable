<?php

namespace Avram\Translatable;

use Avram\Translatable\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Class Translatable
 * @package Avram\Translatable
 */
class Translatable
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var boolean
     */
    public $hideDefault;

    /**
     * @var boolean
     */
    public $autoTranslate;

    /**
     * @var boolean
     */
    public $magicFields;

    protected $defaultLanguage, $activeLanguages, $otherLanguages, $currentLanguage;
    protected $langCache;

    /**
     * Translatable constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        if ($this->hasValidLanguageSegment()) {
            $language = $this->getBy($this->getLanguageSegment());
        } else {
            $language = $this->getDefaultLanguage();
        }

        $this->hideDefault   = config('translatable.hide_default', true);
        $this->autoTranslate = config('translatable.auto_translate', true);
        $this->magicFields   = config('translatable.magic_fields', true);

        if ($language != null) {
            $this->setLanguage($language);
        }
    }

    /**
     * @return boolean
     */
    public function hasLanguageSegment()
    {
        $locale = $this->request->segment(1);
        return (strlen($locale) == 2);
    }

    /**
     * @return array
     */
    public function getValidLanguageSegments()
    {
        return $this->getLanguages()->pluck('code')->toArray();
    }

    /**
     * @param string $locale
     *
     * @return boolean
     */
    public function isValidLanguageSegment($locale)
    {
        return in_array($locale, $this->getValidLanguageSegments());
    }

    /**
     * @return boolean
     */
    public function hasValidLanguageSegment()
    {
        if (!$this->hasLanguageSegment()) {
            return false;
        }

        $locale = $this->getLanguageSegment();

        return $this->isValidLanguageSegment($locale);
    }

    /**
     * @return boolean|string
     */
    public function getLanguageSegment()
    {
        return $this->request->segment(1);
    }

    /**
     * @param string $locale
     */
    public function setLanguageCode($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->locale;
    }

    /**
     * @return Language
     */
    public function getDefaultLanguage()
    {
        if ($this->defaultLanguage) {
            return $this->defaultLanguage;
        }

        return $this->defaultLanguage = Language::getDefaultLanguage();
    }

    /**
     * @return Collection|static[]
     */
    public function getLanguages()
    {
        if ($this->activeLanguages) {
            return $this->activeLanguages;
        }

        return $this->activeLanguages = Language::where('active', true)
            ->orderBy('order', 'ASC')
            ->get();
    }

    /**
     * @return Collection|static[]
     */
    public function getOtherLanguages()
    {
        if ($this->otherLanguages) {
            return $this->otherLanguages;
        }

        return $this->otherLanguages = Language::where('id', '!=', $this->getDefaultLanguage()->getId())
            ->where('active', true)
            ->orderBy('order', 'ASC')
            ->get();
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        if ($this->currentLanguage) {
            return $this->currentLanguage;
        }

        return $this->currentLanguage = $this->getBy($this->getLanguageCode());
    }

    /**
     * @param $locale
     *
     * @return Language
     */
    public function getBy($locale)
    {
        if (!$this->langCache) {
            $this->langCache = $this->getLanguages();
        }

        /** @var Language $lang */
        foreach ($this->langCache as $lang) {
            if (is_numeric($locale) && $lang->getId() == $locale) {
                return $lang;
            } elseif (is_string($locale) && $lang->getCode() == $locale) {
                return $lang;
            } elseif (is_object($locale) && $lang->getId() == $locale->getId()) {
                return $lang;
            }
        }

        throw new \InvalidArgumentException("Invalid language: {$locale}");
    }

    /**
     * @return boolean
     */
    public function clearLanguageCache()
    {
        $this->langCache       = null;
        $this->activeLanguages = null;
        $this->otherLanguages  = null;
//        dd('cache');
        return true;
    }

    /**
     * @param Language $lang
     */
    public function setLanguage(Language $lang)
    {
        $this->currentLanguage = $lang;
        $this->setLanguageCode($lang->getCode());
        app()->setLocale($lang->getCode());
    }

    /**
     * @param string                       $path
     * @param null|integer|string|Language $lang
     *
     * @return string
     */
    public function preparePath($path, $lang = null)
    {
        if ($lang == null) {
            $lang = $this->getLanguage();
        } else {
            $lang = $this->getBy($lang);
        }

        $path = ltrim($path, '/');
        if ($this->hasValidLanguageSegment()) {
            $locale = $this->getLanguageSegment();
            $path   = ltrim($path, $locale);
            $path   = ltrim($path, '/');
        }

        $path = rtrim($path, '?');

        if ($lang->isDefault() && $this->hideDefault) {
            return $path;
        }

        return $lang->getCode().'/'.$path;
    }

    /**
     * @param string     $name
     * @param array      $parameters
     * @param mixed|null $lang
     * @param boolean    $absolute
     *
     * @return string
     */
    public function route($name, $parameters = [], $lang = null, $absolute = true)
    {
        $path = route($name, $parameters, false);
        $path = $this->preparePath($path, $lang);
        if ($absolute) {
            return url($path);
        }

        return $path;
    }

    /**
     * @param string       $path
     * @param array|null   $parameters
     * @param mixed|null   $lang
     * @param boolean|null $secure
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function url($path, $parameters = [], $lang = null, $secure = null)
    {
        $path = $this->preparePath($path, $lang);
        return url($path, $parameters, $secure);
    }

}