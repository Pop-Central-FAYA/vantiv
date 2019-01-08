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
}
