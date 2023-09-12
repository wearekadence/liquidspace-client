<?php

namespace LiquidSpace\Request;

use LiquidSpace\Entity\Enterprise\ReservationStatusFilter;
use LiquidSpace\Response\EnterpriseAccountListReservationsResponse;

class EnterpriseAccountListReservationsRequest implements RequestInterface
{
    public function __construct(
        private readonly string $accountId,
        private readonly ?\DateTimeImmutable $fromDate = null,
        private readonly ?\DateTimeImmutable $toDate = null,
        private readonly ?string $memberId = null,
        private readonly ?string $teamId = null,
        private readonly ReservationStatusFilter $status = ReservationStatusFilter::All,
    ) {
    }

    public static function getResponseClass(): string
    {
        return EnterpriseAccountListReservationsResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return '/enterpriseaccountmanagement/api/enterpriseaccounts/'.$this->accountId.'/activities/reservations';
    }

    public function getOptions(): array
    {
        $optionalOptions = [];

        if ($this->teamId) {
            $optionalOptions['query']['teamId'] = $this->teamId;
        }

        if ($this->memberId) {
            $optionalOptions['query']['memberId'] = $this->memberId;
        }

        if ($this->fromDate) {
            $optionalOptions['query']['start'] = $this->fromDate->format('Y-m-d\TH:i:s');
        }

        if ($this->toDate) {
            $optionalOptions['query']['end'] = $this->toDate->format('Y-m-d\TH:i:s');
        }

        $optionalOptions['query']['reservationsStatuses'] = $this->status->value;

        return $optionalOptions;
    }

    public function requiresEnterpriseToken(): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        return false;
    }
}
