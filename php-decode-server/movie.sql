--
-- Database: `hdviet`
--

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