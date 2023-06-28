<?php

namespace LiquidSpace\Entity\Reservation;

class ReservationInvitee
{
    public readonly string $firstName;
    public readonly string $lastName;
    public readonly ?string $imageUrl;
    public readonly string $email;
    public readonly ?string $spaceName;

    public function __construct(array $inviteeData)
    {
        $this->firstName = $inviteeData['firstName'];
        $this->lastName = $inviteeData['lastName'];
        $this->imageUrl = $inviteeData['imageUrl'];
        $this->email = $inviteeData['email'];
        $this->spaceName = $inviteeData['spaceName'];
    }
}
