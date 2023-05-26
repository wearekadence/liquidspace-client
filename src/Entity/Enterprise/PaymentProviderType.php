<?php

namespace LiquidSpace\Entity\Enterprise;

enum PaymentProviderType: int
{
    case AuthorizeNet = 0;
    case BrainTree = 1;
    case Stripe = 2;
    case StripeWireTransfer = 3;
}
