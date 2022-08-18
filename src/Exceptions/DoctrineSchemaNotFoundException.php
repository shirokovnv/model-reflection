<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Exceptions;

class DoctrineSchemaNotFoundException extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'Doctrine schema manager not found.';

    /**
     * @var int
     */
    protected $code = 500;
}
