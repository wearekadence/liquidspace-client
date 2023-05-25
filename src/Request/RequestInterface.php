<?php

namespace Client\Request;

interface RequestInterface
{
    public function getMethod(): HttpMethod;
    public function getPath(): string;
    public function getOptions(): array;
    public function getResponseClass(): string;
}
