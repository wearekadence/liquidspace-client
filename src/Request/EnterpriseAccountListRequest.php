<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\EnterpriseAccountListResponse;

class EnterpriseAccountListRequest implements RequestInterface
{
    public static function getResponseClass(): string
    {
        return EnterpriseAccountListResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return '/enterpriseaccountmanagement/api/enterpriseaccounts';
    }

    public function getOptions(): array
    {
        return [];
    }

    public function requiresEnterpriseToken(): bool
    {
        return true;
    }
}
