<?php
declare(strict_types=1);

namespace LoggingModule\Repository;

use LoggingModule\Exception\LoggingException;

class EnvironmentRepository
{
    /**
     * @throws LoggingException
     */
    public static function get(string $name): string
    {
        if (getenv($name) === false) {
            throw new LoggingException(sprintf('Environment variable "%s" not found.', $name));
        }

        return getenv($name);
    }
}