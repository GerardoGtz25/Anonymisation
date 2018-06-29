<?php
namespace Anonymization\Anonymization;

class FactoryAnonymization {

    private static $namespace = __NAMESPACE__;

    public static function loadConfig($file){

        $type = pathinfo($file);

        $class_name = ucfirst($type['extension']);

        $class_name = self::$namespace . "\\$class_name";

        return new $class_name($file);

    }

}
