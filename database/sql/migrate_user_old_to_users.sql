
INSERT into users (id, email, username, password, firstname, lastname, phone_number, avatar, address, country_id, birthday, last_login, confirmation_token, status, two_factor_country_code, two_factor_phone, two_factor_options, remember_token, created_at, updated_at)
SELECT id, email, null, password, firstname, lastname, phone_number, null, '', null, null, null, null, 'Active', null, null, null, null, time_created, time_modified from user_old
