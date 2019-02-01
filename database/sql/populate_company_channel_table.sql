
/*
Going forward I wanna create a migration for this and for the hardcoded company type i want to fetch all the company
type and use the id instead of having to hard code the values.
*/
insert into channel_company (channel_company.`channel_id`, channel_company.`company_id`)
select broadcasters.channel_id, companies.id from broadcasters inner join companies on companies.id = broadcasters.id
