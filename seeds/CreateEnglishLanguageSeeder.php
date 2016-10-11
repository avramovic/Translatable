<?php

use Avram\Translatable\Models\Language;
use Illuminate\Database\Seeder;

class CreateEnglishLanguageSeeder extends Seeder
{
    /**
     * Create English language
     *
     * @return void
     */
    public function run()
    {
        $language = new Language();
        $language->setName('English');
        $language->setCode('en');
        $language->setLocale('en_US');
        $language->setActive(true);
        $language->setDefault(true);
        $language->save();
        echo "English language created successfully!".__LINE__;
    }
}
