-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 18. Feb 2015 um 19:56
-- Server Version: 5.6.19-0ubuntu0.14.04.1
-- PHP-Version: 5.6.5-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `seen`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_assignment`
--

CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_item`
--

CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `type` (`type`),
  KEY `auth_item_rule_name` (`rule_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_item_child`
--

CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `auth_item_child_child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_rule`
--

CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `description` text COLLATE utf8_unicode_ci COMMENT 'Description',
  `parent_id` int(10) unsigned DEFAULT NULL COMMENT 'Parent Company',
  `headquarters` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Headquarter',
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Homepage',
  `logo_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Logo path',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email`
--

CREATE TABLE IF NOT EXISTS `email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ts` timestamp NULL DEFAULT NULL COMMENT 'Timestamp',
  `event` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Event',
  `text` text COLLATE utf8_unicode_ci COMMENT 'Text',
  `html` text COLLATE utf8_unicode_ci COMMENT 'Html',
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
  KEY `assigned_user_id` (`assigned_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email_attachment`
--

CREATE TABLE IF NOT EXISTS `email_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `email_id` int(10) unsigned NOT NULL COMMENT 'Email',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Type',
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email_group`
--

CREATE TABLE IF NOT EXISTS `email_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `receiver` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Receiver',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `receiver` (`receiver`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email_to`
--

CREATE TABLE IF NOT EXISTS `email_to` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `email_id` int(10) unsigned NOT NULL COMMENT 'Email',
  `to_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email',
  `to_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  KEY `to_email` (`to_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `episode`
--

CREATE TABLE IF NOT EXISTS `episode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `season_id` int(10) unsigned NOT NULL COMMENT 'Season',
  `number` smallint(5) unsigned DEFAULT NULL COMMENT 'Number',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `overview` text COLLATE utf8_unicode_ci COMMENT 'Overview',
  `air_date` date DEFAULT NULL COMMENT 'Air date',
  `still_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Still path',
  `vote_average` double unsigned DEFAULT NULL COMMENT 'Average vote',
  `vote_count` int(10) unsigned DEFAULT NULL COMMENT 'Vote count',
  `production_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Production code',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  UNIQUE KEY `episode_season_id_number` (`season_id`,`number`),
  KEY `season_id` (`season_id`),
  KEY `themoviedb_id` (`themoviedb_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `iso` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ISO 639-1',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `rtl` tinyint(1) NOT NULL DEFAULT '0',
  `en_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `popular_shows_updated_at` datetime DEFAULT NULL COMMENT 'Popular shows updated at',
  `popular_movies_updated_at` datetime DEFAULT NULL COMMENT 'Popular movies updated at',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level` int(11) DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_time` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `prefix` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_log_level` (`level`),
  KEY `idx_log_category` (`category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie`
--

CREATE TABLE IF NOT EXISTS `movie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `language_id` int(10) unsigned NOT NULL COMMENT 'Language',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Title',
  `original_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Original title',
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Slug',
  `tagline` text COLLATE utf8_unicode_ci COMMENT 'Tagline',
  `overview` text COLLATE utf8_unicode_ci COMMENT 'Overview',
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
  KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_cast`
--

CREATE TABLE IF NOT EXISTS `movie_cast` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `person_id` int(10) unsigned DEFAULT NULL COMMENT 'Person',
  `credit_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Credit ID',
  `character` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Character',
  `order` smallint(5) unsigned DEFAULT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_company`
--

CREATE TABLE IF NOT EXISTS `movie_company` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `company_id` int(10) unsigned NOT NULL COMMENT 'Company',
  PRIMARY KEY (`movie_id`,`company_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_country`
--

CREATE TABLE IF NOT EXISTS `movie_country` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `country_id` int(10) unsigned NOT NULL COMMENT 'Country',
  PRIMARY KEY (`movie_id`,`country_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_crew`
--

CREATE TABLE IF NOT EXISTS `movie_crew` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `person_id` int(10) unsigned DEFAULT NULL COMMENT 'Person',
  `credit_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Credit ID',
  `department` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Department',
  `job` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Job',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_genre`
--

CREATE TABLE IF NOT EXISTS `movie_genre` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `genre_id` int(10) unsigned NOT NULL COMMENT 'Genre',
  PRIMARY KEY (`movie_id`,`genre_id`),
  KEY `genre_id` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_language`
--

CREATE TABLE IF NOT EXISTS `movie_language` (
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `language_id` int(10) unsigned NOT NULL COMMENT 'Language',
  PRIMARY KEY (`movie_id`,`language_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_popular`
--

CREATE TABLE IF NOT EXISTS `movie_popular` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `order` tinyint(4) unsigned NOT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_similar`
--

CREATE TABLE IF NOT EXISTS `movie_similar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `similar_to_movie_id` int(10) unsigned DEFAULT NULL COMMENT 'Similiar Movie',
  `similar_to_themoviedb_id` int(10) unsigned NOT NULL COMMENT 'Similar to TheMovieDB',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  KEY `similar_to_movie_id` (`similar_to_movie_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `movie_video`
--

CREATE TABLE IF NOT EXISTS `movie_video` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The Movie Database ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site` varchar(127) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `type` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `network`
--

CREATE TABLE IF NOT EXISTS `network` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `oauth_access_token`
--

CREATE TABLE IF NOT EXISTS `oauth_access_token` (
  `access_token` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Access token',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `oauth_application_id` int(10) unsigned NOT NULL COMMENT 'Oauth application',
  `scopes` text COLLATE utf8_unicode_ci COMMENT 'Scopes',
  `expires_at` datetime DEFAULT NULL COMMENT 'Expires at',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`access_token`),
  KEY `user_id` (`user_id`),
  KEY `oauth_application_id` (`oauth_application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `oauth_application`
--

CREATE TABLE IF NOT EXISTS `oauth_application` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Description',
  `website` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Website',
  `key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Key',
  `secret` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Secret',
  `callback` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Callback url',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `oauth_refresh_token`
--

CREATE TABLE IF NOT EXISTS `oauth_refresh_token` (
  `refresh_token` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Refresh token',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `oauth_application_id` int(10) unsigned NOT NULL COMMENT 'Oauth application',
  `scopes` text COLLATE utf8_unicode_ci COMMENT 'Scopes',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`refresh_token`),
  KEY `user_id` (`user_id`),
  KEY `oauth_application_id` (`oauth_application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `oauth_request_token`
--

CREATE TABLE IF NOT EXISTS `oauth_request_token` (
  `request_token` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Request token',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `oauth_application_id` int(10) unsigned NOT NULL COMMENT 'Oauth application',
  `scopes` text COLLATE utf8_unicode_ci COMMENT 'Scopes',
  `expires_at` datetime DEFAULT NULL COMMENT 'Expires at',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`request_token`),
  KEY `user_id` (`user_id`),
  KEY `oauth_application_id` (`oauth_application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `person`
--

CREATE TABLE IF NOT EXISTS `person` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `biography` text COLLATE utf8_unicode_ci COMMENT 'Biography',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `person_alias`
--

CREATE TABLE IF NOT EXISTS `person_alias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `person_id` int(10) unsigned NOT NULL COMMENT 'Person',
  `alias` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Alias',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `season`
--

CREATE TABLE IF NOT EXISTS `season` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `number` smallint(5) unsigned DEFAULT NULL COMMENT 'Number',
  `overview` text COLLATE utf8_unicode_ci COMMENT 'Overview',
  `poster_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Poster path',
  `air_date` date DEFAULT NULL COMMENT 'Air date',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`id`),
  UNIQUE KEY `season_show_id_number` (`show_id`,`number`),
  KEY `themoviedb_id` (`themoviedb_id`),
  KEY `show_id` (`show_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show`
--

CREATE TABLE IF NOT EXISTS `show` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `themoviedb_id` int(10) unsigned NOT NULL COMMENT 'TheMovieDB',
  `language_id` int(10) unsigned NOT NULL COMMENT 'Language',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `original_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Original name',
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Slug',
  `overview` text COLLATE utf8_unicode_ci COMMENT 'Overview',
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
  UNIQUE KEY `show_themoviedb_id_language_id` (`themoviedb_id`,`language_id`),
  KEY `language_id` (`language_id`),
  KEY `themoviedb_id` (`themoviedb_id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_cast`
--

CREATE TABLE IF NOT EXISTS `show_cast` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `show_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned DEFAULT NULL COMMENT 'Person',
  `credit_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Credit ID',
  `character` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Character',
  `order` smallint(5) unsigned DEFAULT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_country`
--

CREATE TABLE IF NOT EXISTS `show_country` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `country_id` int(10) unsigned NOT NULL COMMENT 'Country',
  PRIMARY KEY (`show_id`,`country_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_creator`
--

CREATE TABLE IF NOT EXISTS `show_creator` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `person_id` int(10) unsigned NOT NULL COMMENT 'Person',
  PRIMARY KEY (`show_id`,`person_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_crew`
--

CREATE TABLE IF NOT EXISTS `show_crew` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `show_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned DEFAULT NULL COMMENT 'Person',
  `department` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Department',
  `job` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Job',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `updated_at` datetime DEFAULT NULL COMMENT 'Updated at',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_genre`
--

CREATE TABLE IF NOT EXISTS `show_genre` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `genre_id` int(10) unsigned NOT NULL COMMENT 'Genre',
  PRIMARY KEY (`show_id`,`genre_id`),
  KEY `genre_id` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_network`
--

CREATE TABLE IF NOT EXISTS `show_network` (
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `network_id` int(10) unsigned NOT NULL COMMENT 'Network',
  PRIMARY KEY (`show_id`,`network_id`),
  KEY `network_id` (`network_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_popular`
--

CREATE TABLE IF NOT EXISTS `show_popular` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `order` tinyint(4) unsigned NOT NULL COMMENT 'Order',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_runtime`
--

CREATE TABLE IF NOT EXISTS `show_runtime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `show_id` int(11) unsigned NOT NULL COMMENT 'Show',
  `minutes` smallint(5) unsigned NOT NULL COMMENT 'Minutes',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `show_video`
--

CREATE TABLE IF NOT EXISTS `show_video` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The Movie Database ID',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site` varchar(127) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `type` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sync_status`
--

CREATE TABLE IF NOT EXISTS `sync_status` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated` date NOT NULL DEFAULT '0000-00-00',
  `value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`name`,`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `themoviedb_rate`
--

CREATE TABLE IF NOT EXISTS `themoviedb_rate` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `password` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `language_id` int(10) unsigned DEFAULT '2',
  `timezone` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'UTC' COMMENT 'Timezone',
  `reset_key` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validation_key` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_public` tinyint(1) NOT NULL DEFAULT '0',
  `profile_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_email_group`
--

CREATE TABLE IF NOT EXISTS `user_email_group` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `email_group_id` int(10) unsigned NOT NULL COMMENT 'Email group',
  PRIMARY KEY (`user_id`,`email_group_id`),
  KEY `user_email_group_email_group_id` (`email_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_episode`
--

CREATE TABLE IF NOT EXISTS `user_episode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `episode_id` int(10) unsigned NOT NULL COMMENT 'Episode',
  `run_id` int(10) unsigned NOT NULL COMMENT 'Run',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `episode_id` (`episode_id`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_movie`
--

CREATE TABLE IF NOT EXISTS `user_movie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `movie_id` (`movie_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_movie_watchlist`
--

CREATE TABLE IF NOT EXISTS `user_movie_watchlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `movie_id` int(10) unsigned NOT NULL COMMENT 'Movie',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_show`
--

CREATE TABLE IF NOT EXISTS `user_show` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `archived` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Archived',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Deleted at',
  PRIMARY KEY (`show_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `archived` (`archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_show_run`
--

CREATE TABLE IF NOT EXISTS `user_show_run` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User',
  `show_id` int(10) unsigned NOT NULL COMMENT 'Show',
  `created_at` datetime DEFAULT NULL COMMENT 'Created at',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `show_id` (`show_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_item_name` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_rule_name` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_child` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_parent` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `email`
--
ALTER TABLE `email`
  ADD CONSTRAINT `email_assigned_user_id` FOREIGN KEY (`assigned_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `email_respond_user_id` FOREIGN KEY (`respond_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `email_attachment`
--
ALTER TABLE `email_attachment`
  ADD CONSTRAINT `email_attachment_email_id` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `email_to`
--
ALTER TABLE `email_to`
  ADD CONSTRAINT `email_to_email_id` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `episode`
--
ALTER TABLE `episode`
  ADD CONSTRAINT `episode_ibfk_2` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie`
--
ALTER TABLE `movie`
  ADD CONSTRAINT `movie_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_cast`
--
ALTER TABLE `movie_cast`
  ADD CONSTRAINT `movie_cast_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_cast_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_company`
--
ALTER TABLE `movie_company`
  ADD CONSTRAINT `movie_company_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_company_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_country`
--
ALTER TABLE `movie_country`
  ADD CONSTRAINT `movie_country_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_country_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_crew`
--
ALTER TABLE `movie_crew`
  ADD CONSTRAINT `movie_crew_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_crew_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_genre`
--
ALTER TABLE `movie_genre`
  ADD CONSTRAINT `movie_genre_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_language`
--
ALTER TABLE `movie_language`
  ADD CONSTRAINT `movie_language_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_language_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_popular`
--
ALTER TABLE `movie_popular`
  ADD CONSTRAINT `movie_popular_movie_id` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_similar`
--
ALTER TABLE `movie_similar`
  ADD CONSTRAINT `movie_similar_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_similar_ibfk_2` FOREIGN KEY (`similar_to_movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `movie_video`
--
ALTER TABLE `movie_video`
  ADD CONSTRAINT `movie_video_movie_id` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `oauth_access_token`
--
ALTER TABLE `oauth_access_token`
  ADD CONSTRAINT `oauth_access_token_oauth_application_id` FOREIGN KEY (`oauth_application_id`) REFERENCES `oauth_application` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oauth_access_token_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `oauth_application`
--
ALTER TABLE `oauth_application`
  ADD CONSTRAINT `oauth_application_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `oauth_refresh_token`
--
ALTER TABLE `oauth_refresh_token`
  ADD CONSTRAINT `oauth_refresh_token_oauth_application_id` FOREIGN KEY (`oauth_application_id`) REFERENCES `oauth_application` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oauth_refresh_token_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `oauth_request_token`
--
ALTER TABLE `oauth_request_token`
  ADD CONSTRAINT `oauth_request_token_oauth_application_id` FOREIGN KEY (`oauth_application_id`) REFERENCES `oauth_application` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oauth_request_token_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `person_alias`
--
ALTER TABLE `person_alias`
  ADD CONSTRAINT `person_alias_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `season`
--
ALTER TABLE `season`
  ADD CONSTRAINT `season_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show`
--
ALTER TABLE `show`
  ADD CONSTRAINT `show_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_cast`
--
ALTER TABLE `show_cast`
  ADD CONSTRAINT `show_cast_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_cast_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_country`
--
ALTER TABLE `show_country`
  ADD CONSTRAINT `show_country_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_country_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_creator`
--
ALTER TABLE `show_creator`
  ADD CONSTRAINT `show_creator_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_creator_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_crew`
--
ALTER TABLE `show_crew`
  ADD CONSTRAINT `show_crew_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_crew_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_genre`
--
ALTER TABLE `show_genre`
  ADD CONSTRAINT `show_genre_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_network`
--
ALTER TABLE `show_network`
  ADD CONSTRAINT `show_network_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `show_network_ibfk_2` FOREIGN KEY (`network_id`) REFERENCES `network` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_popular`
--
ALTER TABLE `show_popular`
  ADD CONSTRAINT `show_popular_show_id` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_runtime`
--
ALTER TABLE `show_runtime`
  ADD CONSTRAINT `show_runtime_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `show_video`
--
ALTER TABLE `show_video`
  ADD CONSTRAINT `show_video_show_id` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`);

--
-- Constraints der Tabelle `user_email_group`
--
ALTER TABLE `user_email_group`
  ADD CONSTRAINT `user_email_group_email_group_id` FOREIGN KEY (`email_group_id`) REFERENCES `email_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_email_group_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user_episode`
--
ALTER TABLE `user_episode`
  ADD CONSTRAINT `user_episode_ibfk_2` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_episode_ibfk_3` FOREIGN KEY (`run_id`) REFERENCES `user_show_run` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user_movie`
--
ALTER TABLE `user_movie`
  ADD CONSTRAINT `user_movie_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_movie_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user_movie_watchlist`
--
ALTER TABLE `user_movie_watchlist`
  ADD CONSTRAINT `user_movie_watchlist_movie_id` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_movie_watchlist_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user_show`
--
ALTER TABLE `user_show`
  ADD CONSTRAINT `user_show_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_show_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user_show_run`
--
ALTER TABLE `user_show_run`
  ADD CONSTRAINT `user_show_run_ibfk_1` FOREIGN KEY (`show_id`) REFERENCES `show` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_show_run_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
