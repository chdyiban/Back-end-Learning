-- -----------------------------
-- 表结构 `ocenter_issue`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `ocenter_issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `allow_post` tinyint(4) NOT NULL,
  `pid` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `ocenter_issue_content`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `ocenter_issue_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '项目名称',
  `type` varchar(50) NOT NULL COMMENT '所属类别',
  `bind_unitech` boolean NOT NULL COMMENT '是否与高校科技成果相结合',
  `description` varchar(200) NOT NULL COMMENT '项目概述',
  `content` text NOT NULL COMMENT '内容',
  `tc_name` varchar(50) NOT NULL COMMENT '公司(团队)名称',
  `members` varchar(100) NOT NULL COMMENT '其他队员信息',
  `c_zczb` varchar(50) NOT NULL COMMENT '注册资本',
  `c_frdb` varchar(50) NOT NULL COMMENT '法人代表',
  `c_gsdz` varchar(50) NOT NULL COMMENT '公司地址',
  `c_jyfw` varchar(50) NOT NULL COMMENT '经营范围',
  `view_count` int(11) NOT NULL COMMENT '阅读数量',
  `cover_id` int(11) NOT NULL COMMENT '项目图片id',
  `issue_id` int(11) NOT NULL COMMENT '所在项目类别',
  `plan_id` int(11) NOT NULL COMMENT '商业计划书id',
  `uid` int(11) NOT NULL COMMENT '发布者id',
  `reply_count` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` tinyint(11) NOT NULL,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='创新创业项目内容表';

-- -----------------------------
-- 表内记录 `ocenter_issue`
-- -----------------------------

-- -----------------------------
-- 表内记录 `ocenter_issue_content`
-- -----------------------------