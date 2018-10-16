SET SQL_SAFE_UPDATES = 0;
UPDATE api_db.paymentDetails 
SET payment_status = 1 
WHERE broadcaster 
	IN (SELECT broadcaster FROM api_db.campaignDetails WHERE status != 'on_hold');