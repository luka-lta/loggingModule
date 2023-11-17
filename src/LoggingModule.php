<?php
declare(strict_types=1);

namespace LoggingModule;

use LoggingModule\Exception\LoggingException;
use LoggingModule\Repository\EnvironmentRepository;
use LoggingModule\Service\EmbedMapping;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

final class LoggingModule extends AbstractLogger
{
    public function __construct(private readonly EmbedMapping $embedMapping)
    {
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {}

    /**
     * @throws LoggingException
     */
    public function logWithDiscordMessage(string $level, string $action, Stringable|string $message, array $context = []): void
    {
        $webhook = EnvironmentRepository::get('DISCORD_WEBHOOK_URL');
        if ($level === LogLevel::INFO) {
            $this->embedMapping->setInfoEmbed($action, $message, $context);
        }
    }

    /**
     * @throws LoggingException
     */
    public function logErrorWithDiscordMessage(Throwable $throwable): void
    {
        $webhook = EnvironmentRepository::get('DISCORD_WEBHOOK_URL');
        $this->embedMapping->setErrorEmbed($throwable);
        $embedData = json_encode($this->embedMapping->getEmbedData());
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $webhook,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $embedData,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ]
        ]);

        curl_exec($ch);
        curl_close($ch);
    }
}