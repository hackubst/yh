
--增加盘口字段
ALTER TABLE `tp_bet_log`
ADD COLUMN `pan_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1234代表ABCD' AFTER `game_type_id`;

--增加不同盘的赔率
ALTER TABLE `tp_game_type`
ADD COLUMN `bet_json_b` text NOT NULL COMMENT 'b盘投注赔率' AFTER `bet_json`,
ADD COLUMN `bet_json_c` text NOT NULL COMMENT 'c盘投注赔率' AFTER `bet_json_b`,
ADD COLUMN `bet_json_d` text NOT NULL COMMENT 'd盘投注赔率' AFTER `bet_json_c`;

ALTER TABLE `tp_bet_log`
ADD COLUMN `bet_sn` varchar(32) NOT NULL COMMENT '订单号' AFTER `bet_log_id`;

ALTER TABLE `tp_bet_log`
ADD INDEX(`user_id`, `game_type_id`, `is_win`, `is_open`) USING BTREE COMMENT '统计某彩种输赢';

ALTER TABLE `tp_bet_log`
ADD COLUMN `tuishui_money` float(11, 2) NOT NULL COMMENT '退水金额' AFTER `pan_type`;

CREATE TABLE `tp_water` (
  `water_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '名称',
  `part_key` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '格式',
  `a` decimal(10,2) NOT NULL,
  `b` decimal(10,2) NOT NULL,
  `c` decimal(10,2) NOT NULL,
  `d` decimal(10,2) NOT NULL,
  `limit` decimal(10,2) NOT NULL,
  `high_limit` decimal(10,2) NOT NULL,
  `low_limit` decimal(10,2) NOT NULL,
  PRIMARY KEY (`water_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;