<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\EnterpriseAccountListMembersResponse;

class EnterpriseAccountListMembersRequest implements RequestInterface
{
    public function __construct(
        private readonly string $accountId,
    ) {
    }

    public static function getResponseClass(): string
    {
        return EnterpriseAccountListMembersResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return '/enterpriseaccountmanagement/api/enterpriseaccounts/'.$this->accountId.'/members';
    }

    public function getOptions(): array
    {
        return [];
    }

    public function requiresEnterpriseToken(): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        return false;
    }
}
