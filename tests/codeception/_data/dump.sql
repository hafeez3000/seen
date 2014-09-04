-- MySQL dump 10.15  Distrib 10.0.10-MariaDB, for osx10.9 (i386)
--
-- Host: localhost    Database: dev_seen
-- ------------------------------------------------------
-- Server version	10.0.10-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `prod_auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_item_name` FOREIGN KEY (`item_name`) REFERENCES `prod_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` varchar(1024) COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `type` (`type`),
  KEY `auth_item_rule_name` (`rule_name`),
  CONSTRAINT `auth_item_rule_name` FOREIGN KEY (`rule_name`) REFERENCES `prod_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `auth_item_child_child` (`child`),
  CONSTRAINT `auth_item_child_child` FOREIGN KEY (`child`) REFERENCES `prod_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_parent` FOREIGN KEY (`parent`) REFERENCES `prod_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` varchar(1024) COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `description` varchar(1024) COLLATE utf8_unicode_ci COMMENT 'Description',
  `parent_id` int(10) unsigned DEFAULT NULL COMMENT 'Parent Company',
  `headquarters` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Headquarter',
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Homepage',
  `logo_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Logo path',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_email`
--

DROP TABLE IF EXISTS `email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ts` timestamp NULL DEFAULT NULL COMMENT 'Timestamp',
  `event` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Event',
  `text` varchar(1024) COLLATE utf8_unicode_ci COMMENT 'Text',
  `html` varchar(1024) COLLATE utf8_unicode_ci COMMENT 'Html',
  `from_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'From (Email)',
  `from_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'From (Name)',
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Subject',
  `spam_score` float NOT NULL DEFAULT '0' COMMENT 'Spam score',
  `success` tinyint(1) DEFAULT '0' COMMENT 'Success',
  `respond_user_id` int(10) unsigned DEFAULT NULL COMMENT 'Repsonded by',
  `respond_at` datetime DEFAULT NULL COMMENT 'Responded at',
  `assigned_user_id` int(10) unsigned DEFAULT NULL COMMENT 'Assigned user',
  PRIMARY KEY (`id`),
  KEY `ts` (`ts`),
  KEY `from_email` (`from_email`),
  KEY `respond_user_id` (`respond_user_id`),
  KEY `assigned_user_id` (`assigned_user_id`),
  CONSTRAINT `email_assigned_user_id` FOREIGN KEY (`assigned_user_id`) REFERENCES `prod_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `email_respond_user_id` FOREIGN KEY (`respond_user_id`) REFERENCES `prod_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_email_attachment`
--

DROP TABLE IF EXISTS `email_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `email_id` int(10) unsigned NOT NULL COMMENT 'Email',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Type',
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  CONSTRAINT `email_attachment_email_id` FOREIGN KEY (`email_id`) REFERENCES `prod_email` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_email_group`
--

DROP TABLE IF EXISTS `email_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `receiver` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Receiver',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `receiver` (`receiver`)
) ENGINE=MEMORY AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_email_to`
--

DROP TABLE IF EXISTS `email_to`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_to` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `email_id` int(10) unsigned NOT NULL COMMENT 'Email',
  `to_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email',
  `to_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  KEY `to_email` (`to_email`),
  CONSTRAINT `email_to_email_id` FOREIGN KEY (`email_id`) REFERENCES `prod_email` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_episode`
--

DROP TABLE IF EXISTS `episode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `episode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `season_id` int(10) unsigned NOT NULL COMMENT 'Season',
  `number` smallint(5) unsigned DEFAULT NULL COMMENT 'Number',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `overview` varchar(1024) COLLATE utf8_unicode_ci COMMENT 'Overview',
  `air_date` date DEFAULT NULL COMMENT 'Air date',
  `still_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Still path',
  `vote_average` double unsigned DEFAULT NULL COMMENT 'Average vote',
  `vote_count` int(10) unsigned DEFAULT NULL COMMENT 'Vote count',
  `production_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Production code',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `season_id` (`season_id`),
  KEY `themoviedb_id` (`themoviedb_id`),
  CONSTRAINT `prod_episode_ibfk_2` FOREIGN KEY (`season_id`) REFERENCES `prod_season` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=15941 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_genre`
--

DROP TABLE IF EXISTS `genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genre` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `iso` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ISO 639-1',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level` int(11) DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_time` int(11) DEFAULT NULL,
  `message` varchar(1024) COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_log_level` (`level`),
  KEY `idx_log_category` (`category`)
) ENGINE=MEMORY AUTO_INCREMENT=5710 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie`
--

