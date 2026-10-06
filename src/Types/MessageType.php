<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex\Types;

enum MessageType: string
{
    case MarketingAndResearch = 'MarketingAndResearch';
    case NotificationsAndReminders = 'NotificationsAndReminders';
    case AuthenticationAndSecurity = 'AuthenticationAndSecurity';
    case PaymentsAndCollections = 'PaymentsAndCollections';
    case InternalOperations = 'InternalOperations';
    case CustomerSupport = 'CustomerSupport';
    case Conversational = 'Conversational';
}