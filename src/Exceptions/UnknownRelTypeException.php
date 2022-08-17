<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Exceptions;

class UnknownRelTypeException extends \Exception
{
    /**
     * @param string $type
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(string $type, int $code = 500, \Exception $previous = null)
    {
        parent::__construct("Relation type $type is not valid", $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
