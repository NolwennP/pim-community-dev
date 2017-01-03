<?php

namespace Akeneo\Component\StorageUtils\Updater;

// catch this exception if you need
class InvalidPropertyException extends ObjectUpdaterException
{
    protected $property;

    public function __construct($property, $message, $code, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->property = $property;
    }

    public function getProperty() {
        return $this->property;
    }
}