<?php


namespace app\helpers;


class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * Make a string's first character uppercase
     * Works with various encodings
     * @param string $str
     * @param bool $lowerEnd
     * @param string $encoding
     * @return string
     */
    public static function ucfirst($str, $lowerEnd = false, $encoding = 'UTF-8')
    {
        $firstLetter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        if ($lowerEnd) {
            $strEnd = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        } else {
            $strEnd = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        return $firstLetter.$strEnd;
    }

    /**
     * Вернет первую букву из строки
     * @param $string
     * @param bool $ucfirst в верхнем регистре
     * @return string
     */
    public static function firstLetter($string, $ucfirst = false, $encoding = 'UTF-8')
    {
        $letter = mb_substr($string, 0, 1, $encoding);
        return $ucfirst ? self::ucfirst($letter) : $letter;
    }
}