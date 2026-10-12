<?php

declare(strict_types=1);

namespace Sitegeist\LostInTranslation\Domain;

/**
 * TranslationServiceInterface::translate() returns the source texts when the translation fails, so callers cannot tell a
 * failure from a text that needs no translation. Callers that have to report failures use translateOrFail() instead.
 */
interface FailureReportingTranslationServiceInterface extends TranslationServiceInterface
{
    /**
     * @param array<string,string> $texts
     * @param string $targetLanguage
     * @param string|null $sourceLanguage
     * @return array<string,string>
     * @throws TranslationFailedException
     */
    public function translateOrFail(array $texts, string $targetLanguage, ?string $sourceLanguage = null): array;
}
