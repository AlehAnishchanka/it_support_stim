<?php

namespace local\stim\it_support_stim\classes;

/*
 * Класс работает только с первой ошибкой параметров api, которую он захватил.
 * После исправления этой ошибки и повторного обращения он захватит следующую первую ошибку.
 */
class ApiMistake
{
    public static $isMistakeSet = false;
    public static $textMistake;

    public function __construct( $textMistake )
    {
        if( !self::$isMistakeSet ) {
            self::$textMistake = $textMistake;
            self::$isMistakeSet = true;
        }
    }

    public static function getMistake()
    {
        return self::$textMistake;
    }
}