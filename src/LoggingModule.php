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
    private string $webhook;

    /**
     * @throws LoggingException
     */
    public function __construct(private readonly EmbedMapping $embedMapping)
    {
        $this->webhook = EnvironmentRepository::get('DISCORD_WEBHOOK_URL');
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {}

    public function logWithDiscordMessage(string $level, string $action, Stringable|string $message, array $context = []): void
    {
        $this->log($level, $message, $context);
        if ($level === LogLevel::INFO) {
            $this->embedMapping->setInfoEmbed($action, $message, $context);
            $this->sendEmbed(json_encode($this->embedMapping->getEmbedData()));
        }

        if ($level === LogLevel::NOTICE) {
            $this->embedMapping->setCustomEmbed(LogLevel::NOTICE, $action, '', $context);
            $this->sendEmbed(json_encode($this->embedMapping->getEmbedData()));
        }
    }

    public function logErrorWithDiscordMessage(Stringable|string $message, ?Throwable $throwable, array $additionalData = []): void
    {
        if (!$throwable) {
            $this->error($message, $additionalData);
            $this->embedMapping->setCustomEmbed(LogLevel::ERROR, 'Error', $message, $additionalData);
            $this->sendEmbed(json_encode($this->embedMapping->getEmbedData()));
            return;
        }
        $this->error($throwable->getMessage(), [
            'trace' => $throwable,
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'additionalData' => $additionalData
        ]);
        $this->embedMapping->setErrorEmbed($throwable);
        $this->sendEmbed(json_encode($this->embedMapping->getEmbedData()));
    }


    private function sendEmbed(string $embedData): void
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->webhook,
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