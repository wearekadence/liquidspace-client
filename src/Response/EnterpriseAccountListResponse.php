<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Enterprise\AccountList;

class EnterpriseAccountListResponse
{
    /** @var AccountList[] */
    public readonly array $accounts;

    public function __construct(array $accountsData)
    {
        $this->accounts = array_map(
            fn (array $accountData) => new AccountList($accountData),
            $accountsData,
        );
    }
}
