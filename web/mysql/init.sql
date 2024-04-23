# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 8.0.31)
# Database: kaliphp
# Generation Time: 2023-01-20 08:13:47 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table kali_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_config`;

CREATE TABLE `kali_config` (
  `sort` smallint NOT NULL DEFAULT '0' COMMENT '排序id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '变量名',
  `value` text COMMENT '变量值',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '说明标题',
  `info` varchar(200) NOT NULL COMMENT '备注',
  `groupid` smallint unsigned NOT NULL DEFAULT '1' COMMENT '分组',
  `type` varchar(10) NOT NULL DEFAULT 'string' COMMENT '变量类型',
  `group` varchar(10) NOT NULL DEFAULT 'config',
  PRIMARY KEY (`name`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='系统配置变量表';

LOCK TABLES `kali_config` WRITE;
/*!40000 ALTER TABLE `kali_config` DISABLE KEYS */;

INSERT INTO `kali_config` (`sort`, `name`, `value`, `title`, `info`, `groupid`, `type`, `group`)
VALUES
	(1,'app_version','{&quot;web&quot;:{&quot;app_url&quot;:&quot;&quot;,&quot;version&quot;:&quot;&quot;,&quot;tip&quot;:&quot;&quot;,&quot;state&quot;:&quot;0&quot;},&quot;ios&quot;:{&quot;app_url&quot;:&quot;https://apps.apple.com/cn/app/cable-messenger/id1281191882&quot;,&quot;compulsory_version&quot;:&quot;2.4.4&quot;,&quot;version&quot;:&quot;2.5.0&quot;,&quot;tip&quot;:&quot;I have a fully upgraded version\\nGo update me and start a new experience~&quot;,&quot;compulsory_tpl&quot;:{&quot;zh-cn&quot;:&quot;版本全面升级啦\\n快去更新开启新体验吧～&quot;,&quot;en&quot;:&quot;I have a fully upgraded version\\nGo update me and start a new experience~&quot;},&quot;tpl&quot;:{&quot;zh-cn&quot;:&quot;您赏脸更新一下呗？新版好好用的哦～&quot;,&quot;en&quot;:&quot;Would you please update me? The new version is better for you~&quot;},&quot;state&quot;:&quot;1&quot;},&quot;android&quot;:{&quot;app_url&quot;:&quot;&quot;,&quot;version&quot;:&quot;&quot;,&quot;tip&quot;:&quot;&quot;,&quot;state&quot;:&quot;0&quot;},&quot;macos&quot;:{&quot;app_url&quot;:&quot;macappstore://itunes.apple.com/app/id1281191882&quot;,&quot;compulsory_version&quot;:&quot;1.5.5&quot;,&quot;version&quot;:&quot;1.6.0&quot;,&quot;tip&quot;:&quot;I have a fully upgraded version\\nGo update me and start a new experience~&quot;,&quot;tpl&quot;:{&quot;zh-cn&quot;:&quot;版本全面升级啦\\n快去更新开启新体验吧～&quot;,&quot;en&quot;:&quot;I have a fully upgraded version\\nGo update me and start a new experience~&quot;},&quot;compulsory_tpl&quot;:{&quot;zh-cn&quot;:&quot;版本全面升级啦\\n快去更新开启新体验吧～&quot;,&quot;en&quot;:&quot;I have a fully upgraded version\\nGo update me and start a new experience~&quot;},&quot;state&quot;:&quot;1&quot;}}','app版本号配置','',1,'string','config');

/*!40000 ALTER TABLE `kali_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kali_admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_admin`;

CREATE TABLE `kali_admin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'UID',
  `utma` char(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `groups` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '权限组',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户名',
  `realname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '真实姓名',
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户密码',
  `fake_password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `onetime_password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '一次性密码',
  `remember` tinyint(1) DEFAULT '0',
  `seclogin` tinyint(1) DEFAULT '0',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '头像',
  `safe_ips` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '登陆IP白名单',
  `date_expired` datetime DEFAULT '2088-06-06 00:00:00' COMMENT '失效日期时间',
  `otp_secret` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '动态口令(One-Time Password)',
  `session_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `session_expire` int DEFAULT '1440' COMMENT 'SESSION有效期，默认24分钟',
  `is_first_login` tinyint(1) DEFAULT '1',
  `otp_authcode` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:正常 0:禁用',
  `status_err` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '状态错误信息',
  `logintime` int DEFAULT NULL,
  `loginip` char(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `regtime` int DEFAULT NULL,
  `regip` char(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `username` (`username`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户表';

LOCK TABLES `kali_admin` WRITE;
/*!40000 ALTER TABLE `kali_admin` DISABLE KEYS */;

INSERT INTO `kali_admin` (`id`, `uid`, `utma`, `groups`, `username`, `realname`, `password`, `fake_password`, `onetime_password`, `remember`, `seclogin`, `email`, `avatar`, `safe_ips`, `date_expired`, `otp_secret`, `session_id`, `session_expire`, `is_first_login`, `otp_authcode`, `status`, `status_err`, `logintime`, `loginip`, `regtime`, `regip`)
VALUES
	(1,'8453d2d6b3cf6c18','0fdca5d577d3fac9e17967f3099cdbb7','1','admin','超级管理员','$2y$10$0OrD7BK37PlFuokHaK1qoexO.JOzTSTAWXufZnyenHiYznNyzByz2','','',0,0,'','',NULL,'2088-06-06 00:00:00','','5nll78m0i0nlb7m4t4p4jbqns8',1440,0,'',1,NULL,1674202217,'127.0.0.1',1674202217,'127.0.0.1');

/*!40000 ALTER TABLE `kali_admin` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kali_admin_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_admin_group`;

CREATE TABLE `kali_admin_group` (
  `id` char(32) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` varchar(20) DEFAULT NULL COMMENT '用户组名称',
  `purviews` text NOT NULL COMMENT '用户组权限',
  `uptime` int DEFAULT NULL COMMENT '修改时间',
  `addtime` int DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='用户组表';

LOCK TABLES `kali_admin_group` WRITE;
/*!40000 ALTER TABLE `kali_admin_group` DISABLE KEYS */;

INSERT INTO `kali_admin_group` (`id`, `name`, `purviews`, `uptime`, `addtime`)
VALUES
	('1','超级管理员','*',1674202217,1674202217);

/*!40000 ALTER TABLE `kali_admin_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kali_admin_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_admin_log`;

CREATE TABLE `kali_admin_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'UID',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户名',
  `realname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '昵称',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '操作',
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '访问URL',
  `method` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NONE' COMMENT '请求方式',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `country` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'LOC' COMMENT 'IP定位',
  `content` json NOT NULL COMMENT '消息内容',
  `cli_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'MD5(账号+IP), 同一个IP只能错误3次',
  `isalert` tinyint(1) DEFAULT '0',
  `isread` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1=正常，0=异常',
  `status_err` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '失败原因',
  `session_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'SESSION ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int DEFAULT '0' COMMENT '更新用户',
  PRIMARY KEY (`id`),
  KEY `do_time` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户操作日志';



# Dump of table kali_admin_login
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_admin_login`;

CREATE TABLE `kali_admin_login` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` char(32) NOT NULL DEFAULT '' COMMENT '用户ID',
  `username` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `session_id` char(26) DEFAULT NULL COMMENT 'SESSION ID',
  `agent` varchar(500) DEFAULT NULL COMMENT '浏览器信息',
  `logintime` int unsigned NOT NULL COMMENT '登录时间',
  `loginip` varchar(15) NOT NULL DEFAULT '' COMMENT '登录IP',
  `logincountry` varchar(2) DEFAULT NULL COMMENT '登陆国家',
  `loginsta` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '登录时状态 1=成功，0=失败',
  `cli_hash` varchar(32) NOT NULL COMMENT '用户登录名和ip的hash',
  PRIMARY KEY (`id`),
  KEY `logintime` (`logintime`),
  KEY `cli_hash` (`cli_hash`,`loginsta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='用户登陆记录表';



# Dump of table kali_admin_oplog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_admin_oplog`;

CREATE TABLE `kali_admin_oplog` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '项id',
  `uid` char(32) DEFAULT NULL COMMENT '用户ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `session_id` char(26) DEFAULT NULL COMMENT 'SESSION ID',
  `msg` varchar(250) NOT NULL COMMENT '消息内容',
  `do_time` int unsigned NOT NULL COMMENT '发生时间',
  `do_ip` varchar(15) NOT NULL COMMENT '客户端IP',
  `do_country` char(2) NOT NULL DEFAULT '' COMMENT '国家',
  `do_url` varchar(100) NOT NULL COMMENT '操作网址',
  PRIMARY KEY (`id`),
  KEY `user_name` (`username`),
  KEY `do_time` (`do_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='用户操作日志';



# Dump of table kali_admin_purview
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_admin_purview`;

CREATE TABLE `kali_admin_purview` (
  `uid` char(32) NOT NULL DEFAULT '' COMMENT '管理员ID',
  `purviews` text NOT NULL COMMENT '配置字符',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='用户权限表';



# Dump of table kali_admin_session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_admin_session`;

CREATE TABLE `kali_admin_session` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'UID',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户名',
  `realname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '昵称',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录IP',
  `country` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '登陆国家',
  `cli_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'MD5(账号+IP), 同一个IP只能错误3次',
  `utma` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `os_version` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `os` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `device` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `device_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `loginip` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logintime` int DEFAULT NULL,
  `app_name` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '状态 1=成功，0=失败',
  `status_err` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '失败原因',
  `session_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '会话ID',
  `session_ttl` int DEFAULT NULL COMMENT '会话存活时间',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int unsigned DEFAULT '0' COMMENT '更新用户',
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户登陆记录表';



# Dump of table kali_bot
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_bot`;

CREATE TABLE `kali_bot` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '内容表',
  `cid` smallint DEFAULT NULL COMMENT '分类ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `image` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '封面图',
  `images` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '套图',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '内容',
  `status` tinyint DEFAULT NULL COMMENT '状态 0=禁用 1=启用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int DEFAULT '0' COMMENT '更新用户',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `deletor_id` int DEFAULT '0' COMMENT '删除用户',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='内容表';



# Dump of table kali_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_category`;

CREATE TABLE `kali_category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '分类表',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `sort` int DEFAULT '100' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int DEFAULT '0' COMMENT '更新用户',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `deletor_id` int DEFAULT '0' COMMENT '删除用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='分类表';



# Dump of table kali_content
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_content`;

CREATE TABLE `kali_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '内容表',
  `cid` smallint DEFAULT NULL COMMENT '分类ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `image` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '封面图',
  `images` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '套图',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '内容',
  `status` tinyint DEFAULT NULL COMMENT '状态 0=禁用 1=启用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int DEFAULT '0' COMMENT '更新用户',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `deletor_id` int DEFAULT '0' COMMENT '删除用户',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='内容表';



# Dump of table kali_crond
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_crond`;

CREATE TABLE `kali_crond` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sort` smallint NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务名',
  `filename` varchar(248) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '执行脚本',
  `runtime_format` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '时间格式',
  `lasttime` int unsigned NOT NULL DEFAULT '0' COMMENT '最后执行时间',
  `runtime` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '运行时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：1=启动 0=停止',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int DEFAULT '0' COMMENT '更新用户',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `deletor_id` int DEFAULT '0' COMMENT '删除用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='计划任务表';

# Dump of table kali_member
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_member`;

CREATE TABLE `kali_member` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'UID',
  `utma` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `groups` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '权限组',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户名',
  `age` int DEFAULT NULL,
  `address` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `realname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '真实姓名',
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户密码',
  `remember` tinyint(1) DEFAULT '0',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '头像',
  `safe_ips` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '登陆IP白名单',
  `date_expired` datetime DEFAULT '2088-06-06 00:00:00' COMMENT '失效日期时间',
  `otp_secret` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '动态口令(One-Time Password)',
  `session_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `session_expire` int DEFAULT '1440' COMMENT 'SESSION有效期，默认24分钟',
  `is_first_login` tinyint(1) DEFAULT '1',
  `otp_authcode` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:正常 0:禁用',
  `status_err` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '状态错误信息',
  `logintime` int DEFAULT NULL,
  `loginip` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `regtime` int DEFAULT NULL,
  `create_user` int DEFAULT NULL,
  `create_time` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `username` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户表';



# Dump of table kali_migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_migrations`;

CREATE TABLE `kali_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `version` bigint DEFAULT NULL COMMENT '迁移版本,年月日时分秒毫秒',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `start_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '开始时间',
  `end_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '结束时间',
  `breakpoint` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='数据库迁移记录';



# Dump of table kali_setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_setting`;

CREATE TABLE `kali_setting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int DEFAULT NULL COMMENT '设置组ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '描述',
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '代码',
  `content` json DEFAULT NULL COMMENT '设置配置及内容',
  `sort_number` int DEFAULT '1000' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int DEFAULT '0' COMMENT '更新用户',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `deletor_id` int DEFAULT '0' COMMENT '删除用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `kali_setting` WRITE;
/*!40000 ALTER TABLE `kali_setting` DISABLE KEYS */;

INSERT INTO `kali_setting` (`id`, `group_id`, `name`, `description`, `code`, `content`, `sort_number`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `deleted_at`, `deletor_id`)
VALUES
	(1,1,'基本设置','后台基本信息设置','base','[{\"type\": \"input\", \"field\": \"name\", \"title\": \"后台名称\", \"value\": \"Golang Framework\"}, {\"type\": \"input\", \"field\": \"copyright\", \"title\": \"版权所有\", \"value\": \"Golang Community\"}, {\"type\": \"switch\", \"field\": \"status\", \"props\": {\"active-value\": \"1\", \"inactive-value\": \"0\"}, \"title\": \"是否启用\", \"value\": \"1\"}]',1000,'2022-06-03 13:14:43',1,'2022-07-12 16:39:08',1,NULL,NULL),
	(2,1,'登陆设置','后台登陆相关设置','login','[{\"type\": \"switch\", \"field\": \"token\", \"props\": {\"active-value\": \"1\", \"inactive-value\": \"0\"}, \"title\": \"登录token验证\", \"value\": \"1\"}, {\"type\": \"select\", \"field\": \"captcha\", \"title\": \"验证码\", \"value\": \"0\", \"options\": [{\"label\": \"不开启\", \"value\": \"0\", \"disabled\": false}, {\"label\": \"图形验证码\", \"value\": \"1\", \"disabled\": false}, {\"label\": \"滑动验证\", \"value\": \"2\", \"disabled\": false}]}, {\"type\": \"upload\", \"field\": \"background\", \"props\": {\"limit\": 1, \"action\": \"/upload/image\"}, \"title\": \"登录背景\", \"value\": \"/uploads/uploads/20220604/545b79ebe94fd027e3a700f49441573d.jpg\", \"listType\": \"picture\", \"onSuccess\": \"function (file){file.url = file.response.url;}\"}, {\"type\": \"switch\", \"field\": \"login_limit\", \"props\": {\"active-value\": \"1\", \"inactive-value\": \"0\"}, \"title\": \"登录重试限制\", \"value\": \"1\"}, {\"type\": \"number\", \"field\": \"login_limit_count\", \"title\": \"登录重试次数\", \"value\": 5}, {\"type\": \"number\", \"field\": \"login_limit_hour\", \"title\": \"禁止登录时长(小时)\", \"value\": 2}]',1000,'2022-06-03 13:15:00',1,'2022-07-18 14:32:51',1,NULL,NULL),
	(3,1,'安全设置','后台安全相关设置','safe','[{\"type\": \"input\", \"field\": \"ipwhitelist\", \"props\": {\"type\": \"textarea\", \"autosize\": {\"maxRows\": 40, \"minRows\": 3}, \"placeholder\": \"0.0.0.0\"}, \"title\": \"IP白名单(逗号隔开)\", \"value\": \"::1,127.0.0.1\"}, {\"type\": \"input\", \"field\": \"ip-tips\", \"props\": {\"type\": \"textarea\", \"autosize\": {\"maxRows\": 40, \"minRows\": 1}, \"readonly\": true}, \"title\": \" \", \"value\": \"这里输入提示文字，\\n支持换行\"}, {\"type\": \"input\", \"field\": \"secret_key\", \"title\": \"接口密钥\", \"value\": \"cac118f5baaa18735aa2617055338679\"}, {\"type\": \"switch\", \"field\": \"password_check\", \"props\": {\"active-value\": \"1\", \"inactive-value\": \"0\"}, \"title\": \"用户密码强度检测\", \"value\": \"1\"}, {\"type\": \"select\", \"field\": \"password_level\", \"title\": \"密码安全强度等级\", \"value\": \"2\", \"options\": [{\"label\": \"简单密码\", \"value\": \"1\", \"disabled\": false}, {\"label\": \"中等密码\", \"value\": \"2\", \"disabled\": false}, {\"label\": \"复杂密码\", \"value\": \"3\", \"disabled\": false}]}, {\"type\": \"switch\", \"field\": \"one_device_login\", \"props\": {\"active-value\": \"1\", \"inactive-value\": \"0\"}, \"title\": \"单设备登录\", \"value\": \"1\"}, {\"type\": \"switch\", \"field\": \"check_token\", \"props\": {\"active-value\": \"1\", \"inactive-value\": \"0\"}, \"title\": \"CSRFToken 检测\", \"value\": \"1\"}, {\"type\": \"select\", \"field\": \"check_token_action_list\", \"props\": {\"multiple\": true}, \"title\": \"CSRFToken 验证方法\", \"value\": \"\", \"options\": [{\"label\": \"添加\", \"value\": \"POST\", \"disabled\": false}, {\"label\": \"修改\", \"value\": \"PUT\", \"disabled\": false}, {\"label\": \"删除\", \"value\": \"DELETE\", \"disabled\": false}]}]',1000,'2022-06-03 13:15:20',1,'2022-07-15 14:27:15',1,NULL,NULL);

/*!40000 ALTER TABLE `kali_setting` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kali_setting_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kali_setting_group`;

CREATE TABLE `kali_setting_group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `module` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '作用模块',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '描述',
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '代码',
  `sort_number` int DEFAULT '1000' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `creator_id` int DEFAULT '0' COMMENT '创建用户',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `updator_id` int DEFAULT '0' COMMENT '更新用户',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `deletor_id` int DEFAULT '0' COMMENT '删除用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `kali_setting_group` WRITE;
/*!40000 ALTER TABLE `kali_setting_group` DISABLE KEYS */;

INSERT INTO `kali_setting_group` (`id`, `module`, `name`, `description`, `code`, `sort_number`, `created_at`, `creator_id`, `updated_at`, `updator_id`, `deleted_at`, `deletor_id`)
VALUES
	(1,'admin','后台设置','后台管理方面的设置','admin',1000,'2022-06-03 13:12:53',1,'2022-06-22 13:12:55',1,NULL,NULL);

/*!40000 ALTER TABLE `kali_setting_group` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
