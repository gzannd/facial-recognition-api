-- MySQL dump 10.13  Distrib 8.0.27, for Linux (x86_64)
--
-- Host: mysql    Database: facial_recognition_api
-- ------------------------------------------------------
-- Server version	8.0.27

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
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuration` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `secondary_users` json DEFAULT NULL,
  `system_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `primary_user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_people` (`primary_user_id`),
  CONSTRAINT `fk_people` FOREIGN KEY (`primary_user_id`) REFERENCES `people` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuration`
--

LOCK TABLES `configuration` WRITE;
/*!40000 ALTER TABLE `configuration` DISABLE KEYS */;
INSERT INTO `configuration` VALUES (1,'2022-02-19 12:52:54','2022-02-19 12:52:54',NULL,'Test System Name','This is a test configuration.',NULL);
/*!40000 ALTER TABLE `configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_types`
--

DROP TABLE IF EXISTS `device_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_types`
--

LOCK TABLES `device_types` WRITE;
/*!40000 ALTER TABLE `device_types` DISABLE KEYS */;
INSERT INTO `device_types` VALUES (1,'Camera'),(2,'IR Sensor'),(3,'Weight Sensor'),(4,'Lock');
/*!40000 ALTER TABLE `device_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `devices_name_unique` (`name`),
  UNIQUE KEY `devices_systemid_unique` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` VALUES (1,'Test Device 100','9999','changed',1,'2022-03-04 17:26:34','2022-03-04 17:34:02'),(2,'Test Device 2','9999999','This is another test device',1,'2022-03-04 17:27:00','2022-03-04 17:27:00');
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_data`
--

