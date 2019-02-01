insert into channel_company (channel_company.`channel_id`, channel_company.`company_id`)
select broadcasters.channel_id, companies.id from broadcasters inner join companies on companies.id = broadcasters.id
