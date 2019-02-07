<?php

namespace Vanguard\Libraries\Enum;

class CampaignStatus
{
    const ACTIVE_CAMPAIGN = 'active';

    const ON_HOLD = 'on_hold';

    const PENDING = 'pending';

    const PAYMENT_PENDING = 'PENDING';

    const PAYMENT_SUCCESS = 'SUCCESSFUL';

    const TRANSACTION_TYPE = 'PAID FOR CAMPAIGN';

    const FINISHED = 'expired';
}
