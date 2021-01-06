<?php

namespace Shirokovnv\ModelReflection\Exceptions;

use Exception;

class UnknownRelTypeException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(string $type, $code = 500, Exception $previous = null) {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct("Relation type $type is not valid", $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}