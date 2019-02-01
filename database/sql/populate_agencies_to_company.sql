insert into companies (companies.id, companies.name, companies.address, companies.logo, companies.company_type_id, companies.created_at, companies.updated_at, companies.parent_company_id)
select agents.id, agents.brand, agents.location, agents.image_url, '5c52c978338ee', agents.time_created, agents.time_modified,
parent_companies.id from agents inner join parent_companies on agents.brand = parent_companies.name;

insert into company_user (company_user.company_id, company_user.user_id)
select id, user_id from agents;