DROP TABLE IF EXISTS `movie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tagline` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `overview` varchar(1024) COLLATE utf8_unicode_ci,
  `imdb_id` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'IMDB ID',
  `backdrop_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Backdrop path',
  `poster_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Poster path',
  `release_date` date DEFAULT NULL COMMENT 'Release date',
  `budget` int(10) unsigned DEFAULT NULL COMMENT 'Budget',
  `revenue` int(10) unsigned DEFAULT NULL COMMENT 'Revenue',
  `runtime` smallint(5) unsigned DEFAULT NULL COMMENT 'Runtime',
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Status',
  `adult` tinyint(1) DEFAULT NULL COMMENT 'Adult',
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Homepage',
  `popularity` double unsigned DEFAULT NULL COMMENT 'Popularity',
  `vote_average` double unsigned DEFAULT NULL COMMENT 'Average vote',
  `vote_count` int(10) unsigned DEFAULT NULL COMMENT 'Vote count',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`),
  KEY `themoviedb_id` (`themoviedb_id`),
  CONSTRAINT `prod_movie_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `prod_language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_cast`
--

DROP TABLE IF EXISTS `movie_cast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_cast` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `credit_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Credit ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `character` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Character',
  `profile_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Profile path',
  `order` smallint(5) unsigned DEFAULT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  CONSTRAINT `prod_movie_cast_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_company`
--

