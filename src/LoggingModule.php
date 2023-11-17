<?php
declare(strict_types=1);

namespace LoggingModule;

use LoggingModule\Repository\EnvironmentRepository;
use LoggingModule\Service\EmbedMapping;
use Psr\Log\AbstractLogger;

final class LoggingModule extends AbstractLogger
{
    public function __construct(private EmbedMapping $embedMapping)
    {
    }


    public function log($level, \Stringable|string $message, array $context = []): void
    {
        // TODO: Implement log() method.
    }

    public function logWithDiscordMessage(string $level, \Stringable|string $message, array $context = [],): void
    {
        if (EnvironmentRepository::get('DISCORD_WEBHOOK_URL') !== null) {
            $webhook = EnvironmentRepository::get('DISCORD_WEBHOOK_URL');
        }
    }

    public function logErrorWithDiscordMessage(\Throwable $throwable): void
    {
        if (EnvironmentRepository::get('DISCORD_WEBHOOK_URL') !== null) {
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
}