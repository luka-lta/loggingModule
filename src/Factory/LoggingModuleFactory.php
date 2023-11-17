<?php
declare(strict_types=1);

namespace LoggingModule\Factory;

use LoggingModule\LoggingModule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class LoggingModuleFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): LoggingModule
    {
        $logger = $container->get(LoggerInterface::class);
        $logFile = $config['logging']['log_file'] ?? 'data/log/app.log';
        $logFormat = $config['logging']['log_format'] ?? null;
        $logFormat = $logFormat ? new $logFormat() : null;

        return new LoggingModule($logger, $logLevel, $logFile, $logFormat);
    }
}