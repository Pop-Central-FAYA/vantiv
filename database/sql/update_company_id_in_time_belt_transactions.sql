update time_belt_transactions
set company_id =
(select launched_on
from campaignDetails
where campaignDetails.id = time_belt_transactions.`campaign_details_id`)