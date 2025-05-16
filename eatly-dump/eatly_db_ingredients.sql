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
-- Table structure for table `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_id` (`recipe_id`),
  CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredients`
--

LOCK TABLES `ingredients` WRITE;
/*!40000 ALTER TABLE `ingredients` DISABLE KEYS */;
INSERT INTO `ingredients` VALUES (65,4,400.00,'g','spaghetti'),(66,4,200.00,'g','pancetta'),(67,4,3.00,NULL,'large eggs'),(68,4,50.00,'g','pecorino cheese'),(69,4,50.00,'g','parmesan'),(70,4,NULL,NULL,'black pepper'),(71,5,500.00,'g','chicken breast'),(72,5,1.00,NULL,'large onion'),(73,5,3.00,NULL,'garlic cloves'),(74,5,400.00,'g','chopped tomatoes'),(75,5,100.00,'ml','yogurt'),(76,5,NULL,NULL,'spices'),(77,6,1.00,NULL,'bell pepper'),(78,6,1.00,NULL,'carrot'),(79,6,100.00,'g','broccoli'),(80,6,2.00,'tbsp','soy sauce'),(81,7,2.00,'g','washed peeled beans'),(82,7,2.00,'1/3','cup of water'),(83,7,1.00,'g','knorr seasoning cube'),(84,7,1.00,'g','medium sized onions'),(85,7,0.50,'g','fresh/grounded pepper'),(86,7,0.20,'g','salt to taste'),(87,7,NULL,NULL,'oil for deep frying'),(88,8,2.00,'g','washed peeled beans'),(89,8,2.00,'1/3','cup of water'),(90,8,1.00,'g','knorr seasoning cube'),(91,8,1.00,'g','medium sized onions'),(92,8,0.50,'g','fresh/grounded pepper'),(93,8,0.20,'g','salt to taste'),(94,8,NULL,NULL,'oil for deep frying'),(95,9,1.00,'kg','black-eyed peas'),(96,9,1.00,'cup','onions'),(97,9,1.00,'tsp','salt'),(98,9,1.00,'tsp','pepper'),(99,9,1.00,'tsp','thyme'),(100,9,1.00,'cup','palm oil'),(101,10,1.00,'g','egusi');
/*!40000 ALTER TABLE `ingredients` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-16 14:30:35
