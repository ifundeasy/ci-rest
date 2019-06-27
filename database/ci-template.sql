# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.38)
# Database: ci-template
# Generation Time: 2019-06-27 2:58:48 AM +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table log_audit_trail
# ------------------------------------------------------------

DROP TABLE IF EXISTS `log_audit_trail`;

CREATE TABLE `log_audit_trail` (
  `id` varchar(100) NOT NULL,
  `tag` varchar(50) DEFAULT NULL,
  `message` varchar(100) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sec_apps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sec_apps`;

CREATE TABLE `sec_apps` (
  `apps_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `apps_name` varchar(100) NOT NULL,
  `ip_address` varchar(200) NOT NULL,
  `apps_key` varchar(100) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`apps_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sec_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sec_role`;

CREATE TABLE `sec_role` (
  `role_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(100) NOT NULL,
  `status` int(11) DEFAULT '0',
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `sec_role` WRITE;
/*!40000 ALTER TABLE `sec_role` DISABLE KEYS */;

INSERT INTO `sec_role` (`role_id`, `rolename`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
	(1,'Administrator',1,'system','2019-06-27 09:10:24',NULL,NULL),
	(2,'Player',2,'system','2019-06-27 09:10:24',NULL,NULL);

/*!40000 ALTER TABLE `sec_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sec_session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sec_session`;

CREATE TABLE `sec_session` (
  `token` varchar(100) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `valid_until` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sec_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sec_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `sec_session` WRITE;
/*!40000 ALTER TABLE `sec_session` DISABLE KEYS */;

INSERT INTO `sec_session` (`token`, `user_id`, `valid_until`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
	('730a7f85fb663060d5cffc199401909d744f4543',1,'2019-06-27 04:24:42','system','2019-06-27 02:24:42',NULL,NULL),
	('b0891201ccec6b557910142d526561f2d633ef7f',1,'2019-06-27 04:29:52','system','2019-06-27 02:29:52',NULL,NULL);

/*!40000 ALTER TABLE `sec_session` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sec_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sec_user`;

CREATE TABLE `sec_user` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `avatar` int(11) DEFAULT '0' COMMENT '0=no profile pict, 1=with profile pict',
  `roles` text,
  `status` int(11) DEFAULT '0',
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `sec_user` WRITE;
/*!40000 ALTER TABLE `sec_user` DISABLE KEYS */;

INSERT INTO `sec_user` (`user_id`, `username`, `password`, `fullname`, `email`, `avatar`, `roles`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
	(1,'admin','d60b772c6205311fae6fae9f8509986da1fe7029','Administrator','adminus@gmail.com',0,'[1]',1,'system','2019-06-27 09:10:24',NULL,NULL);

/*!40000 ALTER TABLE `sec_user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
