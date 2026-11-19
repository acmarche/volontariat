<?php

namespace AcMarche\Volontariat\Seo;

use Symfony\Component\HttpFoundation\RequestStack;

class SeoService
{
    private const SITE_NAME = 'Plate-forme du volontariat - Marche-en-Famenne';
    private const DEFAULT_DESCRIPTION = 'Un lieu virtuel de rencontre entre volontaires et associations pour des implications concrètes.';

    private ?SeoData $seoData = null;

    public function __construct(private readonly RequestStack $requestStack) {}

    public function setData(SeoData $seoData): void
    {
        $this->seoData = $seoData;
    }

    public function getTitle(): string
    {
        $title = $this->seoData?->title ?: '';

        return $title !== '' ? $title.' - '.self::SITE_NAME : self::SITE_NAME;
    }

    public function getDescription(): string
    {
        return $this->truncate($this->seoData?->description ?: self::DEFAULT_DESCRIPTION, 160);
    }

    public function getSiteName(): string
    {
        return self::SITE_NAME;
    }

    public function getImageUrl(): ?string
    {
        return $this->seoData?->imageUrl;
    }

    public function getUrl(): string
    {
        if ($this->seoData?->url) {
            return $this->seoData->url;
        }

        $request = $this->requestStack->getCurrentRequest();

        return $request?->getUri() ?? '';
    }

    private function truncate(string $text, int $length): string
    {
        $text = strip_tags($text);
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - 3).'...';
    }
}
