<?php

namespace AcMarche\Volontariat\Twig\Extension;

use AcMarche\Volontariat\Seo\SeoService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SeoExtension extends AbstractExtension
{
    public function __construct(private readonly SeoService $seoService) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seo_title', $this->seoService->getTitle(...)),
            new TwigFunction('seo_description', $this->seoService->getDescription(...)),
            new TwigFunction('seo_site_name', $this->seoService->getSiteName(...)),
            new TwigFunction('seo_image', $this->seoService->getImageUrl(...)),
            new TwigFunction('seo_url', $this->seoService->getUrl(...)),
        ];
    }
}
