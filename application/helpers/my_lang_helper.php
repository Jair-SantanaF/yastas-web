<?php

function set_translation_language($lang)
{
    $directorioArchivos = APPPATH.'language/locale';
    //$_ENV['lang']= $lang.'.UTF-8';
    putenv('LANG='.$lang.'.UTF-8');
    setlocale(LC_ALL, $lang.'.UTF-8');
    bindtextdomain('lang', $directorioArchivos);
    textdomain('lang');//nombre de los archivos
}