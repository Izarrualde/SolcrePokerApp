<?php
namespace Test;

trait ContainerAwareTrait
{
    public static $container;
    public static function getContainer()
    {
        return self::$container;
    }
    public static function setContainer($container)
    {
       self::$container = $container;
    }
}