/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50613
Source Host           : localhost:3306
Source Database       : resetpwd

Target Server Type    : MYSQL
Target Server Version : 50613
File Encoding         : 65001

Date: 2013-12-14 20:56:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tbl_user`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户的Id',
  `email` varchar(150) NOT NULL COMMENT '用户的email',
  `passwd` varchar(150) NOT NULL COMMENT '用户密码',
  `is_reset_pwd` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '密码是否重置成功',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tbl_user
-- ----------------------------
INSERT INTO `tbl_user` VALUES ('1', 'lx.xin@qq.com', '1234', '1');
