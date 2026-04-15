-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: campus-org
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel_cache_campus@upg.cat|127.0.0.1','i:1;',1775744718),('laravel_cache_campus@upg.cat|127.0.0.1:timer','i:1775744718;',1775744718),('laravel_cache_global_language','s:2:\"ca\";',1776192578),('laravel_cache_secretaria@upg.cat|127.0.0.1','i:1;',1776174338),('laravel_cache_secretaria@upg.cat|127.0.0.1:timer','i:1776174338;',1776174338),('laravel_cache_spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:101:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"admin.access\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:11:\"users.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:13;i:5;i:14;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:10:\"users.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:10;i:3;i:13;i:4;i:14;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"users.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:13;i:5;i:14;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:10:\"users.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:13;i:5;i:14;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"users.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:13;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:11:\"roles.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:12:\"roles.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:10:\"roles.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"roles.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:17:\"permissions.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:18:\"permissions.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:16:\"permissions.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:18:\"permissions.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:21:\"notifications.publish\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:19:\"notifications.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:11:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:10;i:6;i:11;i:7;i:12;i:8;i:13;i:9;i:14;i:10;i:15;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:20:\"notifications.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:14:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;i:7;i:8;i:8;i:10;i:9;i:11;i:10;i:12;i:11;i:13;i:12;i:14;i:13;i:15;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:18:\"notifications.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:11:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:10;i:6;i:11;i:7;i:12;i:8;i:13;i:9;i:14;i:10;i:15;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:20:\"notifications.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:13;i:6;i:14;i:7;i:15;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:18:\"notifications.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:14:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;i:7;i:8;i:8;i:10;i:9;i:11;i:10;i:12;i:11;i:13;i:12;i:14;i:13;i:15;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:12:\"events.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:13;i:6;i:14;i:7;i:15;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:11:\"events.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:13;i:6;i:14;i:7;i:15;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:13:\"events.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:13;i:6;i:14;i:7;i:15;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:11:\"events.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:13;i:6;i:14;i:7;i:15;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:13:\"events.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:13;i:6;i:14;i:7;i:15;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:17:\"event_types.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:16:\"event_types.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:18:\"event_types.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:16:\"event_types.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:18:\"event_types.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:21:\"event_questions.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:15;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:20:\"event_questions.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:15;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:22:\"event_questions.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:15;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:20:\"event_questions.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:15;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:22:\"event_questions.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:15;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:19:\"event_answers.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:18:\"event_answers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:20:\"event_answers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:18:\"event_answers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:20:\"event_answers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:30:\"event_question_templates.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:29:\"event_question_templates.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:31:\"event_question_templates.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:29:\"event_question_templates.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:31:\"event_question_templates.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:23:\"campus.categories.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:12;i:5;i:13;i:6;i:14;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:22:\"campus.categories.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:12;i:5;i:13;i:6;i:14;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:24:\"campus.categories.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:12;i:3;i:13;i:4;i:14;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:22:\"campus.categories.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:12;i:3;i:13;i:4;i:14;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:24:\"campus.categories.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:13;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:26:\"documents.categories.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:11;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:25:\"documents.categories.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:11;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:27:\"documents.categories.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:11;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:25:\"documents.categories.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:11;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:27:\"documents.categories.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:20:\"campus.seasons.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:12;i:5;i:13;i:6;i:14;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:19:\"campus.seasons.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:12;i:5;i:13;i:6;i:14;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:21:\"campus.seasons.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:12;i:3;i:13;i:4;i:14;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:19:\"campus.seasons.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:12;i:3;i:13;i:4;i:14;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:21:\"campus.seasons.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:13;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:20:\"campus.courses.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;i:9;i:15;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:19:\"campus.courses.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;i:9;i:15;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:21:\"campus.courses.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:12;i:5;i:13;i:6;i:14;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:19:\"campus.courses.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:12;i:5;i:13;i:6;i:14;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:21:\"campus.courses.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:13;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:21:\"campus.courses.enroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:21:\"campus.courses.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:12;i:5;i:13;i:6;i:14;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:21:\"campus.students.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:11;i:5;i:12;i:6;i:13;i:7;i:14;i:8;i:15;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:20:\"campus.students.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;i:9;i:15;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:22:\"campus.students.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:11;i:5;i:13;i:6;i:14;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:20:\"campus.students.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:11;i:5;i:12;i:6;i:13;i:7;i:14;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:22:\"campus.students.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:13;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:22:\"campus.students.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:11;i:5;i:13;i:6;i:14;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:21:\"campus.teachers.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;i:9;i:15;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:20:\"campus.teachers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;i:9;i:15;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:22:\"campus.teachers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:13;i:6;i:14;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:20:\"campus.teachers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:12;i:6;i:13;i:7;i:14;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:22:\"campus.teachers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:13;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:22:\"campus.teachers.assign\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:13;i:6;i:14;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:23:\"campus.consents.request\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:20:\"campus.consents.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:26:\"campus.registrations.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:25:\"campus.registrations.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:10:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;i:5;i:10;i:6;i:11;i:7;i:12;i:8;i:13;i:9;i:14;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:27:\"campus.registrations.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:13;i:7;i:14;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:25:\"campus.registrations.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:8:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:13;i:7;i:14;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:27:\"campus.registrations.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:13;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:28:\"campus.registrations.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:27:\"campus.registrations.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:27:\"campus.registrations.import\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:13;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:20:\"campus.payments.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:13;i:5;i:14;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:22:\"campus.payments.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:13;i:5;i:14;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:23:\"campus.payments.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:13;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:22:\"campus.payments.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:19:\"campus.profile.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:6;i:3;i:7;i:4;i:8;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:19:\"campus.profile.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:6;i:3;i:7;i:4;i:8;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:22:\"campus.my_courses.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:6;i:3;i:7;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:24:\"campus.my_courses.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:6;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:24:\"campus.reports.financial\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:99;a:4:{s:1:\"a\";i:100;s:1:\"b\";s:35:\"campus.teachers.financial_data.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:100;a:4:{s:1:\"a\";i:101;s:1:\"b\";s:37:\"campus.teachers.financial_data.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}}s:5:\"roles\";a:14:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super-admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:6:\"gestor\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:10;s:1:\"b\";s:11:\"coordinacio\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:13;s:1:\"b\";s:8:\"director\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:14;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:8:\"treasury\";s:1:\"c\";s:3:\"web\";}i:7;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:6:\"editor\";s:1:\"c\";s:3:\"web\";}i:8;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:10:\"secretaria\";s:1:\"c\";s:3:\"web\";}i:9;a:3:{s:1:\"a\";i:12;s:1:\"b\";s:6:\"gestio\";s:1:\"c\";s:3:\"web\";}i:10;a:3:{s:1:\"a\";i:15;s:1:\"b\";s:11:\"comunicacio\";s:1:\"c\";s:3:\"web\";}i:11;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:7:\"teacher\";s:1:\"c\";s:3:\"web\";}i:12;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:7:\"student\";s:1:\"c\";s:3:\"web\";}i:13;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:4:\"user\";s:1:\"c\";s:3:\"web\";}}}',1776193231);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `campus_categories`
--

DROP TABLE IF EXISTS `campus_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blue',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tag',
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` bigint unsigned DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_categories_slug_unique` (`slug`),
  KEY `campus_categories_is_active_is_featured_index` (`is_active`,`is_featured`),
  KEY `campus_categories_parent_id_order_index` (`parent_id`,`order`),
  CONSTRAINT `campus_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `campus_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_categories`
--

