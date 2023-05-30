<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Enterprise\Member;

class MemberResponse
{
    public readonly Member $member;

    public function __construct(array $memberData)
    {
        $this->member = new Member($memberData);
    }
}
