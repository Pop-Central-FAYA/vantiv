insert into companies (companies.id, companies.name, companies.address, companies.logo, companies.company_type_id, companies.created_at, companies.updated_at, companies.parent_company_id)
select broadcasters.id, broadcasters.brand, broadcasters.location, broadcasters.image_url, '5c52c978338df', broadcasters.time_created, broadcasters.time_modified,
parent_companies.id from broadcasters inner join parent_companies on broadcasters.brand = parent_companies.name;

insert into company_user (company_user.company_id, company_user.user_id)
select id, user_id from broadcasters;
