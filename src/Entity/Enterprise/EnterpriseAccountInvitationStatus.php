<?php

namespace LiquidSpace\Entity\Enterprise;

enum EnterpriseAccountInvitationStatus: int
{
    case None = 0;
    case Invited = 1;
    case Active = 2;
    case Suspended = 3;
    case Removed = 4;
}
