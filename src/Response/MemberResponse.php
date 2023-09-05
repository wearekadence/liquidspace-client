<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Enterprise\Member;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MemberResponse
{
    public readonly Member $member;

    public function __construct(ResponseInterface $response)
    {
        $content = $response->toArray();

        $this->member = new Member($content);
    }
}
