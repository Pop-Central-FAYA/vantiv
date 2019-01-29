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

    const DEBIT_MESSAGE = 'Debit successful';

    const DEBIT_TRANSACTION_TYPE = 'DEBIT WALLET';

    const WALLET_PAYMENT_METHOD = 'WALLET_PAYMENT';

    const CAMPAIGN_SUBMIT_TO_BROADCASTER_ERROR = 'There was an error submitting this campaign to the broadcaster(s)';

    const CAMPAIGN_SUBMIT_TO_BROADCASTER_SUCCESS = 'Campaign submitted to broadcaster(s) successfully';

    const EMAIL_PASSWORD_EMPTY = 'email and or password cannot be empty';

    const INVALID_EMAIL_PASSWORD = 'email and or password invalid';

    const EMAIL_CONFIRMATION = 'Please confirm your account first';

    const BANNED_ACCOUNT = 'Your account has been banned, please contact your administrator';

    const EMAIL_VERIFIED = 'Your email has been verified, you can now proceed to login with your credentials';

    const EMAIL_ALREADY_VERIFIED = 'You have already verified your email, please proceed to login...';

    const VERIFICATION_LINK = 'Please follow the link sent to your email';

    const PASSWORD_CHANGED = 'You have successfully changed your password, please proceed to login';

    const PROCESSING_ERROR = 'Error occurred while processing your request, please try again';

    const WRONG_ACTIVATION = 'Wrong activation code...';

    const EMAIL_NOT_FOUND = 'Email not found on our application';

    const CAMPAIGN_BUDGET_UPDATE = 'Campaign Budget Updated';

    const CAMPAIGN_BUDGET_ERROR = 'You cannot update the budget with an amount lower than the earlier';

    const BRAND_ALREADY_EXIST = 'Brands already exists';

    const WALKIN_ERROR = 'There was an error creating the client, please contact administrator';

}