DROP TABLE IF EXISTS `event_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_data` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type` int unsigned NOT NULL,
  `device_date` datetime DEFAULT NULL,
  `data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_id` (`event_type`),
  KEY `event_data_device_date_index` (`device_date`),
  CONSTRAINT `event_data_event_type_foreign` FOREIGN KEY (`event_type`) REFERENCES `event_data_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_data`
--

LOCK TABLES `event_data` WRITE;
/*!40000 ALTER TABLE `event_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_data_type`
--

DROP TABLE IF EXISTS `event_data_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_data_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_data_type_device_id_foreign` (`device_id`),
  CONSTRAINT `event_data_type_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_data_type`
--

LOCK TABLES `event_data_type` WRITE;
/*!40000 ALTER TABLE `event_data_type` DISABLE KEYS */;
INSERT INTO `event_data_type` VALUES (1,'SECURITY_CAMERA_IMAGE',1),(2,'WEIGHT_SENSOR_READING',1),(3,'IR_DETECTION',1),(4,'RANGEFINDER_DETECTION',1);
/*!40000 ALTER TABLE `event_data_type` ENABLE KEYS */;
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
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `person_id` bigint unsigned DEFAULT NULL,
  `top` int NOT NULL DEFAULT '0',
  `left` int NOT NULL DEFAULT '0',
  `width` int NOT NULL DEFAULT '0',
  `height` int NOT NULL DEFAULT '0',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created_by_device` datetime NOT NULL,
  `data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` VALUES (32,'2022-03-04 17:02:56','2022-03-04 17:02:56','images\\6222463fee5fb.jpeg',NULL,NULL,0,0,0,0,'test image','image/jpeg','0','2022-02-28 00:00:00',''),(33,'2022-03-04 19:45:34','2022-03-04 19:45:34','images\\62226c5d5cd45.jpeg',32,1,0,0,0,0,'test image','image/jpeg','0','2022-02-28 00:00:00',''),(34,'2022-03-27 19:35:04','2022-03-27 19:35:04','6240bc681826d.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(35,'2022-03-27 19:38:00','2022-03-27 19:38:00','6240bd17877af.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(36,'2022-03-27 19:40:48','2022-03-27 19:40:48','6240bdc01e555.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(37,'2022-03-27 20:18:55','2022-03-27 20:18:55','6240c6af2901c.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(38,'2022-03-27 20:39:20','2022-03-27 20:39:20','6240cb7796e73.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(39,'2022-04-01 17:32:12','2022-04-01 17:32:12','6247371a9d91e.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(40,'2022-04-09 21:37:27','2022-04-09 21:37:27','6251fc96a1850.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(41,'2022-04-13 02:54:18','2022-04-13 02:54:18','62563b5986bdd.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(42,'2022-04-13 02:54:44','2022-04-13 02:54:44','62563b745f3bd.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(43,'2022-04-13 02:55:19','2022-04-13 02:55:19','62563b9715b9d.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(44,'2022-04-13 02:56:00','2022-04-13 02:56:00','62563bc0afe97.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(45,'2022-04-13 02:56:30','2022-04-13 02:56:30','62563bddf0478.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(46,'2022-04-13 02:57:57','2022-04-13 02:57:57','62563c348d0fc.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(47,'2022-04-13 02:58:50','2022-04-13 02:58:50','62563c6a81817.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(48,'2022-04-13 02:59:44','2022-04-13 02:59:44','62563c9fe4948.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(49,'2022-04-13 22:57:29','2022-04-13 22:57:29','62575558d7f1a.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(50,'2022-04-13 23:00:30','2022-04-13 23:00:30','6257560e42c7b.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(51,'2022-04-13 23:02:49','2022-04-13 23:02:49','6257569944f00.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(52,'2022-04-13 23:03:22','2022-04-13 23:03:22','625756ba2ef51.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(53,'2022-04-13 23:04:12','2022-04-13 23:04:12','625756ebe7b73.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(54,'2022-04-13 23:05:04','2022-04-13 23:05:04','6257571fcd846.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(55,'2022-04-13 23:05:26','2022-04-13 23:05:26','6257573638f10.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(56,'2022-04-13 23:05:52','2022-04-13 23:05:52','6257574fc7c2f.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(57,'2022-04-13 23:06:50','2022-04-13 23:06:50','6257578a5478a.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(58,'2022-04-13 23:09:18','2022-04-13 23:09:18','6257581e492bb.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(59,'2022-04-13 23:20:18','2022-04-13 23:20:18','62575ab1ed0fd.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(60,'2022-04-13 23:20:42','2022-04-13 23:20:42','62575aca6ac27.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(61,'2022-04-13 23:21:57','2022-04-13 23:21:57','62575b151f5cb.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(62,'2022-04-13 23:22:47','2022-04-13 23:22:47','62575b46ca145.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(63,'2022-04-13 23:23:51','2022-04-13 23:23:51','62575b875a73e.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(64,'2022-04-13 23:24:20','2022-04-13 23:24:20','62575ba3d4ccc.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(65,'2022-04-13 23:24:47','2022-04-13 23:24:47','62575bbfa9d39.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(66,'2022-04-13 23:26:00','2022-04-13 23:26:00','62575c0794794.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(67,'2022-04-14 02:15:47','2022-04-14 02:15:47','625783d2f1526.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(68,'2022-04-14 02:19:34','2022-04-14 02:19:34','625784b604f57.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(69,'2022-04-14 02:45:09','2022-04-14 02:45:09','62578ab503e82.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(70,'2022-04-14 02:46:57','2022-04-14 02:46:57','62578b20d6599.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(71,'2022-04-14 02:47:50','2022-04-14 02:47:50','62578b566a49b.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(72,'2022-04-14 02:50:09','2022-04-14 02:50:09','62578be115331.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(73,'2022-04-14 02:52:16','2022-04-14 02:52:16','62578c601b71b.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(74,'2022-04-14 02:52:49','2022-04-14 02:52:49','62578c817f2c2.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(75,'2022-04-14 02:53:34','2022-04-14 02:53:34','62578cae0d62f.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(76,'2022-04-14 02:58:18','2022-04-14 02:58:18','62578dc9eb012.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(77,'2022-04-14 02:59:59','2022-04-14 02:59:59','62578e2f2f00f.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(78,'2022-04-14 03:00:49','2022-04-14 03:00:49','62578e616f17c.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(79,'2022-04-14 03:03:01','2022-04-14 03:03:01','62578ee4a8fa8.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(80,'2022-04-14 03:05:08','2022-04-14 03:05:08','62578f638c237.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(81,'2022-04-14 03:05:53','2022-04-14 03:05:53','62578f910c389.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(82,'2022-04-14 03:06:26','2022-04-14 03:06:26','62578fb2527b3.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(83,'2022-04-14 03:09:07','2022-04-14 03:09:07','62579052e3475.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(84,'2022-04-14 03:11:16','2022-04-14 03:11:16','625790d3cb0ad.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(85,'2022-04-14 03:11:54','2022-04-14 03:11:54','625790fa74648.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(86,'2022-04-14 03:12:28','2022-04-14 03:12:28','6257911c6e49e.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(87,'2022-04-14 03:13:31','2022-04-14 03:13:31','6257915ac55ba.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(88,'2022-04-14 03:14:15','2022-04-14 03:14:15','62579187aa053.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(89,'2022-04-14 03:14:48','2022-04-14 03:14:48','625791a801109.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(90,'2022-04-14 03:15:41','2022-04-14 03:15:41','625791dd7ea06.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(91,'2022-04-14 03:16:21','2022-04-14 03:16:21','62579204a6c5b.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(92,'2022-04-14 03:30:07','2022-04-14 03:30:07','6257953f28ff9.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(93,'2022-04-14 03:30:54','2022-04-14 03:30:54','6257956e0ecec.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(94,'2022-04-14 03:31:36','2022-04-14 03:31:36','625795985f1d2.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(95,'2022-04-14 03:32:34','2022-04-14 03:32:34','625795d2905df.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(96,'2022-04-14 03:33:33','2022-04-14 03:33:33','6257960d57f66.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(97,'2022-04-14 03:33:50','2022-04-14 03:33:50','6257961e8860e.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(98,'2022-04-14 03:35:21','2022-04-14 03:35:21','625796790551a.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(99,'2022-04-14 03:36:02','2022-04-14 03:36:02','625796a24a2c8.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(100,'2022-04-14 03:36:20','2022-04-14 03:36:20','625796b3d63da.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(101,'2022-04-14 03:39:11','2022-04-14 03:39:11','6257975f4eab6.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(102,'2022-04-14 03:40:54','2022-04-14 03:40:54','625797c65ce7b.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(103,'2022-04-14 03:41:51','2022-04-14 03:41:51','625797ff29544.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(104,'2022-04-14 03:46:00','2022-04-14 03:46:00','625798f804d7f.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(105,'2022-04-14 03:46:27','2022-04-14 03:46:27','625799134dfb8.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(106,'2022-04-20 00:00:46','2022-04-20 00:00:46','625f4d2ced62c.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(107,'2022-04-20 00:01:18','2022-04-20 00:01:18','625f4d4d75ac4.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','0','2022-02-28 00:00:00',NULL),(108,'2022-04-20 00:10:05','2022-04-20 00:10:05','625f4f5cb4940.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(109,'2022-04-23 01:47:40','2022-04-23 01:47:40','62635abc5bdbf.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(110,'2022-04-23 01:57:39','2022-04-23 01:57:39','62635d1361b78.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(111,'2022-04-23 12:47:40','2022-04-23 12:47:40','6263f56b2a5c8.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(112,'2022-04-23 12:50:01','2022-04-23 12:50:01','6263f5f924f2f.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(113,'2022-04-23 12:51:43','2022-04-23 12:51:43','6263f65ea5baf.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(114,'2022-04-23 12:52:25','2022-04-23 12:52:25','6263f688b1f9f.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(115,'2022-04-23 13:04:21','2022-04-23 13:04:21','6263f95489548.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(116,'2022-04-23 13:07:10','2022-04-23 13:07:10','6263f9fe447e3.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(117,'2022-04-23 13:16:37','2022-04-23 13:16:37','6263fc352f822.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(118,'2022-04-23 13:30:23','2022-04-23 13:30:23','6263ff6e6a8dc.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(119,'2022-04-23 16:10:36','2022-04-23 16:10:36','626424fc1fba9.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(120,'2022-04-23 16:12:13','2022-04-23 16:12:13','6264255ce7b83.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(121,'2022-04-23 16:13:16','2022-04-23 16:13:16','6264259c48600.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(122,'2022-04-23 16:15:45','2022-04-23 16:15:45','626426315efd7.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(123,'2022-04-23 16:16:56','2022-04-23 16:16:56','62642677e3aa2.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(124,'2022-04-23 16:18:52','2022-04-23 16:18:52','626426ec88a23.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(125,'2022-04-23 16:19:52','2022-04-23 16:19:52','62642727dc82d.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(126,'2022-04-23 16:32:57','2022-04-23 16:32:57','62642a391d1d8.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(127,'2022-04-23 16:38:34','2022-04-23 16:38:34','62642b8ab7e01.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(128,'2022-04-23 17:06:39','2022-04-23 17:06:39','6264321f99fdd.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(129,'2022-04-23 17:07:28','2022-04-23 17:07:28','62643250679c7.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(130,'2022-04-23 17:09:04','2022-04-23 17:09:04','626432afe6cfd.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(131,'2022-04-23 17:09:28','2022-04-23 17:09:28','626432c8056ac.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(132,'2022-04-23 17:09:46','2022-04-23 17:09:46','626432da17298.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(133,'2022-04-24 14:36:56','2022-04-24 14:36:56','62656087b275d.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(134,'2022-04-24 14:38:54','2022-04-24 14:38:54','626560fe64953.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(135,'2022-04-24 15:00:39','2022-04-24 15:00:39','6265661774032.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(136,'2022-04-24 15:02:26','2022-04-24 15:02:26','626566827fd20.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(137,'2022-04-24 15:06:07','2022-04-24 15:06:07','6265675eecc69.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(138,'2022-04-24 15:07:38','2022-04-24 15:07:38','626567b9d0aeb.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(139,'2022-04-24 15:09:15','2022-04-24 15:09:15','6265681ad8a72.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(140,'2022-04-24 15:09:43','2022-04-24 15:09:43','62656837775c2.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(141,'2022-04-24 15:11:55','2022-04-24 15:11:55','626568bb4d6c8.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(142,'2022-04-24 15:12:36','2022-04-24 15:12:36','626568e3e5f43.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(143,'2022-04-24 15:16:57','2022-04-24 15:16:57','626569e9965a2.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(144,'2022-04-24 15:19:27','2022-04-24 15:19:27','62656a7ee910a.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(145,'2022-04-24 15:20:14','2022-04-24 15:20:14','62656aaeac292.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(146,'2022-04-24 15:21:34','2022-04-24 15:21:34','62656afebf2a3.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(147,'2022-04-24 15:22:29','2022-04-24 15:22:29','62656b3517406.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(148,'2022-04-24 15:22:59','2022-04-24 15:22:59','62656b52d5707.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(149,'2022-04-24 15:28:43','2022-04-24 15:28:43','62656cab59760.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(150,'2022-04-24 15:31:01','2022-04-24 15:31:01','62656d3543a4c.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(151,'2022-04-24 15:31:32','2022-04-24 15:31:32','62656d5456725.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(152,'2022-04-24 15:49:42','2022-04-24 15:49:42','626571960ca3b.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(153,'2022-04-24 15:50:21','2022-04-24 15:50:21','626571bd815e4.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(154,'2022-04-24 15:54:07','2022-04-24 15:54:07','6265729f9e3c8.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(155,'2022-04-24 15:54:36','2022-04-24 15:54:36','626572bc247f2.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(156,'2022-04-24 16:14:27','2022-04-24 16:14:27','62657762cfd2a.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(157,'2022-04-24 16:15:13','2022-04-24 16:15:13','62657791abbdb.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(158,'2022-04-24 16:15:38','2022-04-24 16:15:38','626577aa6c23f.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(159,'2022-04-24 16:16:49','2022-04-24 16:16:49','626577f1bb03b.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(160,'2022-04-24 16:31:22','2022-04-24 16:31:22','62657b5a267f4.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(161,'2022-04-24 16:31:41','2022-04-24 16:31:41','62657b6da5818.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(162,'2022-04-24 16:43:25','2022-04-24 16:43:25','62657e2dc56df.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(163,'2022-04-24 16:55:42','2022-04-24 16:55:42','6265810ecb449.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(164,'2022-04-24 16:56:23','2022-04-24 16:56:23','626581376e5a6.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(165,'2022-04-24 16:57:36','2022-04-24 16:57:36','6265818084fa4.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(166,'2022-04-24 16:57:53','2022-04-24 16:57:53','626581912e332.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(167,'2022-04-24 16:58:14','2022-04-24 16:58:14','626581a6149ec.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(168,'2022-04-24 16:58:15','2022-04-24 16:58:15','626581a6149ec_1',167,NULL,0,0,0,0,'Cropped from primary image.','image/jpeg','1','2022-04-24 16:58:15',NULL),(169,'2022-04-24 16:58:15','2022-04-24 16:58:15','626581a6149ec_2',167,NULL,0,0,0,0,'Cropped from primary image.','image/jpeg','1','2022-04-24 16:58:15',NULL),(170,'2022-04-24 16:58:15','2022-04-24 16:58:15','626581a6149ec_3',167,NULL,0,0,0,0,'Cropped from primary image.','image/jpeg','1','2022-04-24 16:58:15',NULL),(171,'2022-04-24 17:00:33','2022-04-24 17:00:33','62658230f2031.jpeg',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(172,'2022-04-24 17:00:34','2022-04-24 17:00:34','62658230f2031_1',171,NULL,1146,1688,422,510,'Cropped from primary image.','image/jpeg','1','2022-04-24 17:00:34',NULL),(173,'2022-04-24 17:00:34','2022-04-24 17:00:34','62658230f2031_2',171,NULL,913,642,435,441,'Cropped from primary image.','image/jpeg','1','2022-04-24 17:00:34',NULL),(174,'2022-04-24 17:00:34','2022-04-24 17:00:34','62658230f2031_3',171,NULL,1335,2898,541,554,'Cropped from primary image.','image/jpeg','1','2022-04-24 17:00:34',NULL),(175,'2022-04-24 17:09:00','2022-04-24 17:09:00','6265842c485f7.',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(176,'2022-04-24 17:09:51','2022-04-24 17:09:51','6265845f2d938',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(177,'2022-04-24 17:10:53','2022-04-24 17:10:53','6265849d36268',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(178,'2022-04-24 17:10:54','2022-04-24 17:10:54','6265849d36268_1',177,NULL,1146,1688,422,510,'Cropped from primary image.','image/jpeg','1','2022-04-24 17:10:54',NULL),(179,'2022-04-24 17:10:54','2022-04-24 17:10:54','6265849d36268_2',177,NULL,913,642,435,441,'Cropped from primary image.','image/jpeg','1','2022-04-24 17:10:54',NULL),(180,'2022-04-24 17:10:54','2022-04-24 17:10:54','6265849d36268_3',177,NULL,1335,2898,541,554,'Cropped from primary image.','image/jpeg','1','2022-04-24 17:10:54',NULL),(181,'2022-04-24 21:29:01','2022-04-24 21:29:01','6265c11d92fb1',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(182,'2022-04-24 21:29:03','2022-04-24 21:29:03','6265c11d92fb1_1',181,NULL,636,1688,422,510,'Cropped from primary image.','image/jpeg','1','2022-04-24 21:29:03',NULL),(183,'2022-04-24 21:29:03','2022-04-24 21:29:03','6265c11d92fb1_2',181,NULL,472,642,435,441,'Cropped from primary image.','image/jpeg','1','2022-04-24 21:29:03',NULL),(184,'2022-04-24 21:29:03','2022-04-24 21:29:03','6265c11d92fb1_3',181,NULL,781,2898,541,554,'Cropped from primary image.','image/jpeg','1','2022-04-24 21:29:03',NULL),(185,'2022-07-24 17:54:26','2022-07-24 17:54:26','62dd87522677f',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(186,'2022-07-31 13:40:07','2022-07-31 13:40:07','62e68636d0f8d',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(187,'2022-07-31 13:42:10','2022-07-31 13:42:10','62e686b2006c8',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(188,'2022-07-31 13:43:42','2022-07-31 13:43:42','62e6870da84f6',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(189,'2022-07-31 13:44:50','2022-07-31 13:44:50','62e6875228cb3',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(190,'2022-07-31 13:48:44','2022-07-31 13:48:44','62e6883baf0fd',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(191,'2022-07-31 13:50:26','2022-07-31 13:50:26','62e688a21c655',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL),(192,'2022-09-30 00:48:43','2022-09-30 00:48:43','63363ceae20fc',NULL,NULL,0,0,0,0,NULL,'image/jpeg','1','2022-02-28 00:00:00',NULL);
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (31,'2014_10_12_000000_create_users_table',1),(32,'2014_10_12_100000_create_password_resets_table',1),(33,'2019_08_19_000000_create_failed_jobs_table',1),(34,'2019_12_14_000001_create_personal_access_tokens_table',1),(35,'2022_02_18_133350_create_configurations_table',1),(36,'2022_02_18_135222_create_people_table',1),(37,'2022_02_18_164049_make_columns_nullable',2),(38,'2022_02_18_165541_make_person_columns_nullable',3),(39,'2022_02_18_235844_alter_configuration_primary_user_id',4),(40,'2022_02_19_123647_change_configuration_primary_user_id_datatype',5),(41,'2022_02_19_124631_configuration_drop_primary_user_id_column',6),(42,'2022_02_19_124753_configuration_add_primary_user_id_column',7),(43,'2022_02_19_125752_configuration_add_person_fk',8),(44,'2022_02_20_192418_create_security_info_table',9),(45,'2022_02_23_002354_create_is_primary_column',10),(46,'2022_02_27_142909_create_images_table',11),(47,'2022_02_27_234438_create_mimetype_column',12),(48,'2022_02_28_182238_create_additional_image_columns',13),(49,'2022_02_28_182843_create_data_column',14),(50,'2022_03_03_163014_device',15),(51,'2022_03_04_171416_rename_systemid_column',16),(52,'2022_03_12_172758_create_jobs_table',17),(53,'2022_03_27_192834_update_image_default_values',18),(54,'2022_03_27_193334_update_image_nullable_fields',19),(56,'2022_04_11_012635_security_event_log',20),(57,'2022_07_03_182835_event_data',21),(58,'2022_07_03_182836_event_data',22),(59,'2022_07_03_184201_event_data_types',23),(60,'2022_07_03_185209_change_event_data_type_fk',24),(61,'2022_07_03_194604_create_device_data_type_relationship',25),(62,'2022_07_03_194605_create_device_data_type_relationship',26),(63,'2022_09_19_011514_add_device_token_to_users',27),(64,'2023_08_23_021809_add_default_fcm_value',28),(65,'2023_11_02_015229_add_fields_to_user',29),(66,'2023_11_22_023123_add_user_claim_table',30),(67,'2023_11_25_154100_add_claim_data_field',31),(68,'2023_11_30_191233_add_user_inactive_disabled_fields',32);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `people` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` json DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_info_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `people`
--

LOCK TABLES `people` WRITE;
/*!40000 ALTER TABLE `people` DISABLE KEYS */;
INSERT INTO `people` VALUES (5,'2022-02-23 03:15:05','2022-02-23 03:15:05','Billy',NULL,'Bob',NULL,NULL,NULL,NULL,NULL),(6,'2022-02-23 03:18:13','2022-02-23 03:18:13','Billy',NULL,'Bob',NULL,NULL,NULL,NULL,NULL),(7,'2022-02-23 03:20:23','2022-02-23 03:20:23','Billy',NULL,'Bob',NULL,NULL,NULL,NULL,NULL),(8,'2022-02-23 03:20:57','2022-02-23 03:20:57','Billy',NULL,'Bob',NULL,NULL,NULL,NULL,NULL),(9,'2022-02-23 03:21:48','2022-02-23 03:28:14','Billy',NULL,'Changed',NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `people` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_event_log`
--

DROP TABLE IF EXISTS `security_event_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_event_log` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_date` datetime DEFAULT NULL,
  `data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_id` (`type`),
  KEY `security_event_log_device_date_index` (`device_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_event_log`
--

LOCK TABLES `security_event_log` WRITE;
/*!40000 ALTER TABLE `security_event_log` DISABLE KEYS */;
INSERT INTO `security_event_log` VALUES ('13fdb3d0-bb81-11ec-b960-0242ac130002',NULL,'Info','2022-02-28 00:00:00','Sending raw image data to detection service.','2022-04-13 23:26:00','2022-04-13 23:26:00');
/*!40000 ALTER TABLE `security_event_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_info`
--

DROP TABLE IF EXISTS `security_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_info` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `person_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_person_id` (`person_id`),
  CONSTRAINT `fk_person_id` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_info`
--

LOCK TABLES `security_info` WRITE;
/*!40000 ALTER TABLE `security_info` DISABLE KEYS */;
INSERT INTO `security_info` VALUES (1,'2022-02-23 03:21:48','2022-02-23 03:21:48',1,9,1);
/*!40000 ALTER TABLE `security_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_claim`
--

DROP TABLE IF EXISTS `user_claim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_claim` (
  `userId` int unsigned NOT NULL,
  `claim` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_begin` date DEFAULT NULL,
  `valid_end` date DEFAULT NULL,
  `data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`userId`,`claim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_claim`
--

LOCK TABLES `user_claim` WRITE;
/*!40000 ALTER TABLE `user_claim` DISABLE KEYS */;
INSERT INTO `user_claim` VALUES (50,'End',NULL,NULL,'1/30/2024'),(50,'Role',NULL,NULL,'ROOT_USER'),(50,'Start',NULL,NULL,'12/1/2023');
/*!40000 ALTER TABLE `user_claim` ENABLE KEYS */;
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
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fcm_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `firstName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primaryPhone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (50,'Joe Snuffy','jsnuffy@gmail.com',NULL,'$2y$10$xHvG8xqS.tXFs8YYPkpju.Ktx.oz6y4Imhhn9ZAkLzHI3bV6ELtrK',NULL,'2024-01-27 15:58:17','2024-01-27 15:58:17','','Joe','Snuffy','6122255555',0,0);
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

-- Dump completed on 2024-01-29  4:53:23
