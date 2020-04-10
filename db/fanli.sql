#2016-07-03 jiangwei 积分赠送计划表：主键，赠送类型（自己消费1，推荐他人消费2），消费金额，赠送积分数，开始时间，结束时间（自己消费时，该值为无穷大），用户ID，推荐用户ID），订单ID，消费发生时间；
CREATE TABLE `tp_integral_plan` (
  `integral_plan_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `plan_type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '自己消费1，推荐他人消费2',
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `rec_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'plan_type=2时，消费的用户ID',
  `order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID',
  `pay_amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '消费金额',
  `send_num` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送积分数',
  `start_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始赠送时间',
  `end_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束赠送时间，自己消费时，该值为无穷大',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '确认收货时间',
  PRIMARY KEY (`integral_plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='积分赠送计划表';

#2016-07-03 jiangwei 每日限购金额
INSERT INTO tp_config(config_name, config_value) VALUES ('daily_consume_limit', 10000);
