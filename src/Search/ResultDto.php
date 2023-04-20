<?php

namespace AcMarche\Volontariat\Search;

class ResultDto
{
    public string $name;
    public ?string $excerpt;
    public string $url;

    public function __construct(string $name, ?string $excerpt, string $url)
    {
        $this->name = $name;
        $this->url = $url;
        $this->excerpt = $excerpt;
    }
}