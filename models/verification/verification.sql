--
-- Table structure `PREFIX_customer_verification`
--

CREATE TABLE IF NOT EXISTS `PREFIX_customer_verification` (
  `customer_id` int(11) NOT NULL,
  `verification_code` char(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
