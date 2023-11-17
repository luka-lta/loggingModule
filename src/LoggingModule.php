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
        if ($level === LogLevel::INFO) {
            $this->embedMapping->setInfoEmbed($action, $message, $context);
            $embedData = json_encode($this->embedMapping->getEmbedData());
            $this->sendEmbed($embedData);
        }
    }

    public function logErrorWithDiscordMessage(Throwable $throwable): void
    {
        $this->embedMapping->setErrorEmbed($throwable);
        $embedData = json_encode($this->embedMapping->getEmbedData());
        $this->sendEmbed($embedData);
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