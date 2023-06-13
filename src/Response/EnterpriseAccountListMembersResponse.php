<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Enterprise\MemberList;

class EnterpriseAccountListMembersResponse
{
    /** @var MemberList[] */
    public readonly array $members;

    public function __construct(array $membersData)
    {
        $this->members = array_map(
            fn (array $memberData) => new MemberList($memberData),
            $membersData,
        );
    }
}
