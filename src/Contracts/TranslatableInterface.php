<?php

namespace Avram\Translatable\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface TranslatableInterface
{
    /**
     * @return MorphMany
     */
    public function translations();

    /**
     * @param string|null $lang
     * @param bool        $forceEmpty
     *
     * @return string
     */
    public function translate($lang = null, $forceEmpty = false);
}