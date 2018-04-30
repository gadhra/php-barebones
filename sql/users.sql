CREATE TABLE `users_new` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(255) DEFAULT NULL,
      `hash` varchar(128) DEFAULT NULL,
      `validation` varchar(32) DEFAULT NULL,
      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `modified` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
