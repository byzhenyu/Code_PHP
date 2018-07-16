/*
Navicat MySQL Data Transfer

Source Server         : PHP一组
Source Server Version : 50629
Source Host           : 47.92.120.200:3306
Source Database       : mimaceshi

Target Server Type    : MYSQL
Target Server Version : 50629
File Encoding         : 65001

Date: 2018-07-16 17:09:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ln_password_error_count`
-- ----------------------------
DROP TABLE IF EXISTS `ln_password_error_count`;
CREATE TABLE `ln_password_error_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `count` tinyint(1) NOT NULL DEFAULT '0' COMMENT '错误次数',
  `add_time` int(11) DEFAULT '0' COMMENT '记录时间',
  `over_time` int(11) DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='密码错误次数表';

-- ----------------------------
-- Records of ln_password_error_count
-- ----------------------------
