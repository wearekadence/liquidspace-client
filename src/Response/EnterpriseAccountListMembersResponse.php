<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Enterprise\MemberList;
use Symfony\Contracts\HttpClient\ResponseInterface;

class EnterpriseAccountListMembersResponse
{
    /** @var MemberList[] */
    public readonly array $members;

    public function __construct(ResponseInterface $response)
    {
        $membersData = $response->toArray();

        $this->members = array_map(
            fn (array $memberData) => new MemberList($memberData),
            $membersData,
        );
    }
}
