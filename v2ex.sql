-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `source` varchar(20) NOT NULL,
  `source_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_source` (`user_id`,`source`),
  UNIQUE KEY `source_source_id` (`source`,`source_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cash_flow`;
CREATE TABLE `cash_flow` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `target_id` mediumint(8) unsigned NOT NULL,
  `source_id` mediumint(8) unsigned NOT NULL,
  `coin` mediumint(8) NOT NULL,
  `deal_id` int(10) unsigned NOT NULL,
  `type` varchar(24) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  `position` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `invisible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  PRIMARY KEY (`topic_id`,`position`),
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`,`id`),
  KEY `topic_updated` (`topic_id`,`updated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `commentid`;
CREATE TABLE `commentid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `favorite`;
CREATE TABLE `favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `source_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `target_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_type_target` (`source_id`,`type`,`target_id`),
  KEY `type_target` (`type`,`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `action` tinyint(1) unsigned NOT NULL,
  `action_time` int(10) unsigned NOT NULL,
  `target` int(10) unsigned NOT NULL DEFAULT '0',
  `ext` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `sortid` tinyint(1) unsigned NOT NULL DEFAULT '99',
  `name` varchar(20) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sort_id` (`sortid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `node`;
CREATE TABLE `node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `topic_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `favorite_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `ename` varchar(20) NOT NULL,
  `about` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `ename` (`ename`),
  KEY `topic_id` (`topic_count`,`id`),
  KEY `id_ename` (`id`,`ename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `node` (`id`, `created_at`, `updated_at`, `topic_count`, `favorite_count`, `name`, `ename`, `about`) VALUES
(1, 0,  0,  5,  0,  '一锅粥',  'mass', '散落凡间的主题'),
(2, 0,  0,  0,  0,  '随感', 'feel', '星星点点的夜空'),
(3, 0,  0,  0,  0,  'php',  'php',  'php是世界上最好的语言');

DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `target_id` mediumint(8) unsigned NOT NULL,
  `source_id` mediumint(8) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  `position` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target_status_id` (`target_id`,`status`,`id`),
  KEY `source_id_target_id` (`source_id`,`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `sortid` tinyint(1) unsigned NOT NULL DEFAULT '99',
  `block` varchar(10) NOT NULL DEFAULT '',
  `label` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `key` varchar(50) NOT NULL,
  `value_type` varchar(10) NOT NULL DEFAULT 'text',
  `value` text NOT NULL,
  `option` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `block_sort_id` (`block`,`sortid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `siteinfo`;
CREATE TABLE `siteinfo` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `nodes` smallint(6) unsigned NOT NULL DEFAULT '0',
  `users` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `topics` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `comments` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tab`;
CREATE TABLE `tab` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `ename` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ename_id` (`ename`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `tab` (`id`, `name`, `ename`) VALUES
(1, 'the king of chaos',  'chaos'),
(2, '程序员',  'programmer');

DROP TABLE IF EXISTS `tab_node`;
CREATE TABLE `tab_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` smallint(6) unsigned NOT NULL,
  `tab_id` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tab_name_node_name` (`tab_id`,`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `tab_node` (`id`, `node_id`, `tab_id`) VALUES
(1, 1,  '1'),
(2, 2,  '1'),
(3, 3,  '2');

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `topic_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `name_id` (`name`,`id`),
  KEY `name_topic_count` (`name`,`topic_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tag_topic`;
CREATE TABLE `tag_topic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(10) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_topic` (`tag_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `type` varchar(12) NOT NULL,
  `ename` varchar(12) NOT NULL,
  `coin` int(10) unsigned NOT NULL,
  `about` varchar(60) NOT NULL,
  `role` varchar(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_created_at_type_coid_about` (`id`,`created_at`,`type`,`coin`,`about`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `task` (`id`, `created_at`, `type`, `ename`, `coin`, `about`, `role`) VALUES
(1, 0,  '初始资本', 'base', 2000, '获得初始资本 2000 铜币', 'default'),
(2, 0,  '每日登录奖励', 'signin', 30, '每日登录奖励', 'daily');

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `ext` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_type_expires` (`user_id`,`type`,`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `replied_at` int(10) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `reply_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `alltop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `top` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `invisible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `favorite_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` char(120) NOT NULL,
  `ranked_at` int(10) unsigned NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `client` varchar(12) NOT NULL,
  `author` char(16) NOT NULL,
  `last_reply_username` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alllist` (`alltop`,`replied_at`,`id`),
  KEY `nodelist` (`node_id`,`top`,`replied_at`,`id`),
  KEY `hottopics` (`created_at`,`comment_count`,`replied_at`),
  KEY `updated` (`updated_at`),
  KEY `node_updated` (`node_id`,`updated_at`),
  KEY `user_id` (`user_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `topic_content`;
CREATE TABLE `topic_content` (
  `topic_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  PRIMARY KEY (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `type`;
CREATE TABLE `type` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `ename` varchar(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ename_id` (`ename`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '8',
  `role` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `username` char(16) NOT NULL,
  `email` char(50) NOT NULL,
  `password_hash` char(80) NOT NULL,
  `auth_key` char(32) NOT NULL,
  `avatar` char(50) NOT NULL DEFAULT 'avatar/0_{size}.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status_id` (`status`,`id`),
  KEY `username_id` (`username`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_asset`;
CREATE TABLE `user_asset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `coin` int(10) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `type` varchar(12) NOT NULL,
  `event_type` varchar(12) NOT NULL,
  `about` varchar(60) NOT NULL,
  `event_coin` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user_id_created_at` (`id`,`user_id`,`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_record`;
CREATE TABLE `user_record` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `coin` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_login_at` int(10) unsigned NOT NULL,
  `last_login_ip` int(10) unsigned NOT NULL,
  `reg_ip` int(10) unsigned NOT NULL,
  `topic_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `comment_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `favorite_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `favorite_node_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `favorite_topic_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `favorite_user_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `website` varchar(100) NOT NULL DEFAULT '',
  `about` varchar(255) NOT NULL DEFAULT '',
  `notice_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `unread_notice_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `keep_signin_day` smallint(6) unsigned NOT NULL,
  `last_signin_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 2016-06-07 08:16:20