LOCK TABLES `campus_categories` WRITE;
/*!40000 ALTER TABLE `campus_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_course_schedules`
--

DROP TABLE IF EXISTS `campus_course_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_course_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `space_id` bigint unsigned NOT NULL,
  `time_slot_id` bigint unsigned NOT NULL,
  `semester` enum('1Q','2Q') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('assigned','pending','conflict') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `session_count` int NOT NULL DEFAULT '12',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_space_time_semester` (`space_id`,`time_slot_id`,`semester`),
  KEY `campus_course_schedules_course_id_foreign` (`course_id`),
  KEY `campus_course_schedules_time_slot_id_foreign` (`time_slot_id`),
  KEY `campus_course_schedules_semester_status_index` (`semester`,`status`),
  CONSTRAINT `campus_course_schedules_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `campus_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_course_schedules_space_id_foreign` FOREIGN KEY (`space_id`) REFERENCES `campus_spaces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_course_schedules_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `campus_time_slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_course_schedules`
--

LOCK TABLES `campus_course_schedules` WRITE;
/*!40000 ALTER TABLE `campus_course_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_course_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_course_student`
--

DROP TABLE IF EXISTS `campus_course_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_course_student` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `season_id` bigint unsigned NOT NULL,
  `enrollment_date` date NOT NULL DEFAULT '2026-04-09',
  `academic_status` enum('enrolled','active','completed','dropped','transferred','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enrolled',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `grade_letter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_status` enum('pending','graded','appealed','final') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attendance_status` enum('regular','irregular','excellent','poor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance_percentage` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_course_season` (`student_id`,`course_id`,`season_id`),
  KEY `campus_course_student_season_id_academic_status_index` (`season_id`,`academic_status`),
  KEY `campus_course_student_course_id_academic_status_index` (`course_id`,`academic_status`),
  KEY `campus_course_student_student_id_academic_status_index` (`student_id`,`academic_status`),
  KEY `campus_course_student_season_id_course_id_index` (`season_id`,`course_id`),
  CONSTRAINT `campus_course_student_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `campus_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_course_student_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `campus_seasons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_course_student_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `campus_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_course_student`
--

LOCK TABLES `campus_course_student` WRITE;
/*!40000 ALTER TABLE `campus_course_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_course_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_course_teacher`
--

DROP TABLE IF EXISTS `campus_course_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_course_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'teacher',
  `sessions_assigned` decimal(5,2) NOT NULL DEFAULT '0.00',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `finished_at` date DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_course_teacher_course_id_teacher_id_unique` (`course_id`,`teacher_id`),
  KEY `campus_course_teacher_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `campus_course_teacher_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `campus_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_course_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `campus_teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_course_teacher`
--

LOCK TABLES `campus_course_teacher` WRITE;
/*!40000 ALTER TABLE `campus_course_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_course_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_courses`
--

DROP TABLE IF EXISTS `campus_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `season_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `hours` int NOT NULL DEFAULT '0',
  `sessions` int DEFAULT NULL,
  `max_students` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `level` enum('none','beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schedule` json DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `space_id` bigint unsigned DEFAULT NULL,
  `time_slot_id` bigint unsigned DEFAULT NULL,
  `format` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','planning','in_progress','completed','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `requirements` text COLLATE utf8mb4_unicode_ci,
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_courses_code_unique` (`code`),
  UNIQUE KEY `campus_courses_slug_unique` (`slug`),
  KEY `campus_courses_season_id_is_active_index` (`season_id`,`is_active`),
  KEY `campus_courses_is_public_index` (`is_public`),
  KEY `campus_courses_created_by_index` (`created_by`),
  KEY `campus_courses_category_id_is_active_index` (`category_id`,`is_active`),
  KEY `campus_courses_status_index` (`status`),
  KEY `campus_courses_parent_id_foreign` (`parent_id`),
  KEY `campus_courses_space_id_foreign` (`space_id`),
  KEY `campus_courses_time_slot_id_foreign` (`time_slot_id`),
  CONSTRAINT `campus_courses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `campus_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `campus_courses_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `campus_courses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `campus_courses_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `campus_seasons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_courses_space_id_foreign` FOREIGN KEY (`space_id`) REFERENCES `campus_spaces` (`id`) ON DELETE SET NULL,
  CONSTRAINT `campus_courses_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `campus_time_slots` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_courses`
--

LOCK TABLES `campus_courses` WRITE;
/*!40000 ALTER TABLE `campus_courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_registrations`
--

DROP TABLE IF EXISTS `campus_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `registration_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `season_id` bigint unsigned DEFAULT NULL,
  `registration_date` date NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('pending','paid','partial','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_due_date` date DEFAULT NULL,
  `payment_history` json DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `attendance_status` enum('regular','irregular','absent') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_registrations_student_id_course_id_unique` (`student_id`,`course_id`),
  UNIQUE KEY `campus_registrations_registration_code_unique` (`registration_code`),
  KEY `campus_registrations_course_id_foreign` (`course_id`),
  KEY `campus_registrations_student_id_course_id_index` (`student_id`,`course_id`),
  KEY `campus_registrations_status_index` (`status`),
  KEY `campus_registrations_payment_status_index` (`payment_status`),
  KEY `campus_registrations_season_id_status_index` (`season_id`,`status`),
  KEY `campus_registrations_season_id_payment_status_index` (`season_id`,`payment_status`),
  KEY `campus_registrations_student_id_season_id_index` (`student_id`,`season_id`),
  CONSTRAINT `campus_registrations_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `campus_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_registrations_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `campus_seasons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_registrations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `campus_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_registrations`
--

LOCK TABLES `campus_registrations` WRITE;
/*!40000 ALTER TABLE `campus_registrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_seasons`
--

DROP TABLE IF EXISTS `campus_seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_seasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_start` date NOT NULL,
  `registration_end` date NOT NULL,
  `season_start` date NOT NULL,
  `season_end` date NOT NULL,
  `type` enum('annual','semester','trimester','quarter') COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester_number` tinyint DEFAULT NULL,
  `status` enum('draft','planning','active','registration','in_progress','completed','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `periods` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_seasons_slug_unique` (`slug`),
  KEY `campus_seasons_is_active_is_current_index` (`is_active`,`is_current`),
  KEY `campus_seasons_status_is_active_index` (`status`,`is_active`),
  KEY `campus_seasons_status_is_current_index` (`status`,`is_current`),
  KEY `campus_seasons_parent_id_type_index` (`parent_id`,`type`),
  KEY `campus_seasons_parent_id_semester_number_index` (`parent_id`,`semester_number`),
  CONSTRAINT `campus_seasons_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `campus_seasons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_seasons`
--

LOCK TABLES `campus_seasons` WRITE;
/*!40000 ALTER TABLE `campus_seasons` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_seasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_spaces`
--

DROP TABLE IF EXISTS `campus_spaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_spaces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `type` enum('sala_actes','mitjana','petita','polivalent','extern') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `equipment` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_spaces_code_unique` (`code`),
  KEY `campus_spaces_type_is_active_index` (`type`,`is_active`),
  KEY `campus_spaces_capacity_index` (`capacity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_spaces`
--

LOCK TABLES `campus_spaces` WRITE;
/*!40000 ALTER TABLE `campus_spaces` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_spaces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_students`
--

DROP TABLE IF EXISTS `campus_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `student_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','graduated','suspended','on_leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `enrollment_date` date NOT NULL,
  `academic_record` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_students_user_id_unique` (`user_id`),
  UNIQUE KEY `campus_students_student_code_unique` (`student_code`),
  KEY `campus_students_status_index` (`status`),
  KEY `campus_students_student_code_index` (`student_code`),
  CONSTRAINT `campus_students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_students`
--

LOCK TABLES `campus_students` WRITE;
/*!40000 ALTER TABLE `campus_students` DISABLE KEYS */;
INSERT INTO `campus_students` VALUES (1,21,'STD007','Anna','Martínez i Roca','11223344C','2000-05-15','+34 600 555 666','Carrer Principal 123, Barcelona','alumne@','Pare - Josep Martínez','+34 600 777 888','active','2024-09-01',NULL,NULL,'2026-04-09 12:06:50','2026-04-09 12:06:50'),(2,22,'EST008','Carles','Ruiz i Navarro','55667788D','2001-03-22','+34 600 999 000','Avinguda Central 456, L\'Hospitalet','student@','Mare - Laura Navarro','+34 600 111 222','active','2024-09-01',NULL,NULL,'2026-04-09 12:06:50','2026-04-09 12:06:50');
/*!40000 ALTER TABLE `campus_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_teacher_payments`
--

DROP TABLE IF EXISTS `campus_teacher_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_teacher_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `season_id` bigint unsigned NOT NULL,
  `payment_option` enum('own_fee','ceded_fee','waived_fee') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fiscal_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fiscal_situation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacions` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_titular` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `needs_payment` enum('own_fee','ceded_fee','waived_fee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'own_fee',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_teacher_payments_teacher_id_course_id_season_id_unique` (`teacher_id`,`course_id`,`season_id`),
  KEY `campus_teacher_payments_course_id_foreign` (`course_id`),
  KEY `campus_teacher_payments_season_id_foreign` (`season_id`),
  CONSTRAINT `campus_teacher_payments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `campus_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_teacher_payments_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `campus_seasons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_teacher_payments_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `campus_teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_teacher_payments`
--

LOCK TABLES `campus_teacher_payments` WRITE;
/*!40000 ALTER TABLE `campus_teacher_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_teacher_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_teacher_schedules`
--

DROP TABLE IF EXISTS `campus_teacher_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_teacher_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `time_slot_id` bigint unsigned NOT NULL,
  `semester` enum('1Q','2Q') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `preferences` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_teacher_time_semester` (`teacher_id`,`time_slot_id`,`semester`),
  KEY `campus_teacher_schedules_time_slot_id_foreign` (`time_slot_id`),
  KEY `campus_teacher_schedules_semester_is_available_index` (`semester`,`is_available`),
  CONSTRAINT `campus_teacher_schedules_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `campus_teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campus_teacher_schedules_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `campus_time_slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_teacher_schedules`
--

LOCK TABLES `campus_teacher_schedules` WRITE;
/*!40000 ALTER TABLE `campus_teacher_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_teacher_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_teachers`
--

DROP TABLE IF EXISTS `campus_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `teacher_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` text COLLATE utf8mb4_unicode_ci,
  `bank_titular` text COLLATE utf8mb4_unicode_ci,
  `fiscal_id` text COLLATE utf8mb4_unicode_ci,
  `fiscal_situation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `needs_payment` enum('own_fee','ceded_fee','waived_fee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'own_fee',
  `invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` enum('waived','own','ceded') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'waived: no cobren, own: cobren ells, ceded: cedeixen cobrament',
  `beneficiary_dni` text COLLATE utf8mb4_unicode_ci,
  `beneficiary_iban` text COLLATE utf8mb4_unicode_ci,
  `beneficiary_titular` text COLLATE utf8mb4_unicode_ci,
  `beneficiary_fiscal_situation` enum('autonom','employee','pensioner','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Situació fiscal del beneficiari',
  `beneficiary_invoice` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'El beneficiari presentarà factura',
  `beneficiary_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ciutat del beneficiari (només per ceded)',
  `beneficiary_postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codi postal del beneficiari (només per ceded)',
  `data_consent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Consentiment de dades (per invoice functionality)',
  `fiscal_responsibility` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Responsabilitat fiscal (per invoice functionality)',
  `ceded_confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Confirmació de cobrament cedit',
  `payment_status` enum('pending','confirmed','processed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Estat del procés de pagament',
  `payment_confirmed_at` timestamp NULL DEFAULT NULL COMMENT 'Data de confirmació de dades de pagament',
  `payment_pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta al PDF de confirmació de pagament',
  `degree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `areas` json DEFAULT NULL,
  `status` enum('active','inactive','on_leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `hiring_date` date NOT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_teachers_user_id_unique` (`user_id`),
  UNIQUE KEY `campus_teachers_teacher_code_unique` (`teacher_code`),
  KEY `campus_teachers_status_index` (`status`),
  KEY `campus_teachers_teacher_code_index` (`teacher_code`),
  KEY `campus_teachers_payment_type_index` (`payment_type`),
  KEY `campus_teachers_payment_status_index` (`payment_status`),
  KEY `campus_teachers_payment_type_payment_status_index` (`payment_type`,`payment_status`),
  CONSTRAINT `campus_teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_teachers`
--

LOCK TABLES `campus_teachers` WRITE;
/*!40000 ALTER TABLE `campus_teachers` DISABLE KEYS */;
INSERT INTO `campus_teachers` VALUES (1,19,'PROF005','Joan','Prat i Soler','12345678A','teacher@','+34 600 111 222','Carrer Major 1, Sant Cugat','08001','Barcelona',NULL,'eyJpdiI6Ikh6bnp1Y3BITmFpdStWNlRkMUp5NkE9PSIsInZhbHVlIjoibERQc1lIZ3JCcm1aVEFsZEM2bEpkcHc3TWhieDNmWlA3cTRZR0g1Q0Jud0xrbTQ5TitOQUlZKysxN1ZIeVZUbCIsIm1hYyI6IjFkZDM0ZjUyOGQyN2FiMTBlZjQ0NjE1M2Y3ZTExMzI1MmFlYTZiZjQ1MGM0ZDA4ZWQzYWFmZjU3ZjU1YjhhNzQiLCJ0YWciOiIifQ==','eyJpdiI6IjAvVHUySEdzNTZBSUZ5Q3oyUVhlakE9PSIsInZhbHVlIjoiRExGbnplSmc5MTJBR3kwcVh0OVR2UmxVSmY3TWRCcVhRTXUwZnhjTktJTT0iLCJtYWMiOiI1MjE5ZDQyMDgzODYwYTZhNjJiM2QwN2IwNTU5YmIxNWQwYWZlMGMxNjkyM2Y0MDExMTc2NjQ3MDQzMDg5M2I2IiwidGFnIjoiIn0=',NULL,NULL,'own_fee',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'0','0',0,'pending',NULL,NULL,NULL,'Informàtica','Dr.','[\"Programació\", \"Bases de Dades\"]','active','2023-09-01',NULL,'2026-04-09 12:06:49','2026-04-09 12:06:49'),(2,20,'PROF006','Maria','García i López','87654321B','profe@','+34 600 333 444','Carrer del Poble 2, 2-3','08401','Granollers, Barcelona',NULL,'eyJpdiI6IktraklWSE5ucXQ0Q0h0NlZPNTZnNnc9PSIsInZhbHVlIjoibUd4R2FodUdNY0RKd21hRFV0MXQ3bDJJQnRLeWxwTU9xRnNndlRyWTdpRDhqcTlYNldMSy9PeHlvUG4zUEpPWCIsIm1hYyI6IjFkODBkODNmNDk0MDhiZWRiYzcyNzIwMzkzM2FjNzI5Nzk4NGQyZGVhZDE5NjQwNDNmZWJmNjk5MWIyNjMzMTciLCJ0YWciOiIifQ==','eyJpdiI6IkdVdmRGVk5HZDRwQUJCbzhnL2ZDa3c9PSIsInZhbHVlIjoiZEc2ek93R1NXby9VbXVTUUN1L2gwSFlvYmN3UVdXT3Q1bWdoSkJ1Sjg1bz0iLCJtYWMiOiIwZjNkYzM1YjkxNGU4MTgwMTRkNzFmZGFmNTg5ZjA4MTY1YTZlMGQwMTlhMmFhOTc2NTQ4MDc1ZWZmMmI4Mzc4IiwidGFnIjoiIn0=',NULL,NULL,'own_fee',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'0','0',0,'pending',NULL,NULL,NULL,'Matemàtiques','Dra.','[\"Àlgebra\", \"Càlcul\"]','active','2023-09-01',NULL,'2026-04-09 12:06:49','2026-04-09 12:06:49');
/*!40000 ALTER TABLE `campus_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campus_time_slots`
--

DROP TABLE IF EXISTS `campus_time_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campus_time_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `day_of_week` tinyint NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campus_time_slots_day_of_week_code_unique` (`day_of_week`,`code`),
  KEY `campus_time_slots_day_of_week_is_active_index` (`day_of_week`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campus_time_slots`
--

LOCK TABLES `campus_time_slots` WRITE;
/*!40000 ALTER TABLE `campus_time_slots` DISABLE KEYS */;
/*!40000 ALTER TABLE `campus_time_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consent_histories`
--

DROP TABLE IF EXISTS `consent_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consent_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `delegated_by_user_id` bigint unsigned DEFAULT NULL,
  `delegated_reason` text COLLATE utf8mb4_unicode_ci,
  `season` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accepted_at` timestamp NOT NULL,
  `checksum` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `consent_histories_teacher_id_season_unique` (`teacher_id`,`season`),
  CONSTRAINT `consent_histories_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consent_histories`
--

LOCK TABLES `consent_histories` WRITE;
/*!40000 ALTER TABLE `consent_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `consent_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboard_quick_action_permissions`
--

DROP TABLE IF EXISTS `dashboard_quick_action_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard_quick_action_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dashboard_quick_action_permissions_role_name_action_name_unique` (`role_name`,`action_name`),
  KEY `dashboard_quick_action_permissions_role_name_index` (`role_name`),
  KEY `dashboard_quick_action_permissions_action_name_index` (`action_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboard_quick_action_permissions`
--

LOCK TABLES `dashboard_quick_action_permissions` WRITE;
/*!40000 ALTER TABLE `dashboard_quick_action_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `dashboard_quick_action_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboard_widget_permissions`
--

DROP TABLE IF EXISTS `dashboard_widget_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard_widget_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `widget_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `widget_role_unique` (`widget_name`,`role_name`),
  KEY `dashboard_widget_permissions_role_name_index` (`role_name`),
  KEY `dashboard_widget_permissions_widget_name_index` (`widget_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboard_widget_permissions`
--

LOCK TABLES `dashboard_widget_permissions` WRITE;
/*!40000 ALTER TABLE `dashboard_widget_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `dashboard_widget_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_categories`
--

DROP TABLE IF EXISTS `document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_categories_slug_unique` (`slug`),
  KEY `document_categories_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_categories`
--

LOCK TABLES `document_categories` WRITE;
/*!40000 ALTER TABLE `document_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_downloads`
--

DROP TABLE IF EXISTS `document_downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_downloads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `downloaded_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `document_downloads_user_id_foreign` (`user_id`),
  KEY `document_downloads_document_id_user_id_index` (`document_id`,`user_id`),
  KEY `document_downloads_downloaded_at_index` (`downloaded_at`),
  CONSTRAINT `document_downloads_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_downloads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_downloads`
--

LOCK TABLES `document_downloads` WRITE;
/*!40000 ALTER TABLE `document_downloads` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_downloads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_id` bigint unsigned NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `access_roles` json DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `document_date` date DEFAULT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `download_count` int NOT NULL DEFAULT '0',
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_slug_unique` (`slug`),
  KEY `documents_category_id_is_active_index` (`category_id`,`is_active`),
  KEY `documents_uploaded_by_index` (`uploaded_by`),
  KEY `documents_document_date_index` (`document_date`),
  FULLTEXT KEY `documents_title_description_tags_fulltext` (`title`,`description`,`tags`),
  CONSTRAINT `documents_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
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
-- Table structure for table `event_answers`
--

DROP TABLE IF EXISTS `event_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `question_id` bigint unsigned NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_answers_event_id_user_id_question_id_unique` (`event_id`,`user_id`,`question_id`),
  KEY `event_answers_user_id_foreign` (`user_id`),
  KEY `event_answers_question_id_foreign` (`question_id`),
  CONSTRAINT `event_answers_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `event_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_answers`
--

LOCK TABLES `event_answers` WRITE;
/*!40000 ALTER TABLE `event_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_question_templates`
--

DROP TABLE IF EXISTS `event_question_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_question_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single','multiple','text') COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `is_template` tinyint(1) NOT NULL DEFAULT '0',
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_question_templates_is_template_index` (`is_template`),
  KEY `event_question_templates_template_name_index` (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_question_templates`
--

LOCK TABLES `event_question_templates` WRITE;
/*!40000 ALTER TABLE `event_question_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_question_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_questions`
--

DROP TABLE IF EXISTS `event_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single','multiple','text') COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_questions_event_id_foreign` (`event_id`),
  CONSTRAINT `event_questions_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_questions`
--

LOCK TABLES `event_questions` WRITE;
/*!40000 ALTER TABLE `event_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_types`
--

DROP TABLE IF EXISTS `event_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_types`
--

LOCK TABLES `event_types` WRITE;
/*!40000 ALTER TABLE `event_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_users` int DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `start_visible` datetime DEFAULT NULL,
  `end_visible` datetime DEFAULT NULL,
  `event_type_id` bigint unsigned DEFAULT NULL,
  `recurrence_type` enum('none','daily','weekly','monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `recurrence_interval` int DEFAULT NULL,
  `recurrence_end_date` date DEFAULT NULL,
  `recurrence_count` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_event_type_id_foreign` (`event_type_id`),
  KEY `events_parent_id_foreign` (`parent_id`),
  CONSTRAINT `events_event_type_id_foreign` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `events` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `fcm_tokens`
--

DROP TABLE IF EXISTS `fcm_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcm_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fcm_tokens_user_id_token_unique` (`user_id`,`token`),
  CONSTRAINT `fcm_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcm_tokens`
--

LOCK TABLES `fcm_tokens` WRITE;
/*!40000 ALTER TABLE `fcm_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcm_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_user_id_foreign` (`user_id`),
  CONSTRAINT `feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_articles`
--

DROP TABLE IF EXISTS `help_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','validated','obsolete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `order` int NOT NULL DEFAULT '0',
  `help_category_id` bigint unsigned DEFAULT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `help_articles_slug_unique` (`slug`),
  KEY `help_articles_area_index` (`area`),
  KEY `help_articles_status_index` (`status`),
  KEY `help_articles_order_index` (`order`),
  KEY `help_articles_help_category_id_index` (`help_category_id`),
  KEY `help_articles_created_by_index` (`created_by`),
  KEY `help_articles_updated_by_index` (`updated_by`),
  CONSTRAINT `help_articles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `help_articles_help_category_id_foreign` FOREIGN KEY (`help_category_id`) REFERENCES `help_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `help_articles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_articles`
--

LOCK TABLES `help_articles` WRITE;
/*!40000 ALTER TABLE `help_articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `help_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_categories`
--

DROP TABLE IF EXISTS `help_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `help_categories_area_order_index` (`area`,`order`),
  KEY `help_categories_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_categories`
--

LOCK TABLES `help_categories` WRITE;
/*!40000 ALTER TABLE `help_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `help_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_05_05_085752_create_permission_tables',1),(5,'2025_05_09_172228_create_notifications_table',1),(6,'2025_05_17_111002_create_settings_table',1),(7,'2025_05_26_094635_create_fcm_tokens_table',1),(8,'2025_06_14_113344_add_notification_user_tracking_fields',1),(9,'2025_07_10_085306_create_feedback_table',1),(10,'2025_07_28_101808_add_type_to_notifications_table',1),(11,'2025_08_01_132928_create_user_settings_table',1),(12,'2025_08_29_124622_create_event_types_table',1),(13,'2025_08_29_124705_create_events_table',1),(14,'2025_09_02_110642_create_event_questions_table',1),(15,'2025_09_02_110822_create_event_answers_table',1),(16,'2025_09_06_115252_create_event_question_templates_table',1),(17,'2025_09_08_091540_add_parent_id_to_events_table',1),(18,'2025_12_06_0001_create_campus_categories_table',1),(19,'2025_12_06_0002_create_campus_seasons_table',1),(20,'2025_12_06_0003_create_campus_courses_unified_table',1),(21,'2025_12_06_0004_create_campus_students_table',1),(22,'2025_12_06_0005_create_campus_teachers_with_payment_table',1),(23,'2025_12_06_0006_create_campus_course_teacher_table',1),(24,'2025_12_06_0007_create_campus_registrations_table',1),(25,'2026_01_29_203330_create_treasury_data_table',1),(26,'2026_01_30_060752_create_consent_histories_table',1),(27,'2026_02_01_085931_create_teacher_access_tokens_table',1),(28,'2026_02_02_121337_campus_teacher_payments_table',1),(29,'2026_02_20_065124_create_campus_spaces_table',1),(30,'2026_02_20_065130_create_campus_time_slots_table',1),(31,'2026_02_20_065131_add_campus_courses_foreign_keys',1),(32,'2026_02_20_065220_create_campus_course_schedules_table',1),(33,'2026_02_20_065222_create_campus_teacher_schedules_table',1),(34,'2026_02_25_181659_create_support_requests_table',1),(35,'2026_03_01_094910_create_help_categories_table',1),(36,'2026_03_01_165951_create_help_articles_table',1),(37,'2026_03_01_165955_create_help_tags_table',1),(38,'2026_03_01_170001_create_help_article_role_table',1),(39,'2026_03_01_170006_create_help_article_tag_table',1),(40,'2026_03_03_000001_add_hierarchy_to_campus_seasons',1),(41,'2026_03_03_000002_create_campus_course_student_table',1),(42,'2026_03_19_010606_create_dashboard_widget_permissions_table',1),(43,'2026_03_20_create_dashboard_quick_action_permissions_table',1),(44,'2026_03_21_110113_add_ticket_number_to_support_requests_table',1),(45,'2026_03_23_081849_convert_user_settings_to_json',1),(46,'2026_03_23_082823_cleanup_old_user_settings_table',1),(47,'2026_03_25_184018_create_release_notes_table',1),(48,'2026_03_28_202304_create_teacher_notifications_table',1),(49,'2026_03_29_090223_add_deleted_at_to_teacher_notifications_table',1),(50,'2026_03_29_092506_drop_teacher_notifications_tables',1),(51,'2026_03_29_163150_extend_banking_fields_for_encryption',1),(52,'2026_04_08_130001_create_documents_system_tables',1),(53,'2026_04_08_130002_add_support_fields_to_notifications_table',1),(54,'2026_04_09_000001_create_task_boards_table',2),(55,'2026_04_09_000002_create_task_lists_table',2),(56,'2026_04_09_000003_create_tasks_table',2),(57,'2026_04_09_000004_create_task_comments_table',2),(58,'2026_04_09_000005_create_task_attachments_table',2),(59,'2026_04_09_000006_create_task_activities_table',2),(60,'2026_04_09_000007_create_task_checklists_table',2),(61,'2026_04_09_000008_create_task_dependencies_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
INSERT INTO `model_has_permissions` VALUES (17,'App\\Models\\User',34),(18,'App\\Models\\User',34),(19,'App\\Models\\User',34),(21,'App\\Models\\User',34),(52,'App\\Models\\User',34),(53,'App\\Models\\User',34),(54,'App\\Models\\User',34),(55,'App\\Models\\User',34),(62,'App\\Models\\User',34),(63,'App\\Models\\User',34),(69,'App\\Models\\User',34),(70,'App\\Models\\User',34),(71,'App\\Models\\User',34),(72,'App\\Models\\User',34),(74,'App\\Models\\User',34),(75,'App\\Models\\User',34),(76,'App\\Models\\User',34),(83,'App\\Models\\User',34),(84,'App\\Models\\User',34),(85,'App\\Models\\User',34),(86,'App\\Models\\User',34),(89,'App\\Models\\User',34);
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(10,'App\\Models\\User',3),(11,'App\\Models\\User',4),(4,'App\\Models\\User',5),(12,'App\\Models\\User',6),(15,'App\\Models\\User',7),(5,'App\\Models\\User',8),(6,'App\\Models\\User',9),(1,'App\\Models\\User',11),(2,'App\\Models\\User',12),(10,'App\\Models\\User',13),(11,'App\\Models\\User',14),(4,'App\\Models\\User',15),(12,'App\\Models\\User',16),(15,'App\\Models\\User',17),(5,'App\\Models\\User',18),(6,'App\\Models\\User',19),(6,'App\\Models\\User',20),(7,'App\\Models\\User',21),(7,'App\\Models\\User',22),(8,'App\\Models\\User',23),(9,'App\\Models\\User',24),(1,'App\\Models\\User',25),(2,'App\\Models\\User',26),(10,'App\\Models\\User',27),(11,'App\\Models\\User',28),(4,'App\\Models\\User',29),(12,'App\\Models\\User',30),(15,'App\\Models\\User',31),(5,'App\\Models\\User',32),(6,'App\\Models\\User',33),(10,'App\\Models\\User',34);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_user`
--

DROP TABLE IF EXISTS `notification_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `push_sent` tinyint(1) NOT NULL DEFAULT '0',
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `web_sent` tinyint(1) NOT NULL DEFAULT '0',
  `push_sent_at` timestamp NULL DEFAULT NULL,
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `web_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_user_notification_id_user_id_unique` (`notification_id`,`user_id`),
  KEY `notification_user_user_id_foreign` (`user_id`),
  KEY `notification_user_push_sent_push_sent_at_index` (`push_sent`,`push_sent_at`),
  KEY `notification_user_email_sent_email_sent_at_index` (`email_sent`,`email_sent_at`),
  KEY `notification_user_web_sent_web_sent_at_index` (`web_sent`,`web_sent_at`),
  CONSTRAINT `notification_user_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_user`
--

LOCK TABLES `notification_user` WRITE;
/*!40000 ALTER TABLE `notification_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_support_ticket` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `sender_id` bigint unsigned NOT NULL,
  `recipient_type` enum('all','role','specific') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `recipient_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipient_ids` json DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `web_sent` tinyint(1) NOT NULL DEFAULT '0',
  `push_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_sender_id_foreign` (`sender_id`),
  CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
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
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('admin@upg.cat','$2y$12$lLz/P1DDDwisR0.cqX6ynOuQ0tUgvJfxELEdfjafmvPC.EYptAHI2','2026-04-14 08:45:58'),('fempinyapp@gmail.com','$2y$12$K3e.iJ5r1CXLLkbjyLc7beGyE39ujBdt5Ncsy8w2SmJq5OqKknhY.','2026-04-14 11:49:57');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'admin.access','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(2,'settings.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(3,'users.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(4,'users.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(5,'users.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(6,'users.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(7,'users.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(8,'roles.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(9,'roles.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(10,'roles.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(11,'roles.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(12,'permissions.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(13,'permissions.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(14,'permissions.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(15,'permissions.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(16,'notifications.publish','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(17,'notifications.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(18,'notifications.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(19,'notifications.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(20,'notifications.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(21,'notifications.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(22,'events.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(23,'events.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(24,'events.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(25,'events.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(26,'events.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(27,'event_types.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(28,'event_types.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(29,'event_types.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(30,'event_types.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(31,'event_types.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(32,'event_questions.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(33,'event_questions.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(34,'event_questions.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(35,'event_questions.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(36,'event_questions.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(37,'event_answers.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(38,'event_answers.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(39,'event_answers.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(40,'event_answers.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(41,'event_answers.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(42,'event_question_templates.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(43,'event_question_templates.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(44,'event_question_templates.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(45,'event_question_templates.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(46,'event_question_templates.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(47,'campus.categories.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(48,'campus.categories.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(49,'campus.categories.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(50,'campus.categories.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(51,'campus.categories.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(52,'documents.categories.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(53,'documents.categories.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(54,'documents.categories.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(55,'documents.categories.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(56,'documents.categories.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(57,'campus.seasons.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(58,'campus.seasons.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(59,'campus.seasons.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(60,'campus.seasons.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(61,'campus.seasons.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(62,'campus.courses.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(63,'campus.courses.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(64,'campus.courses.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(65,'campus.courses.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(66,'campus.courses.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(67,'campus.courses.enroll','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(68,'campus.courses.manage','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(69,'campus.students.index','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(70,'campus.students.view','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(71,'campus.students.create','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(72,'campus.students.edit','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(73,'campus.students.delete','web','2026-04-09 12:05:43','2026-04-09 12:05:43'),(74,'campus.students.manage','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(75,'campus.teachers.index','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(76,'campus.teachers.view','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(77,'campus.teachers.create','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(78,'campus.teachers.edit','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(79,'campus.teachers.delete','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(80,'campus.teachers.assign','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(81,'campus.consents.request','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(82,'campus.consents.view','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(83,'campus.registrations.index','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(84,'campus.registrations.view','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(85,'campus.registrations.create','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(86,'campus.registrations.edit','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(87,'campus.registrations.delete','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(88,'campus.registrations.approve','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(89,'campus.registrations.manage','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(90,'campus.registrations.import','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(91,'campus.payments.view','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(92,'campus.payments.manage','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(93,'campus.payments.approve','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(94,'campus.payments.export','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(95,'campus.profile.view','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(96,'campus.profile.edit','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(97,'campus.my_courses.view','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(98,'campus.my_courses.manage','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(99,'campus.reports.financial','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(100,'campus.teachers.financial_data.view','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(101,'campus.teachers.financial_data.update','web','2026-04-09 12:05:44','2026-04-09 12:05:44');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `release_notes`
--

DROP TABLE IF EXISTS `release_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `release_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('major','minor','patch') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'minor',
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `features` json DEFAULT NULL,
  `improvements` json DEFAULT NULL,
  `fixes` json DEFAULT NULL,
  `breaking_changes` json DEFAULT NULL,
  `affected_modules` json DEFAULT NULL,
  `target_audience` json DEFAULT NULL,
  `commits` json DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `published_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `release_notes_slug_unique` (`slug`),
  KEY `release_notes_version_index` (`version`),
  KEY `release_notes_status_index` (`status`),
  KEY `release_notes_type_index` (`type`),
  KEY `release_notes_published_at_index` (`published_at`),
  KEY `release_notes_created_by_index` (`created_by`),
  KEY `release_notes_published_by_index` (`published_by`),
  CONSTRAINT `release_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `release_notes_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `release_notes`
--

LOCK TABLES `release_notes` WRITE;
/*!40000 ALTER TABLE `release_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `release_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1),(78,1),(79,1),(80,1),(81,1),(82,1),(83,1),(84,1),(85,1),(86,1),(87,1),(88,1),(89,1),(90,1),(91,1),(92,1),(93,1),(94,1),(95,1),(96,1),(97,1),(98,1),(99,1),(100,1),(101,1),(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2),(13,2),(14,2),(15,2),(16,2),(17,2),(18,2),(19,2),(20,2),(21,2),(22,2),(23,2),(24,2),(25,2),(26,2),(27,2),(28,2),(29,2),(30,2),(31,2),(32,2),(33,2),(34,2),(35,2),(36,2),(37,2),(38,2),(39,2),(40,2),(41,2),(42,2),(43,2),(44,2),(45,2),(46,2),(47,2),(48,2),(49,2),(50,2),(51,2),(52,2),(53,2),(54,2),(55,2),(56,2),(57,2),(58,2),(59,2),(60,2),(61,2),(62,2),(63,2),(64,2),(65,2),(66,2),(67,2),(68,2),(69,2),(70,2),(71,2),(72,2),(73,2),(74,2),(75,2),(76,2),(77,2),(78,2),(79,2),(80,2),(81,2),(82,2),(83,2),(84,2),(85,2),(86,2),(87,2),(88,2),(89,2),(90,2),(91,2),(92,2),(93,2),(94,2),(95,2),(96,2),(97,2),(98,2),(99,2),(100,2),(101,2),(3,3),(5,3),(6,3),(7,3),(17,3),(18,3),(19,3),(20,3),(21,3),(22,3),(23,3),(24,3),(25,3),(26,3),(32,3),(33,3),(34,3),(35,3),(36,3),(42,3),(43,3),(44,3),(45,3),(46,3),(47,3),(48,3),(57,3),(58,3),(62,3),(63,3),(64,3),(65,3),(66,3),(68,3),(69,3),(70,3),(71,3),(72,3),(73,3),(74,3),(75,3),(76,3),(77,3),(78,3),(79,3),(80,3),(83,3),(84,3),(85,3),(86,3),(87,3),(89,3),(90,3),(91,3),(92,3),(17,4),(18,4),(19,4),(20,4),(21,4),(22,4),(23,4),(24,4),(25,4),(26,4),(62,4),(63,4),(75,4),(76,4),(77,4),(78,4),(79,4),(80,4),(81,4),(82,4),(83,4),(84,4),(85,4),(86,4),(89,4),(91,4),(92,4),(93,4),(94,4),(100,4),(101,4),(17,5),(18,5),(19,5),(20,5),(21,5),(22,5),(23,5),(24,5),(25,5),(26,5),(18,6),(21,6),(70,6),(84,6),(95,6),(96,6),(97,6),(98,6),(18,7),(21,7),(95,7),(96,7),(97,7),(18,8),(21,8),(95,8),(96,8),(3,10),(4,10),(5,10),(6,10),(17,10),(18,10),(19,10),(21,10),(47,10),(48,10),(57,10),(58,10),(62,10),(63,10),(64,10),(65,10),(68,10),(69,10),(70,10),(71,10),(72,10),(74,10),(75,10),(76,10),(77,10),(78,10),(80,10),(83,10),(84,10),(85,10),(86,10),(89,10),(17,11),(18,11),(19,11),(21,11),(52,11),(53,11),(54,11),(55,11),(62,11),(63,11),(69,11),(70,11),(71,11),(72,11),(74,11),(75,11),(76,11),(83,11),(84,11),(85,11),(86,11),(89,11),(17,12),(18,12),(19,12),(21,12),(47,12),(48,12),(49,12),(50,12),(57,12),(58,12),(59,12),(60,12),(62,12),(63,12),(64,12),(65,12),(68,12),(69,12),(70,12),(72,12),(75,12),(76,12),(78,12),(83,12),(84,12),(89,12),(3,13),(4,13),(5,13),(6,13),(7,13),(17,13),(18,13),(19,13),(20,13),(21,13),(22,13),(23,13),(24,13),(25,13),(26,13),(47,13),(48,13),(49,13),(50,13),(51,13),(57,13),(58,13),(59,13),(60,13),(61,13),(62,13),(63,13),(64,13),(65,13),(66,13),(68,13),(69,13),(70,13),(71,13),(72,13),(73,13),(74,13),(75,13),(76,13),(77,13),(78,13),(79,13),(80,13),(83,13),(84,13),(85,13),(86,13),(87,13),(89,13),(90,13),(91,13),(92,13),(93,13),(3,14),(4,14),(5,14),(6,14),(17,14),(18,14),(19,14),(20,14),(21,14),(22,14),(23,14),(24,14),(25,14),(26,14),(47,14),(48,14),(49,14),(50,14),(57,14),(58,14),(59,14),(60,14),(62,14),(63,14),(64,14),(65,14),(68,14),(69,14),(70,14),(71,14),(72,14),(74,14),(75,14),(76,14),(77,14),(78,14),(80,14),(83,14),(84,14),(85,14),(86,14),(89,14),(91,14),(92,14),(17,15),(18,15),(19,15),(20,15),(21,15),(22,15),(23,15),(24,15),(25,15),(26,15),(32,15),(33,15),(34,15),(35,15),(36,15),(62,15),(63,15),(69,15),(70,15),(75,15),(76,15);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'super-admin','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(2,'admin','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(3,'gestor','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(4,'treasury','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(5,'editor','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(6,'teacher','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(7,'student','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(8,'user','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(9,'invited','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(10,'coordinacio','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(11,'secretaria','web','2026-04-09 12:05:44','2026-04-09 12:05:44'),(12,'gestio','web','2026-04-09 12:05:45','2026-04-09 12:05:45'),(13,'director','web','2026-04-09 12:05:45','2026-04-09 12:05:45'),(14,'manager','web','2026-04-09 12:05:45','2026-04-09 12:05:45'),(15,'comunicacio','web','2026-04-09 12:05:45','2026-04-09 12:05:45');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('dwXWGGEaXLscSKW7BzAYnvyjwZSYdXbNmUlZdss9',12,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiVnVUc0poU2dqNlNyemFUeElpRW52VnVwVk8wUTZnTlpuUDhpUmJldCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNDoiaHR0cDovL2NhbXB1cy1vcmcudGVzdC9hZG1pbi91c2VycyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjUzOiJodHRwOi8vY2FtcHVzLW9yZy50ZXN0L2FwaS9ub3RpZmljYXRpb25zL3VucmVhZC1jb3VudCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NjoibG9jYWxlIjtzOjI6ImNhIjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMjt9',1776175024),('I6BHyL7oDg3KpVaHhF0DxCgoJLs1aarr1hvD6Wj0',13,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoic0FHSm1UR3BSV0Z5R1ZSOHF6ZGx3bGhraU5rNzlwYUtkWWp6OExIVCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo2OiJsb2NhbGUiO3M6MjoiY2EiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjUzOiJodHRwOi8vY2FtcHVzLW9yZy50ZXN0L2FwaS9ub3RpZmljYXRpb25zL3VucmVhZC1jb3VudCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzO30=',1776175006),('Wyi8rebiw8ZChCvTy7kKgRH7Tab8PC1f0S1kCXJv',34,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMjliR3RGTVZIVGxnR2ZDSUV4MktBQUVFRVRKdVRuN0ZJTXVkeGYwbCI7czo2OiJsb2NhbGUiO3M6MjoiY2EiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM1OiJodHRwOi8vY2FtcHVzLW9yZy50ZXN0L3ZlcmlmeS1lbWFpbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM0O3M6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NTM6Imh0dHA6Ly9jYW1wdXMtb3JnLnRlc3QvYXBpL25vdGlmaWNhdGlvbnMvdW5yZWFkLWNvdW50Ijt9fQ==',1776175014);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_requests`
--

DROP TABLE IF EXISTS `support_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('service','incident','improvement','consultation') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urgency` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `user_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint unsigned DEFAULT NULL,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_requests_ticket_number_unique` (`ticket_number`),
  KEY `support_requests_resolved_by_foreign` (`resolved_by`),
  KEY `support_requests_status_urgency_index` (`status`,`urgency`),
  KEY `support_requests_user_id_index` (`user_id`),
  KEY `support_requests_email_index` (`email`),
  KEY `support_requests_type_index` (`type`),
  KEY `support_requests_created_at_index` (`created_at`),
  CONSTRAINT `support_requests_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_requests`
--

LOCK TABLES `support_requests` WRITE;
/*!40000 ALTER TABLE `support_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_activities`
--

DROP TABLE IF EXISTS `task_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'created, updated, assigned, completed, etc.',
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_activities_task_id_index` (`task_id`),
  KEY `task_activities_user_id_index` (`user_id`),
  KEY `task_activities_action_index` (`action`),
  KEY `task_activities_task_id_created_at_index` (`task_id`,`created_at`),
  CONSTRAINT `task_activities_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_activities`
--

LOCK TABLES `task_activities` WRITE;
/*!40000 ALTER TABLE `task_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_attachments`
--

DROP TABLE IF EXISTS `task_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL COMMENT 'Mida en bytes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_attachments_task_id_index` (`task_id`),
  KEY `task_attachments_user_id_index` (`user_id`),
  CONSTRAINT `task_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_attachments`
--

LOCK TABLES `task_attachments` WRITE;
/*!40000 ALTER TABLE `task_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_boards`
--

DROP TABLE IF EXISTS `task_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_boards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('course','team','global','department') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'team',
  `entity_id` bigint unsigned DEFAULT NULL COMMENT 'FK a cursos o departaments',
  `created_by` bigint unsigned NOT NULL,
  `visibility` enum('public','team','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'team',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_boards_type_entity_id_index` (`type`,`entity_id`),
  KEY `task_boards_created_by_index` (`created_by`),
  KEY `task_boards_visibility_index` (`visibility`),
  CONSTRAINT `task_boards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_boards`
--

LOCK TABLES `task_boards` WRITE;
/*!40000 ALTER TABLE `task_boards` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_boards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_checklists`
--

DROP TABLE IF EXISTS `task_checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_checklists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_checklists_task_id_index` (`task_id`),
  KEY `task_checklists_task_id_order_index` (`task_id`,`order`),
  CONSTRAINT `task_checklists_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_checklists`
--

LOCK TABLES `task_checklists` WRITE;
/*!40000 ALTER TABLE `task_checklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_checklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_comments`
--

DROP TABLE IF EXISTS `task_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_comments_task_id_index` (`task_id`),
  KEY `task_comments_user_id_index` (`user_id`),
  KEY `task_comments_task_id_created_at_index` (`task_id`,`created_at`),
  CONSTRAINT `task_comments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_comments`
--

LOCK TABLES `task_comments` WRITE;
/*!40000 ALTER TABLE `task_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_dependencies`
--

DROP TABLE IF EXISTS `task_dependencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_dependencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `depends_on_task_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_dependencies_task_id_depends_on_task_id_unique` (`task_id`,`depends_on_task_id`),
  KEY `task_dependencies_task_id_index` (`task_id`),
  KEY `task_dependencies_depends_on_task_id_index` (`depends_on_task_id`),
  CONSTRAINT `task_dependencies_depends_on_task_id_foreign` FOREIGN KEY (`depends_on_task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_dependencies_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_dependencies`
--

LOCK TABLES `task_dependencies` WRITE;
/*!40000 ALTER TABLE `task_dependencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_dependencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_lists`
--

DROP TABLE IF EXISTS `task_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `board_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6B7280' COMMENT 'Color hex per la columna',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Per auto-creació en taules noves',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_lists_board_id_index` (`board_id`),
  KEY `task_lists_board_id_order_index` (`board_id`,`order`),
  CONSTRAINT `task_lists_board_id_foreign` FOREIGN KEY (`board_id`) REFERENCES `task_boards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_lists`
--

LOCK TABLES `task_lists` WRITE;
/*!40000 ALTER TABLE `task_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `list_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `assigned_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Per assignació basada en rols',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','blocked','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_in_list` int NOT NULL DEFAULT '0',
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_list_id_index` (`list_id`),
  KEY `tasks_assigned_to_index` (`assigned_to`),
  KEY `tasks_assigned_role_index` (`assigned_role`),
  KEY `tasks_priority_index` (`priority`),
  KEY `tasks_status_index` (`status`),
  KEY `tasks_due_date_index` (`due_date`),
  KEY `tasks_list_id_order_in_list_index` (`list_id`,`order_in_list`),
  KEY `tasks_assigned_to_status_index` (`assigned_to`,`status`),
  KEY `tasks_created_by_foreign` (`created_by`),
  KEY `tasks_updated_by_foreign` (`updated_by`),
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_list_id_foreign` FOREIGN KEY (`list_id`) REFERENCES `task_lists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
-- Table structure for table `teacher_access_tokens`
--

DROP TABLE IF EXISTS `teacher_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `season_id` bigint unsigned DEFAULT NULL,
  `course_id` bigint unsigned DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_access_tokens_token_unique` (`token`),
  KEY `teacher_access_tokens_teacher_id_foreign` (`teacher_id`),
  KEY `teacher_access_tokens_season_id_foreign` (`season_id`),
  KEY `teacher_access_tokens_course_id_foreign` (`course_id`),
  CONSTRAINT `teacher_access_tokens_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `campus_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_access_tokens_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `campus_seasons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_access_tokens_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_access_tokens`
--

LOCK TABLES `teacher_access_tokens` WRITE;
/*!40000 ALTER TABLE `teacher_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treasury_data`
--

DROP TABLE IF EXISTS `treasury_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `treasury_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `treasury_data_teacher_id_key_unique` (`teacher_id`,`key`),
  CONSTRAINT `treasury_data_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treasury_data`
--

LOCK TABLES `treasury_data` WRITE;
/*!40000 ALTER TABLE `treasury_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `treasury_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `preferences` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_preferences_user_id_unique` (`user_id`),
  CONSTRAINT `user_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
INSERT INTO `user_preferences` VALUES (1,12,'{\"ui\": {\"language\": \"ca\"}, \"notifications\": {\"admin_web\": true, \"frequency\": \"immediate\", \"admin_email\": true, \"support_web\": true, \"web_enabled\": true, \"email_enabled\": true, \"support_email\": true, \"department_web\": true, \"department_email\": true}}','2026-04-13 16:58:13','2026-04-13 16:58:13'),(2,13,'{\"ui\": {\"language\": \"ca\"}, \"notifications\": {\"admin_web\": true, \"frequency\": \"immediate\", \"admin_email\": true, \"support_web\": true, \"web_enabled\": true, \"email_enabled\": true, \"support_email\": true, \"department_web\": true, \"department_email\": true}}','2026-04-14 09:03:50','2026-04-14 09:03:50'),(3,34,'{\"ui\": {\"language\": \"ca\"}, \"notifications\": {\"admin_web\": true, \"frequency\": \"immediate\", \"admin_email\": true, \"support_web\": true, \"web_enabled\": true, \"email_enabled\": true, \"support_email\": true, \"department_web\": true, \"department_email\": true}}','2026-04-14 11:51:49','2026-04-14 11:51:49');
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','suspended','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ca',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (11,'Superadmin','campus@upg.cat','2026-04-09 12:06:46','$2y$12$59dz0e7wwRQI2qjnRPB.HeUWcblEdIrULjwoBViNXBM2yxkIr1X6S','active',NULL,'ca','2026-04-09 12:06:46','2026-04-14 08:39:58'),(12,'Administrador Centre','admin@upg.cat','2026-04-09 12:06:46','$2y$12$fvrhP76e/kjgUiB2ggNgG.CFVr8UVSjb5cRAPvG0772HOezVlRNLq','active',NULL,'ca','2026-04-09 12:06:46','2026-04-14 08:39:58'),(13,'Coordinacio','coordinacio@upg.cat','2026-04-09 12:06:46','$2y$12$S0V.T/J.xMbdI5BbOZKVWOjKIXbomXJWQLD9difA0Faz6D2JNtAnO','active',NULL,'ca','2026-04-09 12:06:46','2026-04-14 08:39:59'),(14,'Secretaria','secretaria@.upg.cat','2026-04-09 12:06:47','$2y$12$GwezBnFK4ZJQ3lNVFayG6u8/nmNm8WvjBTEnRCcVqqyVXNCKXEFDi','active',NULL,'ca','2026-04-09 12:06:47','2026-04-14 08:39:59'),(15,'Tresoreria','tresoreria@upg.cat','2026-04-09 12:06:47','$2y$12$nFFVJRbPRdi8RuHB1Z2WceiBBafY/n0j0cNjbfJBYBGffKGMTz/5.','active',NULL,'ca','2026-04-09 12:06:47','2026-04-14 08:39:59'),(16,'Equip Tècnic','gestio@upg.cat','2026-04-09 12:06:48','$2y$12$Kzg5LaqXjYQKx2tfjdVCPuQcSOPgS0qi4e.mi3xn9Ik18PRhRLo1y','active',NULL,'ca','2026-04-09 12:06:48','2026-04-14 08:40:00'),(17,'Comunicació i Edició','comunicacio@upg.cat','2026-04-09 12:06:48','$2y$12$TkWLbyoupoJC8aK46qQjY.y/TUwd45VRUckKw068TMuanzxTNVIqG','active',NULL,'ca','2026-04-09 12:06:48','2026-04-14 08:40:00'),(18,'Elisabet Edició','editora@upg.cat',NULL,'$2y$12$IHbZIWil0zyY6iuBovs3a.Dq.TpTrY3dETsIRGgXFFZC35bCMPBma','active',NULL,'ca','2026-04-09 12:06:48','2026-04-14 08:40:01'),(19,'Joan Prat i Soler','teacher@upg.cat','2026-04-09 12:06:49','$2y$12$6oK17wnuWaygJeDSpos9TOrY7HY4OS/D309KDDXUIH8Hcrn59cNe2','active',NULL,'ca','2026-04-09 12:06:49','2026-04-14 08:40:01'),(20,'Maria García i López','teacher2@upg.cat',NULL,'$2y$12$Ff7W9BeuUr5T7yQMMp1IJO2JIeC4.eEYbfe3qXeAwBiLbg2sKiZnC','active',NULL,'ca','2026-04-09 12:06:49','2026-04-14 08:40:02'),(21,'Anna Martínez i Roca','alumne@upg.cat','2026-04-09 12:06:49','$2y$12$L5EPbrN7yCt.6e226qFm1OG1P8uDeqZ8N9ucBq.aJ.KBUS4MxoU3a','active',NULL,'ca','2026-04-09 12:06:49','2026-04-14 08:40:02'),(22,'Carles Ruiz i Navarro','student@upg.cat',NULL,'$2y$12$SxXZnxSkAQDkSmKA73jAluWbMhH/MeKLJxqcgFkIwWInDyRt4z81C','active',NULL,'ca','2026-04-09 12:06:50','2026-04-14 08:40:02'),(23,'Usuari Bàsic','usuari@upg.cat','2026-04-09 12:06:50','$2y$12$LEdoTK.WrbxZT1YkMogYJeAfdn8RLsk/PQgIXM5VYNwWfvtQsbUDK','active',NULL,'ca','2026-04-09 12:06:50','2026-04-14 08:40:03'),(24,'Convidat Extern','convidat@upg.cat','2026-04-09 12:06:51','$2y$12$05WRsvEgxGy.z75rfao9f.rzc4y9rp.1ajz4JyFomliXvCoJ4NYXa','active',NULL,'ca','2026-04-09 12:06:51','2026-04-14 08:40:03'),(34,'Fem Pinya','fempinyapp@gmail.com',NULL,'$2y$12$FV4ESyTdDQvsnWJdlMbePuJavGDDaZe5FzaLoj9OKXHk7.hwGBLQS','active',NULL,'ca','2026-04-14 11:47:55','2026-04-14 11:47:55');
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

-- Dump completed on 2026-04-14 15:57:44
