CREATE TABLE `meta_meta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL DEFAULT '',
  `keywords` varchar(60) NOT NULL DEFAULT '',
  `description` varchar(160) NOT NULL DEFAULT '',
  `canonical` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `host` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `host_url_uniq` (`host`,`url`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `meta_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(15) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_uniq` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;