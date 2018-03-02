<?php
namespace app\modules\canteen\components;

class DaysWeek
{
    public static $days = [
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
//        7 => 'Воскресенье',
    ];

    public static function getDayNumbers()
    {
        return array_keys(self::$days);
    }

    public static function getDayName($number)
    {
        return self::$days[$number];
    }

    public static function getDayNames()
    {
        return self::$days;
    }
}