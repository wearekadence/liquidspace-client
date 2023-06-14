<?php

namespace LiquidSpace\Request;

interface RequestInterface
{
    public static function getResponseClass(): string;

    public static function getMethod(): HttpMethod;

    public function getPath(): string;

    public function getOptions(): array;

    public function requiresEnterpriseToken(): bool;
}
