/*
 Navicat Premium Data Transfer

 Source Server         : gouyu
 Source Server Type    : MySQL
 Source Server Version : 50557
 Source Host           : localhost:3306
 Source Schema         : kkl28

 Target Server Type    : MySQL
 Target Server Version : 50557
 File Encoding         : 65001

 Date: 23/08/2019 16:24:43
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tp_account
-- ----------------------------
DROP TABLE IF EXISTS `tp_account`;
CREATE TABLE `tp_account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `change_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '1在线充值(cz打头)，2银行汇入(yh打头)，3手动录入，4支付宝付款，5订单消费，6手动扣款，7提现，8订单退款',
  `amount_in` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '入账金额',
  `amount_out` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '出账金额',
  `amount_before_pay` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '变动前预存款金额',
  `amount_after_pay` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '变动后预存款结余',
  `order_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '相关订单号，如订单编号、充值单号等',
  `operater` int(11) NOT NULL DEFAULT '0' COMMENT '操作人',
  `pay_code` varchar(32) NOT NULL DEFAULT '' COMMENT '第三方支付平台返回的交易码',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '记录生成时间',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `proof` varchar(255) NOT NULL DEFAULT '' COMMENT '支付凭证号，如支付宝交易号、银行流水号等',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT '操作人IP',
  `role_type` tinyint(3) unsigned DEFAULT '0' COMMENT '角色类型，2商家，3用户，4镖师，用于导出财务报表时的筛选',
  `account_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '财务类型，1贡献奖励，2分润，3购物币，4钱包币，5红包）',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0可用   1已撤销',
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=822805 DEFAULT CHARSET=utf8 COMMENT='账户变动明细表';

-- ----------------------------
-- Table structure for tp_account_apply
-- ----------------------------
DROP TABLE IF EXISTS `tp_account_apply`;
CREATE TABLE `tp_account_apply` (
  `account_apply_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '入账金额',
  `order_id` varchar(16) NOT NULL DEFAULT '0' COMMENT '相关订单号，如订单编号、充值单号等',
  `apply_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '申请状态，0未处理，1通过，2拒绝',
  `agent_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '代理商备注',
  `admin_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员备注',
  `operater` int(11) NOT NULL DEFAULT '0' COMMENT '操作人',
  `proof` varchar(255) NOT NULL DEFAULT '' COMMENT '支付凭证号，如支付宝交易号、银行流水号等',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '记录生成时间',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT '操作人IP',
  PRIMARY KEY (`account_apply_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='入账申请表';

-- ----------------------------
-- Table structure for tp_address_area
-- ----------------------------
DROP TABLE IF EXISTS `tp_address_area`;
CREATE TABLE `tp_address_area` (
  `area_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(50) NOT NULL COMMENT '县级市或区或县名',
  `city_id` mediumint(5) NOT NULL DEFAULT '0' COMMENT '地级市id',
  `py` char(1) NOT NULL DEFAULT '' COMMENT '拼音首字母',
  `is_open` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开通，1是，0否',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开通时间',
  PRIMARY KEY (`area_id`),
  KEY `city_id` (`city_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=659017 DEFAULT CHARSET=utf8 COMMENT='县级市或区或县表';

-- ----------------------------
-- Table structure for tp_address_city
-- ----------------------------
DROP TABLE IF EXISTS `tp_address_city`;
CREATE TABLE `tp_address_city` (
  `city_id` mediumint(5) NOT NULL AUTO_INCREMENT COMMENT '城市ID',
  `city_name` varchar(50) NOT NULL COMMENT '地级市名',
  `province_id` mediumint(2) NOT NULL DEFAULT '0' COMMENT '省id',
  `py` char(1) NOT NULL DEFAULT '' COMMENT '拼音首字母',
  `is_open` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开通，1是，0否',
  PRIMARY KEY (`city_id`),
  KEY `province_id` (`province_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=659001 DEFAULT CHARSET=utf8 COMMENT='地级市表';

-- ----------------------------
-- Table structure for tp_address_province
-- ----------------------------
DROP TABLE IF EXISTS `tp_address_province`;
CREATE TABLE `tp_address_province` (
  `province_id` mediumint(2) NOT NULL AUTO_INCREMENT COMMENT '省份ID',
  `province_name` varchar(50) NOT NULL COMMENT '省',
  PRIMARY KEY (`province_id`)
) ENGINE=MyISAM AUTO_INCREMENT=820001 DEFAULT CHARSET=utf8 COMMENT='省表';

-- ----------------------------
-- Table structure for tp_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_admin_log`;
CREATE TABLE `tp_admin_log` (
  `admin_log_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员user_id',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '操作类型 1管理员登录 2 管理员修改排行榜',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`admin_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tp_adv
-- ----------------------------
DROP TABLE IF EXISTS `tp_adv`;
CREATE TABLE `tp_adv` (
  `adv_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `adv_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '广告位类型，1顶部广告位，2其他，以后扩张用',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '广告标题',
  `link` varchar(128) NOT NULL DEFAULT '' COMMENT '链接',
  `pic` varchar(128) NOT NULL DEFAULT '' COMMENT '图片路径',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '有效性，0不显示，1显示，2已删除',
  PRIMARY KEY (`adv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='广告位表';

-- ----------------------------
-- Table structure for tp_agent_income
-- ----------------------------
DROP TABLE IF EXISTS `tp_agent_income`;
CREATE TABLE `tp_agent_income` (
  `agent_income_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` int(10) NOT NULL DEFAULT '0' COMMENT '分销商id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '客户id',
  `isuse` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1可用,2删除',
  `money` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `gain_money` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '收益',
  `addtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`agent_income_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='分销商利润表';

-- ----------------------------
-- Table structure for tp_agent_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_agent_user`;
CREATE TABLE `tp_agent_user` (
  `agent_user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL COMMENT '代理商id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `addtime` int(11) NOT NULL,
  PRIMARY KEY (`agent_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代理商下级用户';

-- ----------------------------
-- Table structure for tp_android_version
-- ----------------------------
DROP TABLE IF EXISTS `tp_android_version`;
CREATE TABLE `tp_android_version` (
  `android_version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
  `remark` text NOT NULL COMMENT '日志',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '下载地址',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0不需要更新, 1需要更新',
  PRIMARY KEY (`android_version_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='安卓版本';

-- ----------------------------
-- Table structure for tp_article
-- ----------------------------
DROP TABLE IF EXISTS `tp_article`;
CREATE TABLE `tp_article` (
  `article_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '文章作者',
  `article_source` varchar(128) NOT NULL DEFAULT '' COMMENT '文章来源',
  `article_tag` varchar(32) NOT NULL DEFAULT '' COMMENT '文章标记，标记特定文章，如退换货说明，分销商招募说明等都有标记',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '文章简介，SEO用',
  `path_img` varchar(128) NOT NULL DEFAULT '' COMMENT '文章主图',
  `clickdot` int(11) NOT NULL DEFAULT '0' COMMENT '点击量',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `serial` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否显示，1是，0否',
  `contents` text COMMENT '文章内容',
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Table structure for tp_article_sort
-- ----------------------------
DROP TABLE IF EXISTS `tp_article_sort`;
CREATE TABLE `tp_article_sort` (
  `article_sort_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `article_sort_name` varchar(128) NOT NULL DEFAULT '' COMMENT '分类名称',
  `article_sort_logo` varchar(128) NOT NULL DEFAULT '' COMMENT '分类LOGO',
  `description` text NOT NULL COMMENT '分类备注',
  `serial` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用，1是，0否',
  PRIMARY KEY (`article_sort_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='文章分类表';

-- ----------------------------
-- Table structure for tp_article_txt
-- ----------------------------
DROP TABLE IF EXISTS `tp_article_txt`;
CREATE TABLE `tp_article_txt` (
  `article_txt_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `article_id` smallint(6) unsigned NOT NULL COMMENT '文章ID',
  `contents` text COMMENT '文章详情',
  PRIMARY KEY (`article_txt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=190 DEFAULT CHARSET=utf8 COMMENT='文章详情表';

-- ----------------------------
-- Table structure for tp_article_txt_photo
-- ----------------------------
DROP TABLE IF EXISTS `tp_article_txt_photo`;
CREATE TABLE `tp_article_txt_photo` (
  `article_txt_photo_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `article_id` int(11) NOT NULL DEFAULT '0' COMMENT '文章ID',
  `path_img` varchar(128) NOT NULL DEFAULT '' COMMENT '文章详情图片地址',
  PRIMARY KEY (`article_txt_photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章详情图片表';

-- ----------------------------
-- Table structure for tp_bank_card
-- ----------------------------
DROP TABLE IF EXISTS `tp_bank_card`;
CREATE TABLE `tp_bank_card` (
  `bank_card_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `bank_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '银行ID',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '银行卡卡号',
  `realname` varchar(8) NOT NULL DEFAULT '' COMMENT '持卡人姓名',
  `opening_bank` varchar(32) NOT NULL DEFAULT '' COMMENT '开户行',
  `bind_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绑定时间',
  PRIMARY KEY (`bank_card_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='银行卡表';

-- ----------------------------
-- Table structure for tp_bank_card_apply
-- ----------------------------
DROP TABLE IF EXISTS `tp_bank_card_apply`;
CREATE TABLE `tp_bank_card_apply` (
  `bank_card_apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `bank_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '银行ID',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '银行卡卡号',
  `realname` varchar(8) NOT NULL DEFAULT '' COMMENT '持卡人姓名',
  `opening_bank` varchar(32) NOT NULL DEFAULT '' COMMENT '开户行',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '打款金额',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提交申请时间',
  `tried_time` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '错误次数',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '申请状态，0未处理，1已打款，2已通过，3管理员拒绝(与营业执照上的姓名不一致），4所填款项与打款金额不一致\n或其他原因，详情见管理员备注)',
  PRIMARY KEY (`bank_card_apply_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='银行卡绑定申请表';

-- ----------------------------
-- Table structure for tp_bet_auto
-- ----------------------------
DROP TABLE IF EXISTS `tp_bet_auto`;
CREATE TABLE `tp_bet_auto` (
  `bet_auto_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL COMMENT '类型 1输赢变换 2对号投注',
  `game_type_id` int(11) NOT NULL COMMENT '游戏类型id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `start_issue` int(11) NOT NULL COMMENT '开始期号',
  `start_mode_id` int(11) NOT NULL COMMENT '开始模式',
  `new_mode_id` int(11) NOT NULL COMMENT '当前模式',
  `issue_number` int(11) NOT NULL COMMENT '投注期数',
  `max_money` double(20,0) NOT NULL COMMENT '最大金额',
  `min_money` double(20,0) NOT NULL COMMENT '最小金额',
  `is_open` tinyint(4) NOT NULL COMMENT '是否开始',
  `bet_mode_json` text NOT NULL COMMENT '投注json',
  `bet_issue_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已经投注期数',
  PRIMARY KEY (`bet_auto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COMMENT='自动投注';

-- ----------------------------
-- Table structure for tp_bet_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_bet_log`;
CREATE TABLE `tp_bet_log` (
  `bet_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `game_result_id` int(11) NOT NULL COMMENT '游戏结果id',
  `game_type_id` int(11) NOT NULL COMMENT '游戏类型id',
  `total_bet_money` float(11,2) NOT NULL DEFAULT '0.00' COMMENT '投注总额',
  `bet_json` text NOT NULL COMMENT '投注json',
  `total_after_money` float(11,2) NOT NULL DEFAULT '0.00' COMMENT '投注结果金额',
  `is_win` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否中奖',
  `addtime` int(11) NOT NULL COMMENT '时间',
  `is_auto_bet` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否是自动投注',
  `bet_auto_id` int(11) NOT NULL DEFAULT '0' COMMENT '自动投注id',
  `is_open` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开奖 0 未开奖 1开奖',
  PRIMARY KEY (`bet_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=218173 DEFAULT CHARSET=utf8mb4 COMMENT='投注记录';



-- ----------------------------
-- Table structure for tp_bet_mode
-- ----------------------------
DROP TABLE IF EXISTS `tp_bet_mode`;
CREATE TABLE `tp_bet_mode` (
  `bet_mode_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '投注模式',
  `mode_name` varchar(255) NOT NULL COMMENT '模式名称',
  `bet_json` text NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `game_type_id` int(11) NOT NULL COMMENT '游戏类型',
  `win_change` int(11) NOT NULL COMMENT '赢变换模式id',
  `loss_change` int(11) NOT NULL COMMENT '输变换模式id',
  `total_money` int(11) NOT NULL COMMENT '投注总数',
  PRIMARY KEY (`bet_mode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COMMENT='投注模式';

-- ----------------------------
-- Table structure for tp_big_area
-- ----------------------------
DROP TABLE IF EXISTS `tp_big_area`;
CREATE TABLE `tp_big_area` (
  `big_area_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `area_name` varchar(16) NOT NULL DEFAULT '' COMMENT '大区名称',
  `province_ids` varchar(255) NOT NULL DEFAULT '0' COMMENT '省份ids',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`big_area_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='大区表';

-- ----------------------------
-- Table structure for tp_brand
-- ----------------------------
DROP TABLE IF EXISTS `tp_brand`;
CREATE TABLE `tp_brand` (
  `brand_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `brand_name` varchar(32) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `brand_logo` varchar(128) NOT NULL DEFAULT '' COMMENT '品牌logo',
  `brand_url` varchar(128) NOT NULL DEFAULT '' COMMENT '品牌官网',
  `brand_desc` text NOT NULL COMMENT '品牌描述',
  `serial` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用，1启用，0关闭',
  PRIMARY KEY (`brand_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='品牌表';

-- ----------------------------
-- Table structure for tp_building
-- ----------------------------
DROP TABLE IF EXISTS `tp_building`;
CREATE TABLE `tp_building` (
  `building_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `province_id` int(10) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` int(10) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` int(10) unsigned NOT NULL DEFAULT '330382' COMMENT '地区ID',
  `building_name` varchar(16) NOT NULL DEFAULT '' COMMENT '小区/写字楼名称',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `longitude` decimal(10,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '经度，商家所在经度',
  `latitude` decimal(9,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '纬度，商家所在纬度',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态，1有效，0已删除',
  PRIMARY KEY (`building_id`),
  KEY `tp_city_id` (`city_id`) USING BTREE,
  KEY `tp_area_id` (`area_id`) USING BTREE,
  KEY `tp_building_addtime` (`addtime`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='小区/写字楼表';

-- ----------------------------
-- Table structure for tp_buy_give
-- ----------------------------
DROP TABLE IF EXISTS `tp_buy_give`;
CREATE TABLE `tp_buy_give` (
  `buy_give_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `merchant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期，开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期，结束时间',
  `gift_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '礼品ID',
  `vouchers_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '抵用券ID',
  `give_num` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '赠送抵用券的数量',
  `amount_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单满多少(此处指合并支付的总金额)可使用该买赠活动',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '当前买赠活动是否有效，1是0否，若为否，发放买赠活动时，选择买赠活动时不会出现该买赠活动',
  `use_time` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户可享受几次这样的折扣，0表示无限制',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题/描述',
  `scope` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用范围，1仅限微信，2仅限APP，0全部',
  `genre_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '三级分类ID，表示该活动只支持该分类下的商品，若为0则无此限制',
  PRIMARY KEY (`buy_give_id`),
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='买赠活动表';

-- ----------------------------
-- Table structure for tp_card_pay_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_card_pay_log`;
CREATE TABLE `tp_card_pay_log` (
  `card_pay_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sales_date` varchar(32) NOT NULL DEFAULT '' COMMENT '交易日期',
  `pos_no` varchar(32) NOT NULL DEFAULT '' COMMENT '机号',
  `center_water_no` varchar(32) NOT NULL DEFAULT '' COMMENT '中心交易流水号',
  `sales_bill_no` varchar(32) NOT NULL DEFAULT '' COMMENT '交易单号',
  `trading_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '记录生成时间',
  `card_code` varchar(32) NOT NULL DEFAULT '0' COMMENT '支付卡号',
  PRIMARY KEY (`card_pay_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='账户变动明细表';

-- ----------------------------
-- Table structure for tp_city_change_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_city_change_log`;
CREATE TABLE `tp_city_change_log` (
  `city_change_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `start_area_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '切换前区县ID',
  `end_area_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '切换后区县ID',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '切换时间',
  `ip_area_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'IP所在区县ID',
  `remark` varchar(128) NOT NULL DEFAULT '' COMMENT '备注，系统自动生成，如 从已付款状态设置为已发货状态',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  PRIMARY KEY (`city_change_log_id`),
  KEY `tp_city` (`city_id`) USING BTREE,
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='城市切换记录表';

-- ----------------------------
-- Table structure for tp_class
-- ----------------------------
DROP TABLE IF EXISTS `tp_class`;
CREATE TABLE `tp_class` (
  `class_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `class_name` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '一级分类名称',
  `class_tag` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '分类标记，标记特定分类，如种子，营养液，种植机等',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效，1有效，0无效',
  `is_index` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否在首页显示，0否，1是',
  `is_first_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否在快速订单显示，0否，1是',
  `class_icon` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '分类图标',
  `has_all_integral` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有积分商品，0否，1是',
  PRIMARY KEY (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商品一级分类表';

-- ----------------------------
-- Table structure for tp_collect
-- ----------------------------
DROP TABLE IF EXISTS `tp_collect`;
CREATE TABLE `tp_collect` (
  `collect_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID，0表示未登录用户',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`collect_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品收藏表';

-- ----------------------------
-- Table structure for tp_config
-- ----------------------------
DROP TABLE IF EXISTS `tp_config`;
CREATE TABLE `tp_config` (
  `config_name` varchar(32) NOT NULL COMMENT '配置项英文名，主键',
  `config_value` varchar(255) NOT NULL DEFAULT '' COMMENT '配置项值',
  PRIMARY KEY (`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统配置表';

INSERT INTO `tp_config` VALUES ('shop_name', 'B2C电商服务平台1');
INSERT INTO `tp_config` VALUES ('smtp_email_open', 'SMTP邮件发送开启');
INSERT INTO `tp_config` VALUES ('smtp_email_host', 'SMTP发送服务器域名');
INSERT INTO `tp_config` VALUES ('smtp_email_account', 'SMTP发送邮箱账号');
INSERT INTO `tp_config` VALUES ('smtp_email_password', 'SMTP发送邮箱密码');
INSERT INTO `tp_config` VALUES ('smtp_email_reply', 'SMTP回复邮箱账号');
INSERT INTO `tp_config` VALUES ('free_shipping_status', '0');
INSERT INTO `tp_config` VALUES ('free_shipping_total', '300');
INSERT INTO `tp_config` VALUES ('new_user_discount_status', '0');
INSERT INTO `tp_config` VALUES ('new_user_discount_total', '90');
INSERT INTO `tp_config` VALUES ('sms_open', '1');
INSERT INTO `tp_config` VALUES ('customer_service_address', '浙江1');
INSERT INTO `tp_config` VALUES ('sn_prefix', 's');
INSERT INTO `tp_config` VALUES ('        dump($card_password);\r\n', '6');
INSERT INTO `tp_config` VALUES ('company_name', '');
INSERT INTO `tp_config` VALUES ('mall_price_name', '');
INSERT INTO `tp_config` VALUES ('real_price_name', '');
INSERT INTO `tp_config` VALUES ('system_money_name', '金币');
INSERT INTO `tp_config` VALUES ('hot_keywords', '番茄,大白菜,樱桃,苹果');
INSERT INTO `tp_config` VALUES ('default_user_rank_id', '1');
INSERT INTO `tp_config` VALUES ('default_msg', '');
INSERT INTO `tp_config` VALUES ('android_version', '20');
INSERT INTO `tp_config` VALUES ('integrals_per_rmb', '100');
INSERT INTO `tp_config` VALUES ('integral_deduct_rmb_rate', '20');
INSERT INTO `tp_config` VALUES ('customer_service_telephone', '0571578920141');
INSERT INTO `tp_config` VALUES ('complaints_hotline', '');
INSERT INTO `tp_config` VALUES ('order_hotline', '');
INSERT INTO `tp_config` VALUES ('service_hotline', '');
INSERT INTO `tp_config` VALUES ('official_record', '浙A备1');
INSERT INTO `tp_config` VALUES ('customer_service_qq', '');
INSERT INTO `tp_config` VALUES ('system_logo', '');
INSERT INTO `tp_config` VALUES ('sms_uid', '达利2015');
INSERT INTO `tp_config` VALUES ('major_title', '994抗鱼眼剂');
INSERT INTO `tp_config` VALUES ('major_link', 'http://cheqishi.yurtree.com/FrontMall/item_detail/item_id/203');
INSERT INTO `tp_config` VALUES ('major_pic', '/Uploads/image/article/thumb/2015-08/2015082419585_85071.jpg');
INSERT INTO `tp_config` VALUES ('default_class_id', '14');
INSERT INTO `tp_config` VALUES ('system_close_reason', '啊,亲,太不巧了！由于累了所以我要休息一会儿，O(∩_∩)O~。很快就会回来哦！');
INSERT INTO `tp_config` VALUES ('sms_key', 'e5d272fbac61f0e59878');
INSERT INTO `tp_config` VALUES ('access_token', '24_gX7SYy5yQGIzol_du4VKK9K6jst6ErjX89L8oHC0dvMdTQK_f2qjsAYA-pB2nIzLSq5hh6Pj1lKMYsyYaEAB7ZYypZElFWNwYAc_cCc6WVRVn6zlDr9W7aXyOWIfsiiLEuumfu9tMJoNTchoOMOeAGAWUW');
INSERT INTO `tp_config` VALUES ('access_token_expire_time', '1566553821');
INSERT INTO `tp_config` VALUES ('jsapi_ticket', 'sM4AOVdWfPE4DxkXGEs8VMkXF0G0i5AQDiP4pE-z34G3bIqtHKPssdp3tt5yLRdw33by9keQpYB99WeXPiw_vA');
INSERT INTO `tp_config` VALUES ('config_watermark', 'http://images.beyondin.com/Uploads/image/default/2018-02/20180201154857_21262.jpg?watermark/1/image/aHR0cDovL2IyY19zdGFuZGFyZF92ZXJzaW9uLnNxbC4xMTguZWFzeXNvZnQxNjguY29tL1B1YmxpYy9JbWFnZXMvZnJvbnQvd2F0ZXJtYXJrL3NodWl5aW5AM3gucG5n');
INSERT INTO `tp_config` VALUES ('jsapi_ticket_expire_time', '1566553821');
INSERT INTO `tp_config` VALUES ('base_fare', '8');
INSERT INTO `tp_config` VALUES ('full_cost', '68');
INSERT INTO `tp_config` VALUES ('qr_code', '/Uploads/image/article/thumb/2016-10/20161015183041_57868.jpg');
INSERT INTO `tp_config` VALUES ('shop_admin_rate', '20');
INSERT INTO `tp_config` VALUES ('is_fenxiao_open', '0');
INSERT INTO `tp_config` VALUES ('fenxiao_level', '3');
INSERT INTO `tp_config` VALUES ('first_level_agent_rate', '12');
INSERT INTO `tp_config` VALUES ('second_level_agent_rate', '10');
INSERT INTO `tp_config` VALUES ('third_level_agent_rate', '8');
INSERT INTO `tp_config` VALUES ('templet_info', '{\"mall_home\":\"4\",\"item_list\":\"5\",\"item_detail\":\"7\",\"\":0}');
INSERT INTO `tp_config` VALUES ('cur_templet_package_id', '25');
INSERT INTO `tp_config` VALUES ('subscribe_title', '欢迎关注盈软科技');
INSERT INTO `tp_config` VALUES ('subscribe_link', 'http://beyondin.com');
INSERT INTO `tp_config` VALUES ('subscribe_pic', '/Uploads/image/config/2017-05/20170519111923_77491.jpg');
INSERT INTO `tp_config` VALUES ('subscribe_content', '欢迎来到杭州盈软科技');
INSERT INTO `tp_config` VALUES ('wx_menu', '');
INSERT INTO `tp_config` VALUES ('deposit_fee', '5');
INSERT INTO `tp_config` VALUES ('qr_code_bg', '/Uploads/image/article/thumb/2016-10/20161028145903_30630.jpg');
INSERT INTO `tp_config` VALUES ('shop_rec_user_rate', '20');
INSERT INTO `tp_config` VALUES ('shop_province_rate', '10');
INSERT INTO `tp_config` VALUES ('shop_city_rate', '10');
INSERT INTO `tp_config` VALUES ('shop_area_rate', '10');
INSERT INTO `tp_config` VALUES ('chou_admin_rate', '20');
INSERT INTO `tp_config` VALUES ('chou_rec_user_rate', '20');
INSERT INTO `tp_config` VALUES ('chou_province_rate', '10');
INSERT INTO `tp_config` VALUES ('chou_city_rate', '10');
INSERT INTO `tp_config` VALUES ('active_one_time_consume', '100');
INSERT INTO `tp_config` VALUES ('active_added_consume', '200');
INSERT INTO `tp_config` VALUES ('consume_return_rate', '10');
INSERT INTO `tp_config` VALUES ('purchase_coupon_rate', '10');
INSERT INTO `tp_config` VALUES ('sms_mobile', '13738778057');
INSERT INTO `tp_config` VALUES ('sms_type', '1');
INSERT INTO `tp_config` VALUES ('order_auto_confirm_time', '336');
INSERT INTO `tp_config` VALUES ('qr_code_kf', 'http://b2c.beyondin.com/Uploads/image/config/2016-11/20161121110040_18800.png');
INSERT INTO `tp_config` VALUES ('limit_open', '1');
INSERT INTO `tp_config` VALUES ('limit_endtime', '1513479788');
INSERT INTO `tp_config` VALUES ('limit_desc', '请立即联系客服进行续费！');
INSERT INTO `tp_config` VALUES ('default_express_company', '6');
INSERT INTO `tp_config` VALUES ('uniform_shipping_fee', '-1');
INSERT INTO `tp_config` VALUES ('red_limit_money', '0.01');
INSERT INTO `tp_config` VALUES ('red_expire_time', '7');
INSERT INTO `tp_config` VALUES ('invite_award', '0');
INSERT INTO `tp_config` VALUES ('red_point', '1');
INSERT INTO `tp_config` VALUES ('recharge_rebate', '0');
INSERT INTO `tp_config` VALUES ('rank1', '2888000');
INSERT INTO `tp_config` VALUES ('rank2', '1888000');
INSERT INTO `tp_config` VALUES ('rank3', '1588000');
INSERT INTO `tp_config` VALUES ('rank4', '1288000');
INSERT INTO `tp_config` VALUES ('rank5', '888000');
INSERT INTO `tp_config` VALUES ('rank6', '88000');
INSERT INTO `tp_config` VALUES ('rank7', '58000');
INSERT INTO `tp_config` VALUES ('min_loss', '0');
INSERT INTO `tp_config` VALUES ('return_rate', '0.07');
INSERT INTO `tp_config` VALUES ('min_flow', '');
INSERT INTO `tp_config` VALUES ('flow_rate', '0.0015');
INSERT INTO `tp_config` VALUES ('poundage', '0.02');
INSERT INTO `tp_config` VALUES ('max_deduct_rate', '2.1');
INSERT INTO `tp_config` VALUES ('min_deduct_rate', '0.1');
INSERT INTO `tp_config` VALUES ('deposit_percent', '5');
INSERT INTO `tp_config` VALUES ('recharge_exp', '0.001');
INSERT INTO `tp_config` VALUES ('sys_qq', '1198394931');
INSERT INTO `tp_config` VALUES ('sys_qq_group', '1001020101');
INSERT INTO `tp_config` VALUES ('sys_qq_key', '1234566457541');
INSERT INTO `tp_config` VALUES ('double_flow', '1');
INSERT INTO `tp_config` VALUES ('return_double_flow', '1');
INSERT INTO `tp_config` VALUES ('valid_flow', '20000');
INSERT INTO `tp_config` VALUES ('dcp_write_off_rate', '2');
-- ----------------------------
-- Table structure for tp_cust_flash
-- ----------------------------
DROP TABLE IF EXISTS `tp_cust_flash`;
CREATE TABLE `tp_cust_flash` (
  `cust_flash_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '商品标题',
  `link` varchar(128) NOT NULL DEFAULT '' COMMENT '链接',
  `pic` varchar(128) NOT NULL DEFAULT '' COMMENT '图片路径',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '有效性，0不显示，1显示，2已删除',
  `adv_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型，1用户版首页，2用户版众筹页',
  PRIMARY KEY (`cust_flash_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='轮播图片表';

-- ----------------------------
-- Table structure for tp_customer_service_online
-- ----------------------------
DROP TABLE IF EXISTS `tp_customer_service_online`;
CREATE TABLE `tp_customer_service_online` (
  `customer_service_online_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `customer_service_online_name` varchar(16) NOT NULL DEFAULT '' COMMENT '客服昵称',
  `service_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '客服类型，1为QQ，2为旺旺',
  `is_after_service` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0售前，1售后',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '客服账号',
  `clickdot` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击量',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示，1是，0否',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`customer_service_online_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='在线客服表';

-- ----------------------------
-- Table structure for tp_daily_bet
-- ----------------------------
DROP TABLE IF EXISTS `tp_daily_bet`;
CREATE TABLE `tp_daily_bet` (
  `daily_bet_id` int(10) NOT NULL AUTO_INCREMENT,
  `bet_type_json` text NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`daily_bet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tp_daily_win
-- ----------------------------
DROP TABLE IF EXISTS `tp_daily_win`;
CREATE TABLE `tp_daily_win` (
  `daily_win_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `addtime` int(11) NOT NULL COMMENT '时间',
  `win` int(11) NOT NULL COMMENT '盈利',
  `loss` int(11) NOT NULL COMMENT '亏损',
  `reward` int(11) NOT NULL COMMENT '排行榜奖励',
  `is_received` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已领取 1已领取 0未领取',
  `daily_flow` int(10) NOT NULL DEFAULT '0' COMMENT '每日流水',
  PRIMARY KEY (`daily_win_id`)
) ENGINE=InnoDB AUTO_INCREMENT=943 DEFAULT CHARSET=utf8mb4 COMMENT='每日盈亏';

-- ----------------------------
-- Table structure for tp_deposit_apply
-- ----------------------------
DROP TABLE IF EXISTS `tp_deposit_apply`;
CREATE TABLE `tp_deposit_apply` (
  `deposit_apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `deposit_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '提现类型，1余额',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '提现状态，0未处理，1已通过，2已拒绝',
  `admin_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员备注',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提交时间',
  `role_type` tinyint(3) unsigned DEFAULT '0' COMMENT '角色类型，筛选用，3用户，4代理商',
  `pass_time` int(10) unsigned DEFAULT '0' COMMENT '处理时间',
  `deposit_server_charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '扣除手续费',
  `real_get_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际提现到账金额',
  PRIMARY KEY (`deposit_apply_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='提现申请表';

-- ----------------------------
-- Table structure for tp_dept
-- ----------------------------
DROP TABLE IF EXISTS `tp_dept`;
CREATE TABLE `tp_dept` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `node_code` varchar(32) NOT NULL DEFAULT '0' COMMENT '门店编码',
  `node_name` varchar(128) NOT NULL DEFAULT '0' COMMENT '门店名称',
  `address` varchar(128) NOT NULL DEFAULT '' COMMENT '地址',
  `phone_number` varchar(32) NOT NULL DEFAULT '' COMMENT '电话',
  `node_type` int(11) NOT NULL DEFAULT '0' COMMENT '类型 0门店 2配送中心',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '记录生成时间',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有效',
  `serial` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dept_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='门店列表';

-- ----------------------------
-- Table structure for tp_email_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_email_log`;
CREATE TABLE `tp_email_log` (
  `email_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `send_email_account` varchar(32) NOT NULL DEFAULT '' COMMENT '发送邮箱账号',
  `email_recipients` text COMMENT '收件人列表，不同收件人直接用逗号隔开',
  `subject` text COMMENT '主题',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送状态',
  PRIMARY KEY (`email_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邮件发送日志表';

-- ----------------------------
-- Table structure for tp_front_version
-- ----------------------------
DROP TABLE IF EXISTS `tp_front_version`;
CREATE TABLE `tp_front_version` (
  `front_version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
  `remark` text NOT NULL COMMENT '日志',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '下载地址',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0不需要更新, 1需要更新',
  PRIMARY KEY (`front_version_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='前端版本';

-- ----------------------------
-- Table structure for tp_game_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_game_log`;
CREATE TABLE `tp_game_log` (
  `game_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_result_id` int(11) NOT NULL COMMENT '福彩开奖结果',
  `game_type_id` int(11) NOT NULL COMMENT '游戏类型id',
  `part_one_sum` int(11) NOT NULL COMMENT '分区1 数字和',
  `part_two_sum` int(11) NOT NULL COMMENT '分区2 数字和',
  `part_three_sum` int(11) NOT NULL COMMENT '分区3 数字和',
  `part_four_sum` int(11) NOT NULL COMMENT '分区4 数字和',
  `part_five_sum` int(11) NOT NULL COMMENT '分区5 数字和',
  `part_six_sum` int(11) NOT NULL COMMENT '分区6 数字和',
  `part_one_result` varchar(255) NOT NULL COMMENT '分区1 数字结果',
  `part_two_result` varchar(255) NOT NULL COMMENT '分区2 数字结果',
  `part_three_result` varchar(255) NOT NULL COMMENT '分区3 数字结果',
  `part_four_result` varchar(255) NOT NULL COMMENT '分区4 数字结果',
  `part_five_result` varchar(255) NOT NULL COMMENT '分区5 数字结果',
  `part_six_result` varchar(255) NOT NULL COMMENT '分区6 数字结果',
  `total_money` varchar(25) NOT NULL DEFAULT '0.00' COMMENT '奖金总数',
  `win_people` int(11) NOT NULL COMMENT '中奖人数',
  `bet_people` int(11) NOT NULL COMMENT '投注人数',
  `remark` varchar(255) NOT NULL COMMENT '开奖说明',
  `result` varchar(255) NOT NULL COMMENT '开奖结果',
  `result_json` varchar(255) NOT NULL COMMENT '结果情况',
  `zhuang_card` varchar(255) NOT NULL COMMENT '庄的牌',
  `xian_card` varchar(255) NOT NULL COMMENT '闲的牌',
  `addtime` int(11) DEFAULT '0' COMMENT '时间',
  `bet_reward` int(11) NOT NULL COMMENT '投注金额',
  `win_reward` int(11) NOT NULL COMMENT '中奖金额',
  `bet_json` text NOT NULL COMMENT '开奖赔率json',
  `last_result` text NOT NULL COMMENT '走势图大小单双',
  `sixteen_hash` text NOT NULL COMMENT '16位hash值',
  `ten_hash` text NOT NULL COMMENT '十进制的16位hash值',
  `sub_result` text NOT NULL COMMENT '除出来的值',
  `sixteen_data` text NOT NULL COMMENT '16进制的结果数组',
  `ten_data` text NOT NULL COMMENT '10进制的结果数组',
  `result_data` text NOT NULL COMMENT '重新排序结果数组',
  `real_bet_money` int(11) NOT NULL DEFAULT '0' COMMENT '真正的投注金额',
  `real_win_money` int(11) NOT NULL DEFAULT '0' COMMENT '真正的赢取金额',
  `real_bet_num` int(11) NOT NULL DEFAULT '0' COMMENT '真正的投注人数',
  `real_win_num` int(11) NOT NULL DEFAULT '0' COMMENT '真正的赢的人数',
  PRIMARY KEY (`game_log_id`),
  KEY `搜索用到。` (`game_type_id`,`game_result_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1107406 DEFAULT CHARSET=utf8mb4 COMMENT='游戏记录';

-- ----------------------------
-- Table structure for tp_game_log_copy
-- ----------------------------
DROP TABLE IF EXISTS `tp_game_log_copy`;
CREATE TABLE `tp_game_log_copy` (
  `game_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_result_id` int(11) NOT NULL COMMENT '福彩开奖结果',
  `game_type_id` int(11) NOT NULL COMMENT '游戏类型id',
  `part_one_sum` int(11) NOT NULL COMMENT '分区1 数字和',
  `part_two_sum` int(11) NOT NULL COMMENT '分区2 数字和',
  `part_three_sum` int(11) NOT NULL COMMENT '分区3 数字和',
  `part_four_sum` int(11) NOT NULL COMMENT '分区4 数字和',
  `part_five_sum` int(11) NOT NULL COMMENT '分区5 数字和',
  `part_six_sum` int(11) NOT NULL COMMENT '分区6 数字和',
  `part_one_result` varchar(255) NOT NULL COMMENT '分区1 数字结果',
  `part_two_result` varchar(255) NOT NULL COMMENT '分区2 数字结果',
  `part_three_result` varchar(255) NOT NULL COMMENT '分区3 数字结果',
  `part_four_result` varchar(255) NOT NULL COMMENT '分区4 数字结果',
  `part_five_result` varchar(255) NOT NULL COMMENT '分区5 数字结果',
  `part_six_result` varchar(255) NOT NULL COMMENT '分区6 数字结果',
  `total_money` float(13,2) NOT NULL DEFAULT '0.00' COMMENT '奖金总数',
  `win_people` int(11) NOT NULL COMMENT '中奖人数',
  `bet_people` int(11) NOT NULL COMMENT '投注人数',
  `remark` varchar(255) NOT NULL COMMENT '开奖说明',
  `result` varchar(255) NOT NULL COMMENT '开奖结果',
  `result_json` varchar(255) NOT NULL COMMENT '结果情况',
  `zhuang_card` varchar(255) NOT NULL COMMENT '庄的牌',
  `xian_card` varchar(255) NOT NULL COMMENT '闲的牌',
  `addtime` int(11) DEFAULT '0' COMMENT '时间',
  `bet_reward` int(11) NOT NULL COMMENT '投注金额',
  `win_reward` int(11) NOT NULL COMMENT '中奖金额',
  `bet_json` text NOT NULL COMMENT '开奖赔率json',
  `last_result` varchar(255) NOT NULL COMMENT '走势图大小单双',
  `sixteen_hash` varchar(255) NOT NULL COMMENT '16位hash值',
  `ten_hash` varchar(255) NOT NULL COMMENT '十进制的16位hash值',
  `sub_result` varchar(255) NOT NULL COMMENT '除出来的值',
  `sixteen_data` varchar(255) NOT NULL COMMENT '16进制的结果数组',
  `ten_data` varchar(255) NOT NULL COMMENT '10进制的结果数组',
  `result_data` varchar(255) NOT NULL COMMENT '重新排序结果数组',
  PRIMARY KEY (`game_log_id`),
  KEY `搜索用到。` (`game_type_id`,`game_result_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=599121 DEFAULT CHARSET=utf8mb4 COMMENT='游戏记录';

-- ----------------------------
-- Table structure for tp_game_log_copy1
-- ----------------------------
DROP TABLE IF EXISTS `tp_game_log_copy1`;
CREATE TABLE `tp_game_log_copy1` (
  `game_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_result_id` int(11) NOT NULL COMMENT '福彩开奖结果',
  `game_type_id` int(11) NOT NULL COMMENT '游戏类型id',
  `part_one_sum` int(11) NOT NULL COMMENT '分区1 数字和',
  `part_two_sum` int(11) NOT NULL COMMENT '分区2 数字和',
  `part_three_sum` int(11) NOT NULL COMMENT '分区3 数字和',
  `part_four_sum` int(11) NOT NULL COMMENT '分区4 数字和',
  `part_five_sum` int(11) NOT NULL COMMENT '分区5 数字和',
  `part_six_sum` int(11) NOT NULL COMMENT '分区6 数字和',
  `part_one_result` varchar(255) NOT NULL COMMENT '分区1 数字结果',
  `part_two_result` varchar(255) NOT NULL COMMENT '分区2 数字结果',
  `part_three_result` varchar(255) NOT NULL COMMENT '分区3 数字结果',
  `part_four_result` varchar(255) NOT NULL COMMENT '分区4 数字结果',
  `part_five_result` varchar(255) NOT NULL COMMENT '分区5 数字结果',
  `part_six_result` varchar(255) NOT NULL COMMENT '分区6 数字结果',
  `total_money` float(13,2) NOT NULL DEFAULT '0.00' COMMENT '奖金总数',
  `win_people` int(11) NOT NULL COMMENT '中奖人数',
  `bet_people` int(11) NOT NULL COMMENT '投注人数',
  `remark` varchar(255) NOT NULL COMMENT '开奖说明',
  `result` varchar(255) NOT NULL COMMENT '开奖结果',
  `result_json` varchar(255) NOT NULL COMMENT '结果情况',
  `zhuang_card` varchar(255) NOT NULL COMMENT '庄的牌',
  `xian_card` varchar(255) NOT NULL COMMENT '闲的牌',
  `addtime` int(11) DEFAULT '0' COMMENT '时间',
  `bet_reward` int(11) NOT NULL COMMENT '投注金额',
  `win_reward` int(11) NOT NULL COMMENT '中奖金额',
  `bet_json` text NOT NULL COMMENT '开奖赔率json',
  `last_result` varchar(255) NOT NULL COMMENT '走势图大小单双',
  PRIMARY KEY (`game_log_id`),
  KEY `搜索用到。` (`game_type_id`,`game_result_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=606360 DEFAULT CHARSET=utf8mb4 COMMENT='游戏记录';

-- ----------------------------
-- Table structure for tp_game_result
-- ----------------------------
DROP TABLE IF EXISTS `tp_game_result`;
CREATE TABLE `tp_game_result` (
  `game_result_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '开奖结果id',
  `type` int(11) NOT NULL COMMENT '福彩来源 1.北京快乐8  2.腾讯   3.重庆',
  `issue` varchar(255) NOT NULL COMMENT '期号',
  `result` varchar(255) NOT NULL COMMENT '结果',
  `addtime` int(11) NOT NULL COMMENT '时间',
  `is_open` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开奖',
  `open_time` int(11) NOT NULL COMMENT '开奖时间',
  `hash_one` varchar(255) NOT NULL COMMENT 'hash值1',
  `hash_two` varchar(255) NOT NULL COMMENT 'hash值2',
  `hash_three` varchar(255) NOT NULL COMMENT 'hash值3',
  `hash_total` varchar(255) NOT NULL COMMENT '号源',
  `hash_new` varchar(255) NOT NULL COMMENT 'SHA256转化值',
  PRIMARY KEY (`game_result_id`)
) ENGINE=InnoDB AUTO_INCREMENT=703394 DEFAULT CHARSET=utf8mb4 COMMENT='开奖结果';


-- ----------------------------
-- Table structure for tp_game_series
-- ----------------------------
DROP TABLE IF EXISTS `tp_game_series`;
CREATE TABLE `tp_game_series` (
  `game_series_id` int(11) NOT NULL COMMENT '游戏系列id',
  `game_series_name` varchar(255) NOT NULL COMMENT '系列名称',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `result_type` varchar(255) NOT NULL COMMENT '开奖类型',
  PRIMARY KEY (`game_series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='游戏系列';

INSERT INTO `tp_game_series` VALUES (1, '蛋蛋系列', 1, '1');
INSERT INTO `tp_game_series` VALUES (2, '北京系列', 1, '1');
INSERT INTO `tp_game_series` VALUES (3, 'PK系列', 1, '2');
INSERT INTO `tp_game_series` VALUES (4, '加拿大系列', 1, '3');
INSERT INTO `tp_game_series` VALUES (5, '韩国系列', 1, '4');
INSERT INTO `tp_game_series` VALUES (6, '腾讯分分彩', 0, '6');
INSERT INTO `tp_game_series` VALUES (7, '重庆时时彩', 1, '5');
INSERT INTO `tp_game_series` VALUES (8, '比特币', 1, '7,8,9');

-- ----------------------------
-- Table structure for tp_game_type
-- ----------------------------
DROP TABLE IF EXISTS `tp_game_type`;
CREATE TABLE `tp_game_type` (
  `game_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '游戏类型',
  `game_series_id` int(11) unsigned NOT NULL COMMENT '游戏系列id',
  `game_type_name` varchar(255) NOT NULL COMMENT '游戏类型名称',
  `game_rule` text NOT NULL COMMENT '游戏规则',
  `base_bonus_pools` varchar(20) NOT NULL COMMENT '基本奖金池',
  `max_bet_num` int(11) NOT NULL DEFAULT '0' COMMENT '最大投注人数',
  `min_bet_num` int(11) NOT NULL DEFAULT '0' COMMENT '最小投注人数',
  `max_win_num` int(11) NOT NULL COMMENT '最大中奖人数',
  `min_win_num` int(11) NOT NULL COMMENT '最小中奖人数',
  `max_bet_money` int(11) NOT NULL COMMENT '最高下注金额',
  `max_win_money` int(11) NOT NULL COMMENT '最高中奖金额',
  `base_img` varchar(255) NOT NULL COMMENT '类型图片',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `bet_json` text NOT NULL COMMENT '投注赔率',
  `is_fixation_rate` tinyint(4) NOT NULL DEFAULT '0' COMMENT '赔率是否固定 1固定',
  `is_index` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否展示在首页',
  `table_type` tinyint(4) NOT NULL COMMENT '表格样式',
  `result_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '游戏开奖模式 1.北京快乐8 2.北京PK10 3.加拿大快乐八 4.韩国系列 5.重庆时时彩系列',
  `max_win_reward` int(11) NOT NULL COMMENT '最大中奖金额',
  `min_win_reward` int(11) NOT NULL COMMENT '最小中奖金额',
  `max_bet_reward` int(11) NOT NULL COMMENT '最大投注金额',
  `min_bet_reward` int(11) NOT NULL COMMENT '最小投注金额',
  `part_type` tinyint(4) NOT NULL COMMENT '分区样式',
  `chart_json` text NOT NULL COMMENT '走势图json',
  `is_use_result` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '走势图使用什么样式   0正常  1结果 2庄闲  3只有结果没有大小单双',
  `max_deduct` float(11,2) NOT NULL DEFAULT '0.00' COMMENT '最大抽水比例',
  `min_deduct` float(11,2) NOT NULL DEFAULT '0.00' COMMENT '最小抽水比例 ',
  `valid_flow` int(10) NOT NULL DEFAULT '0' COMMENT '有效流水号码数',
  `min_bet_money` int(10) NOT NULL COMMENT '最小投注金额',
  `start_time` varchar(11) NOT NULL COMMENT '开始时间',
  `end_time` varchar(11) NOT NULL COMMENT '结束时间',
  `issue_num` varchar(11) NOT NULL COMMENT '期数',
  `each_issue_time` varchar(11) NOT NULL COMMENT '每期时间',
  PRIMARY KEY (`game_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COMMENT='游戏类型';

INSERT INTO `tp_game_type` VALUES (1, 1, '蛋蛋28', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102145_32935.png&quot; title=&quot;20190506102145_32935.png&quot; alt=&quot;20190417150323_29813.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506101711_13548.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 1, 1, 1, 0, 0, 0, 0, 1, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 2.00, 1.22, 21, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (2, 1, '蛋蛋36', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102306_86960.png&quot; title=&quot;20190506102306_86960.png&quot; alt=&quot;20190417150944_37619.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102250_50054.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"rate\":\"100.0000\",\"name\":\"\\u8c79\"},{\"key\":2,\"rate\":\"16.6700\",\"name\":\"\\u987a\"},{\"key\":3,\"rate\":\"3.7000\",\"name\":\"\\u5bf9\"},{\"key\":4,\"rate\":\"2.7800\",\"name\":\"\\u534a\"},{\"key\":5,\"rate\":\"3.3300\",\"name\":\"\\u6742\"}]}]', 0, 1, 1, 1, 0, 0, 0, 0, 1, '[{\"key\":1,\"name\":\"\\u8c79\",\"num\":0.81},{\"key\":2,\"name\":\"\\u987a\",\"num\":26.83},{\"key\":3,\"name\":\"\\u5bf9\",\"num\":4.88},{\"key\":4,\"name\":\"\\u534a\",\"num\":40.65},{\"key\":5,\"name\":\"\\u6742\",\"num\":26.83}]', 3, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (3, 1, '蛋蛋外围', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102428_40635.png&quot; title=&quot;20190506102428_40635.png&quot; alt=&quot;20190417150802_52994.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102419_64401.png', 1, '[{\"part\":1,\"name\":\"\\u5927\\u5c0f\\u5355\\u53cc\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5c0f\",\"rate\":\"2.1315\"},{\"key\":2,\"name\":\"\\u5927\",\"rate\":\"2.1315\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"2.1315\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"2.1315\"},{\"key\":5,\"name\":\"\\u6781\\u5927\",\"rate\":\"17.4930\"},{\"key\":6,\"name\":\"\\u6781\\u5c0f\",\"rate\":\"17.4930\"},{\"key\":7,\"name\":\"\\u5c0f\\u5355\",\"rate\":\"4.6712\"},{\"key\":8,\"name\":\"\\u5c0f\\u53cc\",\"rate\":\"4.2414\"},{\"key\":9,\"name\":\"\\u5927\\u5355\",\"rate\":\"4.2414\"},{\"key\":10,\"name\":\"\\u5927\\u53cc\",\"rate\":\"4.6712\"}]},{\"part\":2,\"name\":\"\\u9f99\\u864e\\u8c79\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f99\",\"rate\":\"2.9400\"},{\"key\":2,\"name\":\"\\u864e\",\"rate\":\"2.9400\"},{\"key\":3,\"name\":\"\\u8c79\",\"rate\":\"2.9400\"}]}]', 1, 1, 2, 1, 0, 0, 0, 0, 1, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (4, 1, '蛋蛋定位', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102509_44281.png&quot; title=&quot;20190506102509_44281.png&quot; alt=&quot;20190417151055_82226.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102458_96010.png', 1, '[{\"part\":1,\"name\":\"\\u9f99\\u864e\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":2,\"name\":\"\\u524d\\u4e24\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":3,\"name\":\"\\u540e\\u4e24\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":4,\"name\":\"\\u53f7\\u7801\\u4e00\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":5,\"name\":\"\\u53f7\\u7801\\u4e8c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":6,\"name\":\"\\u53f7\\u7801\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]}]', 1, 0, 3, 1, 0, 0, 0, 0, 1, '', 1, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (5, 1, '蛋蛋28固定', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102541_22096.png&quot; title=&quot;20190506102541_22096.png&quot; alt=&quot;20190417150323_29813.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102534_68990.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"980.0000\",\"name\":0},{\"key\":1,\"rate\":\"326.6634\",\"name\":1},{\"key\":2,\"rate\":\"163.3366\",\"name\":2},{\"key\":3,\"rate\":\"98.0000\",\"name\":3},{\"key\":4,\"rate\":\"65.3268\",\"name\":4},{\"key\":5,\"rate\":\"46.6578\",\"name\":5},{\"key\":6,\"rate\":\"34.9958\",\"name\":6},{\"key\":7,\"rate\":\"27.2146\",\"name\":7},{\"key\":8,\"rate\":\"21.7756\",\"name\":8},{\"key\":9,\"rate\":\"17.8164\",\"name\":9},{\"key\":10,\"rate\":\"15.5526\",\"name\":10},{\"key\":11,\"rate\":\"14.2002\",\"name\":11},{\"key\":12,\"rate\":\"13.4162\",\"name\":12},{\"key\":13,\"rate\":\"13.0634\",\"name\":13},{\"key\":14,\"rate\":\"13.0634\",\"name\":14},{\"key\":15,\"rate\":\"13.4162\",\"name\":15},{\"key\":16,\"rate\":\"14.2002\",\"name\":16},{\"key\":17,\"rate\":\"15.5526\",\"name\":17},{\"key\":18,\"rate\":\"17.8164\",\"name\":18},{\"key\":19,\"rate\":\"21.7756\",\"name\":19},{\"key\":20,\"rate\":\"27.2146\",\"name\":20},{\"key\":21,\"rate\":\"34.9958\",\"name\":21},{\"key\":22,\"rate\":\"46.6578\",\"name\":22},{\"key\":23,\"rate\":\"65.3268\",\"name\":23},{\"key\":24,\"rate\":\"98.0000\",\"name\":24},{\"key\":25,\"rate\":\"163.3366\",\"name\":25},{\"key\":26,\"rate\":\"326.6634\",\"name\":26},{\"key\":27,\"rate\":\"980.0000\",\"name\":27}]}]', 1, 0, 1, 1, 0, 0, 0, 0, 1, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (6, 1, '蛋蛋百家乐', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102618_20413.png&quot; title=&quot;20190506102618_20413.png&quot; alt=&quot;20190417151350_25424.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 10000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102606_46988.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"name\":\"\\u7403\\u4e00\\u95f2\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u7403\\u4e00\\u5e84\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u7403\\u4e00\\u548c\",\"rate\":\"7.8400\"},{\"key\":4,\"name\":\"\\u7403\\u4e8c\\u95f2\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":\"\\u7403\\u4e8c\\u5e84\",\"rate\":\"1.9600\"},{\"key\":6,\"name\":\"\\u7403\\u4e8c\\u548c\",\"rate\":\"7.8400\"}]}]', 1, 0, 4, 1, 0, 0, 0, 0, 1, '', 2, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (7, 1, '新蛋蛋百家乐', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102719_99881.jpg&quot; title=&quot;20190506102719_99881.jpg&quot; alt=&quot;20190417151936_18775.jpg&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102651_44407.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":8,\"name\":\"\\u4efb\\u610f\\u5bf9\",\"rate\":\"6.0000\"},{\"key\":7,\"name\":\"\\u95f2\\u5bf9\",\"rate\":\"12.0000\"},{\"key\":4,\"name\":\"\\u5927\",\"rate\":\"1.5300\"},{\"key\":5,\"name\":\"\\u5c0f\",\"rate\":\"2.5000\"},{\"key\":6,\"name\":\"\\u5e84\\u5bf9\",\"rate\":\"12.0000\"},{\"key\":9,\"name\":\"\\u5b8c\\u7f8e\\u5bf9\",\"rate\":\"20.0000\"},{\"key\":2,\"name\":\"\\u95f2\",\"rate\":\"1.9700\"},{\"key\":3,\"name\":\"\\u548c\",\"rate\":\"8.0000\"},{\"key\":1,\"name\":\"\\u5e84\",\"rate\":\"1.9700\"}]}]', 1, 0, 5, 1, 0, 0, 0, 0, 2, '', 2, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (8, 1, '蛋蛋星座', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102835_67729.jpg&quot; title=&quot;20190506102835_67729.jpg&quot; alt=&quot;20190417152144_84418.jpg&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 5000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102750_24786.png', 1, '[{\"part\":1,\"name\":\"\\u5927\\u5c0f\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":2,\"name\":\"\\u5927\\u5c0f\\u6781\\u503c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u6781\\u5927\",\"rate\":\"17.4900\"},{\"key\":2,\"name\":\"\\u6781\\u5c0f\",\"rate\":\"17.4900\"},{\"key\":3,\"name\":\"\\u5c0f\\u5355\",\"rate\":\"3.6300\"},{\"key\":4,\"name\":\"\\u5c0f\\u53cc\",\"rate\":\"4.2300\"},{\"key\":5,\"name\":\"\\u5927\\u5355\",\"rate\":\"4.2300\"},{\"key\":6,\"name\":\"\\u5927\\u53cc\",\"rate\":\"3.6300\"}]},{\"part\":3,\"name\":\"\\u8c79\\u5bf9\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"98.0000\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.6200\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.3300\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.7200\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.2600\"}]},{\"part\":4,\"name\":\"\\u4e94\\u884c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u91d1\",\"rate\":\"4.9000\"},{\"key\":2,\"name\":\"\\u6728\",\"rate\":\"4.9000\"},{\"key\":3,\"name\":\"\\u6c34\",\"rate\":\"4.9000\"},{\"key\":4,\"name\":\"\\u706b\",\"rate\":\"4.9000\"},{\"key\":5,\"name\":\"\\u571f\",\"rate\":\"4.9000\"}]},{\"part\":5,\"name\":\"\\u56db\\u5b63\",\"bet_json\":[{\"key\":1,\"name\":\"\\u6625\",\"rate\":\"3.9200\"},{\"key\":2,\"name\":\"\\u590f\",\"rate\":\"3.9200\"},{\"key\":3,\"name\":\"\\u79cb\",\"rate\":\"3.9200\"},{\"key\":4,\"name\":\"\\u51ac\",\"rate\":\"3.9200\"}]},{\"part\":6,\"name\":\"\\u661f\\u5ea7\",\"bet_json\":[{\"key\":3,\"name\":\"\\u767d\\u7f8a\",\"rate\":\"11.6600\"},{\"key\":4,\"name\":\"\\u91d1\\u725b\",\"rate\":\"11.6600\"},{\"key\":6,\"name\":\"\\u5de8\\u87f9\",\"rate\":\"11.6600\"},{\"key\":5,\"name\":\"\\u53cc\\u5b50\",\"rate\":\"11.6600\"},{\"key\":7,\"name\":\"\\u72ee\\u5b50\",\"rate\":\"11.8000\"},{\"key\":8,\"name\":\"\\u5904\\u5973\",\"rate\":\"12.0900\"},{\"key\":9,\"name\":\"\\u5929\\u79e4\",\"rate\":\"12.0900\"},{\"key\":10,\"name\":\"\\u5929\\u874e\",\"rate\":\"11.8000\"},{\"key\":11,\"name\":\"\\u5c04\\u624b\",\"rate\":\"11.6600\"},{\"key\":12,\"name\":\"\\u9b54\\u874e\",\"rate\":\"11.6600\"},{\"key\":1,\"name\":\"\\u6c34\\u74f6\",\"rate\":\"11.6600\"},{\"key\":2,\"name\":\"\\u53cc\\u9c7c\",\"rate\":\"11.6600\"}]},{\"part\":7,\"name\":\"\\u751f\\u8096\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f20\",\"rate\":\"122.0000\"},{\"key\":2,\"name\":\"\\u725b\",\"rate\":\"44.5400\"},{\"key\":3,\"name\":\"\\u864e\",\"rate\":\"24.5000\"},{\"key\":4,\"name\":\"\\u5154\",\"rate\":\"23.3200\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"17.4900\"},{\"key\":6,\"name\":\"\\u86c7\",\"rate\":\"13.6000\"},{\"key\":7,\"name\":\"\\u9a6c\",\"rate\":\"10.8800\"},{\"key\":8,\"name\":\"\\u7f8a\",\"rate\":\"8.9000\"},{\"key\":9,\"name\":\"\\u7334\",\"rate\":\"7.7700\"},{\"key\":10,\"name\":\"\\u9e21\",\"rate\":\"7.1000\"},{\"key\":11,\"name\":\"\\u72d7\",\"rate\":\"6.7000\"},{\"key\":12,\"name\":\"\\u732a\",\"rate\":\"6.5300\"}]},{\"part\":8,\"name\":\"\\u74031:\\u74033\",\"bet_json\":[{\"key\":1,\"name\":\"1\\uff1a3\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"1\\uff1a3\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"1\\uff1a3\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":9,\"name\":\"\\u74032:\\u74033\",\"bet_json\":[{\"key\":1,\"name\":\"2\\uff1a3\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"2\\uff1a3\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"2\\uff1a3\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":10,\"name\":\"\\u524d\\u4e8c\\u5408\\u5e76\",\"bet_json\":[{\"key\":1,\"name\":\"\\u524d\\u4e8c\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u524d\\u4e8c\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u524d\\u4e8c\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u524d\\u4e8c\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":11,\"name\":\"\\u540e\\u4e8c\\u5408\\u5e76\",\"bet_json\":[{\"key\":1,\"name\":\"\\u540e\\u4e8c\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u540e\\u4e8c\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u540e\\u4e8c\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u540e\\u4e8c\\u53cc\",\"rate\":\"1.9600\"}]}]', 1, 0, 6, 1, 0, 0, 0, 0, 3, '', 1, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (9, 1, '蛋蛋16', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506102935_17217.png&quot; title=&quot;20190506102935_17217.png&quot; alt=&quot;20190417152329_65431.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506102921_17724.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":3,\"name\":3,\"rate\":\"216.0000\"},{\"key\":4,\"name\":4,\"rate\":\"72.0000\"},{\"key\":5,\"name\":5,\"rate\":\"36.0000\"},{\"key\":6,\"name\":6,\"rate\":\"21.6000\"},{\"key\":7,\"name\":7,\"rate\":\"14.4000\"},{\"key\":8,\"name\":8,\"rate\":\"10.2900\"},{\"key\":9,\"name\":9,\"rate\":\"8.6400\"},{\"key\":10,\"name\":10,\"rate\":\"8.0000\"},{\"key\":11,\"name\":11,\"rate\":\"8.0000\"},{\"key\":12,\"name\":12,\"rate\":\"8.6400\"},{\"key\":13,\"name\":13,\"rate\":\"10.2900\"},{\"key\":14,\"name\":14,\"rate\":\"14.4000\"},{\"key\":15,\"name\":15,\"rate\":\"21.6000\"},{\"key\":16,\"name\":16,\"rate\":\"36.0000\"},{\"key\":17,\"name\":17,\"rate\":\"72.0000\"},{\"key\":18,\"name\":18,\"rate\":\"216.0000\"}]}]', 0, 0, 1, 1, 0, 0, 0, 0, 1, '[{\"key\":3,\"name\":3,\"num\":0.46},{\"key\":4,\"name\":4,\"num\":1.39},{\"key\":5,\"name\":5,\"num\":2.78},{\"key\":6,\"name\":6,\"num\":4.63},{\"key\":7,\"name\":7,\"num\":6.94},{\"key\":8,\"name\":8,\"num\":9.72},{\"key\":9,\"name\":9,\"num\":11.57},{\"key\":10,\"name\":10,\"num\":12.5},{\"key\":11,\"name\":11,\"num\":12.5},{\"key\":12,\"name\":12,\"num\":11.57},{\"key\":13,\"name\":13,\"num\":9.72},{\"key\":14,\"name\":14,\"num\":6.94},{\"key\":15,\"name\":15,\"num\":4.63},{\"key\":16,\"name\":16,\"num\":2.78},{\"key\":17,\"name\":17,\"num\":1.39},{\"key\":18,\"name\":18,\"num\":0.46}]', 0, 0.00, 0.00, 10, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (10, 1, '蛋蛋11', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506103017_83672.png&quot; title=&quot;20190506103017_83672.png&quot; alt=&quot;20190417152437_67054.png&quot;/&gt;&lt;/p&gt;', '5000000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506103009_41684.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":2,\"name\":2,\"rate\":\"36.0000\"},{\"key\":3,\"name\":3,\"rate\":\"18.0000\"},{\"key\":4,\"name\":4,\"rate\":\"12.0000\"},{\"key\":5,\"name\":5,\"rate\":\"9.0000\"},{\"key\":6,\"name\":6,\"rate\":\"7.2000\"},{\"key\":7,\"name\":7,\"rate\":\"6.0000\"},{\"key\":8,\"name\":8,\"rate\":\"7.2000\"},{\"key\":9,\"name\":9,\"rate\":\"9.0000\"},{\"key\":10,\"name\":10,\"rate\":\"12.0000\"},{\"key\":11,\"name\":11,\"rate\":\"18.0000\"},{\"key\":12,\"name\":12,\"rate\":\"36.0000\"}]}]', 0, 0, 1, 1, 0, 0, 0, 0, 1, '[{\"key\":2,\"name\":2,\"num\":2.78},{\"key\":3,\"name\":3,\"num\":5.56},{\"key\":4,\"name\":4,\"num\":8.33},{\"key\":5,\"name\":5,\"num\":11.11},{\"key\":6,\"name\":6,\"num\":13.89},{\"key\":7,\"name\":7,\"num\":16.67},{\"key\":8,\"name\":8,\"num\":13.89},{\"key\":9,\"name\":9,\"num\":11.11},{\"key\":10,\"name\":10,\"num\":8.33},{\"key\":11,\"name\":11,\"num\":5.56},{\"key\":12,\"name\":12,\"num\":2.78}]', 0, 0.00, 0.00, 8, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (11, 2, '北京28', '&lt;p&gt;&lt;img title=&quot;20190506103134_60764.png&quot; alt=&quot;20190417152520_30058.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506103134_60764.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506103120_64515.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 0, 1, 1, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (12, 2, '北京11', '&lt;p&gt;&lt;img title=&quot;20190506103803_47228.png&quot; alt=&quot;20190417153138_37470.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506103803_47228.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506103537_70662.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":2,\"name\":2,\"rate\":\"36.0000\"},{\"key\":3,\"name\":3,\"rate\":\"18.0000\"},{\"key\":4,\"name\":4,\"rate\":\"12.0000\"},{\"key\":5,\"name\":5,\"rate\":\"9.0000\"},{\"key\":6,\"name\":6,\"rate\":\"7.2000\"},{\"key\":7,\"name\":7,\"rate\":\"6.0000\"},{\"key\":8,\"name\":8,\"rate\":\"7.2000\"},{\"key\":9,\"name\":9,\"rate\":\"9.0000\"},{\"key\":10,\"name\":10,\"rate\":\"12.0000\"},{\"key\":11,\"name\":11,\"rate\":\"18.0000\"},{\"key\":12,\"name\":12,\"rate\":\"36.0000\"}]}]', 0, 1, 1, 1, 0, 0, 0, 0, 4, '[{\"key\":2,\"name\":2,\"num\":2.78},{\"key\":3,\"name\":3,\"num\":5.56},{\"key\":4,\"name\":4,\"num\":8.33},{\"key\":5,\"name\":5,\"num\":11.11},{\"key\":6,\"name\":6,\"num\":13.89},{\"key\":7,\"name\":7,\"num\":16.67},{\"key\":8,\"name\":8,\"num\":13.89},{\"key\":9,\"name\":9,\"num\":11.11},{\"key\":10,\"name\":10,\"num\":8.33},{\"key\":11,\"name\":11,\"num\":5.56},{\"key\":12,\"name\":12,\"num\":2.78}]', 0, 0.00, 0.00, 8, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (13, 2, '北京16', '&lt;p&gt;&lt;img title=&quot;20190506104031_87330.png&quot; alt=&quot;20190417153219_34346.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506104031_87330.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506103821_24218.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":3,\"name\":3,\"rate\":\"216.0000\"},{\"key\":4,\"name\":4,\"rate\":\"72.0000\"},{\"key\":5,\"name\":5,\"rate\":\"36.0000\"},{\"key\":6,\"name\":6,\"rate\":\"21.6000\"},{\"key\":7,\"name\":7,\"rate\":\"14.4000\"},{\"key\":8,\"name\":8,\"rate\":\"10.2900\"},{\"key\":9,\"name\":9,\"rate\":\"8.6400\"},{\"key\":10,\"name\":10,\"rate\":\"8.0000\"},{\"key\":11,\"name\":11,\"rate\":\"8.0000\"},{\"key\":12,\"name\":12,\"rate\":\"8.6400\"},{\"key\":13,\"name\":13,\"rate\":\"10.2900\"},{\"key\":14,\"name\":14,\"rate\":\"14.4000\"},{\"key\":15,\"name\":15,\"rate\":\"21.6000\"},{\"key\":16,\"name\":16,\"rate\":\"36.0000\"},{\"key\":17,\"name\":17,\"rate\":\"72.0000\"},{\"key\":18,\"name\":18,\"rate\":\"216.0000\"}]}]', 0, 0, 1, 1, 0, 0, 0, 0, 4, '[{\"key\":3,\"name\":3,\"num\":0.46},{\"key\":4,\"name\":4,\"num\":1.39},{\"key\":5,\"name\":5,\"num\":2.78},{\"key\":6,\"name\":6,\"num\":4.63},{\"key\":7,\"name\":7,\"num\":6.94},{\"key\":8,\"name\":8,\"num\":9.72},{\"key\":9,\"name\":9,\"num\":11.57},{\"key\":10,\"name\":10,\"num\":12.5},{\"key\":11,\"name\":11,\"num\":12.5},{\"key\":12,\"name\":12,\"num\":11.57},{\"key\":13,\"name\":13,\"num\":9.72},{\"key\":14,\"name\":14,\"num\":6.94},{\"key\":15,\"name\":15,\"num\":4.63},{\"key\":16,\"name\":16,\"num\":2.78},{\"key\":17,\"name\":17,\"num\":1.39},{\"key\":18,\"name\":18,\"num\":0.46}]', 0, 0.00, 0.00, 10, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (14, 2, '北京36', '&lt;p&gt;&lt;img title=&quot;20190506104143_89603.png&quot; alt=&quot;20190417153324_87438.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506104143_89603.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506104101_14067.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"rate\":\"100.0000\",\"name\":\"\\u8c79\"},{\"key\":2,\"rate\":\"16.6700\",\"name\":\"\\u987a\"},{\"key\":3,\"rate\":\"3.7000\",\"name\":\"\\u5bf9\"},{\"key\":4,\"rate\":\"2.7800\",\"name\":\"\\u534a\"},{\"key\":5,\"rate\":\"3.3300\",\"name\":\"\\u6742\"}]}]', 0, 0, 1, 1, 0, 0, 0, 0, 3, '[{\"key\":1,\"name\":\"\\u8c79\",\"num\":0.81},{\"key\":2,\"name\":\"\\u987a\",\"num\":26.83},{\"key\":3,\"name\":\"\\u5bf9\",\"num\":4.88},{\"key\":4,\"name\":\"\\u534a\",\"num\":40.65},{\"key\":5,\"name\":\"\\u6742\",\"num\":26.83}]', 3, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (15, 2, '北京28固定', '&lt;p&gt;&lt;img title=&quot;20190506104318_94855.png&quot; alt=&quot;20190417153405_98233.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506104318_94855.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506104238_49793.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"980.0000\",\"name\":0},{\"key\":1,\"rate\":\"326.6634\",\"name\":1},{\"key\":2,\"rate\":\"163.3366\",\"name\":2},{\"key\":3,\"rate\":\"98.0000\",\"name\":3},{\"key\":4,\"rate\":\"65.3268\",\"name\":4},{\"key\":5,\"rate\":\"46.6578\",\"name\":5},{\"key\":6,\"rate\":\"34.9958\",\"name\":6},{\"key\":7,\"rate\":\"27.2146\",\"name\":7},{\"key\":8,\"rate\":\"21.7756\",\"name\":8},{\"key\":9,\"rate\":\"17.8164\",\"name\":9},{\"key\":10,\"rate\":\"15.5526\",\"name\":10},{\"key\":11,\"rate\":\"14.2002\",\"name\":11},{\"key\":12,\"rate\":\"13.4162\",\"name\":12},{\"key\":13,\"rate\":\"13.0634\",\"name\":13},{\"key\":14,\"rate\":\"13.0634\",\"name\":14},{\"key\":15,\"rate\":\"13.4162\",\"name\":15},{\"key\":16,\"rate\":\"14.2002\",\"name\":16},{\"key\":17,\"rate\":\"15.5526\",\"name\":17},{\"key\":18,\"rate\":\"17.8164\",\"name\":18},{\"key\":19,\"rate\":\"21.7756\",\"name\":19},{\"key\":20,\"rate\":\"27.2146\",\"name\":20},{\"key\":21,\"rate\":\"34.9958\",\"name\":21},{\"key\":22,\"rate\":\"46.6578\",\"name\":22},{\"key\":23,\"rate\":\"65.3268\",\"name\":23},{\"key\":24,\"rate\":\"98.0000\",\"name\":24},{\"key\":25,\"rate\":\"163.3366\",\"name\":25},{\"key\":26,\"rate\":\"326.6634\",\"name\":26},{\"key\":27,\"rate\":\"980.0000\",\"name\":27}]}]', 1, 0, 1, 1, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.31},{\"key\":2,\"name\":2,\"num\":0.61},{\"key\":3,\"name\":3,\"num\":1.02},{\"key\":4,\"name\":4,\"num\":1.53},{\"key\":5,\"name\":5,\"num\":2.14},{\"key\":6,\"name\":6,\"num\":2.86},{\"key\":7,\"name\":7,\"num\":3.67},{\"key\":8,\"name\":8,\"num\":4.59},{\"key\":9,\"name\":9,\"num\":5.61},{\"key\":10,\"name\":10,\"num\":6.43},{\"key\":11,\"name\":11,\"num\":7.04},{\"key\":12,\"name\":12,\"num\":7.45},{\"key\":13,\"name\":13,\"num\":7.65},{\"key\":14,\"name\":14,\"num\":7.65},{\"key\":15,\"name\":15,\"num\":7.45},{\"key\":16,\"name\":16,\"num\":7.04},{\"key\":17,\"name\":17,\"num\":6.43},{\"key\":18,\"name\":18,\"num\":5.61},{\"key\":19,\"name\":19,\"num\":4.59},{\"key\":20,\"name\":20,\"num\":3.67},{\"key\":21,\"name\":21,\"num\":2.86},{\"key\":22,\"name\":22,\"num\":2.14},{\"key\":23,\"name\":23,\"num\":1.53},{\"key\":24,\"name\":24,\"num\":1.02},{\"key\":25,\"name\":25,\"num\":0.61},{\"key\":26,\"name\":26,\"num\":0.31},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (16, 3, 'PK10', '&lt;p&gt;&lt;img title=&quot;20190506104629_82342.png&quot; alt=&quot;20190417153539_97022.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506104629_82342.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506104446_33914.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"name\":1,\"rate\":\"10.0000\"},{\"key\":2,\"name\":2,\"rate\":\"10.0000\"},{\"key\":3,\"name\":3,\"rate\":\"10.0000\"},{\"key\":4,\"name\":4,\"rate\":\"10.0000\"},{\"key\":5,\"name\":5,\"rate\":\"10.0000\"},{\"key\":6,\"name\":6,\"rate\":\"10.0000\"},{\"key\":7,\"name\":7,\"rate\":\"10.0000\"},{\"key\":8,\"name\":8,\"rate\":\"10.0000\"},{\"key\":9,\"name\":9,\"rate\":\"10.0000\"},{\"key\":10,\"name\":10,\"rate\":\"10.0000\"}]}]', 0, 0, 8, 2, 0, 0, 0, 0, 0, '[{\"key\":1,\"name\":1,\"num\":10},{\"key\":2,\"name\":2,\"num\":10},{\"key\":3,\"name\":3,\"num\":10},{\"key\":4,\"name\":4,\"num\":10},{\"key\":5,\"name\":5,\"num\":10},{\"key\":6,\"name\":6,\"num\":10},{\"key\":7,\"name\":7,\"num\":10},{\"key\":8,\"name\":8,\"num\":10},{\"key\":9,\"name\":9,\"num\":10},{\"key\":10,\"name\":10,\"num\":10}]', 0, 0.00, 0.00, 7, 1000, '09:30', '23:50', '43', '20');
INSERT INTO `tp_game_type` VALUES (17, 3, 'PK22', '&lt;p&gt;&lt;img title=&quot;20190506104731_62553.png&quot; alt=&quot;20190417153909_84113.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506104731_62553.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506104700_30441.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":6,\"name\":6,\"rate\":\"120.0000\"},{\"key\":7,\"name\":7,\"rate\":\"120.0000\"},{\"key\":8,\"name\":8,\"rate\":\"60.0000\"},{\"key\":9,\"name\":9,\"rate\":\"40.0000\"},{\"key\":10,\"name\":10,\"rate\":\"30.0000\"},{\"key\":11,\"name\":11,\"rate\":\"24.0000\"},{\"key\":12,\"name\":12,\"rate\":\"17.1400\"},{\"key\":13,\"name\":13,\"rate\":\"15.0000\"},{\"key\":14,\"name\":14,\"rate\":\"13.3300\"},{\"key\":15,\"name\":15,\"rate\":\"12.0000\"},{\"key\":16,\"name\":16,\"rate\":\"12.0000\"},{\"key\":17,\"name\":17,\"rate\":\"12.0000\"},{\"key\":18,\"name\":18,\"rate\":\"12.0000\"},{\"key\":19,\"name\":19,\"rate\":\"13.3300\"},{\"key\":20,\"name\":20,\"rate\":\"15.0000\"},{\"key\":21,\"name\":21,\"rate\":\"17.1700\"},{\"key\":22,\"name\":22,\"rate\":\"24.0000\"},{\"key\":23,\"name\":23,\"rate\":\"30.0000\"},{\"key\":24,\"name\":24,\"rate\":\"40.0000\"},{\"key\":25,\"name\":25,\"rate\":\"60.0000\"},{\"key\":26,\"name\":26,\"rate\":\"120.0000\"},{\"key\":27,\"name\":27,\"rate\":\"120.0000\"}]}]', 0, 1, 8, 2, 0, 0, 0, 0, 5, '[{\"key\":6,\"name\":6,\"num\":0.83},{\"key\":7,\"name\":7,\"num\":0.83},{\"key\":8,\"name\":8,\"num\":1.67},{\"key\":9,\"name\":9,\"num\":2.5},{\"key\":10,\"name\":10,\"num\":3.33},{\"key\":11,\"name\":11,\"num\":4.17},{\"key\":12,\"name\":12,\"num\":5.83},{\"key\":13,\"name\":13,\"num\":6.67},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":8.33},{\"key\":16,\"name\":16,\"num\":8.33},{\"key\":17,\"name\":17,\"num\":8.33},{\"key\":18,\"name\":18,\"num\":8.33},{\"key\":19,\"name\":19,\"num\":7.5},{\"key\":20,\"name\":20,\"num\":6.67},{\"key\":21,\"name\":21,\"num\":5.82},{\"key\":22,\"name\":22,\"num\":4.17},{\"key\":23,\"name\":23,\"num\":3.33},{\"key\":24,\"name\":24,\"num\":2.5},{\"key\":25,\"name\":25,\"num\":1.67},{\"key\":26,\"name\":26,\"num\":0.83},{\"key\":27,\"name\":27,\"num\":0.83}]', 0, 0.00, 0.00, 16, 1000, '09:30', '23:50', '43', '20');
INSERT INTO `tp_game_type` VALUES (18, 3, 'PK冠军', '&lt;p&gt;&lt;img title=&quot;20190506104845_61042.png&quot; alt=&quot;20190417154015_94259.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506104845_61042.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506104804_80046.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"name\":1,\"rate\":\"10.0000\"},{\"key\":2,\"name\":2,\"rate\":\"10.0000\"},{\"key\":3,\"name\":3,\"rate\":\"10.0000\"},{\"key\":4,\"name\":4,\"rate\":\"10.0000\"},{\"key\":5,\"name\":5,\"rate\":\"10.0000\"},{\"key\":6,\"name\":6,\"rate\":\"10.0000\"},{\"key\":7,\"name\":7,\"rate\":\"10.0000\"},{\"key\":8,\"name\":8,\"rate\":\"10.0000\"},{\"key\":9,\"name\":9,\"rate\":\"10.0000\"},{\"key\":10,\"name\":10,\"rate\":\"10.0000\"}]}]', 0, 0, 8, 2, 0, 0, 0, 0, 6, '[{\"key\":1,\"name\":1,\"num\":10},{\"key\":2,\"name\":2,\"num\":10},{\"key\":3,\"name\":3,\"num\":10},{\"key\":4,\"name\":4,\"num\":10},{\"key\":5,\"name\":5,\"num\":10},{\"key\":6,\"name\":6,\"num\":10},{\"key\":7,\"name\":7,\"num\":10},{\"key\":8,\"name\":8,\"num\":10},{\"key\":9,\"name\":9,\"num\":10},{\"key\":10,\"name\":10,\"num\":10}]', 0, 0.00, 0.00, 7, 1000, '09:30', '23:50', '43', '20');
INSERT INTO `tp_game_type` VALUES (19, 3, 'PK龙虎', '&lt;p&gt;&lt;img title=&quot;20190506104945_70441.png&quot; alt=&quot;20190417154108_64258.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506104945_70441.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506104906_16036.png', 1, '[{\"part\":1,\"name\":\"\\u9f99\\u864e\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f99\",\"rate\":\"1.9900\"},{\"key\":2,\"name\":\"\\u864e\",\"rate\":\"2.0000\"}]}]', 0, 0, 8, 2, 0, 0, 0, 0, 7, '[{\"key\":1,\"name\":\"\\u9f99\",\"num\":50},{\"key\":2,\"name\":\"\\u864e\",\"num\":50}]', 3, 0.00, 0.00, 0, 1000, '09:30', '23:50', '43', '20');
INSERT INTO `tp_game_type` VALUES (20, 3, 'PK冠亚军', '&lt;p&gt;&lt;img title=&quot;20190506105058_76465.png&quot; alt=&quot;20190417154140_29508.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105058_76465.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105028_49210.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":3,\"name\":3,\"rate\":\"45.0000\"},{\"key\":4,\"name\":4,\"rate\":\"45.0000\"},{\"key\":5,\"name\":5,\"rate\":\"22.5000\"},{\"key\":6,\"name\":6,\"rate\":\"22.5000\"},{\"key\":7,\"name\":7,\"rate\":\"15.0000\"},{\"key\":8,\"name\":8,\"rate\":\"15.0000\"},{\"key\":9,\"name\":9,\"rate\":\"11.2500\"},{\"key\":10,\"name\":10,\"rate\":\"11.2500\"},{\"key\":11,\"name\":11,\"rate\":\"9.0000\"},{\"key\":12,\"name\":12,\"rate\":\"11.2500\"},{\"key\":13,\"name\":13,\"rate\":\"11.2500\"},{\"key\":14,\"name\":14,\"rate\":\"15.0000\"},{\"key\":15,\"name\":15,\"rate\":\"15.0000\"},{\"key\":16,\"name\":16,\"rate\":\"22.5000\"},{\"key\":17,\"name\":17,\"rate\":\"22.5000\"},{\"key\":18,\"name\":18,\"rate\":\"45.0000\"},{\"key\":19,\"name\":19,\"rate\":\"45.0000\"}]}]', 0, 1, 8, 2, 0, 0, 0, 0, 8, '[{\"key\":3,\"name\":3,\"num\":2.22},{\"key\":4,\"name\":4,\"num\":2.22},{\"key\":5,\"name\":5,\"num\":4.44},{\"key\":6,\"name\":6,\"num\":4.44},{\"key\":7,\"name\":7,\"num\":6.67},{\"key\":8,\"name\":8,\"num\":6.67},{\"key\":9,\"name\":9,\"num\":8.89},{\"key\":10,\"name\":10,\"num\":8.89},{\"key\":11,\"name\":11,\"num\":11.11},{\"key\":12,\"name\":12,\"num\":8.89},{\"key\":13,\"name\":13,\"num\":8.89},{\"key\":14,\"name\":14,\"num\":6.67},{\"key\":15,\"name\":15,\"num\":6.67},{\"key\":16,\"name\":16,\"num\":4.44},{\"key\":17,\"name\":17,\"num\":4.44},{\"key\":18,\"name\":18,\"num\":2.22},{\"key\":19,\"name\":19,\"num\":2.22}]', 0, 0.00, 0.00, 11, 1000, '09:30', '23:50', '43', '20');
INSERT INTO `tp_game_type` VALUES (21, 4, '加拿大11', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105249_45349.png&quot; title=&quot;20190506105249_45349.png&quot; alt=&quot;20190417160226_44762.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105215_26989.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":2,\"name\":2,\"rate\":\"36.0000\"},{\"key\":3,\"name\":3,\"rate\":\"18.0000\"},{\"key\":4,\"name\":4,\"rate\":\"12.0000\"},{\"key\":5,\"name\":5,\"rate\":\"9.0000\"},{\"key\":6,\"name\":6,\"rate\":\"7.2000\"},{\"key\":7,\"name\":7,\"rate\":\"6.0000\"},{\"key\":8,\"name\":8,\"rate\":\"7.2000\"},{\"key\":9,\"name\":9,\"rate\":\"9.0000\"},{\"key\":10,\"name\":10,\"rate\":\"12.0000\"},{\"key\":11,\"name\":11,\"rate\":\"18.0000\"},{\"key\":12,\"name\":12,\"rate\":\"36.0000\"}]}]', 0, 0, 1, 3, 0, 0, 0, 0, 4, '[{\"key\":2,\"name\":2,\"num\":2.78},{\"key\":3,\"name\":3,\"num\":5.56},{\"key\":4,\"name\":4,\"num\":8.33},{\"key\":5,\"name\":5,\"num\":11.11},{\"key\":6,\"name\":6,\"num\":13.89},{\"key\":7,\"name\":7,\"num\":16.67},{\"key\":8,\"name\":8,\"num\":13.89},{\"key\":9,\"name\":9,\"num\":11.11},{\"key\":10,\"name\":10,\"num\":8.33},{\"key\":11,\"name\":11,\"num\":5.56},{\"key\":12,\"name\":12,\"num\":2.78}]', 0, 0.00, 0.00, 8, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (22, 4, '加拿大16', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105337_46875.png&quot; title=&quot;20190506105337_46875.png&quot; alt=&quot;20190417160309_55568.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105309_97739.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":3,\"name\":3,\"rate\":\"216.0000\"},{\"key\":4,\"name\":4,\"rate\":\"72.0000\"},{\"key\":5,\"name\":5,\"rate\":\"36.0000\"},{\"key\":6,\"name\":6,\"rate\":\"21.6000\"},{\"key\":7,\"name\":7,\"rate\":\"14.4000\"},{\"key\":8,\"name\":8,\"rate\":\"10.2900\"},{\"key\":9,\"name\":9,\"rate\":\"8.6400\"},{\"key\":10,\"name\":10,\"rate\":\"8.0000\"},{\"key\":11,\"name\":11,\"rate\":\"8.0000\"},{\"key\":12,\"name\":12,\"rate\":\"8.6400\"},{\"key\":13,\"name\":13,\"rate\":\"10.2900\"},{\"key\":14,\"name\":14,\"rate\":\"14.4000\"},{\"key\":15,\"name\":15,\"rate\":\"21.6000\"},{\"key\":16,\"name\":16,\"rate\":\"36.0000\"},{\"key\":17,\"name\":17,\"rate\":\"72.0000\"},{\"key\":18,\"name\":18,\"rate\":\"216.0000\"}]}]', 0, 0, 1, 3, 0, 0, 0, 0, 4, '[{\"key\":3,\"name\":3,\"num\":0.46},{\"key\":4,\"name\":4,\"num\":1.39},{\"key\":5,\"name\":5,\"num\":2.78},{\"key\":6,\"name\":6,\"num\":4.63},{\"key\":7,\"name\":7,\"num\":6.94},{\"key\":8,\"name\":8,\"num\":9.72},{\"key\":9,\"name\":9,\"num\":11.57},{\"key\":10,\"name\":10,\"num\":12.5},{\"key\":11,\"name\":11,\"num\":12.5},{\"key\":12,\"name\":12,\"num\":11.57},{\"key\":13,\"name\":13,\"num\":9.72},{\"key\":14,\"name\":14,\"num\":6.94},{\"key\":15,\"name\":15,\"num\":4.63},{\"key\":16,\"name\":16,\"num\":2.78},{\"key\":17,\"name\":17,\"num\":1.39},{\"key\":18,\"name\":18,\"num\":0.46}]', 0, 0.00, 0.00, 10, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (23, 4, '加拿大28', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105426_57458.png&quot; title=&quot;20190506105426_57458.png&quot; alt=&quot;20190417160359_98897.png&quot;/&gt;&lt;/p&gt;', '1100000000', 200, 120, 160, 100, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105356_11594.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 0, 1, 3, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (24, 4, '加拿大36', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105513_25774.png&quot; title=&quot;20190506105513_25774.png&quot; alt=&quot;20190417160629_90866.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105443_81400.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"rate\":\"100.0000\",\"name\":\"\\u8c79\"},{\"key\":2,\"rate\":\"16.6700\",\"name\":\"\\u987a\"},{\"key\":3,\"rate\":\"3.7000\",\"name\":\"\\u5bf9\"},{\"key\":4,\"rate\":\"2.7800\",\"name\":\"\\u534a\"},{\"key\":5,\"rate\":\"3.3300\",\"name\":\"\\u6742\"}]}]', 0, 0, 1, 3, 0, 0, 0, 0, 3, '[{\"key\":1,\"name\":\"\\u8c79\",\"num\":0.81},{\"key\":2,\"name\":\"\\u987a\",\"num\":26.83},{\"key\":3,\"name\":\"\\u5bf9\",\"num\":4.88},{\"key\":4,\"name\":\"\\u534a\",\"num\":40.65},{\"key\":5,\"name\":\"\\u6742\",\"num\":26.83}]', 3, 0.00, 0.00, 0, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (25, 4, '加拿大定位', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105717_62275.png&quot; title=&quot;20190506105717_62275.png&quot; alt=&quot;20190417160733_30692.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105619_93217.png', 1, '[{\"part\":1,\"name\":\"\\u9f99\\u864e\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":2,\"name\":\"\\u524d\\u4e24\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":3,\"name\":\"\\u540e\\u4e24\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":4,\"name\":\"\\u53f7\\u7801\\u4e00\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":5,\"name\":\"\\u53f7\\u7801\\u4e8c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":6,\"name\":\"\\u53f7\\u7801\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]}]', 1, 1, 3, 3, 0, 0, 0, 0, 3, '', 1, 0.00, 0.00, 0, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (26, 4, '加拿大外围', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105808_71733.png&quot; title=&quot;20190506105808_71733.png&quot; alt=&quot;20190417160831_80266.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105741_85922.png', 1, '[{\"part\":1,\"name\":\"\\u5927\\u5c0f\\u5355\\u53cc\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5c0f\",\"rate\":\"2.1315\"},{\"key\":2,\"name\":\"\\u5927\",\"rate\":\"2.1315\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"2.1315\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"2.1315\"},{\"key\":5,\"name\":\"\\u6781\\u5927\",\"rate\":\"17.4930\"},{\"key\":6,\"name\":\"\\u6781\\u5c0f\",\"rate\":\"17.4930\"},{\"key\":7,\"name\":\"\\u5c0f\\u5355\",\"rate\":\"4.6712\"},{\"key\":8,\"name\":\"\\u5c0f\\u53cc\",\"rate\":\"4.2414\"},{\"key\":9,\"name\":\"\\u5927\\u5355\",\"rate\":\"4.2414\"},{\"key\":10,\"name\":\"\\u5927\\u53cc\",\"rate\":\"4.6712\"}]},{\"part\":2,\"name\":\"\\u9f99\\u864e\\u8c79\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f99\",\"rate\":\"2.9400\"},{\"key\":2,\"name\":\"\\u864e\",\"rate\":\"2.9400\"},{\"key\":3,\"name\":\"\\u8c79\",\"rate\":\"2.9400\"}]}]', 1, 0, 2, 3, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 0, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (27, 4, '加拿大28固定', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506105903_58287.png&quot; title=&quot;20190506105903_58287.png&quot; alt=&quot;20190417160903_99864.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 50000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105830_36928.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"980.0000\",\"name\":0},{\"key\":1,\"rate\":\"326.6634\",\"name\":1},{\"key\":2,\"rate\":\"163.3366\",\"name\":2},{\"key\":3,\"rate\":\"98.0000\",\"name\":3},{\"key\":4,\"rate\":\"65.3268\",\"name\":4},{\"key\":5,\"rate\":\"46.6578\",\"name\":5},{\"key\":6,\"rate\":\"34.9958\",\"name\":6},{\"key\":7,\"rate\":\"27.2146\",\"name\":7},{\"key\":8,\"rate\":\"21.7756\",\"name\":8},{\"key\":9,\"rate\":\"17.8164\",\"name\":9},{\"key\":10,\"rate\":\"15.5526\",\"name\":10},{\"key\":11,\"rate\":\"14.2002\",\"name\":11},{\"key\":12,\"rate\":\"13.4162\",\"name\":12},{\"key\":13,\"rate\":\"13.0634\",\"name\":13},{\"key\":14,\"rate\":\"13.0634\",\"name\":14},{\"key\":15,\"rate\":\"13.4162\",\"name\":15},{\"key\":16,\"rate\":\"14.2002\",\"name\":16},{\"key\":17,\"rate\":\"15.5526\",\"name\":17},{\"key\":18,\"rate\":\"17.8164\",\"name\":18},{\"key\":19,\"rate\":\"21.7756\",\"name\":19},{\"key\":20,\"rate\":\"27.2146\",\"name\":20},{\"key\":21,\"rate\":\"34.9958\",\"name\":21},{\"key\":22,\"rate\":\"46.6578\",\"name\":22},{\"key\":23,\"rate\":\"65.3268\",\"name\":23},{\"key\":24,\"rate\":\"98.0000\",\"name\":24},{\"key\":25,\"rate\":\"163.3366\",\"name\":25},{\"key\":26,\"rate\":\"326.6634\",\"name\":26},{\"key\":27,\"rate\":\"980.0000\",\"name\":27}]}]', 1, 0, 1, 3, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (28, 4, '加拿大百家乐', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506110027_94344.png&quot; title=&quot;20190506110027_94344.png&quot; alt=&quot;20190417160944_23089.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 10000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506105952_99239.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"name\":\"\\u7403\\u4e00\\u95f2\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u7403\\u4e00\\u5e84\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u7403\\u4e00\\u548c\",\"rate\":\"7.8400\"},{\"key\":4,\"name\":\"\\u7403\\u4e8c\\u95f2\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":\"\\u7403\\u4e8c\\u5e84\",\"rate\":\"1.9600\"},{\"key\":6,\"name\":\"\\u7403\\u4e8c\\u548c\",\"rate\":\"7.8400\"}]}]', 1, 1, 4, 3, 0, 0, 0, 0, 3, '', 2, 0.00, 0.00, 0, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (29, 4, '新加拿大百家乐', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506110130_77421.jpg&quot; title=&quot;20190506110130_77421.jpg&quot; alt=&quot;20190417161232_37723.jpg&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506110106_41444.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":8,\"name\":\"\\u4efb\\u610f\\u5bf9\",\"rate\":\"6.0000\"},{\"key\":7,\"name\":\"\\u95f2\\u5bf9\",\"rate\":\"12.0000\"},{\"key\":4,\"name\":\"\\u5927\",\"rate\":\"1.5300\"},{\"key\":5,\"name\":\"\\u5c0f\",\"rate\":\"2.5000\"},{\"key\":6,\"name\":\"\\u5e84\\u5bf9\",\"rate\":\"12.0000\"},{\"key\":9,\"name\":\"\\u5b8c\\u7f8e\\u5bf9\",\"rate\":\"20.0000\"},{\"key\":2,\"name\":\"\\u95f2\",\"rate\":\"1.9700\"},{\"key\":3,\"name\":\"\\u548c\",\"rate\":\"8.0000\"},{\"key\":1,\"name\":\"\\u5e84\",\"rate\":\"1.9700\"}]}]', 1, 0, 5, 3, 0, 0, 0, 0, 2, '', 2, 0.00, 0.00, 0, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (30, 4, '加拿大星座', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506110959_55932.jpg&quot; title=&quot;20190506110959_55932.jpg&quot; alt=&quot;20190417161457_85445.jpg&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 5000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506110804_25743.png', 0, '[{\"part\":1,\"name\":\"\\u5927\\u5c0f\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":2,\"name\":\"\\u5927\\u5c0f\\u6781\\u503c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u6781\\u5927\",\"rate\":\"17.4900\"},{\"key\":2,\"name\":\"\\u6781\\u5c0f\",\"rate\":\"17.4900\"},{\"key\":3,\"name\":\"\\u5c0f\\u5355\",\"rate\":\"3.6300\"},{\"key\":4,\"name\":\"\\u5c0f\\u53cc\",\"rate\":\"4.2300\"},{\"key\":5,\"name\":\"\\u5927\\u5355\",\"rate\":\"4.2300\"},{\"key\":6,\"name\":\"\\u5927\\u53cc\",\"rate\":\"3.6300\"}]},{\"part\":3,\"name\":\"\\u8c79\\u5bf9\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"98.0000\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.6200\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.3300\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.7200\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.2600\"}]},{\"part\":4,\"name\":\"\\u4e94\\u884c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u91d1\",\"rate\":\"4.9000\"},{\"key\":2,\"name\":\"\\u6728\",\"rate\":\"4.9000\"},{\"key\":3,\"name\":\"\\u6c34\",\"rate\":\"4.9000\"},{\"key\":4,\"name\":\"\\u706b\",\"rate\":\"4.9000\"},{\"key\":5,\"name\":\"\\u571f\",\"rate\":\"4.9000\"}]},{\"part\":5,\"name\":\"\\u56db\\u5b63\",\"bet_json\":[{\"key\":1,\"name\":\"\\u6625\",\"rate\":\"3.9200\"},{\"key\":2,\"name\":\"\\u590f\",\"rate\":\"3.9200\"},{\"key\":3,\"name\":\"\\u79cb\",\"rate\":\"3.9200\"},{\"key\":4,\"name\":\"\\u51ac\",\"rate\":\"3.9200\"}]},{\"part\":6,\"name\":\"\\u661f\\u5ea7\",\"bet_json\":[{\"key\":3,\"name\":\"\\u767d\\u7f8a\",\"rate\":\"11.6600\"},{\"key\":4,\"name\":\"\\u91d1\\u725b\",\"rate\":\"11.6600\"},{\"key\":6,\"name\":\"\\u5de8\\u87f9\",\"rate\":\"11.6600\"},{\"key\":5,\"name\":\"\\u53cc\\u5b50\",\"rate\":\"11.6600\"},{\"key\":7,\"name\":\"\\u72ee\\u5b50\",\"rate\":\"11.8000\"},{\"key\":8,\"name\":\"\\u5904\\u5973\",\"rate\":\"12.0900\"},{\"key\":9,\"name\":\"\\u5929\\u79e4\",\"rate\":\"12.0900\"},{\"key\":10,\"name\":\"\\u5929\\u874e\",\"rate\":\"11.8000\"},{\"key\":11,\"name\":\"\\u5c04\\u624b\",\"rate\":\"11.6600\"},{\"key\":12,\"name\":\"\\u9b54\\u874e\",\"rate\":\"11.6600\"},{\"key\":1,\"name\":\"\\u6c34\\u74f6\",\"rate\":\"11.6600\"},{\"key\":2,\"name\":\"\\u53cc\\u9c7c\",\"rate\":\"11.6600\"}]},{\"part\":7,\"name\":\"\\u751f\\u8096\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f20\",\"rate\":\"122.0000\"},{\"key\":2,\"name\":\"\\u725b\",\"rate\":\"44.5400\"},{\"key\":3,\"name\":\"\\u864e\",\"rate\":\"24.5000\"},{\"key\":4,\"name\":\"\\u5154\",\"rate\":\"23.3200\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"17.4900\"},{\"key\":6,\"name\":\"\\u86c7\",\"rate\":\"13.6000\"},{\"key\":7,\"name\":\"\\u9a6c\",\"rate\":\"10.8800\"},{\"key\":8,\"name\":\"\\u7f8a\",\"rate\":\"8.9000\"},{\"key\":9,\"name\":\"\\u7334\",\"rate\":\"7.7700\"},{\"key\":10,\"name\":\"\\u9e21\",\"rate\":\"7.1000\"},{\"key\":11,\"name\":\"\\u72d7\",\"rate\":\"6.7000\"},{\"key\":12,\"name\":\"\\u732a\",\"rate\":\"6.5300\"}]},{\"part\":8,\"name\":\"\\u74031:\\u74033\",\"bet_json\":[{\"key\":1,\"name\":\"1\\uff1a3\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"1\\uff1a3\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"1\\uff1a3\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":9,\"name\":\"\\u74032:\\u74033\",\"bet_json\":[{\"key\":1,\"name\":\"2\\uff1a3\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"2\\uff1a3\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"2\\uff1a3\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":10,\"name\":\"\\u524d\\u4e8c\\u5408\\u5e76\",\"bet_json\":[{\"key\":1,\"name\":\"\\u524d\\u4e8c\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u524d\\u4e8c\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u524d\\u4e8c\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u524d\\u4e8c\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":10,\"name\":\"\\u540e\\u4e8c\\u5408\\u5e76\",\"bet_json\":[{\"key\":1,\"name\":\"\\u540e\\u4e8c\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u540e\\u4e8c\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u540e\\u4e8c\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u540e\\u4e8c\\u53cc\",\"rate\":\"1.9600\"}]}]', 1, 0, 6, 3, 0, 0, 0, 0, 3, '', 1, 0.00, 0.00, 0, 1000, '20:00', '次日19:00', '394', '3.5');
INSERT INTO `tp_game_type` VALUES (31, 5, '韩国28', '&lt;p&gt;&lt;img title=&quot;20190506111114_81209.png&quot; alt=&quot;20190417154435_25912.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111114_81209.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111042_84822.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 0, 1, 4, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '07:00', '次日05:00', '880', '1.5');
INSERT INTO `tp_game_type` VALUES (32, 5, '韩国16', '&lt;p&gt;&lt;img title=&quot;20190506111204_23036.png&quot; alt=&quot;20190417154519_75222.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111204_23036.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111135_36446.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":3,\"name\":3,\"rate\":\"216.0000\"},{\"key\":4,\"name\":4,\"rate\":\"72.0000\"},{\"key\":5,\"name\":5,\"rate\":\"36.0000\"},{\"key\":6,\"name\":6,\"rate\":\"21.6000\"},{\"key\":7,\"name\":7,\"rate\":\"14.4000\"},{\"key\":8,\"name\":8,\"rate\":\"10.2900\"},{\"key\":9,\"name\":9,\"rate\":\"8.6400\"},{\"key\":10,\"name\":10,\"rate\":\"8.0000\"},{\"key\":11,\"name\":11,\"rate\":\"8.0000\"},{\"key\":12,\"name\":12,\"rate\":\"8.6400\"},{\"key\":13,\"name\":13,\"rate\":\"10.2900\"},{\"key\":14,\"name\":14,\"rate\":\"14.4000\"},{\"key\":15,\"name\":15,\"rate\":\"21.6000\"},{\"key\":16,\"name\":16,\"rate\":\"36.0000\"},{\"key\":17,\"name\":17,\"rate\":\"72.0000\"},{\"key\":18,\"name\":18,\"rate\":\"216.0000\"}]}]', 0, 0, 1, 4, 0, 0, 0, 0, 3, '[{\"key\":3,\"name\":3,\"num\":0.46},{\"key\":4,\"name\":4,\"num\":1.39},{\"key\":5,\"name\":5,\"num\":2.78},{\"key\":6,\"name\":6,\"num\":4.63},{\"key\":7,\"name\":7,\"num\":6.94},{\"key\":8,\"name\":8,\"num\":9.72},{\"key\":9,\"name\":9,\"num\":11.57},{\"key\":10,\"name\":10,\"num\":12.5},{\"key\":11,\"name\":11,\"num\":12.5},{\"key\":12,\"name\":12,\"num\":11.57},{\"key\":13,\"name\":13,\"num\":9.72},{\"key\":14,\"name\":14,\"num\":6.94},{\"key\":15,\"name\":15,\"num\":4.63},{\"key\":16,\"name\":16,\"num\":2.78},{\"key\":17,\"name\":17,\"num\":1.39},{\"key\":18,\"name\":18,\"num\":0.46}]', 0, 0.00, 0.00, 10, 1000, '07:00', '次日05:00', '880', '1.5');
INSERT INTO `tp_game_type` VALUES (33, 5, '韩国36', '&lt;p&gt;&lt;img title=&quot;20190506111251_85252.png&quot; alt=&quot;20190417154601_19169.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111251_85252.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111227_33450.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"rate\":\"100.0000\",\"name\":\"\\u8c79\"},{\"key\":2,\"rate\":\"16.6700\",\"name\":\"\\u987a\"},{\"key\":3,\"rate\":\"3.7000\",\"name\":\"\\u5bf9\"},{\"key\":4,\"rate\":\"2.7800\",\"name\":\"\\u534a\"},{\"key\":5,\"rate\":\"3.3300\",\"name\":\"\\u6742\"}]}]', 0, 1, 1, 4, 0, 0, 0, 0, 3, '[{\"key\":1,\"name\":\"\\u8c79\",\"num\":0.81},{\"key\":2,\"name\":\"\\u987a\",\"num\":26.83},{\"key\":3,\"name\":\"\\u5bf9\",\"num\":4.88},{\"key\":4,\"name\":\"\\u534a\",\"num\":40.65},{\"key\":5,\"name\":\"\\u6742\",\"num\":26.83}]', 3, 0.00, 0.00, 0, 1000, '07:00', '次日05:00', '880', '1.5');
INSERT INTO `tp_game_type` VALUES (34, 5, '韩国10', '&lt;p&gt;&lt;img title=&quot;20190506111349_99417.png&quot; alt=&quot;20190417154632_76829.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111349_99417.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111330_47728.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":1,\"name\":1,\"rate\":\"10.0000\"},{\"key\":2,\"name\":2,\"rate\":\"10.0000\"},{\"key\":3,\"name\":3,\"rate\":\"10.0000\"},{\"key\":4,\"name\":4,\"rate\":\"10.0000\"},{\"key\":5,\"name\":5,\"rate\":\"10.0000\"},{\"key\":6,\"name\":6,\"rate\":\"10.0000\"},{\"key\":7,\"name\":7,\"rate\":\"10.0000\"},{\"key\":8,\"name\":8,\"rate\":\"10.0000\"},{\"key\":9,\"name\":9,\"rate\":\"10.0000\"},{\"key\":10,\"name\":10,\"rate\":\"10.0000\"}]}]', 0, 0, 1, 4, 0, 0, 0, 0, 1, '[{\"key\":1,\"name\":1,\"num\":10},{\"key\":2,\"name\":2,\"num\":10},{\"key\":3,\"name\":3,\"num\":10},{\"key\":4,\"name\":4,\"num\":10},{\"key\":5,\"name\":5,\"num\":10},{\"key\":6,\"name\":6,\"num\":10},{\"key\":7,\"name\":7,\"num\":10},{\"key\":8,\"name\":8,\"num\":10},{\"key\":9,\"name\":9,\"num\":10},{\"key\":10,\"name\":10,\"num\":10}]', 0, 0.00, 0.00, 7, 1000, '07:00', '次日05:00', '880', '1.5');
INSERT INTO `tp_game_type` VALUES (35, 5, '韩国外围', '&lt;p&gt;&lt;img title=&quot;20190506111504_58559.png&quot; alt=&quot;20190417154821_79531.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111504_58559.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111440_68985.png', 1, '[{\"part\":1,\"name\":\"\\u5927\\u5c0f\\u5355\\u53cc\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5c0f\",\"rate\":\"2.1315\"},{\"key\":2,\"name\":\"\\u5927\",\"rate\":\"2.1315\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"2.1315\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"2.1315\"},{\"key\":5,\"name\":\"\\u6781\\u5927\",\"rate\":\"17.4930\"},{\"key\":6,\"name\":\"\\u6781\\u5c0f\",\"rate\":\"17.4930\"},{\"key\":7,\"name\":\"\\u5c0f\\u5355\",\"rate\":\"4.6712\"},{\"key\":8,\"name\":\"\\u5c0f\\u53cc\",\"rate\":\"4.2414\"},{\"key\":9,\"name\":\"\\u5927\\u5355\",\"rate\":\"4.2414\"},{\"key\":10,\"name\":\"\\u5927\\u53cc\",\"rate\":\"4.6712\"}]},{\"part\":2,\"name\":\"\\u9f99\\u864e\\u8c79\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f99\",\"rate\":\"2.9400\"},{\"key\":2,\"name\":\"\\u864e\",\"rate\":\"2.9400\"},{\"key\":3,\"name\":\"\\u8c79\",\"rate\":\"2.9400\"}]}]', 1, 1, 2, 4, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 0, 1000, '07:00', '次日05:00', '880', '1.5');
INSERT INTO `tp_game_type` VALUES (36, 5, '韩国定位', '&lt;p&gt;&lt;img title=&quot;20190506111608_63671.png&quot; alt=&quot;20190417154918_63354.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111608_63671.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111537_63153.png', 1, '[{\"part\":1,\"name\":\"\\u9f99\\u864e\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":2,\"name\":\"\\u524d\\u4e24\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":3,\"name\":\"\\u540e\\u4e24\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":4,\"name\":\"\\u53f7\\u7801\\u4e00\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":5,\"name\":\"\\u53f7\\u7801\\u4e8c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":6,\"name\":\"\\u53f7\\u7801\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]}]', 1, 0, 3, 4, 0, 0, 0, 0, 3, '', 1, 0.00, 0.00, 0, 1000, '07:00', '次日05:00', '880', '1.5');
INSERT INTO `tp_game_type` VALUES (37, 6, '腾讯分分彩', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111740_59195.png&quot; title=&quot;20190506111740_59195.png&quot; alt=&quot;20190417155012_16791.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111703_66811.png', 1, '[{\"part\":1,\"name\":\"\\u524d\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"96.0000\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.0032\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.5520\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.6688\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.1968\"}]},{\"part\":2,\"name\":\"\\u4e2d\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"96.0000\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.0032\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.5520\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.6688\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.1968\"}]},{\"part\":3,\"name\":\"\\u540e\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"96.0000\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.0032\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.5520\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.6688\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.1968\"}]},{\"part\":4,\"name\":\"\\u7b2c\\u4e00\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":5,\"name\":\"\\u7b2c\\u4e8c\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":6,\"name\":\"\\u7b2c\\u4e09\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":7,\"name\":\"\\u7b2c\\u56db\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":8,\"name\":\"\\u7b2c\\u4e94\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]}]', 0, 0, 9, 6, 0, 0, 0, 0, 0, '', 1, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (38, 6, '腾讯28', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506111858_18959.png&quot; title=&quot;20190506111858_18959.png&quot; alt=&quot;20190417155059_12932.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111824_89460.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 1, 1, 6, 0, 0, 0, 0, 0, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (39, 6, '腾讯百家乐', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506112008_17624.jpg&quot; title=&quot;20190506112008_17624.jpg&quot; alt=&quot;20190417155430_62832.jpg&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506111941_10392.png', 0, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":8,\"name\":\"\\u4efb\\u610f\\u5bf9\",\"rate\":\"6.0000\"},{\"key\":7,\"name\":\"\\u95f2\\u5bf9\",\"rate\":\"12.0000\"},{\"key\":4,\"name\":\"\\u5927\",\"rate\":\"1.5300\"},{\"key\":5,\"name\":\"\\u5c0f\",\"rate\":\"2.5000\"},{\"key\":6,\"name\":\"\\u5e84\\u5bf9\",\"rate\":\"12.0000\"},{\"key\":9,\"name\":\"\\u5b8c\\u7f8e\\u5bf9\",\"rate\":\"20.0000\"},{\"key\":2,\"name\":\"\\u95f2\",\"rate\":\"1.9700\"},{\"key\":3,\"name\":\"\\u548c\",\"rate\":\"8.0000\"},{\"key\":1,\"name\":\"\\u5e84\",\"rate\":\"1.9700\"}]}]', 0, 0, 5, 6, 0, 0, 0, 0, 0, '', 2, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (40, 6, '腾讯星座', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506112122_68071.jpg&quot; title=&quot;20190506112122_68071.jpg&quot; alt=&quot;20190417155653_63014.jpg&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506112114_37165.png', 0, '[{\"part\":1,\"name\":\"\\u5927\\u5c0f\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":2,\"name\":\"\\u5927\\u5c0f\\u6781\\u503c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u6781\\u5927\",\"rate\":\"17.4900\"},{\"key\":2,\"name\":\"\\u6781\\u5c0f\",\"rate\":\"17.4900\"},{\"key\":3,\"name\":\"\\u5c0f\\u5355\",\"rate\":\"3.6300\"},{\"key\":4,\"name\":\"\\u5c0f\\u53cc\",\"rate\":\"4.2300\"},{\"key\":5,\"name\":\"\\u5927\\u5355\",\"rate\":\"4.2300\"},{\"key\":6,\"name\":\"\\u5927\\u53cc\",\"rate\":\"3.6300\"}]},{\"part\":3,\"name\":\"\\u8c79\\u5bf9\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"98.0000\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.6200\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.3300\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.7200\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.2600\"}]},{\"part\":4,\"name\":\"\\u4e94\\u884c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u91d1\",\"rate\":\"4.9000\"},{\"key\":2,\"name\":\"\\u6728\",\"rate\":\"4.9000\"},{\"key\":3,\"name\":\"\\u6c34\",\"rate\":\"4.9000\"},{\"key\":4,\"name\":\"\\u706b\",\"rate\":\"4.9000\"},{\"key\":5,\"name\":\"\\u571f\",\"rate\":\"4.9000\"}]},{\"part\":5,\"name\":\"\\u56db\\u5b63\",\"bet_json\":[{\"key\":1,\"name\":\"\\u6625\",\"rate\":\"3.9200\"},{\"key\":2,\"name\":\"\\u590f\",\"rate\":\"3.9200\"},{\"key\":3,\"name\":\"\\u79cb\",\"rate\":\"3.9200\"},{\"key\":4,\"name\":\"\\u51ac\",\"rate\":\"3.9200\"}]},{\"part\":6,\"name\":\"\\u661f\\u5ea7\",\"bet_json\":[{\"key\":3,\"name\":\"\\u767d\\u7f8a\",\"rate\":\"11.6600\"},{\"key\":4,\"name\":\"\\u91d1\\u725b\",\"rate\":\"11.6600\"},{\"key\":6,\"name\":\"\\u5de8\\u87f9\",\"rate\":\"11.6600\"},{\"key\":5,\"name\":\"\\u53cc\\u5b50\",\"rate\":\"11.6600\"},{\"key\":7,\"name\":\"\\u72ee\\u5b50\",\"rate\":\"11.8000\"},{\"key\":8,\"name\":\"\\u5904\\u5973\",\"rate\":\"12.0900\"},{\"key\":9,\"name\":\"\\u5929\\u79e4\",\"rate\":\"12.0900\"},{\"key\":10,\"name\":\"\\u5929\\u874e\",\"rate\":\"11.8000\"},{\"key\":11,\"name\":\"\\u5c04\\u624b\",\"rate\":\"11.6600\"},{\"key\":12,\"name\":\"\\u9b54\\u874e\",\"rate\":\"11.6600\"},{\"key\":1,\"name\":\"\\u6c34\\u74f6\",\"rate\":\"11.6600\"},{\"key\":2,\"name\":\"\\u53cc\\u9c7c\",\"rate\":\"11.6600\"}]},{\"part\":7,\"name\":\"\\u751f\\u8096\",\"bet_json\":[{\"key\":1,\"name\":\"\\u9f20\",\"rate\":\"122.0000\"},{\"key\":2,\"name\":\"\\u725b\",\"rate\":\"44.5400\"},{\"key\":3,\"name\":\"\\u864e\",\"rate\":\"24.5000\"},{\"key\":4,\"name\":\"\\u5154\",\"rate\":\"23.3200\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"17.4900\"},{\"key\":6,\"name\":\"\\u86c7\",\"rate\":\"13.6000\"},{\"key\":7,\"name\":\"\\u9a6c\",\"rate\":\"10.8800\"},{\"key\":8,\"name\":\"\\u7f8a\",\"rate\":\"8.9000\"},{\"key\":9,\"name\":\"\\u7334\",\"rate\":\"7.7700\"},{\"key\":10,\"name\":\"\\u9e21\",\"rate\":\"7.1000\"},{\"key\":11,\"name\":\"\\u72d7\",\"rate\":\"6.7000\"},{\"key\":12,\"name\":\"\\u732a\",\"rate\":\"6.5300\"}]},{\"part\":8,\"name\":\"\\u74031:\\u74033\",\"bet_json\":[{\"key\":1,\"name\":\"1\\uff1a3\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"1\\uff1a3\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"1\\uff1a3\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":9,\"name\":\"\\u74032:\\u74033\",\"bet_json\":[{\"key\":1,\"name\":\"2\\uff1a3\\u9f99\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"2\\uff1a3\\u864e\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"2\\uff1a3\\u548c\",\"rate\":\"7.8400\"}]},{\"part\":10,\"name\":\"\\u524d\\u4e8c\\u5408\\u5e76\",\"bet_json\":[{\"key\":1,\"name\":\"\\u524d\\u4e8c\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u524d\\u4e8c\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u524d\\u4e8c\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u524d\\u4e8c\\u53cc\",\"rate\":\"1.9600\"}]},{\"part\":10,\"name\":\"\\u540e\\u4e8c\\u5408\\u5e76\",\"bet_json\":[{\"key\":1,\"name\":\"\\u540e\\u4e8c\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u540e\\u4e8c\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u540e\\u4e8c\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u540e\\u4e8c\\u53cc\",\"rate\":\"1.9600\"}]}]', 0, 0, 6, 6, 0, 0, 0, 0, 0, '', 2, 0.00, 0.00, 0, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (41, 6, '腾讯16', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506112254_70959.png&quot; title=&quot;20190506112254_70959.png&quot; alt=&quot;20190417155735_98055.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506112226_34535.png', 0, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":3,\"name\":3,\"rate\":\"216.0000\"},{\"key\":4,\"name\":4,\"rate\":\"72.0000\"},{\"key\":5,\"name\":5,\"rate\":\"36.0000\"},{\"key\":6,\"name\":6,\"rate\":\"21.6000\"},{\"key\":7,\"name\":7,\"rate\":\"14.4000\"},{\"key\":8,\"name\":8,\"rate\":\"10.2900\"},{\"key\":9,\"name\":9,\"rate\":\"8.6400\"},{\"key\":10,\"name\":10,\"rate\":\"8.0000\"},{\"key\":11,\"name\":11,\"rate\":\"8.0000\"},{\"key\":12,\"name\":12,\"rate\":\"8.6400\"},{\"key\":13,\"name\":13,\"rate\":\"10.2900\"},{\"key\":14,\"name\":14,\"rate\":\"14.4000\"},{\"key\":15,\"name\":15,\"rate\":\"21.6000\"},{\"key\":16,\"name\":16,\"rate\":\"36.0000\"},{\"key\":17,\"name\":17,\"rate\":\"72.0000\"},{\"key\":18,\"name\":18,\"rate\":\"216.0000\"}]}]', 0, 0, 1, 6, 0, 0, 0, 0, 0, '[{\"key\":3,\"name\":3,\"num\":0.46},{\"key\":4,\"name\":4,\"num\":1.39},{\"key\":5,\"name\":5,\"num\":2.78},{\"key\":6,\"name\":6,\"num\":4.63},{\"key\":7,\"name\":7,\"num\":6.94},{\"key\":8,\"name\":8,\"num\":9.72},{\"key\":9,\"name\":9,\"num\":11.57},{\"key\":10,\"name\":10,\"num\":12.5},{\"key\":11,\"name\":11,\"num\":12.5},{\"key\":12,\"name\":12,\"num\":11.57},{\"key\":13,\"name\":13,\"num\":9.72},{\"key\":14,\"name\":14,\"num\":6.94},{\"key\":15,\"name\":15,\"num\":4.63},{\"key\":16,\"name\":16,\"num\":2.78},{\"key\":17,\"name\":17,\"num\":1.39},{\"key\":18,\"name\":18,\"num\":0.46}]', 0, 0.00, 0.00, 10, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (42, 6, '腾讯11', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506112343_99537.png&quot; title=&quot;20190506112343_99537.png&quot; alt=&quot;20190417155753_90524.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506112319_91405.png', 0, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":2,\"name\":2,\"rate\":\"36.0000\"},{\"key\":3,\"name\":3,\"rate\":\"18.0000\"},{\"key\":4,\"name\":4,\"rate\":\"12.0000\"},{\"key\":5,\"name\":5,\"rate\":\"9.0000\"},{\"key\":6,\"name\":6,\"rate\":\"7.2000\"},{\"key\":7,\"name\":7,\"rate\":\"6.0000\"},{\"key\":8,\"name\":8,\"rate\":\"7.2000\"},{\"key\":9,\"name\":9,\"rate\":\"9.0000\"},{\"key\":10,\"name\":10,\"rate\":\"12.0000\"},{\"key\":11,\"name\":11,\"rate\":\"18.0000\"},{\"key\":12,\"name\":12,\"rate\":\"36.0000\"}]}]', 0, 0, 1, 6, 0, 0, 0, 0, 0, '[{\"key\":2,\"name\":2,\"num\":2.78},{\"key\":3,\"name\":3,\"num\":5.56},{\"key\":4,\"name\":4,\"num\":8.33},{\"key\":5,\"name\":5,\"num\":11.11},{\"key\":6,\"name\":6,\"num\":13.89},{\"key\":7,\"name\":7,\"num\":16.67},{\"key\":8,\"name\":8,\"num\":13.89},{\"key\":9,\"name\":9,\"num\":11.11},{\"key\":10,\"name\":10,\"num\":8.33},{\"key\":11,\"name\":11,\"num\":5.56},{\"key\":12,\"name\":12,\"num\":2.78}]', 0, 0.00, 0.00, 8, 1000, '09:05', '00:00', '179', '5');
INSERT INTO `tp_game_type` VALUES (43, 7, '重庆时时彩', '&lt;p&gt;&lt;img title=&quot;20190506112707_77093.jpg&quot; alt=&quot;20190417160040_83756.jpg&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506112707_77093.jpg&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506112618_28430.png', 1, '[{\"part\":1,\"name\":\"\\u603b\\u548c-\\u9f99\\u864e\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.9600\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.9600\"},{\"key\":7,\"name\":\"\\u548c\",\"rate\":\"1.9600\"}]},{\"part\":2,\"name\":\"\\u7b2c\\u4e00\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":3,\"name\":\"\\u7b2c\\u4e8c\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":4,\"name\":\"\\u7b2c\\u4e09\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":5,\"name\":\"\\u7b2c\\u56db\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":6,\"name\":\"\\u7b2c\\u4e94\\u7403\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.9600\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.9600\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.9600\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.9600\"},{\"key\":5,\"name\":0,\"rate\":\"9.8000\"},{\"key\":6,\"name\":1,\"rate\":\"9.8000\"},{\"key\":7,\"name\":2,\"rate\":\"9.8000\"},{\"key\":8,\"name\":3,\"rate\":\"9.8000\"},{\"key\":9,\"name\":4,\"rate\":\"9.8000\"},{\"key\":10,\"name\":5,\"rate\":\"9.8000\"},{\"key\":11,\"name\":6,\"rate\":\"9.8000\"},{\"key\":12,\"name\":7,\"rate\":\"9.8000\"},{\"key\":13,\"name\":8,\"rate\":\"9.8000\"},{\"key\":14,\"name\":9,\"rate\":\"9.8000\"}]},{\"part\":7,\"name\":\"\\u524d\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"58.8000\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.3366\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.2634\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.7244\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.2634\"}]},{\"part\":8,\"name\":\"\\u4e2d\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"58.8000\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.3366\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.2634\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.7244\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.2634\"}]},{\"part\":9,\"name\":\"\\u540e\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u8c79\",\"rate\":\"58.8000\"},{\"key\":2,\"name\":\"\\u987a\",\"rate\":\"16.3366\"},{\"key\":3,\"name\":\"\\u5bf9\",\"rate\":\"3.2634\"},{\"key\":4,\"name\":\"\\u534a\",\"rate\":\"2.7244\"},{\"key\":5,\"name\":\"\\u6742\",\"rate\":\"3.2634\"}]}]', 1, 0, 7, 5, 0, 0, 0, 0, 0, '', 1, 0.00, 0.00, 0, 1000, '07:10', '次日02:50', '59', '20');
INSERT INTO `tp_game_type` VALUES (44, 5, '韩国28固定', '&lt;p&gt;&lt;img title=&quot;20190506112811_36092.png&quot; alt=&quot;20190417160136_12892.png&quot; src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-05/20190506112811_36092.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 20000000, 1200000000, 'http://img.jinlong28.com/Uploads/image/default/2019-05/20190506112749_20262.png', 0, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"980.0000\",\"name\":0},{\"key\":1,\"rate\":\"326.6634\",\"name\":1},{\"key\":2,\"rate\":\"163.3366\",\"name\":2},{\"key\":3,\"rate\":\"98.0000\",\"name\":3},{\"key\":4,\"rate\":\"65.3268\",\"name\":4},{\"key\":5,\"rate\":\"46.6578\",\"name\":5},{\"key\":6,\"rate\":\"34.9958\",\"name\":6},{\"key\":7,\"rate\":\"27.2146\",\"name\":7},{\"key\":8,\"rate\":\"21.7756\",\"name\":8},{\"key\":9,\"rate\":\"17.8164\",\"name\":9},{\"key\":10,\"rate\":\"15.5526\",\"name\":10},{\"key\":11,\"rate\":\"14.2002\",\"name\":11},{\"key\":12,\"rate\":\"13.4162\",\"name\":12},{\"key\":13,\"rate\":\"13.0634\",\"name\":13},{\"key\":14,\"rate\":\"13.0634\",\"name\":14},{\"key\":15,\"rate\":\"13.4162\",\"name\":15},{\"key\":16,\"rate\":\"14.2002\",\"name\":16},{\"key\":17,\"rate\":\"15.5526\",\"name\":17},{\"key\":18,\"rate\":\"17.8164\",\"name\":18},{\"key\":19,\"rate\":\"21.7756\",\"name\":19},{\"key\":20,\"rate\":\"27.2146\",\"name\":20},{\"key\":21,\"rate\":\"34.9958\",\"name\":21},{\"key\":22,\"rate\":\"46.6578\",\"name\":22},{\"key\":23,\"rate\":\"65.3268\",\"name\":23},{\"key\":24,\"rate\":\"98.0000\",\"name\":24},{\"key\":25,\"rate\":\"163.3366\",\"name\":25},{\"key\":26,\"rate\":\"326.6634\",\"name\":26},{\"key\":27,\"rate\":\"980.0000\",\"name\":27}]}]', 1, 0, 1, 4, 0, 0, 0, 0, 3, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 21, 1000, '00:00', '24:00', '960', '1.5');
INSERT INTO `tp_game_type` VALUES (45, 8, '比特币1分28', '&lt;p&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-07/20190726173558_85337.png&quot; title=&quot;20190726173558_85337.png&quot; alt=&quot;游戏乐园 - 好运来28|好运来娱乐|豆玩28.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-07/20190726171116_55376.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 0, 1, 7, 0, 0, 0, 0, 4, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 0, 1000, '00:00', '23:59', '1440', '1');
INSERT INTO `tp_game_type` VALUES (46, 8, '比特币1分赛车', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-07/20190726174013_15558.png&quot; title=&quot;20190726174013_15558.png&quot; alt=&quot;游戏乐园 - 好运来28|好运来娱乐|豆玩28.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-07/20190726173736_83972.png', 1, '[{\"part\":1,\"name\":\"\\u51a0\\u4e9a\\u519b\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u51a0\\u4e9a\\u5927\",\"rate\":\"2.205\"},{\"key\":2,\"name\":\"\\u51a0\\u4e9a\\u5c0f\",\"rate\":\"1.764\"},{\"key\":3,\"name\":\"\\u51a0\\u4e9a\\u5355\",\"rate\":\"1.764\"},{\"key\":4,\"name\":\"\\u51a0\\u4e9a\\u53cc\",\"rate\":\"2.205\"}]},{\"part\":2,\"name\":\"\\u8d5b\\u8f66\\u4e00\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":3,\"name\":\"\\u8d5b\\u8f66\\u4e8c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":4,\"name\":\"\\u8d5b\\u8f66\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":5,\"name\":\"\\u8d5b\\u8f66\\u56db\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":6,\"name\":\"\\u8d5b\\u8f66\\u4e94\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":7,\"name\":\"\\u8d5b\\u8f66\\u516d\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":8,\"name\":\"\\u8d5b\\u8f66\\u4e03\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":9,\"name\":\"\\u8d5b\\u8f66\\u516b\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":10,\"name\":\"\\u8d5b\\u8f66\\u4e5d\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":11,\"name\":\"\\u8d5b\\u8f66\\u5341\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]}]', 1, 0, 10, 7, 0, 0, 0, 0, 0, '', 1, 0.00, 0.00, 0, 1000, '00:00', '23:59', '1440', '1');
INSERT INTO `tp_game_type` VALUES (47, 8, '比特币1.5分28', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-07/20190726174210_32181.png&quot; title=&quot;20190726174210_32181.png&quot; alt=&quot;3.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-07/20190726174049_38512.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 0, 1, 8, 0, 0, 0, 0, 4, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 0, 1000, '00:00', '23:58:30', '960', '1.5');
INSERT INTO `tp_game_type` VALUES (48, 8, '比特币1.5分赛车', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-07/20190726174358_71315.png&quot; title=&quot;20190726174358_71315.png&quot; alt=&quot;4.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-07/20190726174241_57138.png', 1, '[{\"part\":1,\"name\":\"\\u51a0\\u4e9a\\u519b\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u51a0\\u4e9a\\u5927\",\"rate\":\"2.205\"},{\"key\":2,\"name\":\"\\u51a0\\u4e9a\\u5c0f\",\"rate\":\"1.764\"},{\"key\":3,\"name\":\"\\u51a0\\u4e9a\\u5355\",\"rate\":\"1.764\"},{\"key\":4,\"name\":\"\\u51a0\\u4e9a\\u53cc\",\"rate\":\"2.205\"}]},{\"part\":2,\"name\":\"\\u8d5b\\u8f66\\u4e00\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":3,\"name\":\"\\u8d5b\\u8f66\\u4e8c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":4,\"name\":\"\\u8d5b\\u8f66\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":5,\"name\":\"\\u8d5b\\u8f66\\u56db\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":6,\"name\":\"\\u8d5b\\u8f66\\u4e94\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":7,\"name\":\"\\u8d5b\\u8f66\\u516d\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":8,\"name\":\"\\u8d5b\\u8f66\\u4e03\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":9,\"name\":\"\\u8d5b\\u8f66\\u516b\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":10,\"name\":\"\\u8d5b\\u8f66\\u4e5d\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":11,\"name\":\"\\u8d5b\\u8f66\\u5341\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]}]', 1, 0, 10, 8, 0, 0, 0, 0, 0, '', 1, 0.00, 0.00, 0, 1000, '00:00', '23:58:30', '960', '1.5');
INSERT INTO `tp_game_type` VALUES (49, 8, '比特币3分28', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-07/20190726174551_87742.png&quot; title=&quot;20190726174551_87742.png&quot; alt=&quot;5.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-07/20190726174436_60636.png', 1, '[{\"part\":1,\"name\":\"\",\"bet_json\":[{\"key\":0,\"rate\":\"1000.0000\",\"name\":0},{\"key\":1,\"rate\":\"333.3300\",\"name\":1},{\"key\":2,\"rate\":\"166.6700\",\"name\":2},{\"key\":3,\"rate\":\"100.0000\",\"name\":3},{\"key\":4,\"rate\":\"66.6600\",\"name\":4},{\"key\":5,\"rate\":\"47.6100\",\"name\":5},{\"key\":6,\"rate\":\"35.7100\",\"name\":6},{\"key\":7,\"rate\":\"27.7700\",\"name\":7},{\"key\":8,\"rate\":\"22.2200\",\"name\":8},{\"key\":9,\"rate\":\"18.1800\",\"name\":9},{\"key\":10,\"rate\":\"15.8700\",\"name\":10},{\"key\":11,\"rate\":\"14.4900\",\"name\":11},{\"key\":12,\"rate\":\"13.6900\",\"name\":12},{\"key\":13,\"rate\":\"13.3300\",\"name\":13},{\"key\":14,\"rate\":\"13.3300\",\"name\":14},{\"key\":15,\"rate\":\"13.6900\",\"name\":15},{\"key\":16,\"rate\":\"14.4900\",\"name\":16},{\"key\":17,\"rate\":\"15.8700\",\"name\":17},{\"key\":18,\"rate\":\"18.1800\",\"name\":18},{\"key\":19,\"rate\":\"22.2200\",\"name\":19},{\"key\":20,\"rate\":\"27.7700\",\"name\":20},{\"key\":21,\"rate\":\"35.7100\",\"name\":21},{\"key\":22,\"rate\":\"47.6100\",\"name\":22},{\"key\":23,\"rate\":\"66.6600\",\"name\":23},{\"key\":24,\"rate\":\"100.0000\",\"name\":24},{\"key\":25,\"rate\":\"166.6600\",\"name\":25},{\"key\":26,\"rate\":\"333.3300\",\"name\":26},{\"key\":27,\"rate\":\"1000.0000\",\"name\":27}]}]', 0, 0, 1, 9, 0, 0, 0, 0, 4, '[{\"key\":0,\"name\":0,\"num\":0.1},{\"key\":1,\"name\":1,\"num\":0.3},{\"key\":2,\"name\":2,\"num\":0.6},{\"key\":3,\"name\":3,\"num\":1},{\"key\":4,\"name\":4,\"num\":1.5},{\"key\":5,\"name\":5,\"num\":2.1},{\"key\":6,\"name\":6,\"num\":2.8},{\"key\":7,\"name\":7,\"num\":3.6},{\"key\":8,\"name\":8,\"num\":4.5},{\"key\":9,\"name\":9,\"num\":5.5},{\"key\":10,\"name\":10,\"num\":6.3},{\"key\":11,\"name\":11,\"num\":6.9},{\"key\":12,\"name\":12,\"num\":7.3},{\"key\":13,\"name\":13,\"num\":7.5},{\"key\":14,\"name\":14,\"num\":7.5},{\"key\":15,\"name\":15,\"num\":7.3},{\"key\":16,\"name\":16,\"num\":6.9},{\"key\":17,\"name\":17,\"num\":6.3},{\"key\":18,\"name\":18,\"num\":5.5},{\"key\":19,\"name\":19,\"num\":4.5},{\"key\":20,\"name\":20,\"num\":3.6},{\"key\":21,\"name\":21,\"num\":2.8},{\"key\":22,\"name\":22,\"num\":2.1},{\"key\":23,\"name\":23,\"num\":1.5},{\"key\":24,\"name\":24,\"num\":1},{\"key\":25,\"name\":25,\"num\":0.6},{\"key\":26,\"name\":26,\"num\":0.3},{\"key\":27,\"name\":27,\"num\":0.1}]', 0, 0.00, 0.00, 0, 1000, '00:00', '23:57', '480', '3');
INSERT INTO `tp_game_type` VALUES (50, 8, '比特币3分赛车', '&lt;p&gt;&lt;img src=&quot;http://img.jinlong28.com/Uploads/image/rich_text/2019-07/20190726174737_41069.png&quot; title=&quot;20190726174737_41069.png&quot; alt=&quot;6.png&quot;/&gt;&lt;/p&gt;', '1500000000', 100, 80, 80, 60, 1000000, 20000000, 'http://img.jinlong28.com/Uploads/image/default/2019-07/20190726174733_53021.png', 1, '[{\"part\":1,\"name\":\"\\u51a0\\u4e9a\\u519b\\u548c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u51a0\\u4e9a\\u5927\",\"rate\":\"2.205\"},{\"key\":2,\"name\":\"\\u51a0\\u4e9a\\u5c0f\",\"rate\":\"1.764\"},{\"key\":3,\"name\":\"\\u51a0\\u4e9a\\u5355\",\"rate\":\"1.764\"},{\"key\":4,\"name\":\"\\u51a0\\u4e9a\\u53cc\",\"rate\":\"2.205\"}]},{\"part\":2,\"name\":\"\\u8d5b\\u8f66\\u4e00\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":3,\"name\":\"\\u8d5b\\u8f66\\u4e8c\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":4,\"name\":\"\\u8d5b\\u8f66\\u4e09\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":5,\"name\":\"\\u8d5b\\u8f66\\u56db\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":6,\"name\":\"\\u8d5b\\u8f66\\u4e94\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":\"\\u9f99\",\"rate\":\"1.96\"},{\"key\":6,\"name\":\"\\u864e\",\"rate\":\"1.96\"},{\"key\":7,\"name\":1,\"rate\":\"9.8\"},{\"key\":8,\"name\":2,\"rate\":\"9.8\"},{\"key\":9,\"name\":3,\"rate\":\"9.8\"},{\"key\":10,\"name\":4,\"rate\":\"9.8\"},{\"key\":11,\"name\":5,\"rate\":\"9.8\"},{\"key\":12,\"name\":6,\"rate\":\"9.8\"},{\"key\":13,\"name\":7,\"rate\":\"9.8\"},{\"key\":14,\"name\":8,\"rate\":\"9.8\"},{\"key\":15,\"name\":9,\"rate\":\"9.8\"},{\"key\":16,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":7,\"name\":\"\\u8d5b\\u8f66\\u516d\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":8,\"name\":\"\\u8d5b\\u8f66\\u4e03\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":9,\"name\":\"\\u8d5b\\u8f66\\u516b\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":10,\"name\":\"\\u8d5b\\u8f66\\u4e5d\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]},{\"part\":11,\"name\":\"\\u8d5b\\u8f66\\u5341\",\"bet_json\":[{\"key\":1,\"name\":\"\\u5927\",\"rate\":\"1.96\"},{\"key\":2,\"name\":\"\\u5c0f\",\"rate\":\"1.96\"},{\"key\":3,\"name\":\"\\u5355\",\"rate\":\"1.96\"},{\"key\":4,\"name\":\"\\u53cc\",\"rate\":\"1.96\"},{\"key\":5,\"name\":1,\"rate\":\"9.8\"},{\"key\":6,\"name\":2,\"rate\":\"9.8\"},{\"key\":7,\"name\":3,\"rate\":\"9.8\"},{\"key\":8,\"name\":4,\"rate\":\"9.8\"},{\"key\":9,\"name\":5,\"rate\":\"9.8\"},{\"key\":10,\"name\":6,\"rate\":\"9.8\"},{\"key\":11,\"name\":7,\"rate\":\"9.8\"},{\"key\":12,\"name\":8,\"rate\":\"9.8\"},{\"key\":13,\"name\":9,\"rate\":\"9.8\"},{\"key\":14,\"name\":10,\"rate\":\"9.8\"}]}]', 1, 0, 10, 9, 0, 0, 0, 0, 0, '', 1, 0.00, 0.00, 0, 1000, '00:00', '23:57', '480', '3');


-- ----------------------------
-- Table structure for tp_gift
-- ----------------------------
DROP TABLE IF EXISTS `tp_gift`;
CREATE TABLE `tp_gift` (
  `gift_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `merchant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `gift_name` varchar(32) NOT NULL DEFAULT '' COMMENT '礼品名称',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '礼品图片地址',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '礼品描述',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否上架，1是0否',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  PRIMARY KEY (`gift_id`),
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='礼品表';

-- ----------------------------
-- Table structure for tp_gift_card
-- ----------------------------
DROP TABLE IF EXISTS `tp_gift_card`;
CREATE TABLE `tp_gift_card` (
  `gift_card_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cash` int(11) NOT NULL COMMENT '现金',
  `money` int(11) NOT NULL COMMENT '金豆',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `img` varchar(255) NOT NULL COMMENT '图片',
  `isuse` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可用  1可用',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `first_code` varchar(255) NOT NULL DEFAULT '' COMMENT '卡密开头',
  PRIMARY KEY (`gift_card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='兑换卡券';

-- ----------------------------
-- Table structure for tp_index_ads
-- ----------------------------
DROP TABLE IF EXISTS `tp_index_ads`;
CREATE TABLE `tp_index_ads` (
  `index_ads_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '商品标题',
  `link` varchar(128) NOT NULL DEFAULT '' COMMENT '链接',
  `pic` varchar(128) NOT NULL DEFAULT '' COMMENT '图片路径',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '有效性，0不显示，1显示，2已删除',
  `size_style` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '图片尺寸类型，0.全幅大图，1.半幅中图，2.小图1/3幅',
  PRIMARY KEY (`index_ads_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='首页广告图片表';

-- ----------------------------
-- Table structure for tp_index_nav
-- ----------------------------
DROP TABLE IF EXISTS `tp_index_nav`;
CREATE TABLE `tp_index_nav` (
  `index_nav_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `link` varchar(128) NOT NULL DEFAULT '' COMMENT '链接',
  `pic` varchar(128) NOT NULL DEFAULT '' COMMENT '图标路径',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '有效性，0不显示，1显示，2已删除',
  PRIMARY KEY (`index_nav_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='首页导航定制表';

-- ----------------------------
-- Table structure for tp_integral
-- ----------------------------
DROP TABLE IF EXISTS `tp_integral`;
CREATE TABLE `tp_integral` (
  `integral_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `change_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '变动类型，1充值购买，2订单赠送，3积分兑换，4系统赠送，5活动获得，6订单支付抵扣，7订单退款',
  `integral` int(10) NOT NULL DEFAULT '0' COMMENT '变动数量',
  `start_integral` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '变动前积分数量',
  `end_integral` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '变动后积分数量',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '若是活动获得，则为活动ID，若是订单相关，则为订单ID',
  `operater` int(10) NOT NULL DEFAULT '0' COMMENT '操作人',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录生成时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT '操作人IP',
  `pay_code` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方支付平台返回的交易码',
  PRIMARY KEY (`integral_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COMMENT='积分变动明细表';

-- ----------------------------
-- Table structure for tp_invite_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_invite_log`;
CREATE TABLE `tp_invite_log` (
  `invite_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '被推荐人用户id',
  `addtime` int(11) NOT NULL COMMENT '时间',
  `parent_id` int(11) NOT NULL COMMENT '推荐人用户id',
  `reward` int(11) NOT NULL COMMENT '推荐奖励',
  PRIMARY KEY (`invite_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='推广记录';

-- ----------------------------
-- Table structure for tp_level
-- ----------------------------
DROP TABLE IF EXISTS `tp_level`;
CREATE TABLE `tp_level` (
  `level_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(255) NOT NULL COMMENT '等级名',
  `min_exp` int(11) NOT NULL COMMENT '最小经验值',
  `max_exp` int(11) NOT NULL COMMENT '最大经验值',
  `sign_reward` int(11) NOT NULL COMMENT '每日救济豆',
  `exchange_rate` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '卡密兑换增加比例',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='等级';


INSERT INTO `tp_level` VALUES (1, 'LV0', 0, 99, 0, 5.00);
INSERT INTO `tp_level` VALUES (2, 'LV1', 100, 1499, 100, 4.50);
INSERT INTO `tp_level` VALUES (3, 'LV2', 1500, 4999, 200, 4.00);
INSERT INTO `tp_level` VALUES (4, 'LV3', 5000, 7999, 300, 3.50);
INSERT INTO `tp_level` VALUES (5, 'LV4', 8000, 29999, 400, 3.00);
INSERT INTO `tp_level` VALUES (6, 'LV5', 30000, 44999, 500, 2.50);
INSERT INTO `tp_level` VALUES (7, 'LV6', 45000, 49999, 600, 2.00);
INSERT INTO `tp_level` VALUES (8, 'LV7', 50000, 8999999, 700, 0.00);

-- ----------------------------
-- Table structure for tp_link
-- ----------------------------
DROP TABLE IF EXISTS `tp_link`;
CREATE TABLE `tp_link` (
  `link_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `link_name` varchar(32) NOT NULL DEFAULT '' COMMENT '链接名称',
  `link_url` varchar(128) NOT NULL DEFAULT '' COMMENT '链接地址',
  `link_logo` varchar(128) NOT NULL DEFAULT '' COMMENT 'LOGO图片路径',
  `serial` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有效，1有效，0无效',
  PRIMARY KEY (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='友情链接表';

-- ----------------------------
-- Table structure for tp_login_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_login_log`;
CREATE TABLE `tp_login_log` (
  `login_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT 'ip',
  `ip_address` varchar(16) NOT NULL DEFAULT '' COMMENT 'ip所在城市',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '登陆状态 1成功 0失败',
  PRIMARY KEY (`login_log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1102 DEFAULT CHARSET=utf8 COMMENT='登录日志表';

-- ----------------------------
-- Table structure for tp_logs
-- ----------------------------
DROP TABLE IF EXISTS `tp_logs`;
CREATE TABLE `tp_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `op_user_id` int(10) NOT NULL COMMENT '操作者用户ID',
  `change_user_id` int(10) DEFAULT NULL COMMENT '被修改的用户ID',
  `ip` varchar(25) DEFAULT NULL COMMENT '操作者IP',
  `op_time` int(10) NOT NULL COMMENT '操作时间戳',
  `op_type` tinyint(1) NOT NULL COMMENT '操作方式，1增加、2修改、3删除、4访问',
  `tb_name` varchar(100) DEFAULT NULL COMMENT '操作表名',
  `tb_id` bigint(20) DEFAULT NULL COMMENT '操作表中的id号',
  `mark` text COMMENT '日志说明',
  `linkman` varchar(50) DEFAULT NULL COMMENT '操作者姓名',
  `op_date_time` varchar(20) NOT NULL COMMENT '操作时间',
  `sql` varchar(255) NOT NULL DEFAULT '' COMMENT 'SQL',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15348 DEFAULT CHARSET=utf8 COMMENT='系统日志表';

-- ----------------------------
-- Table structure for tp_marketing_rule
-- ----------------------------
DROP TABLE IF EXISTS `tp_marketing_rule`;
CREATE TABLE `tp_marketing_rule` (
  `marketing_rule_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `marketing_type` tinyint(4) NOT NULL COMMENT '1.每周亏损返利 2.每日首充返利 3.下线投注返利（投注流水限制）4.每日救济 5.经验换豆 6.每日排行上榜有礼',
  `condition` varchar(16) NOT NULL COMMENT '营销规则的条件',
  `result` varchar(16) NOT NULL COMMENT '条件后的结果',
  `marketing_desc` varchar(64) NOT NULL COMMENT '描述',
  `start_time` int(11) NOT NULL COMMENT '活动的时间',
  `end_time` int(11) NOT NULL COMMENT '结束的时间',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用 1是 0否 ',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `marketing_rule_name` varchar(256) NOT NULL COMMENT '营销活动名称',
  `contents` text NOT NULL COMMENT '活动内容',
  `imgurl` varchar(128) NOT NULL DEFAULT '' COMMENT '活动封面图',
  PRIMARY KEY (`marketing_rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COMMENT='活动规则表（根据具体活动再修改字段）';

-- ----------------------------
-- Table structure for tp_message
-- ----------------------------
DROP TABLE IF EXISTS `tp_message`;
CREATE TABLE `tp_message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `is_advice` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否用户建议，1是，0否',
  `message_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '消息类型，1发送，2回复，3管理员群发',
  `send_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '发送者用户ID',
  `reply_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '回复者用户ID',
  `send_username` varchar(32) NOT NULL DEFAULT '' COMMENT '发送者用户名',
  `reply_username` varchar(32) NOT NULL DEFAULT '' COMMENT '接受者用户名',
  `main_message_id` int(11) NOT NULL DEFAULT '0' COMMENT '如果是回复消息类型，值为回复的消息ID，否则为0',
  `message_title` varchar(128) NOT NULL DEFAULT '' COMMENT '消息标题，若为回复消息类型，该值为空',
  `is_read` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已读，1是，0否',
  `message_contents` text COMMENT '消息内容',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否显示，1显示，0不显示',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建消息的时间',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站内消息表';

-- ----------------------------
-- Table structure for tp_notice
-- ----------------------------
DROP TABLE IF EXISTS `tp_notice`;
CREATE TABLE `tp_notice` (
  `notice_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '公告标题',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '公告作者',
  `notice_source` varchar(128) NOT NULL DEFAULT '' COMMENT '公告来源',
  `notice_tag` varchar(32) NOT NULL DEFAULT '' COMMENT '公告标记，标记特定公告，如退换货说明，分销商招募说明等都有标记',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '公告简介，SEO用',
  `path_img` varchar(128) NOT NULL DEFAULT '' COMMENT '公告主图',
  `clickdot` int(11) NOT NULL DEFAULT '0' COMMENT '点击量',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `serial` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否显示，1是，0否',
  `content` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`notice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='公告表';

-- ----------------------------
-- Table structure for tp_notice_sort
-- ----------------------------
DROP TABLE IF EXISTS `tp_notice_sort`;
CREATE TABLE `tp_notice_sort` (
  `notice_sort_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `notice_sort_name` varchar(128) NOT NULL DEFAULT '' COMMENT '分类名称',
  `notice_sort_logo` varchar(128) NOT NULL DEFAULT '' COMMENT '分类LOGO',
  `description` text NOT NULL COMMENT '分类备注',
  `serial` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用，1是，0否',
  PRIMARY KEY (`notice_sort_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='公告分类表';

-- ----------------------------
-- Table structure for tp_notice_txt
-- ----------------------------
DROP TABLE IF EXISTS `tp_notice_txt`;
CREATE TABLE `tp_notice_txt` (
  `notice_txt_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `notice_id` smallint(6) unsigned NOT NULL COMMENT '公告ID',
  `contents` text COMMENT '公告详情',
  PRIMARY KEY (`notice_txt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='公告详情表';

-- ----------------------------
-- Table structure for tp_notice_txt_photo
-- ----------------------------
DROP TABLE IF EXISTS `tp_notice_txt_photo`;
CREATE TABLE `tp_notice_txt_photo` (
  `notice_txt_photo_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `notice_id` int(11) NOT NULL DEFAULT '0' COMMENT '公告ID',
  `path_img` varchar(128) NOT NULL DEFAULT '' COMMENT '公告详情图片地址',
  PRIMARY KEY (`notice_txt_photo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='公告详情图片表';

-- ----------------------------
-- Table structure for tp_page
-- ----------------------------
DROP TABLE IF EXISTS `tp_page`;
CREATE TABLE `tp_page` (
  `page_id` int(16) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `page_name` varchar(32) NOT NULL DEFAULT '' COMMENT '页面名称',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '缩略图',
  `page_url` varchar(128) NOT NULL DEFAULT '' COMMENT '页面地址',
  `page_desc` text NOT NULL COMMENT '页面描述',
  `serial` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用，1启用，0关闭',
  `file_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '文件名（查找时使用）',
  `page_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '页面类型，1首页，2列表页，3详情页，4团购，其他待扩充',
  `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号，模板自动更新用',
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='页面表';

-- ----------------------------
-- Table structure for tp_partner
-- ----------------------------
DROP TABLE IF EXISTS `tp_partner`;
CREATE TABLE `tp_partner` (
  `partner_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `partner_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '合伙人类型，1正式，0试用',
  `bank_name` varchar(32) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bank_account` varchar(32) NOT NULL DEFAULT '' COMMENT '银行账号',
  `bank_realname` varchar(16) NOT NULL DEFAULT '' COMMENT '持卡人姓名',
  `openning_bank` varchar(32) NOT NULL DEFAULT '' COMMENT '开户行',
  `bank_mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '预留手机号',
  PRIMARY KEY (`partner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合伙人表';

-- ----------------------------
-- Table structure for tp_payway
-- ----------------------------
DROP TABLE IF EXISTS `tp_payway`;
CREATE TABLE `tp_payway` (
  `payway_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `pay_type` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '类型，1普通，2银行',
  `pay_tag` varchar(16) NOT NULL DEFAULT '' COMMENT '英文支付标签，唯一',
  `pay_name` varchar(32) NOT NULL DEFAULT '' COMMENT '支付名称 中文名',
  `pay_logo` varchar(128) NOT NULL DEFAULT '' COMMENT '支付LOGO',
  `pay_config` text COMMENT '支付配置信息',
  `pay_desc` text COMMENT '支付说明',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用，1是，0否',
  PRIMARY KEY (`payway_id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COMMENT='支付方式表';

-- ----------------------------
-- Table structure for tp_push_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_push_log`;
CREATE TABLE `tp_push_log` (
  `push_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `opt` varchar(32) NOT NULL DEFAULT '' COMMENT '消息类型',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `content` text COMMENT '消息内容',
  `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读，1是，0否',
  PRIMARY KEY (`push_log_id`),
  KEY `tp_push_log_user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='消息记录表';

-- ----------------------------
-- Table structure for tp_pv_main
-- ----------------------------
DROP TABLE IF EXISTS `tp_pv_main`;
CREATE TABLE `tp_pv_main` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `merchant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT 'PV访问时间的时间戳',
  `visit_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '访问日期',
  `province_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省份ID',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市ID',
  `from_domain` varchar(32) NOT NULL DEFAULT '0' COMMENT '来源域名',
  `from_url` varchar(255) NOT NULL DEFAULT '0' COMMENT '来源URL',
  `ip` char(16) NOT NULL DEFAULT '0' COMMENT '原始IP',
  `client_key` char(32) NOT NULL DEFAULT '0' COMMENT '访问者身份标识',
  `is_new_visitor` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否新访客',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PV基础数据主表';

-- ----------------------------
-- Table structure for tp_pv_respondent_data
-- ----------------------------
DROP TABLE IF EXISTS `tp_pv_respondent_data`;
CREATE TABLE `tp_pv_respondent_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '访问时间',
  `domain` varchar(64) NOT NULL DEFAULT '0' COMMENT '受访域名',
  `visit_url` varchar(255) NOT NULL DEFAULT '0' COMMENT '被访问的路径',
  `page_title` varchar(64) NOT NULL DEFAULT '0' COMMENT '页面标题',
  `is_entrance` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否入口',
  `have_jump` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否跳出，默认1=跳出,0=为跳出',
  `last_time` smallint(5) NOT NULL DEFAULT '0' COMMENT '页面停留时间last_time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PV基础数据.受访数据副表';

-- ----------------------------
-- Table structure for tp_pv_system_analysis
-- ----------------------------
DROP TABLE IF EXISTS `tp_pv_system_analysis`;
CREATE TABLE `tp_pv_system_analysis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `add_time` int(11) DEFAULT '0' COMMENT '访问时间的时间戳',
  `browser` tinyint(3) NOT NULL DEFAULT '0' COMMENT '浏览器的ID号',
  `screen_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '分辨率的ID',
  `os_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '客户端系统编号',
  `language_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '语言ID编号',
  `terminal_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '终端类型ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PV系统分析副表';

-- ----------------------------
-- Table structure for tp_quick_item
-- ----------------------------
DROP TABLE IF EXISTS `tp_quick_item`;
CREATE TABLE `tp_quick_item` (
  `quick_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `item_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  PRIMARY KEY (`quick_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='日常快速订单表';

-- ----------------------------
-- Table structure for tp_rank_list
-- ----------------------------
DROP TABLE IF EXISTS `tp_rank_list`;
CREATE TABLE `tp_rank_list` (
  `rank_list_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `addtime` int(11) NOT NULL COMMENT '时间',
  `win` int(11) NOT NULL COMMENT '流水',
  `reward` int(11) NOT NULL COMMENT '排行榜奖励',
  `is_received` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已领取 1已领取 0未领取 2已过期',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '名次',
  `is_send` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否发放奖励',
  PRIMARY KEY (`rank_list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=290 DEFAULT CHARSET=utf8mb4 COMMENT='排行榜';

-- ----------------------------
-- Table structure for tp_recommend_item
-- ----------------------------
DROP TABLE IF EXISTS `tp_recommend_item`;
CREATE TABLE `tp_recommend_item` (
  `recommend_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `item_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  PRIMARY KEY (`recommend_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='今日推荐商品表';

-- ----------------------------
-- Table structure for tp_red_packet
-- ----------------------------
DROP TABLE IF EXISTS `tp_red_packet`;
CREATE TABLE `tp_red_packet` (
  `red_packet_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL COMMENT '1 普通 2拼手气',
  `total_money` decimal(10,2) NOT NULL COMMENT '总金额',
  `each_money` decimal(10,2) NOT NULL COMMENT '每一个金额',
  `num` int(11) NOT NULL COMMENT '总数量',
  `residue_money` decimal(10,2) NOT NULL COMMENT '剩余金额',
  `residue_num` int(11) NOT NULL COMMENT '剩余数量',
  `title` varchar(255) NOT NULL COMMENT '红包名',
  `url` varchar(255) NOT NULL COMMENT '链接',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '生成时间',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `is_cancel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否取消 1表示取消 0 未取消',
  PRIMARY KEY (`red_packet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COMMENT='红包类型表';

-- ----------------------------
-- Table structure for tp_red_packet_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_red_packet_log`;
CREATE TABLE `tp_red_packet_log` (
  `red_packet_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `red_packet_id` int(11) NOT NULL COMMENT '红包id',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  `addtime` int(11) NOT NULL,
  PRIMARY KEY (`red_packet_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COMMENT='红包领取记录';

-- ----------------------------
-- Table structure for tp_return_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_return_log`;
CREATE TABLE `tp_return_log` (
  `return_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `account_id` int(11) NOT NULL COMMENT 'account记录',
  `return_type` int(11) NOT NULL COMMENT '返利类型 1.每周亏损返利 2.每日首充返利 3.下线投注返利（投注流水限制）',
  `money` int(11) NOT NULL COMMENT '充值金豆',
  `addtime` int(11) NOT NULL,
  `lower_id` int(11) NOT NULL DEFAULT '0' COMMENT '下级id',
  PRIMARY KEY (`return_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8mb4 COMMENT='返利记录';

-- ----------------------------
-- Table structure for tp_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_sms_log`;
CREATE TABLE `tp_sms_log` (
  `sms_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `send_mobile_list` text COMMENT '发送手机号列表，英文逗号隔开',
  `send_text` varchar(255) NOT NULL DEFAULT '' COMMENT '发送内容',
  `sms_send_time` int(11) NOT NULL DEFAULT '0' COMMENT '短信发送时间',
  `sms_send_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送状态，1成功，0失败',
  PRIMARY KEY (`sms_log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=190 DEFAULT CHARSET=utf8 COMMENT='短信发送日志表';

-- ----------------------------
-- Table structure for tp_sms_set
-- ----------------------------
DROP TABLE IF EXISTS `tp_sms_set`;
CREATE TABLE `tp_sms_set` (
  `sms_set_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `send_name` varchar(32) NOT NULL DEFAULT '' COMMENT '发送标记，英文唯一',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否使用，1是，0否',
  `to_admin` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否抄送管理员，是1，否0，默认0',
  `sms_text` varchar(255) NOT NULL DEFAULT '' COMMENT '短信内容',
  `default_sms_text` varchar(255) NOT NULL COMMENT '短信内容的系统默认模板',
  PRIMARY KEY (`sms_set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tp_sort
-- ----------------------------
DROP TABLE IF EXISTS `tp_sort`;
CREATE TABLE `tp_sort` (
  `sort_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '一级分类ID',
  `sort_name` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '二级分类名称',
  `sort_tag` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '分类标签',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效，1有效，0无效',
  `is_index` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否在首页显示，0否，1是',
  `is_first_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否在快速订单显示，0否，1是',
  PRIMARY KEY (`sort_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商品二级分类表';

-- ----------------------------
-- Table structure for tp_street
-- ----------------------------
DROP TABLE IF EXISTS `tp_street`;
CREATE TABLE `tp_street` (
  `street_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `street_name` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '街道名称',
  `class_tag` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效，1有效，0无效',
  PRIMARY KEY (`street_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='街道表';

-- ----------------------------
-- Table structure for tp_templet
-- ----------------------------
DROP TABLE IF EXISTS `tp_templet`;
CREATE TABLE `tp_templet` (
  `templet_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(8) NOT NULL DEFAULT '0' COMMENT '页面ID',
  `templet_package_id` int(8) NOT NULL DEFAULT '0' COMMENT '模板ＩＤ',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效，1有效，0无效',
  PRIMARY KEY (`templet_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='套餐—页面明细表';

-- ----------------------------
-- Table structure for tp_templet_package
-- ----------------------------
DROP TABLE IF EXISTS `tp_templet_package`;
CREATE TABLE `tp_templet_package` (
  `templet_package_id` int(16) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `templet_package_name` varchar(32) NOT NULL DEFAULT '' COMMENT '页面名称',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '缩略图',
  `user_id` int(16) NOT NULL DEFAULT '0' COMMENT '所属用户',
  `desc` text NOT NULL COMMENT '描述',
  `serial` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序号',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用，1启用，0关闭',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '记录生成时间',
  PRIMARY KEY (`templet_package_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='模板套餐表';

-- ----------------------------
-- Table structure for tp_ticket
-- ----------------------------
DROP TABLE IF EXISTS `tp_ticket`;
CREATE TABLE `tp_ticket` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `voucher_code` varchar(128) NOT NULL DEFAULT '' COMMENT '券号码',
  `value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价值',
  `is_reuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否储值',
  `period_of_validity` int(11) NOT NULL DEFAULT '0' COMMENT '有效期',
  `used_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '已经使用金额',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '记录生成时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '券状态，0未开通 2正常 3已使用 4已作废  注：只有2状态可以使用',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有效',
  `is_only_show` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否展示',
  `card_code` varchar(32) NOT NULL DEFAULT '0' COMMENT '支付卡号',
  PRIMARY KEY (`ticket_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='券表';

-- ----------------------------
-- Table structure for tp_tmp_data
-- ----------------------------
DROP TABLE IF EXISTS `tp_tmp_data`;
CREATE TABLE `tp_tmp_data` (
  `nickname` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `realname` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `mobile` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `integral` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `rank` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `reg_time` varchar(128) DEFAULT NULL,
  `birthday` varchar(128) DEFAULT NULL,
  `sex` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `member_card_id` varchar(128) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tp_trading_area
-- ----------------------------
DROP TABLE IF EXISTS `tp_trading_area`;
CREATE TABLE `tp_trading_area` (
  `trading_area_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `trading_area_name` varchar(16) NOT NULL DEFAULT '' COMMENT '商圈名称',
  `trading_area_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '商圈简介',
  `serial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  PRIMARY KEY (`trading_area_id`),
  KEY `city_id` (`city_id`) USING BTREE,
  KEY `area_id` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='商圈表';

-- ----------------------------
-- Table structure for tp_user_address
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_address`;
CREATE TABLE `tp_user_address` (
  `user_address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `realname` varchar(32) NOT NULL DEFAULT '' COMMENT '分销商姓名(真实名字)',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `province_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省份ID',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市ID',
  `area_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地区ID',
  `longitude` decimal(10,7) unsigned NOT NULL DEFAULT '120.0000000' COMMENT '经度',
  `latitude` decimal(9,7) unsigned NOT NULL DEFAULT '30.0000000' COMMENT '纬度',
  `building_id` int(10) unsigned NOT NULL DEFAULT '2' COMMENT '小区/写字楼ID',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '用户真实的联系地址',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `use_time` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前地址使用次数',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态，1有效，0已删除',
  PRIMARY KEY (`user_address_id`),
  KEY `tp_user_address_province_id` (`province_id`) USING BTREE,
  KEY `tp_user_address_city_id` (`city_id`) USING BTREE,
  KEY `tp_user_address_area_id` (`area_id`) USING BTREE,
  KEY `tp_user_address_user_id` (`user_id`) USING BTREE,
  KEY `tp_user_address_addtime` (`addtime`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='用户地址表';

-- ----------------------------
-- Table structure for tp_user_buy_give
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_buy_give`;
CREATE TABLE `tp_user_buy_give` (
  `user_buy_give_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `buy_give_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买赠活动ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `merchant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  `genre_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '三级分类ID，表示该活动只支持该分类下的商品，若为0则无此限制',
  PRIMARY KEY (`user_buy_give_id`),
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=121 DEFAULT CHARSET=utf8 COMMENT='用户买赠活动表';

-- ----------------------------
-- Table structure for tp_user_comment
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_comment`;
CREATE TABLE `tp_user_comment` (
  `user_comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `score` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评分，1好评，0差评',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示，1是，0否，默认1',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价时间',
  PRIMARY KEY (`user_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户评价表';

-- ----------------------------
-- Table structure for tp_user_gift
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_gift`;
CREATE TABLE `tp_user_gift` (
  `user_gift_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gift_card_id` int(11) NOT NULL COMMENT '卡券类型id',
  `card_password` varchar(255) NOT NULL COMMENT '卡密',
  `addtime` int(11) NOT NULL COMMENT '领取时间',
  `use_time` int(11) NOT NULL COMMENT '兑换时间',
  `end_time` int(11) NOT NULL COMMENT '过期时间',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否被使用 未使用1  已使用0',
  `dcp_id` int(11) NOT NULL COMMENT '代理商id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '兑换数量',
  `cash` int(11) NOT NULL DEFAULT '0' COMMENT '现金',
  `money` int(11) NOT NULL DEFAULT '0' COMMENT '金豆',
  PRIMARY KEY (`user_gift_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COMMENT='用户兑换记录';

-- ----------------------------
-- Table structure for tp_user_gift_password
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_gift_password`;
CREATE TABLE `tp_user_gift_password` (
  `user_gift_password_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_gift_id` int(11) NOT NULL DEFAULT '0' COMMENT '兑换记录',
  `card_password` varchar(255) NOT NULL DEFAULT '' COMMENT '卡密',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '领取时间',
  `use_time` int(11) NOT NULL DEFAULT '0' COMMENT '兑换时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否被使用 未使用1  已使用0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `dcp_id` int(10) NOT NULL COMMENT '兑换的代理商id',
  `money` float(20,0) NOT NULL DEFAULT '0' COMMENT '金豆数量',
  `cash` float(20,0) NOT NULL DEFAULT '0' COMMENT '现金',
  PRIMARY KEY (`user_gift_password_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COMMENT='用户点卡卡密';

-- ----------------------------
-- Table structure for tp_user_rank
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_rank`;
CREATE TABLE `tp_user_rank` (
  `user_rank_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `rank_name` varchar(32) NOT NULL DEFAULT '' COMMENT '用户等级名称',
  `upgrade_money` int(11) NOT NULL DEFAULT '0' COMMENT '升到该等级所需消费额，包括预存款和下单支付的金额',
  `discount` tinyint(4) NOT NULL DEFAULT '0' COMMENT '该等级用户享受的商品批发折扣，保存100以内的整数，如85即为85折',
  `desc` text COMMENT '备注',
  `logo` varchar(128) NOT NULL DEFAULT '' COMMENT '等级LOGO，非必填',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  PRIMARY KEY (`user_rank_id`),
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户等级表';

-- ----------------------------
-- Table structure for tp_user_requirement
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_requirement`;
CREATE TABLE `tp_user_requirement` (
  `user_requirement_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `requirement` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '需求描述',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0未解决，1已解决，2已拒绝',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `attachment` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '附件路径',
  `require_time` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '希望解决时间',
  PRIMARY KEY (`user_requirement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户需求表';

-- ----------------------------
-- Table structure for tp_user_requirement_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_requirement_log`;
CREATE TABLE `tp_user_requirement_log` (
  `user_requirement_log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `user_requirement_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户需求ID',
  PRIMARY KEY (`user_requirement_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户需求日志表';

-- ----------------------------
-- Table structure for tp_user_suggest
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_suggest`;
CREATE TABLE `tp_user_suggest` (
  `user_suggest_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `message_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1给点建议，2售后问题',
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '消息状态，0未读，1无用，2有待商榷，3有用，4将更新到系统中',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  `admin_remark` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  PRIMARY KEY (`user_suggest_id`),
  KEY `tp_city` (`city_id`) USING BTREE,
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for tp_user_vouchers
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_vouchers`;
CREATE TABLE `tp_user_vouchers` (
  `user_vouchers_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `vouchers_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券类型ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `merchant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `num` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '面额',
  `order_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单总金额',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `use_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题/描述',
  `scope` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用范围，1仅限微信，2仅限APP，0全部',
  `amount_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单(商家为商家总金额，系统为合并>支付的总金额)满多少可使用该>优惠券',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期，开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期，结束时间',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否可用，1是0否',
  `building_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '小区ID',
  `genre_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '三级分类ID，表示该活动只支持该分类下的商\n品，若为0则无此限制',
  PRIMARY KEY (`user_vouchers_id`),
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户优惠券表';

-- ----------------------------
-- Table structure for tp_users
-- ----------------------------
DROP TABLE IF EXISTS `tp_users`;
CREATE TABLE `tp_users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型，1为管理员，3为用户，4为代理商',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '(用户名)',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码，md5加密',
  `pay_password` varchar(32) NOT NULL DEFAULT '' COMMENT '支付密码，MD5加密值，默认为123456的MD5值',
  `user_cookie` varchar(32) NOT NULL DEFAULT '' COMMENT '用户COOKIE',
  `user_rank_id` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '用户等级ID',
  `group_id` smallint(5) NOT NULL DEFAULT '0' COMMENT '用户组id',
  `is_enable` tinyint(4) NOT NULL DEFAULT '2' COMMENT '是否可用，1可用，2、禁用',
  `reg_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `login_try_times` tinyint(4) NOT NULL DEFAULT '0' COMMENT '已失败登录次数',
  `block_time` int(11) NOT NULL DEFAULT '0' COMMENT '多次登录失败后在这个时间后自动解锁',
  `realname` varchar(32) NOT NULL DEFAULT '' COMMENT '分销商姓名(真实名字)',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别： 0保密、1 男、2 女（和微信一致）',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '用户真实的联系地址',
  `province_id` int(10) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` int(10) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` int(10) unsigned NOT NULL DEFAULT '330382' COMMENT '地区ID',
  `openid` varchar(64) NOT NULL DEFAULT '' COMMENT '微信的openid',
  `access_token` varchar(32) NOT NULL DEFAULT '' COMMENT '访问令牌',
  `refresh_token` varchar(32) NOT NULL DEFAULT '' COMMENT '刷新令牌',
  `token_expires_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '访问令牌过期时间',
  `nickname` varchar(16) NOT NULL DEFAULT '' COMMENT '微信用户昵称',
  `city` varchar(8) NOT NULL DEFAULT '' COMMENT '用户所在城市名称',
  `province` varchar(8) NOT NULL DEFAULT '0' COMMENT '用户所在省份名称',
  `headimgurl` varchar(255) NOT NULL DEFAULT '' COMMENT '头像地址',
  `subscribe` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否关注，1是，0否',
  `consumed_money` decimal(16,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '消费总金额',
  `is_rank_manual` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否手动设置用户等级',
  `left_money` decimal(16,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金豆',
  `frozen_money` decimal(16,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '存入银行的金豆',
  `total_integral` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分总数，只增不减',
  `left_integral` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分余额',
  `mobile_registered` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '手机号是否已注册，在微信绑定手机号时判定是否已在APP注册过>，1是，0否',
  `qq` varchar(16) NOT NULL DEFAULT '' COMMENT 'QQ号',
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型，0系统内用户，1系统外用户,用户区分加盟商和非加盟商',
  `store_sn` varchar(16) NOT NULL DEFAULT '' COMMENT '门店编号',
  `big_area_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '大区ID',
  `tel` varchar(32) NOT NULL DEFAULT '0' COMMENT '固话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '用户备注',
  `birthday` varchar(32) NOT NULL DEFAULT '' COMMENT '生日',
  `user_address_id` int(10) DEFAULT NULL COMMENT '用户默认地址',
  `member_card_id` varchar(128) NOT NULL DEFAULT '' COMMENT '会员卡卡号',
  `dept_list` text NOT NULL COMMENT '门店管理员所管理的门店列表， 用 ，号隔开',
  `street` varchar(128) NOT NULL DEFAULT '' COMMENT '街道',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '推荐人ID',
  `baby_birthday` int(11) NOT NULL DEFAULT '0' COMMENT '宝宝生日',
  `is_extend_user` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否推广员',
  `ticket` varchar(128) NOT NULL DEFAULT '' COMMENT '调用微信带参数二维码接口生成的ticket，用于唯一标>\n记二维码来源',
  `qr_code` varchar(128) NOT NULL DEFAULT '' COMMENT '调用微信带参数二维码接口生成的二维码，用于转发生\n成上下级关系，并引导下级关注平台',
  `qr_code_expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '专属二维码过期时间，过期将自动重新生成',
  `first_agent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第一级上级正式代理ID',
  `second_agent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第二级上级正式代理ID',
  `third_agent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第三级上级正式代理ID',
  `first_agent_rate` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '一级代理提成比例',
  `alipay_account` varchar(32) NOT NULL DEFAULT '' COMMENT '支付宝账户',
  `alipay_account_name` varchar(8) NOT NULL DEFAULT '' COMMENT '支付宝账户户名',
  `wx_account` varchar(32) NOT NULL DEFAULT '' COMMENT '微信账户',
  `jpush_reg_id` varchar(32) NOT NULL DEFAULT '' COMMENT '极光推送的用户reg_id',
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `safe_password` varchar(32) NOT NULL COMMENT '安全密码',
  `bank_password` varchar(32) NOT NULL COMMENT '银行密码',
  `open_chenck_login` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否打开登录验证',
  `open_chenck_personal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否打开用户中心验证',
  `level_id` int(11) NOT NULL DEFAULT '1' COMMENT '等级',
  `exp` int(11) NOT NULL DEFAULT '0' COMMENT '经验',
  `level_name` varchar(32) NOT NULL DEFAULT '' COMMENT '等级名',
  `more_exp` int(11) NOT NULL DEFAULT '0' COMMENT '满级后多的经验值',
  `login_limit_province_id` int(11) NOT NULL COMMENT '登录地区限制省id',
  `login_limit_city_id` int(11) NOT NULL COMMENT '登录地区限制市id',
  `another_limit_province_id` int(11) NOT NULL COMMENT '登录地区限制2 省id',
  `another_limit_city_id` int(11) NOT NULL COMMENT '登录地区限制2 市id',
  `open_login_limit` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否打开地址限制',
  `invite_id` int(8) NOT NULL COMMENT '用户ID（身份的象征）',
  `introduce` varchar(255) NOT NULL DEFAULT '' COMMENT '代理商，商务合作介绍',
  `game_name` varchar(255) NOT NULL DEFAULT '' COMMENT '点卡名称',
  `agent_deduct` int(8) NOT NULL DEFAULT '0' COMMENT '代理商折扣',
  `is_index` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否展示在首页',
  PRIMARY KEY (`user_id`),
  KEY `tp_user_password` (`password`) USING BTREE,
  KEY `tp_user_reg_time` (`reg_time`) USING BTREE,
  KEY `tp_user_province_id` (`province_id`) USING BTREE,
  KEY `tp_user_city_id` (`city_id`) USING BTREE,
  KEY `tp_user_area_id` (`area_id`) USING BTREE,
  KEY `tp_user_group_id` (`group_id`,`is_enable`,`reg_time`) USING BTREE,
  KEY `tp_user_username` (`username`) USING BTREE,
  KEY `tp_user_openid` (`openid`) USING BTREE,
  FULLTEXT KEY `tp_user_realname` (`realname`)
) ENGINE=MyISAM AUTO_INCREMENT=145283 DEFAULT CHARSET=utf8 COMMENT='用户数据表';

-- ----------------------------
-- Table structure for tp_users_group
-- ----------------------------
DROP TABLE IF EXISTS `tp_users_group`;
CREATE TABLE `tp_users_group` (
  `group_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL COMMENT '用户组名',
  `is_enable` tinyint(4) NOT NULL COMMENT '是否可用，1可用，2、禁用，3已删除',
  `priv_str` text COMMENT '用户组权限id（每个权限id中间用,号分开）',
  `manage_group_id_list` text COMMENT '如果是可操作管理员的用户组时此处必填，这里是该用户组下的管理员可操作的用户组id列表，多个id>之间用逗号分开',
  `user_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '用户类型，1管理员，2会员',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`) USING BTREE,
  KEY `user_type` (`user_type`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='用户组';

-- ----------------------------
-- Table structure for tp_verify_code
-- ----------------------------
DROP TABLE IF EXISTS `tp_verify_code`;
CREATE TABLE `tp_verify_code` (
  `verify_code_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `verify_code` varchar(6) NOT NULL DEFAULT '' COMMENT '验证码',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `cookie_value` varchar(32) NOT NULL DEFAULT '' COMMENT 'COOKIE值',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证码过期时间，默认有效期30分钟内',
  `isuse` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否可用，1可用，0不可用',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  PRIMARY KEY (`verify_code_id`),
  KEY `tp_verify_code_user_id` (`user_id`) USING BTREE,
  KEY `tp_verify_code_expire_time` (`expire_time`) USING BTREE,
  KEY `tp_verify_code_cookie_value` (`cookie_value`) USING BTREE,
  KEY `tp_city` (`city_id`) USING BTREE,
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=195 DEFAULT CHARSET=utf8 COMMENT='验证码表';

-- ----------------------------
-- Table structure for tp_vouchers
-- ----------------------------
DROP TABLE IF EXISTS `tp_vouchers`;
CREATE TABLE `tp_vouchers` (
  `vouchers_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `merchant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期，开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期，结束时间',
  `num` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '面额',
  `amount_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单满多少(此处指合并支付的总金额)可使用该优惠券',
  `isuse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '当前优惠券类型是否有效，1是0否，若为否，发放优惠券时，选择优惠券类型时不会出现该优惠券类型',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '330000' COMMENT '省份ID',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '330300' COMMENT '城市ID',
  `area_id` mediumint(8) unsigned NOT NULL DEFAULT '330382' COMMENT '区/县ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题/描述',
  `scope` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用范围，1仅限微信，2仅限APP，3全部',
  `use_time` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户可享受几次这样的折扣，0表示无限制',
  `keywords` varchar(16) NOT NULL DEFAULT '' COMMENT '关键词，用于根据关键词领取优惠券',
  `building_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '小区ID',
  `class_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '限制的一级分类ID，为0则不限制',
  `sort_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '限制的二级分类ID，为0则不限制',
  `genre_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '限制的三级分类ID，为0则不限制',
  PRIMARY KEY (`vouchers_id`),
  KEY `tp_area` (`area_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='优惠券类型表';

-- ----------------------------
-- Table structure for tp_weather
-- ----------------------------
DROP TABLE IF EXISTS `tp_weather`;
CREATE TABLE `tp_weather` (
  `weather_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(8) NOT NULL DEFAULT '' COMMENT '城市名称',
  `weather` varchar(8) NOT NULL DEFAULT '' COMMENT '天气描述',
  `temp1` tinyint(4) NOT NULL DEFAULT '0' COMMENT '温度上限',
  `temp2` tinyint(4) NOT NULL DEFAULT '0' COMMENT '温度下限',
  `aqi` smallint(6) NOT NULL DEFAULT '0' COMMENT '空气质量指数',
  `pm2_5` smallint(6) NOT NULL DEFAULT '0' COMMENT 'PM2.5指数',
  `weather_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日期时间戳',
  `station_code` varchar(8) NOT NULL DEFAULT '0' COMMENT '监测点码',
  `quality` varchar(8) NOT NULL DEFAULT '0' COMMENT '空气质量等级',
  PRIMARY KEY (`weather_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='天气预报表';

-- ----------------------------
-- Table structure for tp_wx_kw_reply
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_kw_reply`;
CREATE TABLE `tp_wx_kw_reply` (
  `wx_kw_reply_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `rule_name` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `reply_type` varchar(20) NOT NULL DEFAULT '' COMMENT '回复类型，text（文本），news（图文），image（图片）',
  `keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `text_value` varchar(255) NOT NULL DEFAULT '' COMMENT '文本消息的内容 或 图文消息的摘要',
  `news_title` varchar(126) NOT NULL DEFAULT '' COMMENT '图文标题',
  `news_link` varchar(255) NOT NULL DEFAULT '' COMMENT '图文链接',
  `img_url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片消息的图片链接 或 图文消息缩略图的链接',
  `media_url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片上传到微信后返回的图片链接地址',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片有效时间',
  `media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '图片上传到微信后返回的media_id，用于标记>图片地址',
  PRIMARY KEY (`wx_kw_reply_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='微信关键字回复表';

-- ----------------------------
-- Table structure for tp_wx_menu
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_menu`;
CREATE TABLE `tp_wx_menu` (
  `wx_menu` text COMMENT 'json格式的菜单信息'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信菜单表';

-- ----------------------------
-- Table structure for tp_wx_merchant
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_merchant`;
CREATE TABLE `tp_wx_merchant` (
  `wx_merchant_id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(32) NOT NULL DEFAULT '' COMMENT 'appid',
  `appsecret` varchar(32) NOT NULL DEFAULT '' COMMENT 'appsecret',
  `mcid` varchar(32) NOT NULL DEFAULT '' COMMENT '商户号',
  `pay_key` varchar(32) NOT NULL DEFAULT '' COMMENT '支付密钥',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `price_fixed` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否固定金额',
  PRIMARY KEY (`wx_merchant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='微信商户支付信息表';


-- ----------------------------
-- Table structure for tp_robot
-- ----------------------------
DROP TABLE IF EXISTS `tp_robot`;
CREATE TABLE `tp_robot` (
  `robot_id` int(11) NOT NULL COMMENT '机器人列表',
  `robot_name` varchar(255) NOT NULL DEFAULT '' COMMENT '机器人名称',
  `today_money` float(20,0) NOT NULL COMMENT '今日盈亏',
  PRIMARY KEY (`robot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人列表';

-- ----------------------------
-- Records of tp_robot
-- ----------------------------
INSERT INTO `tp_robot` VALUES (1, 'Mandy', 717);
INSERT INTO `tp_robot` VALUES (2, 'ado11457', -8415);
INSERT INTO `tp_robot` VALUES (3, 'y2j423633', -6944);
INSERT INTO `tp_robot` VALUES (4, '阿寸大大', 5457);
INSERT INTO `tp_robot` VALUES (5, '倘若世间有爱', 7652);
INSERT INTO `tp_robot` VALUES (6, '程凯', 11841);
INSERT INTO `tp_robot` VALUES (7, '孙律', -12558);
INSERT INTO `tp_robot` VALUES (8, 'Suzanne', 2284);
INSERT INTO `tp_robot` VALUES (9, 'Maxwell', 6227);
INSERT INTO `tp_robot` VALUES (10, '康禅', 11797);
INSERT INTO `tp_robot` VALUES (11, 'youxiid', -1462);
INSERT INTO `tp_robot` VALUES (12, '丁筠', 3112);
INSERT INTO `tp_robot` VALUES (13, '得意须尽欢', 7807);
INSERT INTO `tp_robot` VALUES (14, 'qq1695283376', -104);
INSERT INTO `tp_robot` VALUES (15, '9876543210', -4631);
INSERT INTO `tp_robot` VALUES (16, 'Wheeler', -5758);
INSERT INTO `tp_robot` VALUES (17, 'ascbest6', -9919);
INSERT INTO `tp_robot` VALUES (18, 'Taylor', 9291);
INSERT INTO `tp_robot` VALUES (19, '易福娃', 8780);
INSERT INTO `tp_robot` VALUES (20, '振鹏哥', -9817);
INSERT INTO `tp_robot` VALUES (21, '一周网络科技', 1653);
INSERT INTO `tp_robot` VALUES (22, '奇奇怪怪', -5095);
INSERT INTO `tp_robot` VALUES (23, '我不是米', 7575);
INSERT INTO `tp_robot` VALUES (24, '人生的旅途', 3418);
INSERT INTO `tp_robot` VALUES (25, 'yman.W', 166);
INSERT INTO `tp_robot` VALUES (26, '找虫虫', -7200);
INSERT INTO `tp_robot` VALUES (27, '风子猪', -4510);
INSERT INTO `tp_robot` VALUES (28, '用户4405144', -121);
INSERT INTO `tp_robot` VALUES (29, 'zqfan', -7800);
INSERT INTO `tp_robot` VALUES (30, 'dgdw', 3143);
INSERT INTO `tp_robot` VALUES (31, 'lcvsd', -7781);
INSERT INTO `tp_robot` VALUES (32, 'domfe', 1108);
INSERT INTO `tp_robot` VALUES (33, '用户545673', 4848);
INSERT INTO `tp_robot` VALUES (34, 'Jinqn', -135);
INSERT INTO `tp_robot` VALUES (35, '骑牛看晨曦', 978);
INSERT INTO `tp_robot` VALUES (36, 'love&peace~', -6297);
INSERT INTO `tp_robot` VALUES (37, 'qq987123643', -3772);
INSERT INTO `tp_robot` VALUES (38, 'q458493034', 8048);
INSERT INTO `tp_robot` VALUES (39, '16489272093', 6053);
INSERT INTO `tp_robot` VALUES (40, '用户982364', 6830);
INSERT INTO `tp_robot` VALUES (41, '13187238992', 6444);
INSERT INTO `tp_robot` VALUES (42, '13826764284', -1109);
INSERT INTO `tp_robot` VALUES (43, 'o o', 3279);
INSERT INTO `tp_robot` VALUES (44, '白米', 2339);
INSERT INTO `tp_robot` VALUES (45, '阿斯顿', -705);
INSERT INTO `tp_robot` VALUES (46, 'LSD', -9916);
INSERT INTO `tp_robot` VALUES (47, '用户237489', 6581);
INSERT INTO `tp_robot` VALUES (48, 'as多少分的', -624);
INSERT INTO `tp_robot` VALUES (49, 'fd哥', 9376);
INSERT INTO `tp_robot` VALUES (50, 'r50', 5361);

-- ----------------------------
-- View structure for tp_view_pv
-- ----------------------------
DROP VIEW IF EXISTS `tp_view_pv`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `tp_view_pv` AS select `pm`.`id` AS `id`,`pm`.`user_id` AS `user_id`,`pm`.`merchant_id` AS `merchant_id`,`pm`.`add_time` AS `add_time`,`pm`.`visit_date` AS `visit_date`,`pm`.`province_id` AS `province_id`,`pm`.`city_id` AS `city_id`,`pm`.`from_domain` AS `from_domain`,`pm`.`from_url` AS `from_url`,`pm`.`ip` AS `ip`,`pm`.`client_key` AS `client_key`,`pm`.`is_new_visitor` AS `is_new_visitor`,`prd`.`visit_url` AS `visit_url`,`prd`.`page_title` AS `page_title`,`psa`.`browser` AS `browser`,`psa`.`screen_id` AS `screen_id`,`psa`.`os_id` AS `os_id`,`psa`.`language_id` AS `language_id`,`psa`.`terminal_id` AS `terminal_id` from ((`tp_pv_main` `pm` join `tp_pv_respondent_data` `prd`) join `tp_pv_system_analysis` `psa`) where ((`pm`.`id` = `prd`.`id`) and (`pm`.`id` = `psa`.`id`));

SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE tp_account ADD `bank_money_after` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '操作后的银行余额',
ALTER TABLE tp_account ADD `bank_money_before` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '操作前的银行余额',

CREATE TABLE `tp_daliy_left` (
  `daliy_left_id` int(11) NOT NULL AUTO_INCREMENT,
  `left_money` varchar(100) NOT NULL DEFAULT '' COMMENT '用户',
  `frozen_money` varchar(100) NOT NULL DEFAULT '' COMMENT '代理',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`daliy_left_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE tp_game_type ADD `is_bet` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可以投注';

ALTER TABLE tp_users ADD `is_no_activity` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否参加活动';

ALTER TABLE tp_account ADD `recharge_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应代理给用户的充值记录id';

ALTER TABLE tp_game_series ADD `index` int(11) NOT NULL DEFAULT '1' COMMENT '排序号';

//加拿大开奖冬令时和夏令时切换
INSERT INTO tp_config (`config_name`, `config_value`) VALUES ('jnd_time', '0');