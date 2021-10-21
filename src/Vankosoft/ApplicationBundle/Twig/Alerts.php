<?php namespace VS\ApplicationBundle\Twig;

class Alerts
{
    public static $WARNINGS  = [];
    
    public static function warnings()
    {
        return self::$WARNINGS;
    }
}
