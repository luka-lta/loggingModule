<?php
declare(strict_types=1);

namespace LoggingModule\Repository;

class EnvironmentRepository
{
    public static function get(string $name): string
    {
        return getenv($name);
    }
}