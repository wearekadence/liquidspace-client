<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\MemberResponse;

class MemberRequest implements RequestInterface
{
    public function __construct(
        private readonly string $accountId,
        private readonly string $memberIdOrEmail,
    ) {}

    public static function getResponseClass(): string
    {
        return MemberResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return '/enterpriseaccountmanagement/api/enterpriseaccounts/'.
            $this->accountId.'/members/'.\urlencode($this->memberIdOrEmail);
    }

    public function getOptions(): array
    {
        return [];
    }
}