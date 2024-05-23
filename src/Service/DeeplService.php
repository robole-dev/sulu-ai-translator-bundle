<?php

namespace Robole\SuluAiTranslatorBundle\Service;

use DeepL\Translator;

class DeeplService
{
    private Translator $client;

    public function __construct(
        private readonly string $deeplApiKey,
    ) {
        $this->client = new Translator($this->deeplApiKey);
    }

    public function translateText(string $text, string $source = null, string $target, ?array $options = []): object
    {
        return $this->client->translateText($text, $source, $target, $options);
    }

    public function getUsage(): ?object
    {
        $usage = $this->client->getUsage();

        if ($usage->character) {
            return (object) [
                'character_count' => $usage->character->count,
                'character_limit' => $usage->character->limit
            ];
        }

        return null;
    }
}
