<?php

namespace LiquidSpace\Entity;

class Impersonation
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $memberId,
        public readonly string $enterpriseToken,
        public readonly string $memberToken,
    ) {
    }
}
