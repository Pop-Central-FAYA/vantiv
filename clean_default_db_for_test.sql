# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.24)
# Database: api_db
# Generation Time: 2019-02-03 14:19:35 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table admins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `user_id` varchar(45) DEFAULT NULL,
  `nationality` varchar(45) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `image_url` text,
  `status` int(10) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table adslot_filePositions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `adslot_filePositions`;

CREATE TABLE `adslot_filePositions` (
  `id` varchar(45) NOT NULL,
  `adslot_id` varchar(45) NOT NULL,
  `filePosition_id` varchar(45) NOT NULL,
  `status` int(10) DEFAULT '0',
  `select_status` int(10) DEFAULT '0',
  `broadcaster_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table adslotPercentages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `adslotPercentages`;

CREATE TABLE `adslotPercentages` (
  `id` varchar(45) NOT NULL,
  `adslot_id` varchar(45) DEFAULT NULL,
  `price_60` double DEFAULT NULL,
  `percentage` int(11) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price_45` double DEFAULT NULL,
  `price_30` double DEFAULT NULL,
  `price_15` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table adslotPrices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `adslotPrices`;

CREATE TABLE `adslotPrices` (
  `id` varchar(45) NOT NULL,
  `adslot_id` varchar(45) DEFAULT NULL,
  `price_60` double DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price_45` double DEFAULT NULL,
  `price_30` double DEFAULT NULL,
  `price_15` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table adslots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `adslots`;

CREATE TABLE `adslots` (
  `id` varchar(50) NOT NULL,
  `rate_card` varchar(50) NOT NULL,
  `target_audience` varchar(50) NOT NULL,
  `day_parts` varchar(50) NOT NULL,
  `region` varchar(50) NOT NULL,
  `from_to_time` varchar(50) NOT NULL,
  `min_age` int(11) NOT NULL,
  `max_age` int(11) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  `broadcaster` varchar(45) DEFAULT NULL,
  `is_available` tinyint(4) DEFAULT '0',
  `time_difference` int(11) DEFAULT NULL,
  `time_used` int(11) unsigned DEFAULT NULL,
  `channels` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table advertisers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `advertisers`;

CREATE TABLE `advertisers` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `sector_id` varchar(50) DEFAULT NULL,
  `sub_sector_id` varchar(50) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `location` text,
  `image_url` text,
  `brand` varchar(200) DEFAULT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table agents
# ------------------------------------------------------------

DROP TABLE IF EXISTS `agents`;

CREATE TABLE `agents` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `sector_id` varchar(50) DEFAULT NULL,
  `sub_sector_id` varchar(50) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `location` text,
  `image_url` text,
  `brand` varchar(200) DEFAULT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `agent_index` (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table agentUsers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `agentUsers`;

CREATE TABLE `agentUsers` (
  `id` varchar(50) NOT NULL,
  `agent_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_agentsUsers_agent_id` (`agent_id`),
  KEY `fk_agentsUsers_user_id` (`user_id`),
  CONSTRAINT `fk_agentsUsers_agent_id` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  CONSTRAINT `fk_agentsUsers_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table brand_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `brand_products`;

CREATE TABLE `brand_products` (
  `id` varchar(45) NOT NULL,
  `brand_id` varchar(45) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table brands
# ------------------------------------------------------------

DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `id` varchar(45) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `walkin_id` varchar(45) DEFAULT NULL,
  `broadcaster_agency` varchar(45) DEFAULT NULL,
  `image_url` text,
  `status` int(11) DEFAULT '0',
  `industry_id` varchar(45) DEFAULT NULL,
  `sub_industry_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table broadcasters
# ------------------------------------------------------------

DROP TABLE IF EXISTS `broadcasters`;

CREATE TABLE `broadcasters` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `sector_id` varchar(50) NOT NULL,
  `sub_sector_id` varchar(50) NOT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `location` text,
  `image_url` text,
  `brand` varchar(200) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `channel_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand` (`brand`),
  KEY `broadcaster_index` (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table broadcasterUsers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `broadcasterUsers`;

CREATE TABLE `broadcasterUsers` (
  `id` varchar(50) NOT NULL,
  `broadcaster_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `image_url` text,
  `nationality` varchar(45) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `brand` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table campaignChannels
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campaignChannels`;

CREATE TABLE `campaignChannels` (
  `id` varchar(50) NOT NULL,
  `channel` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channel` (`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table campaignDetails
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campaignDetails`;

CREATE TABLE `campaignDetails` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `broadcaster` varchar(50) DEFAULT NULL,
  `brand` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `product` varchar(200) NOT NULL,
  `channel` varchar(50) NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `stop_date` timestamp NULL DEFAULT NULL,
  `campaign_status` int(11) NOT NULL DEFAULT '0',
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  `day_parts` text,
  `target_audience` text,
  `min_age` int(11) DEFAULT NULL,
  `max_age` int(11) DEFAULT NULL,
  `Industry` varchar(45) DEFAULT NULL,
  `sub_industry` varchar(45) DEFAULT NULL,
  `duration` varchar(45) DEFAULT NULL,
  `adslots` int(11) DEFAULT NULL,
  `region` text,
  `walkins_id` varchar(45) DEFAULT NULL,
  `adslots_id` text,
  `agency` varchar(45) DEFAULT NULL,
  `agency_broadcaster` varchar(45) DEFAULT NULL,
  `campaign_id` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table campaigns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campaigns`;

CREATE TABLE `campaigns` (
  `id` varchar(45) NOT NULL,
  `campaign_status` int(10) NOT NULL DEFAULT '0',
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `campaign_reference` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table client_brands
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_brands`;

CREATE TABLE `client_brands` (
  `id` varchar(45) NOT NULL,
  `brand_id` varchar(45) DEFAULT NULL,
  `client_id` varchar(45) DEFAULT NULL,
  `product_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `sector_id` varchar(50) NOT NULL,
  `sub_sector_id` varchar(50) NOT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `location` text,
  `image_url` text,
  `brand` varchar(200) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `client_type_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand` (`brand`),
  KEY `client_index` (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table clientTypes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clientTypes`;

CREATE TABLE `clientTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table clientUsers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clientUsers`;

CREATE TABLE `clientUsers` (
  `id` varchar(50) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_clientUsers_client_id` (`client_id`),
  KEY `fk_clientUsers_user_id` (`user_id`),
  CONSTRAINT `fk_clientUsers_client_id` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `fk_clientUsers_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table compliances
# ------------------------------------------------------------

DROP TABLE IF EXISTS `compliances`;

CREATE TABLE `compliances` (
  `id` varchar(45) NOT NULL,
  `campaign_id` varchar(45) DEFAULT NULL,
  `adslot_id` varchar(45) DEFAULT NULL,
  `amount_spent` int(100) DEFAULT NULL,
  `broadcaster_id` varchar(45) DEFAULT NULL,
  `channel` varchar(45) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table dayParts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dayParts`;

CREATE TABLE `dayParts` (
  `id` varchar(50) NOT NULL,
  `day_parts` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table days
# ------------------------------------------------------------

DROP TABLE IF EXISTS `days`;

CREATE TABLE `days` (
  `id` varchar(50) NOT NULL,
  `day` varchar(100) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table discount_classes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `discount_classes`;

CREATE TABLE `discount_classes` (
  `id` varchar(50) NOT NULL,
  `class` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table discount_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `discount_types`;

CREATE TABLE `discount_types` (
  `id` varchar(50) NOT NULL,
  `type_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table discounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `discounts`;

CREATE TABLE `discounts` (
  `id` varchar(50) NOT NULL,
  `broadcaster` varchar(50) NOT NULL,
  `discount_type` varchar(50) NOT NULL,
  `discount_class` varchar(50) NOT NULL,
  `percent_value` bigint(20) DEFAULT NULL,
  `percent_start_date` timestamp NULL DEFAULT NULL,
  `percent_stop_date` timestamp NULL DEFAULT NULL,
  `value` bigint(20) DEFAULT NULL,
  `value_start_date` timestamp NULL DEFAULT NULL,
  `value_stop_date` timestamp NULL DEFAULT NULL,
  `discount_type_value` varchar(100) NOT NULL,
  `discount_type_sub_value` varchar(100) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table filePositions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filePositions`;

CREATE TABLE `filePositions` (
  `id` varchar(45) NOT NULL,
  `position` varchar(45) NOT NULL,
  `percentage` int(10) NOT NULL,
  `broadcaster_id` varchar(45) NOT NULL,
  `status` int(10) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table files
# ------------------------------------------------------------

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `campaign_id` varchar(50) NOT NULL,
  `adslot` varchar(50) NOT NULL,
  `broadcaster_id` varchar(50) NOT NULL,
  `file_name` text NOT NULL,
  `file_url` text NOT NULL,
  `file_code` varchar(50) NOT NULL,
  `is_file_accepted` tinyint(4) DEFAULT '0',
  `inventory_status` tinyint(4) DEFAULT '2',
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `agency_id` varchar(45) DEFAULT NULL,
  `agency_broadcaster` varchar(45) DEFAULT NULL,
  `time_picked` varchar(45) DEFAULT NULL,
  `airbox_status` int(11) DEFAULT '0',
  `rejection_reason` text,
  `position_id` varchar(45) DEFAULT NULL,
  `public_id` text,
  `format` varchar(45) DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_code` (`file_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table hourlyRanges
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hourlyRanges`;

CREATE TABLE `hourlyRanges` (
  `id` varchar(50) NOT NULL,
  `time_range` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `dayparts` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table invoiceDetails
# ------------------------------------------------------------

DROP TABLE IF EXISTS `invoiceDetails`;

CREATE TABLE `invoiceDetails` (
  `id` varchar(50) NOT NULL,
  `invoice_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `broadcaster_id` varchar(50) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `actual_amount_paid` double DEFAULT NULL,
  `refunded_amount` double DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '0',
  `walkins_id` varchar(45) DEFAULT NULL,
  `agency_id` varchar(45) DEFAULT NULL,
  `agency_broadcaster` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table invoices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `invoices`;

CREATE TABLE `invoices` (
  `id` varchar(45) NOT NULL,
  `campaign_id` varchar(45) NOT NULL,
  `campaign_reference` varchar(45) NOT NULL,
  `invoice_number` varchar(45) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '0',
  `payment_id` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `logs`;

CREATE TABLE `logs` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `request` text,
  `response` text,
  `reference` varchar(200) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table mpoDetails
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mpoDetails`;

CREATE TABLE `mpoDetails` (
  `id` varchar(50) NOT NULL,
  `mpo_id` varchar(50) NOT NULL,
  `broadcaster_id` varchar(50) NOT NULL,
  `is_mpo_accepted` tinyint(4) DEFAULT '0',
  `discount` double DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  `agency_id` varchar(45) DEFAULT NULL,
  `agency_broadcaster` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table mpos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mpos`;

CREATE TABLE `mpos` (
  `id` varchar(45) NOT NULL,
  `campaign_id` varchar(45) NOT NULL,
  `campaign_reference` varchar(45) NOT NULL,
  `invoice_number` varchar(45) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table paymentDetails
# ------------------------------------------------------------

DROP TABLE IF EXISTS `paymentDetails`;

CREATE TABLE `paymentDetails` (
  `id` varchar(50) NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `payment_status` tinyint(4) DEFAULT '0',
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `broadcaster` varchar(45) DEFAULT NULL,
  `walkins_id` varchar(45) DEFAULT NULL,
  `agency_id` varchar(45) DEFAULT NULL,
  `agency_broadcaster` varchar(45) DEFAULT NULL,
  `campaign_budget` int(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` varchar(45) NOT NULL,
  `campaign_id` varchar(45) NOT NULL,
  `campaign_reference` varchar(45) NOT NULL,
  `total` int(100) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(10) NOT NULL,
  `campaign_budget` int(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table rateCards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rateCards`;

CREATE TABLE `rateCards` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `broadcaster` varchar(50) NOT NULL,
  `hourly_range_id` varchar(50) NOT NULL,
  `day` varchar(100) NOT NULL,
  `is_airing` tinyint(4) DEFAULT '0',
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table regions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `regions`;

CREATE TABLE `regions` (
  `id` varchar(50) NOT NULL,
  `region` varchar(100) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table sectors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sectors`;

CREATE TABLE `sectors` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sector_code` varchar(20) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table status_logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `status_logs`;

CREATE TABLE `status_logs` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table subSectors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subSectors`;

CREATE TABLE `subSectors` (
  `id` varchar(50) NOT NULL,
  `sector_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sub_sector_code` varchar(20) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table targetAudiences
# ------------------------------------------------------------

DROP TABLE IF EXISTS `targetAudiences`;

CREATE TABLE `targetAudiences` (
  `id` varchar(50) NOT NULL,
  `audience` varchar(100) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table transactions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `id` varchar(45) NOT NULL,
  `amount` double DEFAULT NULL,
  `user_id` varchar(45) DEFAULT NULL,
  `reference` varchar(45) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `card_type` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `fees` double DEFAULT NULL,
  `message` text,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table userAccounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `userAccounts`;

CREATE TABLE `userAccounts` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `account_number` int(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT NULL,
  `time_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_userAccounts_user_id` (`user_id`),
  CONSTRAINT `fk_userAccounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(200) DEFAULT NULL,
  `password` text NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `user_type` int(11) NOT NULL,
  `reset_password_token` text,
  `reset_expires_password_time` bigint(20) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table walkIns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `walkIns`;

CREATE TABLE `walkIns` (
  `id` varchar(50) NOT NULL,
  `broadcaster_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '0',
  `nationality` varchar(45) DEFAULT NULL,
  `location` text,
  `image_url` text,
  `brand_id` varchar(45) DEFAULT NULL,
  `client_type_id` varchar(45) DEFAULT NULL,
  `agency_id` varchar(45) DEFAULT NULL,
  `company_name` varchar(45) DEFAULT NULL,
  `company_logo` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table walletHistories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `walletHistories`;

CREATE TABLE `walletHistories` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `amount` double DEFAULT NULL,
  `prev_balance` double DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(45) DEFAULT 'PENDING',
  `current_balance` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table wallets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wallets`;

CREATE TABLE `wallets` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `current_balance` double DEFAULT '0',
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(45) DEFAULT NULL,
  `prev_balance` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