DROP TABLE IF EXISTS `movie_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_company` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `company_id` int(10) unsigned NOT NULL COMMENT 'Company',
  PRIMARY KEY (`movie_id`,`company_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `prod_movie_company_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_movie_company_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `prod_company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_country`
--

DROP TABLE IF EXISTS `movie_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_country` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `country_id` int(10) unsigned NOT NULL COMMENT 'Country',
  PRIMARY KEY (`movie_id`,`country_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `prod_movie_country_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_movie_country_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `prod_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_crew`
--

DROP TABLE IF EXISTS `movie_crew`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_crew` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `credit_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Credit ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `department` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Department',
  `job` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Job',
  `profile_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Profile path',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  CONSTRAINT `prod_movie_crew_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_genre`
--

DROP TABLE IF EXISTS `movie_genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_genre` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `genre_id` int(10) unsigned NOT NULL COMMENT 'Genre',
  PRIMARY KEY (`movie_id`,`genre_id`),
  KEY `genre_id` (`genre_id`),
  CONSTRAINT `prod_movie_genre_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_movie_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `prod_genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_language`
--

DROP TABLE IF EXISTS `movie_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_language` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `language_id` int(10) unsigned NOT NULL COMMENT 'Language',
  PRIMARY KEY (`movie_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `prod_movie_language_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_movie_language_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `prod_language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_popular`
--

DROP TABLE IF EXISTS `movie_popular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_popular` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `order` tinyint(4) unsigned NOT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  CONSTRAINT `movie_popular_movie_id` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_movie_similar`
--

DROP TABLE IF EXISTS `movie_similar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movie_similar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `similar_to_movie_id` int(10) unsigned DEFAULT NULL COMMENT 'Similiar Movie',
  `similar_to_themoviedb_id` int(10) unsigned NOT NULL COMMENT 'Similar to TheMovieDB',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  KEY `similar_to_movie_id` (`similar_to_movie_id`),
  CONSTRAINT `prod_movie_similar_ibfk_2` FOREIGN KEY (`similar_to_movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_movie_similar_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `prod_movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_network`
--

DROP TABLE IF EXISTS `network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `biography` varchar(1024) COLLATE utf8_unicode_ci COMMENT 'Biography',
  `birthday` date DEFAULT NULL COMMENT 'Birthday',
  `deathday` date DEFAULT NULL COMMENT 'Deathday',
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Homepage',
  `adult` tinyint(1) DEFAULT NULL COMMENT 'Adult',
  `place_of_birth` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Place of birth',
  `profile_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Profile path',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_person_alias`
--

DROP TABLE IF EXISTS `person_alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_alias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `person_id` int(10) unsigned NOT NULL COMMENT 'Person',
  `alias` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Alias',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `prod_person_alias_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `prod_person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_season`
--

DROP TABLE IF EXISTS `season`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `season` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `number` smallint(5) unsigned NOT NULL COMMENT 'Number',
  `overview` varchar(1024) COLLATE utf8_unicode_ci COMMENT 'Overview',
  `poster_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Poster path',
  `air_date` date DEFAULT NULL COMMENT 'Air date',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `themoviedb_id` (`themoviedb_id`),
  KEY `show_id` (`show_id`),
  CONSTRAINT `prod_season_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=957 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show`
--

DROP TABLE IF EXISTS `show`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `language_id` int(10) unsigned NOT NULL COMMENT 'Language',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `original_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Original name',
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Slug',
  `overview` varchar(1024) COLLATE utf8_unicode_ci COMMENT 'Overview',
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Homepage',
  `first_air_date` date DEFAULT NULL COMMENT 'First air date',
  `last_air_date` date DEFAULT NULL COMMENT 'Last air date',
  `in_production` tinyint(1) DEFAULT NULL COMMENT 'In production',
  `popularity` double DEFAULT NULL COMMENT 'Popularity',
  `backdrop_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Backdrop path',
  `poster_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Poster path',
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Staus',
  `vote_average` double DEFAULT NULL COMMENT 'Average vote',
  `vote_count` int(10) unsigned DEFAULT NULL COMMENT 'Vote count',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`),
  KEY `themoviedb_id` (`themoviedb_id`),
  KEY `slug` (`slug`),
  CONSTRAINT `prod_show_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `prod_language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=1027 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_cast`
--

DROP TABLE IF EXISTS `show_cast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_cast` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `show_id` int(10) unsigned NOT NULL,
  `credit_id` int(10) unsigned DEFAULT NULL COMMENT 'Credit ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `character` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Character',
  `profile_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Profile path',
  `order` smallint(5) unsigned DEFAULT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`),
  CONSTRAINT `prod_show_cast_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_country`
--

DROP TABLE IF EXISTS `show_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_country` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `country_id` int(10) unsigned NOT NULL COMMENT 'Country',
  PRIMARY KEY (`show_id`,`country_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `prod_show_country_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_show_country_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `prod_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_creator`
--

DROP TABLE IF EXISTS `show_creator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_creator` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `person_id` int(10) unsigned NOT NULL COMMENT 'Person',
  PRIMARY KEY (`show_id`,`person_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `prod_show_creator_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_show_creator_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `prod_person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_crew`
--

DROP TABLE IF EXISTS `show_crew`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_crew` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `show_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `department` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Department',
  `job` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Job',
  `profile_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Profile path',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`),
  CONSTRAINT `prod_show_crew_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_genre`
--

DROP TABLE IF EXISTS `show_genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_genre` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `genre_id` int(10) unsigned NOT NULL COMMENT 'Genre',
  PRIMARY KEY (`show_id`,`genre_id`),
  KEY `genre_id` (`genre_id`),
  CONSTRAINT `prod_show_genre_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_show_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `prod_genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_network`
--

DROP TABLE IF EXISTS `show_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_network` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `network_id` int(10) unsigned NOT NULL COMMENT 'Network',
  PRIMARY KEY (`show_id`,`network_id`),
  KEY `network_id` (`network_id`),
  CONSTRAINT `prod_show_network_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_show_network_ibfk_2` FOREIGN KEY (`network_id`) REFERENCES `prod_network` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_popular`
--

DROP TABLE IF EXISTS `show_popular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_popular` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `order` tinyint(4) unsigned NOT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`),
  CONSTRAINT `show_popular_show_id` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_show_runtime`
--

DROP TABLE IF EXISTS `show_runtime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `show_runtime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `show_id` int(11) unsigned NOT NULL COMMENT 'Show',
  `minutes` smallint(5) unsigned NOT NULL COMMENT 'Minutes',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`),
  CONSTRAINT `prod_show_runtime_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=292 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_themoviedb_rate`
--

DROP TABLE IF EXISTS `themoviedb_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `themoviedb_rate` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `password` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `language_id` int(10) unsigned DEFAULT '2',
  `timezone` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'UTC' COMMENT 'Timezone',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reset_key` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validation_key` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `prod_user_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `prod_language` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_user_email_group`
--

DROP TABLE IF EXISTS `user_email_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_email_group` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `email_group_id` int(10) unsigned NOT NULL COMMENT 'Email group',
  PRIMARY KEY (`user_id`,`email_group_id`),
  KEY `user_email_group_email_group_id` (`email_group_id`),
  CONSTRAINT `user_email_group_email_group_id` FOREIGN KEY (`email_group_id`) REFERENCES `prod_email_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_email_group_user_id` FOREIGN KEY (`user_id`) REFERENCES `prod_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_user_episode`
--

DROP TABLE IF EXISTS `user_episode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_episode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `episode_id` int(10) unsigned NOT NULL COMMENT 'Episode',
  `run_id` int(10) unsigned NOT NULL COMMENT 'Run',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `episode_id` (`episode_id`),
  KEY `run_id` (`run_id`),
  CONSTRAINT `prod_user_episode_ibfk_2` FOREIGN KEY (`episode_id`) REFERENCES `prod_episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_user_episode_ibfk_3` FOREIGN KEY (`run_id`) REFERENCES `prod_user_show_run` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=9977 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_user_movie`
--

DROP TABLE IF EXISTS `user_movie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_movie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned zerofill NOT NULL COMMENT 'User',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `movie_id` (`movie_id`)
) ENGINE=MEMORY AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_user_show`
--

DROP TABLE IF EXISTS `user_show`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_show` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `archived` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Archived',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`show_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `archived` (`archived`),
  CONSTRAINT `prod_user_show_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_user_show_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `prod_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prod_user_show_run`
--

DROP TABLE IF EXISTS `user_show_run`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_show_run` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `show_id` (`show_id`),
  CONSTRAINT `prod_user_show_run_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `prod_show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prod_user_show_run_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `prod_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MEMORY AUTO_INCREMENT=222 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-28 17:05:09
