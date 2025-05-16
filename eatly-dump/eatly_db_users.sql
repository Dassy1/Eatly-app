-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: eatly_db
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'dassy01','dassy@gmail.com','1234567890','2025-05-16 06:34:18'),(2,'jane_smith','jane@outlook.com','yyyyyyyy','2025-05-16 06:34:18'),(3,'jadiii','jadi@gmail.com','$2y$10$WxKfRCdow8gtIAnYm4FKR.IR8jA7gAwxdUCddwhawCOMwpvJlsJjW','2025-05-16 08:32:27'),(4,'testuser','test@example.com','$2y$10$nOF3cQ/XoTcavmneZqGmBOf3MGVA2JCdedOiN2qZXbzJUvEG8PHoq','2025-05-16 08:44:47'),(5,'adewale','adek@outlook.com','$2y$10$xmKKoVaAe7RYzb3unTwGJudBZsc1KdpNn2vZp9/pc0PPVhs6FJMKO','2025-05-16 08:58:39'),(6,'testid','id@example.com','$2y$10$7VOcmPi5pP8MuV4CLXzbjuYV05mQvegDvJsyvj4SX.D6NyfIPiyTu','2025-05-16 11:47:29'),(7,'kenke','kenke2003@gmail.com','$2y$10$0aesWp9qtGneuSDlBC8C..YpXAiZmhAtzszlX6M9ZEuTjyTZkXzBK','2025-05-16 11:59:10'),(8,'ddh','giftola219@gmail.com','$2y$10$t0L7l9wTvd1gj80uzgksTONnrHSscIiwRp.JxCpU6w7zw92V8iiea','2025-05-16 12:05:01'),(9,'tsola','tsola@yahoo.com','$2y$10$E9Bz.AU8N7/Xw8OpInzipObErf80ASyMgX.l1kKM5UfBCFyw5Cfqy','2025-05-16 12:55:08');
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

-- Dump completed on 2025-05-16 14:30:36
