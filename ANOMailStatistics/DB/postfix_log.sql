-- MySQL dump 10.14  Distrib 5.5.50-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: postfix_log
-- ------------------------------------------------------
-- Server version	5.5.50-MariaDB

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
-- Table structure for table `BouncePieChart`
--

DROP TABLE IF EXISTS `BouncePieChart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BouncePieChart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) DEFAULT NULL,
  `b_code` varchar(50) DEFAULT NULL,
  `b_count` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BouncePieChart`
--

LOCK TABLES `BouncePieChart` WRITE;
/*!40000 ALTER TABLE `BouncePieChart` DISABLE KEYS */;
/*!40000 ALTER TABLE `BouncePieChart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SentChart`
--

DROP TABLE IF EXISTS `SentChart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SentChart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) DEFAULT NULL,
  `sent` int(11) DEFAULT NULL,
  `incoming` int(11) DEFAULT NULL,
  `deferred` int(11) DEFAULT NULL,
  `bounced` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SentChart`
--

LOCK TABLES `SentChart` WRITE;
/*!40000 ALTER TABLE `SentChart` DISABLE KEYS */;
/*!40000 ALTER TABLE `SentChart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bounce_codes`
--

DROP TABLE IF EXISTS `bounce_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bounce_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `b_code` char(15) DEFAULT NULL,
  `b_type` varchar(50) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bounce_codes`
--

LOCK TABLES `bounce_codes` WRITE;
/*!40000 ALTER TABLE `bounce_codes` DISABLE KEYS */;
INSERT INTO `bounce_codes` VALUES (2,'5.4.4','Hard','Unable to route'),(3,'5.7.606','Hard','Access denied, banned sending IP'),(4,'5.1.1','Hard','Bad destination mailbox address'),(5,'4.4.1','Soft','The host is not responding'),(6,'4.0.0','Soft','Delivery temporarily suspended host'),(7,'5.7.1','Hard','Delivery not authorized, message refused'),(8,'4.4.3','Soft','Host or domain name not found'),(9,'5.2.1','Hard','Mailbox disabled, not accepting messages'),(10,'5.2.2','Hard','Mailbox full'),(11,'5.3.0','Hard','Other or undefined mail system status'),(12,'4.7.1','Soft','Delivery not authorized, message refused'),(13,'5.2.0','Hard','Other or undefined mailbox status'),(14,'4.4.2','Soft','Bad connection'),(15,'5.1.0','Hard','Other address status'),(16,'5.4.6','Hard','Routing loop detected'),(17,'5.5.0','Hard','Other or undefined protocol status'),(18,'4.1.7','Soft','Bad sender\'s mailbox address syntax'),(19,'4.1.1','Soft','Bad destination mailbox address'),(20,'4.2.2','Soft','Mailbox full'),(21,'4.7.0','Soft','Other or undefined security status'),(22,'5.7.0','Hard','Other or undefined security status'),(23,'4.3.2','Soft','System not accepting network messages'),(24,'5.3.2','Hard','System not accepting network messages'),(25,'5.1.2','Hard','Bad destination system address'),(26,'5.7.64','Hard','TenantAttribution Relay Access Denied'),(27,'4.2.1','Soft','Mailbox disabled, not accepting messages'),(28,'5.1.10','Hard','Recipient not found by SMTP address lookup'),(29,'5.5.4','Hard','Invalid command arguments'),(30,'5.3.4','Hard','Message too big for system'),(31,'4.1.0','Soft','Other address status'),(32,'5.5.1','Hard','Invalid command'),(33,'5.7.54','Hard','SMTP Unable to relay recipient in non-accepted domain'),(34,'5.4.1','Hard','No answer from host'),(35,'4.3.5','Soft','System incorrectly configured'),(36,'4.5.1','Soft','Invalid command'),(37,'4.1.8','Soft','Bad sender\'s system address'),(38,'4.3.1','Soft','Mail system full'),(39,'5.1.8','Hard','Bad sender\'s system address'),(40,'5.7.23','Hard','OpenSPF error'),(41,'5.7.105','Hard','Sender denied as sender\'s email address is on SenderFilterConfig list'),(42,'4.5.2','Soft','Syntax error'),(43,'4.1.2','Soft','Bad destination system address'),(44,'4.3.0','Soft','Other or undefined mail system status'),(45,'4.6.0','Soft','Other or undefined media error'),(46,'5.0.1','Hard','User Unknown');
/*!40000 ALTER TABLE `bounce_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bounce_report`
--

DROP TABLE IF EXISTS `bounce_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bounce_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mbox_id` int(11) DEFAULT NULL,
  `reporting-mta` varchar(255) DEFAULT NULL,
  `mess_id` varchar(50) DEFAULT NULL,
  `from_addr` varchar(255) DEFAULT NULL,
  `to_addr` varchar(255) DEFAULT NULL,
  `orig_to` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `remote-mta` varchar(255) DEFAULT NULL,
  `diagnostic-code` varchar(500) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_reporting-mta` (`reporting-mta`),
  KEY `i_mess_id` (`mess_id`),
  KEY `i_from_addr` (`from_addr`),
  KEY `i_to_addr` (`to_addr`),
  KEY `i_orig_to` (`orig_to`),
  KEY `i_status` (`status`),
  KEY `i_remote-mta` (`remote-mta`),
  KEY `i_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bounce_report`
--

LOCK TABLES `bounce_report` WRITE;
/*!40000 ALTER TABLE `bounce_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `bounce_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_log`
--

DROP TABLE IF EXISTS `config_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(50) DEFAULT NULL,
  `logrotate` int(11) DEFAULT NULL,
  `log` varchar(255) DEFAULT NULL,
  `log_description` varchar(255) DEFAULT NULL,
  `logtype` int(11) DEFAULT NULL,
  `iteration_num` int(11) DEFAULT NULL,
  `R_H` int(11) DEFAULT NULL,
  `R_M` int(11) DEFAULT NULL,
  `R_W` int(11) DEFAULT NULL,
  `del_older_rows` int(11) DEFAULT NULL,
  `del_older_logs` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_log`
--

LOCK TABLES `config_log` WRITE;
/*!40000 ALTER TABLE `config_log` DISABLE KEYS */;
INSERT INTO `config_log` VALUES (1,'off',1,'/var/log/maillog','postfix_log',1,5000,8,35,0,30,7,1);
/*!40000 ALTER TABLE `config_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_mbb`
--

DROP TABLE IF EXISTS `config_mbb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_mbb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(50) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `ssl` int(11) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `iteration_num` int(11) DEFAULT NULL,
  `del_older_rows` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_mbb`
--

LOCK TABLES `config_mbb` WRITE;
/*!40000 ALTER TABLE `config_mbb` DISABLE KEYS */;
INSERT INTO `config_mbb` VALUES (1,'off','localhost',110,0,'bounce@example.com','pop-password',500,30,1);
/*!40000 ALTER TABLE `config_mbb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (1,'example.com');
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logvar`
--

DROP TABLE IF EXISTS `logvar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logvar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path_id` int(11) DEFAULT NULL,
  `log_id` varchar(255) DEFAULT NULL,
  `log_description` varchar(255) DEFAULT NULL,
  `line_num` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_id` (`log_id`),
  KEY `idate` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logvar`
--

LOCK TABLES `logvar` WRITE;
/*!40000 ALTER TABLE `logvar` DISABLE KEYS */;
/*!40000 ALTER TABLE `logvar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_client`
--

DROP TABLE IF EXISTS `m_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_client` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_id` varchar(150) DEFAULT NULL,
  `cur_num` int(11) DEFAULT NULL,
  `srv` varchar(50) DEFAULT NULL,
  `inst` varchar(80) DEFAULT NULL,
  `proc` varchar(20) DEFAULT NULL,
  `mess_id` char(20) DEFAULT NULL,
  `client` varchar(255) DEFAULT NULL,
  `sasl_method` varchar(255) DEFAULT NULL,
  `sasl_username` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ilog_id` (`log_id`),
  KEY `iclient` (`client`),
  KEY `idate` (`date`),
  KEY `imess_id` (`mess_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_client`
--

LOCK TABLES `m_client` WRITE;
/*!40000 ALTER TABLE `m_client` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_delivery`
--

DROP TABLE IF EXISTS `m_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_delivery` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_id` varchar(150) DEFAULT NULL,
  `cur_num` int(11) DEFAULT NULL,
  `srv` varchar(50) DEFAULT NULL,
  `inst` varchar(80) DEFAULT NULL,
  `proc` varchar(20) DEFAULT NULL,
  `mess_id` char(20) DEFAULT NULL,
  `to_addr` varchar(255) DEFAULT NULL,
  `orig_to` varchar(255) DEFAULT NULL,
  `relay` varchar(255) DEFAULT NULL,
  `delay` varchar(30) DEFAULT NULL,
  `delays` varchar(40) DEFAULT NULL,
  `dsn` varchar(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `status_b` tinyint(4) DEFAULT NULL,
  `details` varchar(500) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ito_addr` (`to_addr`),
  KEY `iorig_to` (`orig_to`),
  KEY `irelay` (`relay`),
  KEY `istatus` (`status`),
  KEY `idate` (`date`),
  KEY `imess_id` (`mess_id`) USING BTREE,
  KEY `ilog_id` (`log_id`),
  KEY `istatus_b` (`status_b`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_delivery`
--

LOCK TABLES `m_delivery` WRITE;
/*!40000 ALTER TABLE `m_delivery` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_from`
--

DROP TABLE IF EXISTS `m_from`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_from` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_id` varchar(150) DEFAULT NULL,
  `cur_num` int(11) DEFAULT NULL,
  `srv` varchar(50) DEFAULT NULL,
  `inst` varchar(80) DEFAULT NULL,
  `proc` varchar(20) DEFAULT NULL,
  `mess_id` char(20) DEFAULT NULL,
  `from_addr` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ifrom_addr` (`from_addr`),
  KEY `idate` (`date`),
  KEY `imess_id` (`mess_id`) USING BTREE,
  KEY `ilog_id` (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_from`
--

LOCK TABLES `m_from` WRITE;
/*!40000 ALTER TABLE `m_from` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_from` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_reject`
--

DROP TABLE IF EXISTS `m_reject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_reject` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_id` varchar(150) DEFAULT NULL,
  `cur_num` int(11) DEFAULT NULL,
  `srv` varchar(50) DEFAULT NULL,
  `inst` varchar(80) DEFAULT NULL,
  `proc` varchar(20) DEFAULT NULL,
  `mess_id` varchar(20) DEFAULT NULL,
  `from_addr` varchar(255) DEFAULT NULL,
  `to_addr` varchar(255) DEFAULT NULL,
  `reject_from` varchar(255) DEFAULT NULL,
  `helo` varchar(255) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ifrom_addr` (`from_addr`),
  KEY `ito_addr` (`to_addr`),
  KEY `ireject_from` (`reject_from`),
  KEY `idate` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_reject`
--

LOCK TABLES `m_reject` WRITE;
/*!40000 ALTER TABLE `m_reject` DISABLE KEYS */;
/*!40000 ALTER TABLE `m_reject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relays`
--

DROP TABLE IF EXISTS `relays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `include_relay` varchar(255) DEFAULT NULL,
  `exclude_relay` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relays`
--

LOCK TABLES `relays` WRITE;
/*!40000 ALTER TABLE `relays` DISABLE KEYS */;
INSERT INTO `relays` VALUES (1,'dovecot','dovecot');
/*!40000 ALTER TABLE `relays` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-03 12:13:33
