<?php

namespace Vanguard\Libraries\Enum;

class ClassMessages
{
    const NEGATIVE_AGE_MESSAGE = 'The minimum or maximum age cannot have a negative value';

    const AGE_ERROR_MESSAGE = 'The minimum age cannot be greater than the maximum age';

    const DATE_ERROR_MESSAGE = 'Start Date cannot be greater than End Date';

    const CAMPAIGN_INFROMATION_SESSION_DATA_LOSS = 'Data lost, please go back and select your filter criteria';

    const EMPTY_ADSLOT_RESULT_FROM_FILTER = 'You have no matches for the criteria selected, please go back and adjust the values';

    const FIRST_CHANNEL_ERROR = 'An error occurred in getting the media channels';

    const FILE_DELETE_SUCCESS_MESSAGE = 'File deleted successfully...';

    const FILE_DELETE_ERROR_MESSAGE = 'Error deleting file...';

    const EMPTY_CART_MESSAGE = 'Your cart is empty';

    const REMOVE_PRESELECTED_ADSLOT = 'Adslot removed successfully';

    const CAMPAIGN_SUCCESS_MESSAGE = 'Campaign created successfully, please review and submit';

    const CAMPAIGN_ERROR_MESSAGE = 'There was problem creating campaign';

    const WALLET_NOT_EXIST = 'Wallet does not exist for this agency';

    const INSUFFICIENT_FUND = 'Insufficient fund, please fund your wallet to complete campaign creation';

    const START_DATE_ERROR = 'Campaign cant be submitted because the start date has exceeded the current date';
}
