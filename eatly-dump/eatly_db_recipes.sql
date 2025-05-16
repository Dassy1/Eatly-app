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
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `source_url` varchar(255) DEFAULT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `cooking_time` int(11) DEFAULT NULL,
  `servings` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (4,'Spaghetti Carbonara','https://static01.nyt.com/images/2021/02/14/dining/carbonara-horizontal/carbonara-horizontal-master768-v2.jpg?quality=75&auto=webp','https://cooking.nytimes.com/recipes/12965-spaghetti-carbonara','Italian Cuisine',30,4,1,'2025-05-16 06:36:21'),(5,'Chicken Tikka Masala','https://www.jocooks.com/wp-content/uploads/2024/01/chicken-tikka-masala-1-26-730x913.jpg','https://www.jocooks.com/recipes/chicken-tikka-masala/','Indian Delights',45,6,1,'2025-05-16 06:36:21'),(6,'Vegetable Stir Fry','https://i0.wp.com/kristineskitchenblog.com/wp-content/uploads/2024/01/vegetable-stir-fry-22-2.jpg?resize=700%2C1050&ssl=1','https://kristineskitchenblog.com/vegetable-stir-fry/','Healthy Eats',20,2,2,'2025-05-16 06:36:21'),(7,'akara','https://foodace.co.uk/wp-content/uploads/2016/03/akara.jpg','https://foodace.co.uk/simple-akara-2/','adek',5,3,5,'2025-05-16 09:07:30'),(8,'akara','https://foodace.co.uk/wp-content/uploads/2016/03/akara.jpg','https://foodace.co.uk/simple-akara-2/','adek',5,3,5,'2025-05-16 09:07:30'),(9,'Akara (Bean Cakes)','https://www.eatingnigeria.com/wp-content/uploads/2023/03/Akara-Bean-Cakes.jpg','https://www.eatingnigeria.com/akara-bean-cakes/','Nigerian Cuisine',30,6,1,'2025-05-16 12:15:35'),(10,'egusi','https://www.chefadora.com/_next/image?url=https%3A%2F%2Fchefadora.b-cdn.net%2Fmedium_Top_21_Nigerian_Foods_3f02bbe408.jpg&amp;w=1920&amp;q=75','https://www.chefadora.com/taiws/egusi-soup-and-eba-pak4pempte','tsola',30,5,9,'2025-05-16 12:59:11');
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
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
