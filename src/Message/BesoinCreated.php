<?php

namespace AcMarche\Volontariat\Message;

class BesoinCreated
{
    public function __construct(
        private readonly int $besoinId,
    ) {}

    public function getBesoinId(): int
    {
        return $this->besoinId;
    }
}