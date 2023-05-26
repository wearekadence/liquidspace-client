<?php

namespace LiquidSpace\Entity\Enterprise;

enum CreditCardType: int
{
    case Visa = 0;
    case MasterCard = 1;
    case Discover = 2;
    case Amex = 3;
}
