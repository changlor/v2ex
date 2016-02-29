-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-01-14 12:01:37
-- 服务器版本： 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `v2ex`
--

-- --------------------------------------------------------

--
-- 表的结构 `auth`
--

CREATE TABLE IF NOT EXISTS `auth` (
  `id` mediumint(8) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `source` varchar(20) NOT NULL,
  `source_id` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  `position` mediumint(8) unsigned NOT NULL,
  `invisible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `commentid`
--

CREATE TABLE IF NOT EXISTS `commentid` (
  `id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `favorite`
--

CREATE TABLE IF NOT EXISTS `favorite` (
  `id` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `source_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `target_id` mediumint(8) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `favorite`
--

INSERT INTO `favorite` (`id`, `type`, `source_id`, `target_id`) VALUES
(7, 1, 1, 1),
(6, 1, 1, 2);

-- --------------------------------------------------------

--
-- 表的结构 `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(10) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `action` tinyint(1) unsigned NOT NULL,
  `action_time` int(10) unsigned NOT NULL,
  `target` int(10) unsigned NOT NULL DEFAULT '0',
  `ext` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `id` smallint(6) unsigned NOT NULL,
  `sortid` tinyint(1) unsigned NOT NULL DEFAULT '99',
  `name` varchar(20) NOT NULL,
  `url` varchar(100) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `link`
--

INSERT INTO `link` (`id`, `sortid`, `name`, `url`) VALUES
(1, 0, '极简论坛', 'http://simpleforum.org/');

-- --------------------------------------------------------

--
-- 表的结构 `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` smallint(6) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `topic_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `favorite_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `ename` varchar(20) NOT NULL,
  `about` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `node`
--

INSERT INTO `node` (`id`, `created_at`, `updated_at`, `topic_count`, `favorite_count`, `name`, `ename`, `about`) VALUES
(1, 1451806330, 1451806330, 0, 0, '默认分类', 'default', '');

-- --------------------------------------------------------

--
-- 表的结构 `notice`
--

CREATE TABLE IF NOT EXISTS `notice` (
  `id` int(10) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `target_id` mediumint(8) unsigned NOT NULL,
  `source_id` mediumint(8) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL,
  `position` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `notice_count` smallint(6) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` smallint(6) unsigned NOT NULL,
  `sortid` tinyint(1) unsigned NOT NULL DEFAULT '99',
  `block` varchar(10) NOT NULL DEFAULT '',
  `label` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `key` varchar(50) NOT NULL,
  `value_type` varchar(10) NOT NULL DEFAULT 'text',
  `value` text NOT NULL,
  `option` text NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `setting`
--

INSERT INTO `setting` (`id`, `sortid`, `block`, `label`, `type`, `key`, `value_type`, `value`, `option`, `description`) VALUES
(1, 1, 'info', '网站名称', 'text', 'site_name', 'text', '极简论坛', '', '考虑到手机浏览，网站名称不要设置过长。'),
(2, 2, 'info', '网站副标题', 'text', 'slogan', 'text', '功能简单,界面简洁,移动优先', '', '15字以内'),
(3, 3, 'info', '网站描述', 'textarea', 'description', 'text', 'Simple Forum,极简论坛', '', '给搜索引擎看的，150字以内'),
(4, 4, 'info', '备案号', 'text', 'icp', 'text', '', '', '若有就填，如 京ICP证0603xx号'),
(5, 5, 'info', '管理员邮箱', 'text', 'admin_email', 'text', '', '', '用来接收用户错误报告'),
(6, 1, 'manage', '网站暂时关闭', 'select', 'offline', 'integer', '0', '["0(开启)","1(关闭)"]', '默认:0(开启)'),
(7, 2, 'manage', '网站暂时关闭提示', 'textarea', 'offline_msg', 'text', '网站维护中，请稍后访问', '', '简单写明关闭原因'),
(8, 3, 'manage', '只允许登录访问', 'select', 'access_auth', 'integer', '0', '["0(公开)","1(登录访问)"]', '默认0（公开），若规定登录用户才能访问就设为1（适合内部交流）'),
(9, 4, 'manage', '注册需邮箱验证', 'select', 'email_verify', 'integer', '0', '["0(关闭验证)","1(开启验证)"]', '建议设为1（需验证），若不需要验证就设为0'),
(10, 5, 'manage', '注册需管理员验证', 'select', 'admin_verify', 'integer', '0', '["0(关闭验证)","1(开启验证)"]', '默认0（不用验证），若需要管理员验证就设为1（适合内部交流）'),
(11, 6, 'manage', '关闭用户注册', 'select', 'close_register', 'integer', '0', '["0(开启注册)","1(关闭注册)"]', '默认0，若停止新用户注册就设为1（仍旧可以通过第三方帐号登录方式注册）'),
(12, 7, 'manage', '过滤用户名', 'text', 'username_filter', 'text', '', '', '指定用户名不能含有某些指定词汇，用半角逗号(,)分割，例：<br />admin,webmaster,admin*'),
(13, 8, 'manage', '开启验证码', 'select', 'captcha_enabled', 'integer', '0', '["0(关闭","1(开启)"]', '开启后，注册和登录时会要求输入验证码'),
(14, 1, 'extend', '放在页面头部<br/>head标签里面的<br/>meta或其它信息', 'textarea', 'head_meta', 'text', '', '', '示例:<br/>&lt;meta property="qc:admins" content="331146677212163161xxxxxxx" /&gt;<br/>&lt;meta name="cpalead-verification" content="ymEun344mP9vt-B2idFRxxxxxxx" /&gt;'),
(15, 2, 'extend', '放在页面底部的<br/>统计代码', 'textarea', 'analytics_code', 'text', '', '', '示例： 直接粘贴google 或 百度统计代码'),
(16, 3, 'extend', '底部链接', 'textarea', 'footer_links', 'text', '', '', '一行一个链接，格式： 描述 http://url<br />如：关于本站 http://simpleforum.org/t/1'),
(17, 4, 'extend', '时区', 'select', 'timezone', 'text', 'Asia/Shanghai', '', '修改时要特别注意！默认Asia/Shanghai'),
(18, 5, 'extend', '编辑器', 'select', 'editor', 'text', 'wysibb', '{"wysibb":"Wysibb编辑器(BBCode)","smd":"SimpleMarkdown编辑器"}', '普通论坛推荐Wysibb编辑器(BBCode)，技术类论坛推荐SimpleMarkdown编辑器。注意：换编辑器可能会使以前发的帖子格式混乱。'),
(19, 1, 'cache', '开启缓存', 'select', 'cache_enabled', 'integer', '0', '["0(关闭)","1(开启)"]', '默认0（不开启）'),
(20, 2, 'cache', '缓存时间(分)', 'text', 'cache_time', 'integer', '10', '', '默认10分'),
(21, 3, 'cache', '缓存类型', 'select', 'cache_type', 'text', 'file', '{"file":"file","apc":"apc","memcache":"memcache 或 memcached"}', '默认file'),
(22, 4, 'cache', '缓存服务器', 'textarea', 'cache_servers', 'text', '', '', '缓存类型设为MemCache时设置<br/>一个服务器一行，格式为：IP 端口 权重<br />示例：<br />127.0.0.1 11211 100<br />127.0.0.2 11211 200'),
(23, 1, 'auth', '开启第三方登录', 'select', 'auth_enabled', 'integer', '0', '["0(关闭)","1(开启)"]', ''),
(24, 1, 'auth.qq', 'appid', 'text', 'qq_appid', 'text', '', '', ''),
(25, 2, 'auth.qq', 'appkey', 'text', 'qq_appkey', 'text', '', '', ''),
(26, 3, 'auth.qq', 'scope', 'text', 'qq_scope', 'text', 'get_user_info', '', ''),
(27, 1, 'auth.weibo', 'App Key', 'text', 'wb_key', 'text', '', '', ''),
(28, 2, 'auth.weibo', 'App Secret', 'text', 'wb_secret', 'text', '', '', ''),
(29, 1, 'other', '首页显示帖子数', 'text', 'index_pagesize', 'integer', '20', '', '默认20'),
(30, 2, 'other', '每页显示帖子数', 'text', 'list_pagesize', 'integer', '20', '', '默认20'),
(31, 3, 'other', '每页显示回复数', 'text', 'comment_pagesize', 'integer', '20', '', '默认20'),
(32, 4, 'other', '最热主题数', 'text', 'hot_topic_num', 'integer', '10', '', '默认10'),
(33, 5, 'other', '最热节点数', 'text', 'hot_node_num', 'integer', '20', '', '默认20'),
(34, 6, 'other', '可编辑时间(分)', 'text', 'edit_space', 'integer', '30', '', '默认30，主题贴和回复发表后可修改时间。'),
(35, 7, 'other', 'static目录自定义网址', 'text', 'alias_static', 'text', '', '', '自定义web/static目录的网址，可用于CDN。例：http://static.simpleforum.org'),
(36, 8, 'other', '头像目录自定义网址', 'text', 'alias_avatar', 'text', '', '', '自定义web/avatar目录的网址，可用于CDN。例：http://avatar.simpleforum.org'),
(37, 9, 'other', '附件目录自定义网址', 'text', 'alias_upload', 'text', '', '', '自定义web/upload目录的网址，可用于CDN。例：http://upload.simpleforum.org'),
(38, 1, 'mailer', 'SMTP服务器', 'text', 'mailer_host', 'text', '', '', ''),
(39, 2, 'mailer', 'SMTP端口', 'text', 'mailer_port', 'integer', '', '', ''),
(40, 3, 'mailer', 'SMTP加密协议', 'text', 'mailer_encryption', 'text', '', '', '如ssl,tls等，不加密留空'),
(41, 4, 'mailer', 'SMTP验证邮箱', 'text', 'mailer_username', 'text', '', '', '请输入完整邮箱地址'),
(42, 5, 'mailer', 'SMTP验证密码', 'text', 'mailer_password', 'text', '', '', '验证邮箱的密码'),
(43, 1, 'upload', '头像上传', 'select', 'upload_avatar', 'text', 'local', '{"local":"上传到网站所在空间","remote":"上传到第三方空间"}', '默认:上传到网站所在空间'),
(44, 2, 'upload', '附件上传', 'select', 'upload_file', 'text', 'disable', '{"disable":"关闭上传","local":"上传到网站所在空间","remote":"上传到第三方空间"}', '默认:网站空间'),
(45, 3, 'upload', '附件上传条件(注册时间)', 'text', 'upload_file_regday', 'integer', '30', '', '默认：30天'),
(46, 3, 'upload', '附件上传条件(主题数)', 'text', 'upload_file_topicnum', 'integer', '20', '', '默认：20'),
(47, 4, 'upload', '第三方空间', 'select', 'upload_remote', 'text', '', '{"qiniu":"七牛云","upyun":"又拍云"}', ''),
(48, 5, 'upload', '第三方空间信息', 'text', 'upload_remote_info', 'text', '', '', '逗号分隔。又拍云：空间名,操作员,密码；<br />七牛：空间名,access key,secret key'),
(49, 6, 'upload', '第三方空间URL', 'text', 'upload_remote_url', 'text', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `siteinfo`
--

CREATE TABLE IF NOT EXISTS `siteinfo` (
  `id` smallint(6) unsigned NOT NULL,
  `nodes` smallint(6) unsigned NOT NULL DEFAULT '0',
  `users` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `topics` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `comments` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `siteinfo`
--

INSERT INTO `siteinfo` (`id`, `nodes`, `users`, `topics`, `comments`) VALUES
(1, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `topic_count` smallint(6) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tag_topic`
--

CREATE TABLE IF NOT EXISTS `tag_topic` (
  `id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `topic_id` mediumint(8) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `token`
--

CREATE TABLE IF NOT EXISTS `token` (
  `id` mediumint(8) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `ext` varchar(200) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `topic`
--

CREATE TABLE IF NOT EXISTS `topic` (
  `id` mediumint(8) unsigned NOT NULL,
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
  `title` char(120) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `topic`
--

INSERT INTO `topic` (`id`, `created_at`, `updated_at`, `replied_at`, `node_id`, `user_id`, `reply_id`, `alltop`, `top`, `invisible`, `closed`, `comment_closed`, `comment_count`, `favorite_count`, `views`, `title`) VALUES
(1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '仿照大师写的练手商场，采用先进框架架构，安利一下自己的作品，大家可以下下来玩玩'),
(2, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '说好的逗比呢'),
(3, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '又一个kotori_frame框架');

-- --------------------------------------------------------

--
-- 表的结构 `topic_content`
--

CREATE TABLE IF NOT EXISTS `topic_content` (
  `topic_id` mediumint(8) unsigned NOT NULL,
  `content` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `topic_content`
--

INSERT INTO `topic_content` (`topic_id`, `content`) VALUES
(2, '测试'),
(3, '测试用内容'),
(1, '瞎fv输入');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` mediumint(8) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '8',
  `role` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `username` char(16) NOT NULL,
  `email` char(50) NOT NULL,
  `password_hash` char(80) NOT NULL,
  `auth_key` char(32) NOT NULL,
  `avatar` char(50) NOT NULL DEFAULT 'avatar/0_{size}.png'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `created_at`, `updated_at`, `status`, `role`, `username`, `email`, `password_hash`, `auth_key`, `avatar`) VALUES
(1, 0, 0, 8, 0, '老金', '958142428@qq.com', '123', '', 'avatar/0_{size}.png');

-- --------------------------------------------------------

--
-- 表的结构 `user_info`
--

CREATE TABLE IF NOT EXISTS `user_info` (
  `user_id` mediumint(8) unsigned NOT NULL,
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
  `about` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_info`
--

INSERT INTO `user_info` (`user_id`, `last_login_at`, `last_login_ip`, `reg_ip`, `topic_count`, `comment_count`, `favorite_count`, `favorite_node_count`, `favorite_topic_count`, `favorite_user_count`, `website`, `about`) VALUES
(1, 0, 0, 0, 0, 0, 2, 0, 2, 0, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_source` (`user_id`,`source`),
  ADD UNIQUE KEY `source_source_id` (`source`,`source_id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`topic_id`,`position`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`,`id`),
  ADD KEY `topic_updated` (`topic_id`,`updated_at`);

--
-- Indexes for table `commentid`
--
ALTER TABLE `commentid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorite`
--
ALTER TABLE `favorite`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source_type_target` (`source_id`,`type`,`target_id`),
  ADD KEY `type_target` (`type`,`target_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`id`);

--
-- Indexes for table `link`
--
ALTER TABLE `link`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort_id` (`sortid`,`id`);

--
-- Indexes for table `node`
--
ALTER TABLE `node`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `ename` (`ename`),
  ADD KEY `topic_id` (`topic_count`,`id`);

--
-- Indexes for table `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `target_status_id` (`target_id`,`status`,`id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `block_sort_id` (`block`,`sortid`,`id`);

--
-- Indexes for table `siteinfo`
--
ALTER TABLE `siteinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tag_topic`
--
ALTER TABLE `tag_topic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tag_topic` (`tag_id`,`topic_id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_type_expires` (`user_id`,`type`,`expires`);

--
-- Indexes for table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alllist` (`alltop`,`replied_at`,`id`),
  ADD KEY `nodelist` (`node_id`,`top`,`replied_at`,`id`),
  ADD KEY `hottopics` (`created_at`,`comment_count`,`replied_at`),
  ADD KEY `updated` (`updated_at`),
  ADD KEY `node_updated` (`node_id`,`updated_at`),
  ADD KEY `user_id` (`user_id`,`id`);

--
-- Indexes for table `topic_content`
--
ALTER TABLE `topic_content`
  ADD PRIMARY KEY (`topic_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `status_id` (`status`,`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth`
--
ALTER TABLE `auth`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `position` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commentid`
--
ALTER TABLE `commentid`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `favorite`
--
ALTER TABLE `favorite`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `link`
--
ALTER TABLE `link`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `node`
--
ALTER TABLE `node`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `notice`
--
ALTER TABLE `notice`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT for table `siteinfo`
--
ALTER TABLE `siteinfo`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tag_topic`
--
ALTER TABLE `tag_topic`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `token`
--
ALTER TABLE `token`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `topic`
--
ALTER TABLE `topic`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `topic_content`
--
ALTER TABLE `topic_content`
  MODIFY `topic_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
