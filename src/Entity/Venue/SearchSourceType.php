<?php

namespace LiquidSpace\Entity\Venue;

enum SearchSourceType: int
{
    case Unknown = 0;
    case Onboarding = 1;
    case SavedSearch = 2;
    case CreateAlertNoResults = 3;
    case TourRequest = 4;
    case NeighborhoodRequest = 5;
    case CityRequest = 6;
    case WorkspaceRequest = 7;
    case HomepageConcierge = 8;
    case MyLiquidSpaceConcierge = 9;
    case WorkspaceTypeRequest = 10;
    case NeighborhoodFollowRequest = 11;
    case MessageHost = 12;
    case CheckAvailability = 13;
    case ColliersConcierge = 14;
    case WashingtonReit = 15;
    case Brandywine = 16;
    case AvisonYoung = 17;
    case VenueHostReferral = 18;
    case BrandedPortal = 19;
    case RequestForProposal = 20;
    case AffiliateUserInvite = 21;
    case DealProposal = 22;
    case AltSpaceRequest = 23;
    case CollectionConcierge = 24;
    case GuestOnboarding = 25;
    case EmbeddedConcierge = 26;
    case Membership = 27;
}
