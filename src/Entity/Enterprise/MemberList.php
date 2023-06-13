<?php

namespace LiquidSpace\Entity\Enterprise;

class MemberList
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $email;

    public function __construct(array $memberData)
    {
        $this->id = $memberData['memberId'];
        $this->name = $memberData['fullName'];
        $this->email = $memberData['email'];
    }
}
