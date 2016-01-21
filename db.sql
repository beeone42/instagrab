SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `ig`;
CREATE TABLE `ig` (
  `id` bigint(20) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `full` varchar(255) NOT NULL,
  `moderation` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

