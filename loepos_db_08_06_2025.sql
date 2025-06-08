/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.11-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: loepos_db
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'België',
  `national_registry_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_email_unique` (`email`),
  UNIQUE KEY `clients_phone_unique` (`phone`),
  UNIQUE KEY `clients_national_registry_number_unique` (`national_registry_number`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES
(1,'Jonathan','Schuermans','jonathan.schuermans@gmail.be','0457884936','Stationstraat 57','Jabbeke','8490','België','624108359','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(2,'Jerry','Tromp','cruz.jacobi@yahoo.com','+1.410.322.5178','802 Doyle Manor Apt. 344\nNorth Lourdes, OK 48973','Lake Alfredoside','74199-7319','België','246289757','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(3,'Elfrieda','Torphy','tyrese.kuphal@hotmail.com','+1.330.945.3866','617 Ratke Cliff\nAbernathyshire, OR 02651-8115','North Shaylee','62765','België','147041625','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(4,'Coleman','Denesik','altenwerth.dale@friesen.net','602.654.2860','477 Ernser Flats Apt. 676\nWest Gabe, FL 93395','Lake Pabloview','74669-9701','België','936826402','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(5,'Drew','Marvin','reilly.oconnell@hotmail.com','(281) 867-6330','5842 Jenifer Locks\nGorczanyburgh, VA 45059-2017','Robertsville','52244-2782','België','251291733','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(6,'Dwight','Auer','mmills@gislason.info','(463) 734-7314','175 Hermann Mews Suite 521\nFelipemouth, TX 35844-8867','Oberbrunnerfort','16417','België','313386018','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(7,'Elouise','Stark','wolf.nettie@gmail.com','+1-229-626-4798','8572 Alexandre Cape Suite 382\nBradtkeburgh, DE 51477-2332','Kadenchester','32617','België','69675267','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(8,'Libbie','Goyette','loyce.beahan@yahoo.com','(870) 739-9740','493 Mose Spurs\nMuellerstad, NJ 94449','North Joeyland','67669','België','812614398','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(9,'Kathryn','Kuhlman','beahan.dedric@gmail.com','980-937-3787','6187 Vicente Gateway Apt. 850\nMakenziebury, NM 91472-7430','Port Benton','75514-3953','België','367108765','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(10,'Malachi','Champlin','jbednar@tremblay.com','+1.351.904.4684','844 Ignacio Summit Suite 067\nDooleyville, MS 79762','Lake Dell','64774-3830','België','765069127','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(11,'Scarlett','Brekke','vadams@jones.org','+1-216-333-8020','17704 Nolan Pike\nFrederiquebury, TN 40869','Connberg','33918','België','133607034','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(12,'Maritza','Mitchell','elisa14@gmail.com','667.991.7872','671 Beer Via\nSouth Golda, MN 28577','Kalimouth','24008-0990','België','393134038','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(13,'Forest','Dickinson','mcrona@nolan.org','858.449.9965','183 Christy Plaza\nEast Quinn, WV 65379-9800','Lake Gildaborough','79756','België','961278910','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(14,'Viviane','Koelpin','fkub@yahoo.com','(972) 238-0437','3413 Gerhold Stream Apt. 655\nWolfbury, NM 86419','Darronmouth','35710','België','580723594','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(15,'Mavis','Reichert','aniya.schneider@stokes.com','(580) 789-4796','82696 Romaguera Garden\nSouth Jameson, UT 47794','West Tommie','52538-9813','België','301482792','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(16,'Dominique','Jerde','unique53@gmail.com','283.654.8780','2059 Dax Views Apt. 294\nNickolasstad, SC 71991-8921','New Franco','28642','België','623634384','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(17,'Chesley','Ebert','ytillman@rau.net','(773) 658-5296','1014 Brooke Point\nMariobury, NJ 49042','Greenholtburgh','99913-4028','België','430747662','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(18,'Gust','Bogan','linda46@breitenberg.com','845.425.8213','48245 Anderson Drive\nLake Florencehaven, ME 87598-6225','South Celiafurt','70813-6845','België','311221215','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(19,'Sheldon','Kunde','reynolds.lowell@muller.biz','484-320-1778','4555 Lucy Islands Suite 557\nSouth Jermaine, ND 19818-0702','South Dejuanton','92997-2954','België','906566249','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(20,'Brenna','Grimes','moen.pearl@yahoo.com','+1-231-775-9506','377 Candido Drive\nPort Dedrick, WV 61395-7556','Lake Lola','98559-0831','België','207893643','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(21,'Royce','Witting','tyree14@yahoo.com','475.236.5438','131 Antonina Light\nDinastad, SC 70991','Port Svenfurt','53170-7103','België','907127569','2025-06-08 19:45:26','2025-06-08 19:45:26');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `debts`
--

DROP TABLE IF EXISTS `debts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `debts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dossier_id` bigint(20) unsigned NOT NULL,
  `creditor` varchar(255) NOT NULL,
  `amount` decimal(10,5) NOT NULL,
  `status` enum('open','settled','suspended') NOT NULL DEFAULT 'open',
  `due_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debts`
--

LOCK TABLES `debts` WRITE;
/*!40000 ALTER TABLE `debts` DISABLE KEYS */;
INSERT INTO `debts` VALUES
(1,1,'FOD',5000.00000,'open','2026-06-08 19:45:26','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(2,2,'RegSol',8956.00000,'open','2026-06-08 19:45:26','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(3,3,'TCM',1400.00000,'open','2026-06-08 19:45:26','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(4,4,'Coeo Incasso',4889.00000,'open','2026-06-08 19:45:26','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(5,5,'Justitie België',2648.00000,'open','2026-06-08 19:45:26','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(6,6,'Stamm, Corkery and Bradtke',2740.00000,'open','2001-05-27 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(7,7,'Schinner Inc',4089.00000,'open','1998-11-10 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(8,8,'Emmerich, Bartell and Ruecker',8454.00000,'suspended','2024-12-01 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(9,9,'Howe, King and Emard',7485.00000,'settled','2013-05-03 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(10,10,'Hagenes-Turcotte',77.00000,'suspended','1976-08-08 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(11,11,'Blanda, Effertz and Marks',774.00000,'settled','2022-11-16 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(12,12,'Grady and Sons',5640.00000,'open','1995-09-20 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(13,13,'Nitzsche, Yost and Kiehn',2488.00000,'open','2000-12-27 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(14,14,'Volkman PLC',6477.00000,'suspended','1976-06-26 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(15,15,'Kuhn and Sons',7145.00000,'open','2021-04-01 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(16,16,'Lakin-Wunsch',782.00000,'settled','1997-02-15 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(17,17,'Rice-Towne',2393.00000,'suspended','1997-11-15 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(18,18,'Dach LLC',1669.00000,'suspended','1998-10-06 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(19,19,'Stoltenberg, Maggio and Wilderman',4413.00000,'open','2014-06-30 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(20,20,'Sauer, Dibbert and Huel',5303.00000,'settled','2005-11-11 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(21,21,'Bahringer-Murazik',3773.00000,'open','2011-08-13 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(22,22,'Spencer-Hoeger',783.00000,'open','1973-03-25 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(23,23,'Leuschke Group',5336.00000,'settled','1992-05-17 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(24,24,'Hessel-Marks',836.00000,'settled','2021-04-04 22:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(25,25,'Jakubowski-Dare',555.00000,'suspended','2023-11-22 23:00:00','2025-06-08 19:45:26','2025-06-08 19:45:26');
/*!40000 ALTER TABLE `debts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` bigint(20) unsigned NOT NULL,
  `dossier_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('invoice','reminder','identity','agreement') NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `parsed_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`parsed_data`)),
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) NOT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossiers`
--

DROP TABLE IF EXISTS `dossiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dossiers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` enum('active','in process','closed') NOT NULL DEFAULT 'in process',
  `type` enum('schuldbemiddeling','budgetbeheer') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossiers`
--

LOCK TABLES `dossiers` WRITE;
/*!40000 ALTER TABLE `dossiers` DISABLE KEYS */;
INSERT INTO `dossiers` VALUES
(1,1,1,'active','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(2,2,1,'active','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(3,3,1,'active','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(4,4,1,'active','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(5,5,1,'active','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(6,2,7,'active','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(7,3,8,'closed','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(8,4,9,'in process','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(9,5,10,'active','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(10,6,11,'closed','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(11,7,12,'closed','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(12,8,13,'active','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(13,9,14,'in process','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(14,10,15,'active','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(15,11,16,'in process','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(16,12,17,'in process','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(17,13,18,'in process','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(18,14,19,'closed','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(19,15,20,'closed','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(20,16,21,'closed','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(21,17,22,'closed','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(22,18,23,'closed','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(23,19,24,'in process','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(24,20,25,'closed','schuldbemiddeling','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(25,21,26,'in process','budgetbeheer','2025-06-08 19:45:26','2025-06-08 19:45:26');
/*!40000 ALTER TABLE `dossiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `family_infos`
--

DROP TABLE IF EXISTS `family_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `family_infos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `status` enum('single','married','living together','divorced','widowed') NOT NULL,
  `children` int(11) NOT NULL,
  `partner_name` varchar(255) NOT NULL,
  `partner_income` decimal(8,2) NOT NULL,
  `children_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`children_info`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `family_infos`
--

LOCK TABLES `family_infos` WRITE;
/*!40000 ALTER TABLE `family_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `family_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_infos`
--

DROP TABLE IF EXISTS `financial_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `financial_infos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `iban` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `monthly_income` decimal(10,2) NOT NULL,
  `monthly_expenses` decimal(10,2) NOT NULL,
  `employer` varchar(255) NOT NULL,
  `contract` enum('permanent','temporary','self-employed','unemployed') NOT NULL,
  `education` enum('primary','secondary','higher') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_infos_iban_unique` (`iban`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_infos`
--

LOCK TABLES `financial_infos` WRITE;
/*!40000 ALTER TABLE `financial_infos` DISABLE KEYS */;
INSERT INTO `financial_infos` VALUES
(1,1,'BE32456785991124','Argenta',1655.00,1200.00,'Delhaize DE LEEUW','temporary','secondary','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(2,2,'BE12488698771233','ING',1890.50,1300.00,'Karel De Grote','permanent','higher','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(3,3,'BE11457136489512','Argenta',2060.45,1400.00,'Mechelen Accountancy','temporary','higher','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(4,4,'BE58664423748799','KBC',2140.55,1500.00,'Tech Create','permanent','higher','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(5,5,'BE55324466984753','KBC',1499.00,1000.00,'Champlin-Zemlak','unemployed','secondary','2025-06-08 19:45:26','2025-06-08 19:45:26');
/*!40000 ALTER TABLE `financial_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=865 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(849,'0001_01_01_000000_create_users_table',1),
(850,'0001_01_01_000001_create_cache_table',1),
(851,'0001_01_01_000002_create_jobs_table',1),
(852,'2025_04_20_000000_create_organizations_table',1),
(853,'2025_04_23_135300_create_documents_table',1),
(854,'2025_04_30_233902_create_clients_table',1),
(855,'2025_04_30_233910_create_dossiers_table',1),
(856,'2025_05_08_100227_create_debts_table',1),
(857,'2025_05_10_141807_create_financial_infos_table',1),
(858,'2025_05_20_155451_create_payments_table',1),
(859,'2025_05_26_150718_create_tasks_table',1),
(860,'2025_05_26_180631_create_notifications_table',1),
(861,'2025_05_28_194631_create_family_infos_table',1),
(862,'2025_05_28_211907_create_uploads_table',1),
(863,'2025_05_28_212756_create_pages_table',1),
(864,'2025_06_08_204012_make_phone_and_registry_nullable_in_clients_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `VAT` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'België',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organizations_email_unique` (`email`),
  UNIQUE KEY `organizations_phone_unique` (`phone`),
  UNIQUE KEY `organizations_website_unique` (`website`),
  UNIQUE KEY `organizations_vat_unique` (`VAT`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES
(1,'Schowalter-Ankunding','beier.jarrod@jenkins.com','+1-641-735-5787','https://balistreri.com/quia-deserunt-dolores-enim-ratione-tempora-dolor-et-eum.html','29925927006866','1264 Sauer Run Apt. 844','26100-0053','Lenoraborough','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(2,'Green-Mohr','susana.beer@gmail.com','463.414.9456','http://www.steuber.info/nostrum-nihil-quidem-eos-iusto-eius','84499569377661','822 Cristobal Valley','17699','Mullertown','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(3,'Streich, Breitenberg and Kautzer','yfarrell@yahoo.com','423.818.0267','http://www.mueller.org/ut-sunt-placeat-nisi-sed-et-vero','72819466879737','617 Cruickshank Ferry Suite 728','06169-7746','Daughertyburgh','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(4,'Renner-Daugherty','emmerich.eunice@hotmail.com','+1-469-472-4032','http://collins.biz/quaerat-quas-explicabo-culpa-reprehenderit-itaque','90525379160305','316 Elza Manors Suite 539','67088-8026','East Marcellus','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(5,'Purdy, Considine and Morissette','kraig.feil@prohaska.com','+1 (458) 777-7221','http://www.bahringer.com/corrupti-aliquam-consequuntur-molestiae','92944025556200','3750 Precious Inlet Apt. 646','59290','Chrisbury','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(6,'Lakin Group','lafayette25@yahoo.com','+1-956-378-8966','http://rempel.com/voluptatum-laudantium-consequatur-illo','56912371776228','54778 Meghan Creek','54167-4776','Aimeetown','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(7,'Kerluke PLC','enos.borer@gmail.com','+1 (530) 882-7238','http://lemke.com/','08524499956101','85244 Gabrielle Lane','81145-1587','Arturohaven','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(8,'Wolf-Farrell','scarlett.kuhic@gmail.com','1-720-261-3031','http://upton.com/aut-et-sapiente-eum','21532805391403','3560 Ruecker Dam Suite 478','35700-4180','North Nannieburgh','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(9,'Fadel and Sons','rylan.wilkinson@gmail.com','518-318-6005','http://mckenzie.net/','85422979005879','92558 Brad Knolls Apt. 471','49034','Sandyhaven','België','2025-06-08 19:45:24','2025-06-08 19:45:24'),
(10,'Mitchell, Schaden and McLaughlin','harber.greyson@luettgen.org','561-789-3866','https://upton.com/esse-aspernatur-natus-autem-beatae-ea.html','48217568974467','2799 Melyna Grove Apt. 858','36961-5567','West Linwood','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(11,'Lynch PLC','aliza.cole@barrows.com','914-800-4695','http://grady.com/ipsam-qui-similique-libero-sit-id','48500809933207','6924 Durgan Ford Suite 704','59948','Port Troy','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(12,'Wiza Inc','gerardo22@effertz.com','325-867-6453','https://feeney.com/voluptatibus-asperiores-vel-similique.html','52553844881608','701 Wisozk Centers Apt. 795','18047','Beattychester','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(13,'Jones, Fahey and Keeling','howell.sincere@yahoo.com','845.214.2865','http://kutch.com/unde-quidem-est-dolore-quis-non','63693145278233','3873 Sigurd Lake','37343','North Guidofort','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(14,'Steuber-Hodkiewicz','mhackett@gmail.com','(678) 334-3389','http://www.parisian.biz/nemo-magni-aut-repudiandae-soluta','45483058906535','4083 Fisher Brook Apt. 491','74953-1174','Wadeburgh','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(15,'Mitchell and Sons','kyla.bashirian@brakus.info','+1-352-783-3583','http://www.bartoletti.biz/','38808821520715','456 Welch Mews Suite 243','05656','Lake Buster','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(16,'Boyle, Herzog and Schneider','gjacobi@gmail.com','(309) 491-6465','http://blick.com/','36428618912261','203 Franecki Ridge','29310-3588','Keeblertown','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(17,'VonRueden PLC','bret.pacocha@schultz.com','(361) 715-0121','https://gulgowski.org/eligendi-cupiditate-suscipit-et.html','94934875542619','2293 Johan Isle','10633-6718','Strosinborough','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(18,'Ledner-Beier','lschultz@steuber.biz','+1-920-517-7645','http://hegmann.com/','73085217717702','54912 Josiah Station','19261-6660','Joannyside','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(19,'Erdman, Lind and Gutmann','xskiles@hotmail.com','1-802-794-6664','https://www.kshlerin.net/non-repellat-nihil-corporis','80631366522777','38240 McGlynn Trail Suite 612','20064','Medhursthaven','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(20,'Casper, Leannon and Quitzon','rodrigo23@gmail.com','+1.442.990.7824','https://www.blanda.info/cum-et-perferendis-aut-doloribus-praesentium-nam','94430338879799','273 Hoppe Highway','06172','Kuphalland','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(21,'Ward-Kessler','ortiz.dorian@mitchell.com','+1.559.606.5859','http://cummerata.com/quo-suscipit-error-ipsum-illo','57690894824790','85305 Dereck Ville Suite 645','07784-7420','East Santinaland','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(22,'Romaguera Group','julio.kovacek@yahoo.com','1-718-805-9580','http://www.wehner.com/reprehenderit-nesciunt-et-aliquid-pariatur-odit-repudiandae','12267496564821','9316 Emory Tunnel Apt. 810','33402-4224','Jamaalchester','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(23,'Macejkovic Ltd','oconner.ellen@yahoo.com','+1-443-957-9901','http://www.klocko.biz/sit-eum-dolorem-est-quod-et-voluptas-dicta','01135139833315','760 Amira Mall','83249','East Maegan','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(24,'Schuster-Paucek','elijah77@gmail.com','+1-925-547-5418','https://kessler.com/ea-quia-quam-sequi-accusamus-perspiciatis-aspernatur.html','12500664409101','310 Hane Creek Apt. 161','64276','Vidalville','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(25,'Beier, Nitzsche and Hodkiewicz','audreanne.leuschke@yahoo.com','+1-769-558-7415','http://grimes.com/','19026617256334','27544 Blanche Mountain','80255-5221','Antwanhaven','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(26,'Blick-Rempel','kihn.muhammad@hotmail.com','1-863-858-3522','http://www.lindgren.com/voluptas-beatae-nihil-sapiente-est-deserunt','38106810923367','409 Mohr Summit','05409-4135','Port Kevin','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(27,'Heathcote and Sons','owhite@wiza.com','1-570-675-2187','https://rosenbaum.com/id-minus-omnis-quae-veritatis-et-dolor-voluptate.html','93362954713187','46170 Berge Rapids','37252','East Orie','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(28,'Homenick, Hamill and Wyman','andrew25@yahoo.com','+1-619-493-3497','http://wilkinson.com/','85968984510946','414 Kavon Port','01384','Bergstromberg','België','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(29,'Streich, Feeney and Cartwright','kauer@yahoo.com','+1-657-493-1599','http://www.kertzmann.biz/ut-sed-ut-incidunt-ad','02053104380863','22910 Dare Ports Suite 361','00501-3972','Strackefort','België','2025-06-08 19:45:26','2025-06-08 19:45:26');
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `page_number` int(11) NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`content`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned DEFAULT NULL,
  `debt_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('transfer','automatic','cash') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,NULL,1,149.99,'transfer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(2,NULL,1,350.00,'transfer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(3,NULL,1,249.99,'transfer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(4,NULL,2,39.98,'automatic','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(5,NULL,2,249.98,'automatic','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(6,NULL,2,223.25,'automatic','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(7,NULL,3,121.74,'transfer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(8,NULL,3,101.55,'transfer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(9,NULL,3,151.75,'transfer','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(10,NULL,4,250.00,'cash','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(11,NULL,4,250.00,'cash','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(12,NULL,4,250.00,'cash','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(13,NULL,5,88.55,'automatic','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(14,NULL,5,128.39,'automatic','2025-06-08 19:45:26','2025-06-08 19:45:26'),
(15,NULL,5,112.66,'automatic','2025-06-08 19:45:26','2025-06-08 19:45:26');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` enum('pending','in progress','completed') NOT NULL DEFAULT 'pending',
  `due_date` timestamp NULL DEFAULT NULL,
  `urgency` enum('low','medium','high') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `uploads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `organization_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `parsed_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`parsed_data`)),
  `documents` int(11) NOT NULL,
  `status` enum('pending','uploaded','verified','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploads`
--

LOCK TABLES `uploads` WRITE;
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `organization_id` bigint(20) unsigned DEFAULT NULL,
  `role` enum('employee','admin','superadmin') NOT NULL DEFAULT 'employee',
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Medewerker','Loepos','medewerker@loepos.be','2025-06-08 19:45:24','$2y$12$dZRKcAVXBRSK5LKZ.VI7Oe/QivhNwAjnWxQvUlolE5Jnrq/yPsiWS',1,'employee','W3gAFiGW7G',NULL,'2025-06-08 19:45:25','2025-06-08 19:45:25'),
(2,'Admin','Loepos','admin@loepos.be','2025-06-08 19:45:25','$2y$12$/T7Q4kg8LJOElc96r6jIs.2n7wtHy9e2uqGf/78UsWYnTBy8MfrHm',1,'admin','74dzWgkU1X',NULL,'2025-06-08 19:45:25','2025-06-08 19:45:25'),
(3,'Super','Admin','superadmin@loepos.be','2025-06-08 19:45:25','$2y$12$dce0zsyUzlidvwsBu6k3q.45BWLD55XR5/zyq7hsO9Y7qIsTIL0Q.',NULL,'superadmin','RRLMAVDCWb',NULL,'2025-06-08 19:45:25','2025-06-08 19:45:25'),
(4,'James','Doe','james.doe@mail.be','2025-06-08 19:45:25','$2y$12$VmEmYZfnFTr9mVlQXWeox.FGFg9rViUTnl3VbSZqWTcHOJUUpWzpG',1,'employee','1SV2FzCHiU',NULL,'2025-06-08 19:45:25','2025-06-08 19:45:25'),
(5,'Jane','Doe','jane.doe@mail.be','2025-06-08 19:45:25','$2y$12$9re2wvAjlqGidTicxeU.keI3PfOmucUApz4Aten7wIlzpjF0bJZAK',1,'admin','AC5ARmoMtt',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(6,'Admin 2','Loepos','admin2@loepos.be','2025-06-08 19:45:26','$2y$12$LGkdrlqSx02nK96aI4lgrOLivdepUrY8lYDp1AYgKd486KP6lg9YW',1,'superadmin','Zn2b7ds7Wn',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(7,'Mortimer','Aufderhar','kenneth.larson@example.org','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',10,'employee','agDnP157JN',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(8,'Jennyfer','DuBuque','little.kianna@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',11,'employee','U2pFT8fyTf',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(9,'Allison','O\'Conner','rdaniel@example.org','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',12,'employee','KAeCqEpDFI',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(10,'Rolando','Veum','udietrich@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',13,'superadmin','MHIQ0ODYHJ',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(11,'Conner','Predovic','ebba.klocko@example.net','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',14,'employee','Yj247p4zrp',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(12,'Aubree','Russel','clark23@example.net','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',15,'employee','NKi5boZ4HA',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(13,'Rowland','Terry','brock44@example.org','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',16,'admin','G5ooxrbYLP',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(14,'Rickie','Gusikowski','sabryna.stokes@example.org','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',17,'superadmin','kahaaSKHrN',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(15,'Josephine','Rodriguez','mann.naomie@example.net','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',18,'admin','r9Ow2Dp3Eb',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(16,'Royce','Bernier','granville57@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',19,'admin','MfYBT5kom9',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(17,'Riley','Spencer','andres.goodwin@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',20,'admin','DeWWvHHjUW',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(18,'Arielle','Oberbrunner','justen.stokes@example.net','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',21,'employee','5PDKnxENrF',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(19,'Abigayle','Harvey','miracle.bartell@example.org','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',22,'admin','KHVSHvOpoc',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(20,'Nico','Kuhlman','bernser@example.net','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',23,'admin','yO2ig8HKY7',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(21,'Tressie','Bayer','cconnelly@example.org','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',24,'employee','PPr9jyipAo',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(22,'Laurie','Champlin','sharon70@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',25,'employee','vgckuKnfAI',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(23,'Stan','Weimann','jessica.christiansen@example.org','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',26,'employee','PEjjD60pmo',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(24,'Mckenna','Erdman','yosinski@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',27,'superadmin','OZgDfjz4D9',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(25,'Peggie','Goyette','nienow.devon@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',28,'superadmin','6tZH9Zntfx',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26'),
(26,'Shany','Treutel','jmarvin@example.com','2025-06-08 19:45:26','$2y$12$3T0pO37Q3.sEwy5aSzVuTOIPPfzVoch3q6vwBJNe49LyIyWA1hWba',29,'employee','ls342iXefj',NULL,'2025-06-08 19:45:26','2025-06-08 19:45:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-08 23:47:29
