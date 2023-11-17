<?php
declare(strict_types=1);

namespace LoggingModule\Service;

use Throwable;

class EmbedMapping
{
    private string $avatarUrl;
    private string $webhookTag;
    private array $embedData;

    public function __construct()
    {
        $this->avatarUrl = 'https://cdn-icons-png.flaticon.com/512/1960/1960242.png';
        $this->webhookTag = 'Logger';
    }

    private function getDefaultEmbedData(): array
    {
        return [
            'username'   => $this->webhookTag,
            'avatar_url' => $this->avatarUrl,
        ];
    }

    public function setErrorEmbed(Throwable $throwable): void
    {
        $this->embedData = [
            "embeds" => [
                [
                    "title" => "Error",
                    "type" => "rich",
                    "timestamp" => date('Y-m-d H:i:s'),
                    "color" => hexdec('c20b08'),
                    "footer" => [
                        "text" => "Logger | Error",
                        "icon_url" => $this->avatarUrl
                    ],
                    "fields" => [
                        [
                            "name" => "ExceptionName",
                            "value" => $throwable::class,
                            "inline" => false
                        ],
                        [
                            "name" => "Message",
                            "value" => $throwable->getMessage(),
                            "inline" => false
                        ]
                    ]
                ]
            ]
        ];
    }

    public function setInfoEmbed(string $action, string $message, array $context = []): void
    {
        $this->embedData = [
            "embeds" => [
                [
                    "title" => $action,
                    "type" => "rich",
                    "timestamp" => date('Y-m-d H:i:s'),
                    "color" => hexdec('edb832'),
                    "footer" => [
                        "text" => "Logger | Info",
                        "icon_url" => $this->avatarUrl
                    ],
                    "fields" => [
                        [
                            "name" => "Message",
                            "value" => $message,
                            "inline" => false
                        ],
                        [
                            "name" => "Context",
                            "value" => json_encode($context),
                            "inline" => false
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getEmbedData(): array
    {
        return array_merge($this->getDefaultEmbedData(), $this->embedData);
    }
}