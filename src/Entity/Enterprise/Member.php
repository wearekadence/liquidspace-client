<?php

namespace LiquidSpace\Entity\Enterprise;

class Member
{
    public readonly string $id;
    public readonly string $fullName;
    public readonly string $email;
    public readonly \DateTimeImmutable $createdDate;
    public readonly string $notes;
    public readonly EnterpriseAccountInvitationStatus $accountInvitationStatus;
    public readonly MemberGroupInvitationStatus $groupInvitationStatus;
    public readonly string $title;
    public readonly ?string $phoneNumber;
    public readonly string $city;
    public readonly string $country;
    public readonly string $picture;
    public readonly string $team;
    public readonly string $externalAccountId;
    public readonly string $costCenter;
    public readonly ?string $company;
    public readonly string $teamId;
    public readonly \DateTimeImmutable $joinedDate;
    public readonly \DateTimeImmutable $lastActivityDate;
    /** @var OnDemandLocation[] */
    public readonly array $locations;

    public function __construct(array $memberData)
    {
        $accountInvitationStatus = EnterpriseAccountInvitationStatus::tryFrom($memberData['status']);
        if (null === $accountInvitationStatus) {
            throw new \InvalidArgumentException('Invalid location type: '.$memberData['status']);
        }

        $groupInvitationStatus = MemberGroupInvitationStatus::tryFrom($memberData['memberGroupStatus']);
        if (null === $groupInvitationStatus) {
            throw new \InvalidArgumentException('Invalid location type: '.$memberData['memberGroupStatus']);
        }

        $this->id = $memberData['id'];
        $this->fullName = $memberData['fullName'];
        $this->email = $memberData['email'];
        $this->createdDate = new \DateTimeImmutable($memberData['createdDate']);
        $this->notes = $memberData['notes'];
        $this->accountInvitationStatus = $accountInvitationStatus;
        $this->groupInvitationStatus = $groupInvitationStatus;
        $this->title = $memberData['title'];
        if (isset($memberData['phoneNumber'])) {
            $this->phoneNumber = $memberData['phoneNumber'];
        } else {
            $this->phoneNumber = null;
        }
        $this->city = $memberData['geoCity'];
        $this->country = $memberData['geoCountry'];
        $this->picture = $memberData['picture'];
        $this->team = $memberData['team'];
        $this->externalAccountId = $memberData['externalAccountId'];
        $this->costCenter = $memberData['costCenter'];
        if (isset($memberData['company'])) {
            $this->company = $memberData['company'];
        } else {
            $this->company = null;
        }
        $this->teamId = $memberData['teamId'];
        $this->joinedDate = new \DateTimeImmutable($memberData['joinedDate']);
        $this->lastActivityDate = new \DateTimeImmutable($memberData['lastActivityDate']);
        $this->locations = array_map(
            fn (array $onDemandLocationData) => new OnDemandLocation($onDemandLocationData),
            $memberData['locations'],
        );
    }
}
