<?php

namespace AcMarche\Volontariat\Seo;

class SeoData
{
    public function __construct(
        public string $title = '',
        public string $description = '',
        public ?string $imageUrl = null,
        public ?string $url = null,
    ) {}
}