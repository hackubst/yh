CREATE TABLE `tp_material` (
  `material_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  `isuse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可用 2不可用 1可用',
  `img_url` varchar(255) NOT NULL COMMENT '图片地址',
  `money` float(20,0) NOT NULL DEFAULT '0' COMMENT '兑换所需金豆',
  `content` text NOT NULL COMMENT '详情',
  `serial` int(11) NOT NULL DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='实物表';