<?php

namespace LiquidSpace\Entity\Reservation;

class ReservationOperations
{
    public readonly bool $editAllowed;
    public readonly bool $cancelAllowed;
    public readonly bool $checkInAllowed;
    public readonly bool $checkOutAllowed;
    public readonly bool $doNotRenewAllowed;
    public readonly bool $extendAllowed;
    public readonly bool $inviteAllowed;

    public function __construct(array $operationsData)
    {
        $this->editAllowed = $operationsData['editAllowed'];
        $this->cancelAllowed = $operationsData['cancelAllowed'];
        $this->checkInAllowed = $operationsData['checkInAllowed'];
        $this->checkOutAllowed = $operationsData['checkOutAllowed'];
        $this->doNotRenewAllowed = $operationsData['doNotRenewAllowed'];
        $this->extendAllowed = $operationsData['extendAllowed'];
        $this->inviteAllowed = $operationsData['inviteAllowed'];
    }
}
