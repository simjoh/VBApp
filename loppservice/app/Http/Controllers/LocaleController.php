<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function switchLang($lang)
    {
        if (array_key_exists($lang, Config::get('app.available_locales'))) {
            App::setLocale($lang);
            Session::put('applocale', $lang);
        }
        return Redirect::back();
    }
}
