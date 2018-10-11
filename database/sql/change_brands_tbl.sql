RENAME TABLE `brands` TO `brand_old`;

INSERT INTO brands (id, `name`, image_url, industry_code, sub_industry_code, slug, created_at, updated_at) SELECT id, `name`, image_url, industry_id, sub_industry_id, md5(`name`), time_created, time_modified FROM brand_old;