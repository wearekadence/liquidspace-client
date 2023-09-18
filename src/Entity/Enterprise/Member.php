<?php

namespace LiquidSpace\Entity\Enterprise;

class Member
{
    public readonly string $id;
    public readonly string $fullName;
    public readonly string $email;
    public readonly \DateTimeImmutable $createdDate;
    public readonly ?string $notes;
    public readonly EnterpriseAccountInvitationStatus $accountInvitationStatus;
    public readonly MemberGroupInvitationStatus $groupInvitationStatus;
    public readonly ?string $title;
    public readonly ?string $phoneNumber;
    public readonly ?string $city;
    public readonly ?string $country;
    public readonly ?string $picture;
    public readonly string $team;
    public readonly ?string $externalAccountId;
    public readonly ?string $costCenter;
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
        if (isset($memberData['notes'])) {
            $this->notes = $memberData['notes'];
        }
        $this->accountInvitationStatus = $accountInvitationStatus;
        $this->groupInvitationStatus = $groupInvitationStatus;
        if (isset($memberData['title'])) {
            $this->title = $memberData['title'];
        }
        if (isset($memberData['phoneNumber'])) {
            $this->phoneNumber = $memberData['phoneNumber'];
        }
        if (isset($memberData['geoCity'])) {
            $this->city = $memberData['geoCity'];
        }
        if (isset($memberData['geoCountry'])) {
            $this->country = $memberData['geoCountry'];
        }
        if (isset($memberData['picture'])) {
            $this->picture = $memberData['picture'];
        }
        $this->team = $memberData['team'];
        if (isset($memberData['externalAccountId'])) {
            $this->externalAccountId = $memberData['externalAccountId'];
        }
        if (isset($memberData['costCenter'])) {
            $this->costCenter = $memberData['costCenter'];
        }
        if (isset($memberData['company'])) {
            $this->company = $memberData['company'];
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
