<?php
class ModelFactory {
    public static function create($modelName) {
        $classPath = "../models/{$modelName}.php";
        if (file_exists($classPath)) {
            require_once $classPath;
            if (class_exists($modelName)) {
                return new $modelName();
            }
        }
        throw new Exception("Model {$modelName} not found.");
    }
}
?>
