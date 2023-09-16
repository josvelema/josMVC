CREATE DATABASE IF NOT EXISTS `phplogin_mvc` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phplogin_mvc`;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(50) NOT NULL DEFAULT '',
  `rememberme` varchar(255) NOT NULL DEFAULT '',
  `role` enum('Member','Admin') NOT NULL DEFAULT 'Member',
  `registered` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  `reset` varchar(50) NOT NULL,
  `tfa_code` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `accounts` (`id`, `username`, `password`, `email`, `activation_code`, `rememberme`, `role`, `registered`, `last_seen`, `reset`, `tfa_code`, `ip`) VALUES
(1, 'admin', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'admin@example.com', 'activated', '', 'Admin', '2023-01-01 00:00:00', '2023-01-01 00:00:00', '', '', ''),
(2, 'member', '$2y$10$YXiKXipJfkEZEV2KSFPMV.gtyV5VIO.Ly4EZCayLamagqvnX.o4zu', 'member@example.com', 'activated', '', 'Member', '2023-01-01 00:00:00', '2023-01-01 00:00:00', '', '', '');

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) NOT NULL,
  `attempts_left` tinyint(1) NOT NULL DEFAULT 5,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;