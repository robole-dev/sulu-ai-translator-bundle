<?php

declare(strict_types=1);

namespace Robole\SuluAITranslatorBundle\Service;

use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

/**
 * Decides whether the current user may translate in the area of a resource.
 *
 * An area is the resource key of a form (e.g. "pages", "articles", "snippets"). Areas that are not listed in the
 * configuration share the area "other". Every area has its own security context.
 */
class TranslationPermissionChecker
{
    public const SECURITY_CONTEXT_PREFIX = 'sulu.ai_translator.';
    public const AREA_OTHER = 'other';

    /**
     * @param string[] $areas resource keys with their own security context
     */
    public function __construct(
        private readonly SecurityCheckerInterface $securityChecker,
        private readonly bool $enabled = false,
        private readonly array $areas = []
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string[] all areas including "other"
     */
    public function getAreas(): array
    {
        return array_values(array_unique([...$this->areas, self::AREA_OTHER]));
    }

    public function getArea(?string $resourceKey): string
    {
        return null !== $resourceKey && in_array($resourceKey, $this->areas, true) ? $resourceKey : self::AREA_OTHER;
    }

    public function getSecurityContext(string $area): string
    {
        return self::SECURITY_CONTEXT_PREFIX . $area;
    }

    public function isAllowed(?string $resourceKey): bool
    {
        if (!$this->enabled) {
            return true;
        }

        return $this->securityChecker->hasPermission(
            $this->getSecurityContext($this->getArea($resourceKey)),
            PermissionTypes::EDIT
        );
    }
}
