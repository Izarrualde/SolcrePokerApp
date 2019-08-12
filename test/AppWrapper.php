<?php

namespace Test;

class AppWrapper 
{
    public static $app;
    public static function getApp()
    {
        return self::$app;
    }
    public static function setApp($app)
    {
       self::$app = $app;
    }
    public static function getContainer()
    {
        return self::$app->getContainer();
    }
}