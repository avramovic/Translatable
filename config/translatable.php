<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hide Default Language Code
    |--------------------------------------------------------------------------
    |
    | Hiding default language code in URLs enables the middleware redirection.
    | Once URL has been accessed with language slug of the default language
    | it will be automatically be redirected to the URL without slug.
    |
    */

    'hide_default' => true,


    /*
    |--------------------------------------------------------------------------
    | Automatically Translate Models
    |--------------------------------------------------------------------------
    |
    | Enabling this option will automatically translate all the models' fields
    | to the currently active language. You can still use the translate()
    | method and magic fields to access other languages translations.
    |
    */

    'auto_translate' => true,


    /*
    |--------------------------------------------------------------------------
    | Enable Magic Fields
    |--------------------------------------------------------------------------
    |
    | Enabling this option allows you to access models' fields translation via
    | ->FIELD_LANGCODE fields. It also introduces the ->FIELD_i18n which
    | will always point to the FIELD's current language translation.
    |
    */

    'magic_fields' => true,
];