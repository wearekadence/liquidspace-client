<?php

namespace LiquidSpace\Entity\Enterprise;

enum MemberGroupInvitationStatus: int
{
    case None = -1;
    case Pending = 0;
    case Active = 1;
}
