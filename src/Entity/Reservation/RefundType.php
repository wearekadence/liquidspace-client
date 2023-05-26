<?php

namespace LiquidSpace\Entity\Reservation;

enum RefundType: int
{
    case FullRefund = 0;
    case MonthlyRate = 1;
    case Incidentals = 2;
    case Deposit = 3;
    case SetupFee = 4;
    case EditFee = 5;
    case SalesTax = 6;
    case HourlySalesPrice = 7;
    case ServiceCharge = 8;
}
