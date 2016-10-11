<?php namespace Avram\Translatable\Facades;

use Avram\Translatable\Translatable;
use Illuminate\Support\Facades\Facade;

class TranslatableFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return Translatable::class;
    }

}