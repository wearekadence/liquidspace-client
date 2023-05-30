<?php

namespace LiquidSpace\Entity\Enterprise;

class AccountList
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $discriminator;

    public function __construct(array $accountListData)
    {
        $this->id = $accountListData['id'];
        $this->name = $accountListData['name'];
        $this->discriminator = $accountListData['discriminator'];
    }
}
