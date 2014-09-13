--
-- Database: `hdviet`
--

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `xml_id` varchar(10) DEFAULT '',
  `name` varchar(200) DEFAULT '',
  `description` text,
  `image_url` text,
  `ip` varchar(50) DEFAULT '',
  `client_id` varchar(100) DEFAULT '',
  `create_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `xml_id` (`xml_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

DROP TABLE IF EXISTS `movie`;
CREATE TABLE IF NOT EXISTS `movie` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `xml_id` varchar(10) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `client_id` varchar(100) DEFAULT NULL,
  `xml_data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;