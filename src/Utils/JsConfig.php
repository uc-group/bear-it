<?php

namespace App\Utils;

class JsConfig
{
    public function __construct(
        private string $wsUrl
    ) {}

    public function getConfig()
    {
        return [
            'wsUrl' => $this->wsUrl
        ];
    }
}
