/*
Navicat MySQL Data Transfer

Source Server         : demo.dangjian.co
Source Server Version : 50647
Source Host           : demo.dangjian.co:3306
Source Database       : demo_dj

Target Server Type    : MYSQL
Target Server Version : 50647
File Encoding         : 65001

Date: 2020-03-19 17:28:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sp_bd_alertmsg
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_alertmsg`;
CREATE TABLE `sp_bd_alertmsg` (
  `alert_id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_type` int(11) DEFAULT NULL COMMENT '提醒类型（那种事务的提醒）',
  `alert_man` varchar(255) DEFAULT NULL COMMENT '提醒人',
  `alert_url` varchar(255) DEFAULT '' COMMENT '提醒链接地址',
  `alert_param` varchar(255) DEFAULT NULL COMMENT '提醒参数',
  `alert_title` varchar(255) DEFAULT NULL COMMENT '事件名称',
  `alert_content` varchar(255) DEFAULT NULL COMMENT '内容',
  `alert_time` varchar(50) DEFAULT NULL COMMENT '提醒的时间',
  `alert_nexttime` varchar(50) DEFAULT NULL COMMENT '下次提醒时间',
  `alert_cycle` varchar(255) DEFAULT NULL COMMENT '提醒周期(one,hour,day,week,month,year)',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT '0' COMMENT '状态    1已读 0未读',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `alert_source` int(11) DEFAULT '1' COMMENT '来源（1，街道。2：党建）',
  PRIMARY KEY (`alert_id`)
) ENGINE=InnoDB AUTO_INCREMENT=550 DEFAULT CHARSET=utf8 COMMENT='提醒消息表';

-- ----------------------------
-- Table structure for sp_bd_area
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_area`;
CREATE TABLE `sp_bd_area` (
  `area_no` int(255) NOT NULL,
  `area_name` varchar(255) DEFAULT NULL,
  `area_pno` int(255) DEFAULT NULL,
  `area_code` varchar(255) DEFAULT NULL,
  `area_sort` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`area_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='区域表';

-- ----------------------------
-- Table structure for sp_bd_code
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_code`;
CREATE TABLE `sp_bd_code` (
  `code_id` int(11) NOT NULL AUTO_INCREMENT,
  `code_no` int(11) DEFAULT NULL,
  `code_name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `code_pid` int(11) DEFAULT NULL COMMENT '所属父级id',
  `code_group` varchar(255) DEFAULT NULL COMMENT '基础信息类型',
  `code_order` int(11) DEFAULT NULL COMMENT '排序字段',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`code_id`)
) ENGINE=InnoDB AUTO_INCREMENT=620 DEFAULT CHARSET=utf8 COMMENT='基础信息表';

-- ----------------------------
-- Table structure for sp_bd_group
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_group`;
CREATE TABLE `sp_bd_group` (
  `group_id` int(11) NOT NULL,
  `group_no` int(11) DEFAULT NULL COMMENT '分组号',
  `group_name` varchar(255) DEFAULT NULL COMMENT '分组名',
  `group_pid` int(11) DEFAULT NULL COMMENT '分组父id',
  `group_code` varchar(255) DEFAULT NULL COMMENT '分组编码',
  `group_order` int(11) DEFAULT NULL COMMENT '分组排序',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分组表';

-- ----------------------------
-- Table structure for sp_bd_nation
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_nation`;
CREATE TABLE `sp_bd_nation` (
  `nation_id` int(11) NOT NULL COMMENT '民族id',
  `nation_no` text COMMENT '民族编号',
  `nation_name` varchar(255) DEFAULT NULL COMMENT '民族名称',
  `nation_code` varchar(255) DEFAULT NULL COMMENT '民族编号',
  `nation_sort` varchar(255) DEFAULT NULL COMMENT '民族简称',
  PRIMARY KEY (`nation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='民族表';

-- ----------------------------
-- Table structure for sp_bd_status
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_status`;
CREATE TABLE `sp_bd_status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '状态表ID',
  `status_group` varchar(255) DEFAULT NULL COMMENT '模块code',
  `status_no` int(11) DEFAULT NULL COMMENT '编号',
  `status_color` varchar(255) DEFAULT NULL COMMENT '状态颜色',
  `status_value` varchar(255) DEFAULT NULL COMMENT '状态值',
  `status_order` int(255) DEFAULT '0' COMMENT '排序',
  `status_name` varchar(255) DEFAULT NULL COMMENT '状态名',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT ' 状态',
  `update_time` datetime DEFAULT NULL COMMENT '	修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=458 DEFAULT CHARSET=utf8 COMMENT='状态表';

-- ----------------------------
-- Table structure for sp_bd_type
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_type`;
CREATE TABLE `sp_bd_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '状态表ID',
  `type_group` varchar(255) DEFAULT NULL COMMENT '类型分组',
  `type_no` int(11) DEFAULT NULL COMMENT '编号',
  `type_code` varchar(255) DEFAULT NULL COMMENT '英文编码',
  `type_name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `type_pno` int(11) DEFAULT NULL COMMENT '所属父级id',
  `type_order` int(11) DEFAULT NULL COMMENT '排序字段',
  `add_staff` varchar(255) DEFAULT NULL,
  `setting` text COMMENT '配置信息',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=889 DEFAULT CHARSET=utf8 COMMENT='类型表';

-- ----------------------------
-- Table structure for sp_bd_upload
-- ----------------------------
DROP TABLE IF EXISTS `sp_bd_upload`;
CREATE TABLE `sp_bd_upload` (
  `upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_path` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '文件路径',
  `upload_size` int(11) DEFAULT NULL COMMENT '文件大小',
  `upload_source` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '原文件名',
  `function_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '所属模块编码',
  `upload_type` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '类型区分   同页面 ',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '上传时间',
  `add_staff` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`upload_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3336 DEFAULT CHARSET=latin1 COMMENT='上传';

-- ----------------------------
-- Table structure for sp_ccp_communist
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist`;
CREATE TABLE `sp_ccp_communist` (
  `communist_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `communist_no` int(11) DEFAULT NULL COMMENT '人员编号',
  `communist_name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `communist_initial` varchar(255) DEFAULT NULL COMMENT '姓名首字母',
  `party_no` int(11) DEFAULT NULL COMMENT '部门',
  `post_no` varchar(255) DEFAULT NULL COMMENT '党内职位',
  `communist_avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `communist_sex` varchar(255) DEFAULT NULL COMMENT '性别 1男 0女',
  `communist_birthday` varchar(255) DEFAULT NULL COMMENT '生日',
  `communist_islunar` varchar(255) DEFAULT NULL COMMENT '农历',
  `communist_leapmonth` varchar(255) DEFAULT NULL COMMENT '闰月',
  `communist_diploma` varchar(255) DEFAULT NULL COMMENT '学历',
  `communist_school` varchar(255) DEFAULT NULL COMMENT '毕业学校',
  `communist_specialty` varchar(255) DEFAULT NULL COMMENT '专业',
  `communist_idnumber` varchar(255) DEFAULT NULL COMMENT '身份证',
  `communist_tel` varchar(15) DEFAULT NULL COMMENT '固话',
  `communist_mobile` varchar(15) DEFAULT NULL COMMENT '手机',
  `communist_email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `communist_qq` varchar(255) DEFAULT NULL COMMENT 'QQ',
  `communist_homepage` varchar(255) DEFAULT NULL COMMENT '主页',
  `communist_blog` varchar(255) DEFAULT NULL COMMENT '博客',
  `communist_address` text COMMENT '地址',
  `communist_paddress` text COMMENT '籍贯',
  `communist_level` varchar(255) DEFAULT NULL COMMENT '职称',
  `add_staff` varchar(255) DEFAULT NULL,
  `communist_type` int(11) DEFAULT '1' COMMENT '1.党员 2. 非党员',
  `status` int(5) DEFAULT NULL COMMENT '关联状态表    0 离职 11入党申请  13积极分子  15发展对象  17预备党员  18考察未通过  21正式党员 22流动党员 23党员流入 24党员流出 31系统外转出 32失联 33清退 34死亡',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `is_hide` varchar(5) DEFAULT '0' COMMENT '隐藏通讯录中的个人资料(0:显示 1:隐藏)',
  `communist_vlan` varchar(15) DEFAULT NULL COMMENT '虚拟网号码',
  `communist_entry_date` date DEFAULT NULL COMMENT '正式入职时间',
  `communist_positive_date` date DEFAULT NULL COMMENT '转正日期',
  `communist_ccp_date` date DEFAULT NULL COMMENT '入党日期',
  `communist_qualifi` text COMMENT '个人资质介绍',
  `communist_qualifi_attach` varchar(255) DEFAULT NULL COMMENT '资质附件',
  `communist_graduate_date` date DEFAULT NULL COMMENT '毕业时间',
  `communist_integral` decimal(8,2) DEFAULT '0.00' COMMENT '党员积分',
  `communist_source` int(11) DEFAULT NULL COMMENT '党员来源类型(关联bd_code表）',
  `is_volunteer` int(11) DEFAULT '0' COMMENT '是否为志愿者 (0：否   1：是)',
  `open_id` varchar(255) DEFAULT NULL COMMENT '微信openid',
  `communist_nation` int(255) DEFAULT NULL COMMENT '民族编号（对应nation表）',
  `communist_remindtime` datetime DEFAULT NULL COMMENT '下次状态改变提醒时间',
  `communist_remindstatus` varchar(11) DEFAULT NULL COMMENT '下次提醒改变状态 0 离职 1提交入党申请  2入党积极分子  3党员发展对象  4预备党员  5考察未通过  6正式党员',
  `communist_honor` varchar(255) DEFAULT NULL COMMENT '党员荣誉',
  `communist_applytime` date DEFAULT NULL COMMENT '入党申请时间',
  `status_date` date DEFAULT NULL COMMENT '状态改变事件（失联，辞职，死亡）',
  `approve_status` int(11) DEFAULT NULL COMMENT '审核状态 审批流',
  `approval_no` int(11) DEFAULT NULL COMMENT '对应审批单编号    ',
  `accid` varchar(255) DEFAULT NULL COMMENT '网易云信账号',
  `token` varchar(255) DEFAULT NULL COMMENT '网易云信密码',
  `communist_label` varchar(255) DEFAULT NULL COMMENT '党员标签',
  `username` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '密码',
  `age_distribute` varchar(255) DEFAULT '3' COMMENT '年龄分布',
  PRIMARY KEY (`communist_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=467 DEFAULT CHARSET=utf8 COMMENT='党员表';

-- ----------------------------
-- Table structure for sp_ccp_communist_bio
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_bio`;
CREATE TABLE `sp_ccp_communist_bio` (
  `bio_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `communist_no` int(11) DEFAULT NULL COMMENT '人员编号（关联sp_hr_communist表）',
  `bio_no` longtext COMMENT '指纹/脸纹编号',
  `bio_type` longtext COMMENT '信息类型：FP（指纹）',
  `is_do_status` varchar(255) DEFAULT NULL COMMENT '信息上传/下载状态(0:未完成;1:已完成;2:已跳过）',
  PRIMARY KEY (`bio_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COMMENT='党员指纹/脸纹信息表';

-- ----------------------------
-- Table structure for sp_ccp_communist_change
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_change`;
CREATE TABLE `sp_ccp_communist_change` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '人员信息变动流水ID',
  `communist_no` int(11) DEFAULT NULL COMMENT '党员编号（关联communist表）',
  `change_type` int(11) DEFAULT NULL COMMENT '变动类型 1.系统内转移  2.系统外转移 3.系统内流动 4.系统外流动',
  `old_party` int(11) DEFAULT NULL COMMENT '原党支部',
  `new_party` varchar(11) DEFAULT NULL COMMENT '现党支部',
  `change_audit_man` varchar(255) DEFAULT NULL COMMENT '审核人',
  `change_audit_content` text COMMENT '审核内容(评语)',
  `change_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `change_audit_status` varchar(5) CHARACTER SET swe7 DEFAULT '0' COMMENT '审核状态  0:待审核  10:已审核(待接收)  20:已接受  30:转移成功  40:驳回 50：流动结束',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `start_time` datetime DEFAULT NULL COMMENT '流动开始',
  `end_time` datetime DEFAULT NULL COMMENT '流动结束',
  `memo` text COMMENT '备注',
  `status` int(11) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`change_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=289 DEFAULT CHARSET=utf8 COMMENT='党员变动表（党员关系转移和流动党员）';

-- ----------------------------
-- Table structure for sp_ccp_communist_change_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_change_log`;
CREATE TABLE `sp_ccp_communist_change_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'log ID',
  `change_id` int(11) DEFAULT NULL COMMENT '党员变动的编号',
  `check_staff` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_content` varchar(255) DEFAULT NULL COMMENT '审核评语',
  `add_time` datetime DEFAULT NULL,
  `status` varchar(11) DEFAULT '1' COMMENT '状态',
  `memo` text COMMENT '备注',
  `log_audit_man` varchar(255) DEFAULT NULL COMMENT '经办人',
  `log_contact_tel` varchar(255) DEFAULT NULL COMMENT '联系方式',
  `log_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8 COMMENT='党员变动log表';

-- ----------------------------
-- Table structure for sp_ccp_communist_chat
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_chat`;
CREATE TABLE `sp_ccp_communist_chat` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `communist_no` varchar(50) DEFAULT NULL COMMENT '聊天人',
  `chat_content` varchar(255) DEFAULT NULL COMMENT '聊天内容',
  `status` int(5) DEFAULT NULL COMMENT '关联状态表',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sp_ccp_communist_comment
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_comment`;
CREATE TABLE `sp_ccp_communist_comment` (
  `comment_no` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `comment_title` varchar(255) DEFAULT NULL COMMENT '评议标题',
  `comment_content` varchar(255) DEFAULT NULL COMMENT '评议内容',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号（关联party表）',
  `comment_date` date DEFAULT NULL COMMENT '评议日期',
  `add_staff` varchar(20) DEFAULT NULL,
  `status` int(5) DEFAULT NULL COMMENT '状态',
  `update_time` varchar(255) DEFAULT NULL COMMENT '修改时间',
  `add_time` varchar(255) DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`comment_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='民主评议表';

-- ----------------------------
-- Table structure for sp_ccp_communist_comment_details
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_comment_details`;
CREATE TABLE `sp_ccp_communist_comment_details` (
  `details_no` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `comment_no` int(11) DEFAULT NULL COMMENT '民主评议编号（关联民主评议表）',
  `communist_no` int(11) DEFAULT NULL COMMENT '员工编号(关联communist表)',
  `details_test_score` varchar(255) DEFAULT '0' COMMENT '测评得分',
  `details_party_score` varchar(255) DEFAULT '0' COMMENT '支部评分',
  `details_total_score` varchar(255) DEFAULT '0' COMMENT '党员评价意见票数（差）',
  `details_opinion` text COMMENT '激励和处理意见',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(5) DEFAULT NULL COMMENT '状态',
  `update_time` varchar(255) DEFAULT NULL COMMENT '修改时间',
  `add_time` varchar(255) DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`details_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=786 DEFAULT CHARSET=utf8 COMMENT='民主评议明细表';

-- ----------------------------
-- Table structure for sp_ccp_communist_help
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_help`;
CREATE TABLE `sp_ccp_communist_help` (
  `help_id` int(11) NOT NULL AUTO_INCREMENT,
  `help_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '姓名',
  `help_sex` int(11) DEFAULT NULL COMMENT '性别 2：女    1：男',
  `help_mobile` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机',
  `add_staff` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` int(5) DEFAULT '1' COMMENT '状态 （0：删除  ）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text CHARACTER SET utf8 COMMENT '备注',
  `help_cadres` int(11) DEFAULT NULL COMMENT '是否为干部  1是  2否',
  `communist_no` int(11) DEFAULT NULL COMMENT '党员编号',
  PRIMARY KEY (`help_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1 COMMENT='困难帮扶';

-- ----------------------------
-- Table structure for sp_ccp_communist_help_team
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_help_team`;
CREATE TABLE `sp_ccp_communist_help_team` (
  `help_team_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '帮扶队伍编号',
  `help_team_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '帮扶队伍名称',
  `help_team_head` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '帮扶队伍负责人',
  `help_team_type` varchar(255) DEFAULT NULL COMMENT '队伍类型',
  `help_team_members` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '队伍成员',
  `staff_no` int(11) DEFAULT NULL COMMENT '工作编号',
  `status` int(6) NOT NULL DEFAULT '1' COMMENT '状态',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '创建时间',
  `memo` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`help_team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='帮扶队伍';

-- ----------------------------
-- Table structure for sp_ccp_communist_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_communist_log`;
CREATE TABLE `sp_ccp_communist_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '人员信息变动流水ID',
  `communist_no` int(11) DEFAULT NULL COMMENT '员工工号',
  `log_name` varchar(255) DEFAULT NULL COMMENT '变动名称',
  `log_type` int(11) DEFAULT NULL COMMENT '变动类型 10 党员历程',
  `log_oldcontent` text COMMENT '变动前的内容',
  `log_content` text COMMENT '变动后的内容',
  `log_audit_man` varchar(50) DEFAULT NULL COMMENT '审核人',
  `log_audit_content` text COMMENT '审核内容(评语)',
  `log_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `log_attach` varchar(255) DEFAULT NULL COMMENT '变动资料',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=462 DEFAULT CHARSET=utf8 COMMENT='党员变动表';

-- ----------------------------
-- Table structure for sp_ccp_dues
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_dues`;
CREATE TABLE `sp_ccp_dues` (
  `dues_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '缴费编号',
  `dues_amount` decimal(10,2) DEFAULT '0.00' COMMENT '缴纳金额',
  `dues_content` text COMMENT '流水内容(显示流水日志)',
  `dues_time` date DEFAULT NULL COMMENT '缴纳时间',
  `communist_no` int(11) DEFAULT NULL COMMENT '缴费党员编号（关联communist表）',
  `add_staff_name` varchar(255) DEFAULT NULL COMMENT '添加人姓名',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '操作人',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL COMMENT '关联 status 默认1 未缴纳  2 已缴纳',
  `memo` text,
  `dues_month` varchar(255) DEFAULT NULL COMMENT '缴纳月份',
  `communist_name` varchar(255) DEFAULT NULL COMMENT '缴费党员姓名',
  `party_no` int(11) DEFAULT NULL COMMENT '缴纳人员所在党组织编号（关联party表）',
  `party_name` varchar(255) DEFAULT NULL COMMENT '缴纳人员所在党组织名称（关联party表）',
  PRIMARY KEY (`dues_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='党费记录表    Chinese Communist communist中国共产党';

-- ----------------------------
-- Table structure for sp_ccp_group
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_group`;
CREATE TABLE `sp_ccp_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `group_name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `add_staff` varchar(20) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0 离职 1在职 2非员工',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1569 DEFAULT CHARSET=utf8 COMMENT='党员分组表';

-- ----------------------------
-- Table structure for sp_ccp_group_communist
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_group_communist`;
CREATE TABLE `sp_ccp_group_communist` (
  `communist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_communist_id` int(11) DEFAULT NULL COMMENT '关联group_id',
  `communist_no` int(11) DEFAULT NULL COMMENT '党员编号关联communist表',
  `add_staff` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0 离职 1在职 2非员工',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`communist_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='分组关联党员表';

-- ----------------------------
-- Table structure for sp_ccp_help_measures
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_help_measures`;
CREATE TABLE `sp_ccp_help_measures` (
  `measures_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(255) DEFAULT NULL COMMENT '区分2人员和1队伍',
  `measures_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `measures_team` varchar(255) DEFAULT NULL COMMENT '所属队伍',
  `measures_leader` varchar(255) DEFAULT NULL COMMENT '负责人',
  `measures_help` varchar(255) DEFAULT NULL COMMENT '帮扶人',
  `measures_phone` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `measures_help_time` date DEFAULT NULL COMMENT '帮扶时间',
  `measures_genre` int(11) DEFAULT NULL COMMENT '帮扶对象',
  `measures_genre_no` text CHARACTER SET utf8mb4 COMMENT '帮扶那个村，那个户',
  `measures_type` varchar(255) DEFAULT NULL COMMENT 'help measures 关联type',
  `status` int(11) DEFAULT '1',
  `add_staff` varchar(255) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`measures_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sp_ccp_import
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_import`;
CREATE TABLE `sp_ccp_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_type` varchar(255) DEFAULT NULL COMMENT '1 党组织 2党员',
  `party_no` varchar(255) DEFAULT NULL COMMENT '编号',
  `party_pno` varchar(255) DEFAULT NULL COMMENT '上级组织编号',
  `party_name` varchar(255) DEFAULT NULL COMMENT '党组织名称',
  `communist_name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `communist_no` varchar(255) DEFAULT NULL COMMENT '编号',
  `communist_sex` varchar(255) DEFAULT NULL COMMENT '性别',
  `communist_nation` varchar(255) DEFAULT NULL COMMENT '民族',
  `communist_birthday` varchar(255) DEFAULT NULL COMMENT '出生日期',
  `communist_idnumber` varchar(255) DEFAULT NULL COMMENT '身份证',
  `communist_paddress` varchar(255) DEFAULT NULL COMMENT '籍贯',
  `communist_address` varchar(255) DEFAULT NULL COMMENT '现住址',
  `communist_diploma` varchar(255) DEFAULT NULL COMMENT '学历',
  `communist_specialty` varchar(255) DEFAULT NULL COMMENT '专业',
  `communist_school` varchar(255) DEFAULT NULL COMMENT '学校',
  `communist_graduate_date` varchar(255) DEFAULT NULL COMMENT '参加工作时间',
  `communist_tel` varchar(255) DEFAULT NULL COMMENT '电话',
  `communist_mobile` varchar(255) DEFAULT NULL COMMENT '手机',
  `communist_email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `communist_qq` varchar(255) DEFAULT NULL COMMENT 'Qq',
  `communist_applytime` varchar(255) DEFAULT NULL COMMENT '申请入党时间',
  `communist_ccp_date` varchar(255) DEFAULT NULL COMMENT '入党时间',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='导入数据存储表';

-- ----------------------------
-- Table structure for sp_ccp_integral_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_integral_log`;
CREATE TABLE `sp_ccp_integral_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_relation_type` varchar(5) NOT NULL COMMENT '1:党组织2：党员',
  `log_relation_no` int(11) NOT NULL COMMENT '党员编号或党组织编号',
  `change_type` int(11) NOT NULL COMMENT '变动类型 7 增加 8 减少',
  `change_integral` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动积分',
  `status` varchar(5) NOT NULL COMMENT '审核状态(1:已审核0:未审核)',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `year` varchar(255) DEFAULT NULL COMMENT '年',
  `month` varchar(255) DEFAULT NULL COMMENT '月',
  `cause` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4850 DEFAULT CHARSET=utf8 COMMENT='积分明细表';

-- ----------------------------
-- Table structure for sp_ccp_party
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_party`;
CREATE TABLE `sp_ccp_party` (
  `party_no` int(11) NOT NULL COMMENT '编号',
  `party_name` varchar(255) DEFAULT NULL COMMENT '党组织名称',
  `party_name_short` varchar(255) DEFAULT NULL COMMENT '党组织简称',
  `party_pno` int(11) DEFAULT NULL COMMENT '上级党组织',
  `party_manager` varchar(100) DEFAULT NULL COMMENT '负责人',
  `add_staff` varchar(255) DEFAULT NULL,
  `party_type` int(11) DEFAULT '1' COMMENT '1.党支部 2.非党员工作团体',
  `status` varchar(5) DEFAULT NULL COMMENT '	状态',
  `update_time` datetime DEFAULT NULL COMMENT '	修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `party_integral` decimal(8,2) DEFAULT '0.00' COMMENT '支部积分',
  `party_camera` varchar(255) DEFAULT NULL COMMENT '摄像头id',
  `party_propagate` varchar(255) DEFAULT NULL COMMENT '党组织风采（图片）',
  `gc_lng` varchar(255) DEFAULT NULL COMMENT '经度地理坐标 gc geographical coordinates 的缩写',
  `gc_lat` varchar(255) DEFAULT NULL COMMENT '维度 地理坐标 gc geographical coordinates 的缩写',
  `party_avatar` varchar(255) DEFAULT NULL COMMENT '党组织头像',
  `party_level_code` varchar(255) DEFAULT NULL COMMENT '党组织分类 关联code表',
  `party_address` varchar(255) DEFAULT NULL COMMENT '地址',
  `party_user` varchar(255) DEFAULT NULL COMMENT '组织账号',
  `party_pwd` varchar(255) DEFAULT NULL COMMENT '组织密码',
  `party_change_time` varchar(100) DEFAULT NULL COMMENT '换届时间',
  `party_branch_secretary` varchar(255) DEFAULT NULL COMMENT '党支部书记',
  PRIMARY KEY (`party_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='党组织（支部结构）表';

-- ----------------------------
-- Table structure for sp_ccp_party_duty
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_party_duty`;
CREATE TABLE `sp_ccp_party_duty` (
  `post_no` int(11) NOT NULL COMMENT '职务编号',
  `post_name` varchar(255) DEFAULT NULL COMMENT '职务名称',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`post_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='党内职务表';

-- ----------------------------
-- Table structure for sp_ccp_party_grid
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_party_grid`;
CREATE TABLE `sp_ccp_party_grid` (
  `party_no` int(11) DEFAULT NULL COMMENT '编号',
  `gc_lng` varchar(255) DEFAULT NULL COMMENT '经度地理坐标 gc geographical coordinates 的缩写',
  `gc_lat` varchar(255) DEFAULT NULL COMMENT '维度 地理坐标 gc geographical coordinates 的缩写',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(5) DEFAULT NULL COMMENT '	状态',
  `update_time` datetime DEFAULT NULL COMMENT '	修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='党组织地图（支部）表';

-- ----------------------------
-- Table structure for sp_ccp_partyday_category
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_partyday_category`;
CREATE TABLE `sp_ccp_partyday_category` (
  `partyday_id` int(11) NOT NULL AUTO_INCREMENT,
  `partyday_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `partyday_time` date DEFAULT NULL COMMENT '活动时间',
  `partyday_content` text COMMENT '活动内容',
  `partyday_require` varchar(255) DEFAULT NULL COMMENT '活动要求',
  `partyday_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `approval_staff` varchar(255) DEFAULT NULL COMMENT '审批人',
  `status` int(5) DEFAULT '0' COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `update_staff` varchar(255) DEFAULT NULL COMMENT '更改人',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`partyday_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='党日活动栏目表';

-- ----------------------------
-- Table structure for sp_ccp_partyday_plan
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_partyday_plan`;
CREATE TABLE `sp_ccp_partyday_plan` (
  `partyday_id` int(11) NOT NULL AUTO_INCREMENT,
  `partyday_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `partyday_time` date DEFAULT NULL COMMENT '活动时间',
  `partyday_content` text COMMENT '活动内容',
  `partyday_require` varchar(255) DEFAULT NULL COMMENT '活动要求',
  `partyday_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `approval_staff` varchar(255) DEFAULT NULL COMMENT '审批人',
  `status` int(5) DEFAULT '0' COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `update_staff` varchar(255) DEFAULT NULL COMMENT '更改人',
  `memo` text COMMENT '备注',
  `partyday_log_status` varchar(5) DEFAULT '0' COMMENT '计划跟踪审核状态',
  PRIMARY KEY (`partyday_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='党日活动计划表';

-- ----------------------------
-- Table structure for sp_ccp_partyday_plan_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_partyday_plan_log`;
CREATE TABLE `sp_ccp_partyday_plan_log` (
  `partyday_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '党日活动表',
  `partyday_id` int(11) DEFAULT NULL COMMENT '计划编号',
  `party_no` varchar(255) DEFAULT NULL COMMENT '党组织编号',
  `meeting_grade` decimal(11,2) DEFAULT '0.00' COMMENT '三会一课积分',
  `dues_grade` decimal(11,2) DEFAULT '0.00' COMMENT '党费缴纳积分',
  `cms_grade` decimal(11,2) DEFAULT '0.00' COMMENT '宣传文稿',
  `partyday_grade` decimal(11,2) DEFAULT '0.00' COMMENT '工作计划情况',
  `response_grade` decimal(11,2) DEFAULT '0.00' COMMENT '思想动态反应积分',
  `condition_grade` decimal(11,2) DEFAULT '0.00' COMMENT '遵纪守法情况',
  `workable_grade` decimal(11,2) DEFAULT '0.00' COMMENT '支部计划落实',
  `cultivate_grade` decimal(11,2) DEFAULT '0.00' COMMENT '积极分子培训',
  `activity_grade` decimal(11,2) DEFAULT '0.00' COMMENT '创新活动',
  `total_grade` decimal(11,2) DEFAULT '0.00' COMMENT '合计',
  `average_grade` decimal(11,2) DEFAULT '0.00' COMMENT '平均分',
  `meeting_content` text COMMENT '三会一课积分',
  `dues_content` text COMMENT '党费缴纳积分',
  `cms_content` text COMMENT '宣传文稿',
  `partyday_content` text COMMENT '工作计划情况',
  `response_content` text COMMENT '思想动态反应积分',
  `condition_content` text COMMENT '遵纪守法情况',
  `workable_content` text COMMENT '支部计划落实',
  `cultivate_content` text COMMENT '积极分子培训',
  `activity_content` text COMMENT '创新活动',
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`partyday_log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8 COMMENT='党日活动记录表';

-- ----------------------------
-- Table structure for sp_ccp_people
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_people`;
CREATE TABLE `sp_ccp_people` (
  `people_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `people_no` varchar(25) DEFAULT NULL COMMENT '关联党员表非党员表编号',
  `people_name` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '姓名',
  `people_card` varchar(35) DEFAULT NULL COMMENT '身份证',
  `status` int(4) DEFAULT NULL COMMENT '区分 1党员 2工作人员 3工作人员党员  4群众',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `add_staff` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '添加人',
  `memo` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`people_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='党员表和工作人员表的内容组合';

-- ----------------------------
-- Table structure for sp_ccp_secretary
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_secretary`;
CREATE TABLE `sp_ccp_secretary` (
  `secretary_id` int(11) NOT NULL AUTO_INCREMENT,
  `secretary_type` int(11) DEFAULT NULL COMMENT '区分第一书记与双联双创工作组',
  `secretary_name` varchar(255) DEFAULT NULL COMMENT '工作组名称（双联双创使用）',
  `communist_no` varchar(255) DEFAULT NULL COMMENT '党员编号（关联communist表）',
  `communist_party` int(11) DEFAULT NULL COMMENT '下乡工作所在党组织编号（关联party表）',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(255) DEFAULT NULL COMMENT '状态',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `secretary_start_date` date DEFAULT NULL COMMENT '第一书记开始日期',
  `secretary_end_date` date DEFAULT NULL COMMENT '第一书记结束日期',
  PRIMARY KEY (`secretary_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='第一书记表\r\n';

-- ----------------------------
-- Table structure for sp_ccp_secretary_sign
-- ----------------------------
DROP TABLE IF EXISTS `sp_ccp_secretary_sign`;
CREATE TABLE `sp_ccp_secretary_sign` (
  `sign_id` int(11) NOT NULL AUTO_INCREMENT,
  `sign_type` int(11) DEFAULT NULL COMMENT '区分双联双创与第一书记',
  `sign_name` varchar(255) DEFAULT NULL,
  `communist_no` int(11) DEFAULT NULL COMMENT '党员编号（关联communist表）',
  `communist_party` int(11) DEFAULT NULL,
  `sign_position` varchar(255) DEFAULT NULL COMMENT '签到的地理位置',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` int(11) DEFAULT NULL COMMENT '状态',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `sign_gc` varchar(255) DEFAULT NULL COMMENT '签到地点的经纬度 地理坐标 gc geographical coordinates 的缩写',
  `sign_time` datetime DEFAULT NULL COMMENT '签到时间',
  `sign_img` text COMMENT '签到图片',
  PRIMARY KEY (`sign_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='第一书记签到表\r\n';

-- ----------------------------
-- Table structure for sp_cms_affairs
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_affairs`;
CREATE TABLE `sp_cms_affairs` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文字ID',
  `article_cat` int(11) DEFAULT NULL COMMENT '文章分类id',
  `article_title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `article_keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `article_description` varchar(255) DEFAULT NULL COMMENT '描述',
  `article_content` text COMMENT '文章内容',
  `article_thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `article_order` int(11) DEFAULT NULL COMMENT '排序',
  `article_img` varchar(255) DEFAULT NULL,
  `setting` text COMMENT '存储json',
  `article_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `article_audit_staff` varchar(255) DEFAULT '0' COMMENT '审核人（关联staff表）',
  `article_audit_content` text COMMENT '审核意见',
  `article_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `communist_no` int(11) DEFAULT NULL COMMENT '文稿人员编号（关联communist表）',
  `party_no` int(11) DEFAULT NULL COMMENT '文稿部门编号（关联party表）',
  `article_type` varchar(255) DEFAULT '11' COMMENT '文稿类型 （11：文章  21：会议 ）',
  `article_author` varchar(255) DEFAULT NULL COMMENT '文稿作者',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`article_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='三务公开';

-- ----------------------------
-- Table structure for sp_cms_affairs_category
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_affairs_category`;
CREATE TABLE `sp_cms_affairs_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `cat_name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `cat_pid` int(11) DEFAULT NULL COMMENT '所属父级id',
  `cat_type` int(11) DEFAULT NULL COMMENT '类型与文章的标识',
  `cat_content` text COMMENT '文章内容',
  `cat_title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `cat_keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `cat_description` varchar(255) DEFAULT NULL COMMENT '文章描述',
  `cat_img` varchar(255) DEFAULT NULL COMMENT '图片',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`cat_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='三务公开栏目表';

-- ----------------------------
-- Table structure for sp_cms_article
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_article`;
CREATE TABLE `sp_cms_article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文字ID',
  `article_cat` int(11) DEFAULT NULL COMMENT '文章分类id',
  `article_title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `article_keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `article_source` varchar(255) DEFAULT NULL COMMENT '来源',
  `article_description` varchar(255) DEFAULT NULL COMMENT '描述',
  `article_content` mediumtext COMMENT '文章内容',
  `article_thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `article_img` varchar(255) DEFAULT NULL,
  `article_view` int(11) DEFAULT '0' COMMENT '文章浏览量',
  `article_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `article_audit` int(11) DEFAULT '0' COMMENT '是否审核过',
  `article_audit_staff` varchar(255) DEFAULT NULL COMMENT '审核人（关联staff表）',
  `article_audit_content` text COMMENT '审核意见',
  `article_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `communist_no` text COMMENT '文稿人员编号（关联communist表）作者',
  `party_no` text COMMENT '文稿部门编号（关联party表）',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_staff` varchar(255) DEFAULT '9999',
  `status` varchar(5) DEFAULT '1' COMMENT '状态1正常/审核通过21待审核31驳回',
  `memo` text COMMENT '备注',
  `article_point` tinyint(4) DEFAULT NULL COMMENT '文章指向',
  `contributor` varchar(255) DEFAULT NULL COMMENT '投稿人',
  `author` varchar(100) DEFAULT NULL COMMENT '投稿作者',
  PRIMARY KEY (`article_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Table structure for sp_cms_article_category
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_article_category`;
CREATE TABLE `sp_cms_article_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `cat_name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `cat_pid` int(11) DEFAULT NULL COMMENT '所属父级id',
  `cat_type` int(11) DEFAULT '1' COMMENT '类型与文章的标识1普通文章栏目，2为标题并存在内容，3.内置栏目（例如：精准扶贫） 4 文章内置栏目 ',
  `cat_content` text COMMENT '文章内容',
  `cat_title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `cat_keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `cat_description` varchar(255) DEFAULT NULL COMMENT '文章描述',
  `cat_img` varchar(255) DEFAULT NULL COMMENT '图片',
  `status` varchar(5) DEFAULT '1' COMMENT '状态0停用1启用4为隐藏',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`cat_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1405 DEFAULT CHARSET=utf8 COMMENT='文章栏目表';

-- ----------------------------
-- Table structure for sp_cms_article_comment
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_article_comment`;
CREATE TABLE `sp_cms_article_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '留言与回复公用（id）',
  `comment_content` text COMMENT '评论内容',
  `communist_no` int(11) DEFAULT NULL COMMENT '评论人（党员）编号（关联communist表）',
  `comment_time` datetime DEFAULT NULL COMMENT '帖子的评论时间',
  `comment_type` varchar(10) DEFAULT '0' COMMENT '0 回复 1评论',
  `status` varchar(255) DEFAULT '0' COMMENT '0 待审核 1通过 2驳回',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL COMMENT '帖子id',
  PRIMARY KEY (`comment_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='帖子评论表';

-- ----------------------------
-- Table structure for sp_cms_article_fav
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_article_fav`;
CREATE TABLE `sp_cms_article_fav` (
  `fav_id` int(11) NOT NULL AUTO_INCREMENT,
  `communist_no` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`fav_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='文章（知识）收藏表';

-- ----------------------------
-- Table structure for sp_cms_article_give
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_article_give`;
CREATE TABLE `sp_cms_article_give` (
  `give_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL COMMENT '对应文章id',
  `comment_id` int(11) DEFAULT NULL COMMENT '对应评论的id',
  `communist_no` int(11) DEFAULT NULL COMMENT '点赞人编号',
  `give_type` int(2) DEFAULT NULL COMMENT '1文章的点赞     2评论的点赞',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `add_staff` varchar(50) DEFAULT NULL,
  `status` int(3) DEFAULT '1',
  PRIMARY KEY (`give_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='点赞表';

-- ----------------------------
-- Table structure for sp_cms_contribute
-- ----------------------------
DROP TABLE IF EXISTS `sp_cms_contribute`;
CREATE TABLE `sp_cms_contribute` (
  `contribute_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文稿ID',
  `contribute_title` varchar(255) DEFAULT NULL COMMENT '文稿标题',
  `contribute_description` varchar(255) DEFAULT NULL COMMENT '文稿描述',
  `contribute_content` text COMMENT '文稿内容',
  `contribute_order` int(11) DEFAULT NULL COMMENT '排序',
  `contribute_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `contribute_audit_communist` varchar(255) DEFAULT '0' COMMENT '审核人（关联communist表）',
  `contribute_audit_content` text COMMENT '审核意见',
  `contribute_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `memo` text COMMENT '备注',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `communist_no` int(11) DEFAULT NULL COMMENT '人员编号（关联communist表）',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号（关联party表）',
  `contribute_type` int(11) DEFAULT '11' COMMENT '文稿类型 （11：文章  21：会议 ）',
  `contribute_author` varchar(255) DEFAULT NULL COMMENT '作者',
  PRIMARY KEY (`contribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='党员投稿表（党员声音）';

-- ----------------------------
-- Table structure for sp_com_email_inbox
-- ----------------------------
DROP TABLE IF EXISTS `sp_com_email_inbox`;
CREATE TABLE `sp_com_email_inbox` (
  `inbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_receiver` int(11) DEFAULT NULL COMMENT '收件人',
  `email_contentid` int(11) DEFAULT NULL COMMENT '邮件内容',
  `is_read` varchar(5) DEFAULT NULL COMMENT '是否已读',
  `email_read_time` datetime DEFAULT NULL COMMENT '阅读时间',
  `is_del` varchar(5) DEFAULT NULL COMMENT '是否已删除',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`inbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='邮件收件箱';

-- ----------------------------
-- Table structure for sp_com_email_outbox
-- ----------------------------
DROP TABLE IF EXISTS `sp_com_email_outbox`;
CREATE TABLE `sp_com_email_outbox` (
  `imail_id` int(11) NOT NULL AUTO_INCREMENT,
  `imail_sender` varchar(255) DEFAULT NULL COMMENT '发件人',
  `imail_receivers` varchar(255) DEFAULT NULL COMMENT '接收人',
  `imail_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `imail_content` varchar(255) DEFAULT NULL COMMENT '内容',
  `imail_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `is_del` varchar(5) DEFAULT NULL COMMENT '是否已删除',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `people_type` int(255) DEFAULT NULL COMMENT '员工类型（0：员工   1：客户   2：供应商）',
  PRIMARY KEY (`imail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='邮件发件箱';

-- ----------------------------
-- Table structure for sp_com_imail_inbox
-- ----------------------------
DROP TABLE IF EXISTS `sp_com_imail_inbox`;
CREATE TABLE `sp_com_imail_inbox` (
  `inbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `imail_receiver` varchar(20) DEFAULT NULL COMMENT '收件人',
  `imail_contentid` int(11) DEFAULT NULL COMMENT '邮件内容',
  `is_read` int(255) DEFAULT '0' COMMENT '是否已读（0未读  1已读）',
  `imail_read_time` datetime DEFAULT NULL COMMENT '阅读时间',
  `is_del` varchar(5) DEFAULT '0' COMMENT '是否已删除（0未删除   1已删除）',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`inbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COMMENT='站内信收件箱';

-- ----------------------------
-- Table structure for sp_com_imail_outbox
-- ----------------------------
DROP TABLE IF EXISTS `sp_com_imail_outbox`;
CREATE TABLE `sp_com_imail_outbox` (
  `imail_id` int(11) NOT NULL AUTO_INCREMENT,
  `imail_sender` varchar(255) DEFAULT NULL COMMENT '发件人',
  `imail_receivers` varchar(255) DEFAULT NULL COMMENT '接收人',
  `imail_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `imail_content` longtext COMMENT '内容',
  `imail_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `is_del` varchar(5) DEFAULT '0' COMMENT '是否已删除',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `people_type` int(11) DEFAULT NULL COMMENT '员工类型（0：员工   1：客户   2：供应商）',
  `is_check` int(11) DEFAULT '0' COMMENT '是否公示',
  PRIMARY KEY (`imail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='站内信发件箱';

-- ----------------------------
-- Table structure for sp_com_msg_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_com_msg_log`;
CREATE TABLE `sp_com_msg_log` (
  `msglog_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_code` varchar(255) DEFAULT NULL COMMENT '接收人类型(客户，员工，供应商)',
  `msg_sender` varchar(255) DEFAULT NULL COMMENT '发送人编号',
  `msg_receivers` varchar(255) DEFAULT NULL COMMENT '接收人编号（多编号-逗号分隔）',
  `msg_template` varchar(255) DEFAULT NULL COMMENT '模板ID-关联模版表',
  `msg_content` text,
  `msg_param` text COMMENT '模版参数json格式',
  `is_send` int(255) DEFAULT NULL COMMENT '是否发送成功（0：否 1：是）',
  `is_sms` int(255) DEFAULT NULL COMMENT '是否是发送短息(1：是 0：否）-只用于短信列表发送记录查询数据',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`msglog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8 COMMENT='消息记录表（消息池）';

-- ----------------------------
-- Table structure for sp_com_msg_template
-- ----------------------------
DROP TABLE IF EXISTS `sp_com_msg_template`;
CREATE TABLE `sp_com_msg_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '模板ID',
  `template_name` varchar(255) DEFAULT NULL COMMENT '模板名称',
  `template_type` varchar(255) DEFAULT NULL COMMENT '模板类型',
  `is_alertmsg` varchar(255) DEFAULT '0' COMMENT '是否消息提醒（0：否 1：是）',
  `is_sms` varchar(255) DEFAULT '0' COMMENT '是否发送短信（0：否 1：是）',
  `is_email` varchar(255) DEFAULT '0' COMMENT '是否发送邮件',
  `is_pushmsg` varchar(255) DEFAULT '0' COMMENT '是否推送',
  `alidayu_code` varchar(255) DEFAULT NULL,
  `template_content` varchar(255) DEFAULT NULL COMMENT '模板内容',
  `template_param` varchar(255) DEFAULT NULL COMMENT '模板所需参数',
  `template_aliparam` varchar(255) DEFAULT NULL,
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='消息模板（消息池）';

-- ----------------------------
-- Table structure for sp_com_msg_type
-- ----------------------------
DROP TABLE IF EXISTS `sp_com_msg_type`;
CREATE TABLE `sp_com_msg_type` (
  `type_id` int(255) NOT NULL AUTO_INCREMENT,
  `type_no` varchar(255) DEFAULT NULL COMMENT '类型编号',
  `type_code` varchar(255) DEFAULT NULL,
  `type_name` varchar(255) DEFAULT NULL COMMENT '类型名称',
  `is_msg` varchar(255) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='消息类型表（消息池）';

-- ----------------------------
-- Table structure for sp_edu_customization
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_customization`;
CREATE TABLE `sp_edu_customization` (
  `customization_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `communist_no` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '党员编号',
  `material_group` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '所选的对应群体',
  `material_data` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '所选的资料标签',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` int(5) DEFAULT '1',
  `memo` varchar(255) DEFAULT NULL,
  `add_staff` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`customization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='党员是否定制表';

-- ----------------------------
-- Table structure for sp_edu_customization_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_customization_log`;
CREATE TABLE `sp_edu_customization_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customization_id` int(11) DEFAULT NULL COMMENT '党员定制表对应主键',
  `edu_type` int(11) DEFAULT NULL COMMENT '1课件文章     2视频     3考试',
  `all_data_id` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '所对应类型的所有数据的 id',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `memo` varchar(255) DEFAULT NULL,
  `add_staff` varchar(255) DEFAULT NULL,
  `edu_num` varchar(50) DEFAULT NULL COMMENT '学习的总个数',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='党员定制子表';

-- ----------------------------
-- Table structure for sp_edu_exam
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_exam`;
CREATE TABLE `sp_edu_exam` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_topic` varchar(255) DEFAULT NULL COMMENT '专题',
  `exam_thumb` varchar(255) DEFAULT '' COMMENT '考试缩略图',
  `exam_title` varchar(255) DEFAULT NULL COMMENT '试卷标题',
  `exam_time` varchar(255) DEFAULT NULL COMMENT '考试时长',
  `exam_score` varchar(255) DEFAULT NULL COMMENT '分值',
  `exam_integral` varchar(255) DEFAULT NULL COMMENT '考试所得积分',
  `exam_party` varchar(255) DEFAULT NULL COMMENT '考试范围（逗号分隔的支部编号）',
  `exam_date` date DEFAULT NULL COMMENT '考试时间',
  `exam_questions` varchar(255) DEFAULT NULL COMMENT '关联试题库',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '11' COMMENT '状态 （11：未开始   21：考试中   31：已结束）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `exam_group` varchar(255) DEFAULT NULL COMMENT '对应群体',
  `exam_data_r` varchar(255) DEFAULT NULL COMMENT '资料标签',
  `is_simulation` int(5) DEFAULT '0' COMMENT '是否是模拟考试  1是',
  PRIMARY KEY (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COMMENT='考试表';

-- ----------------------------
-- Table structure for sp_edu_exam_answer
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_exam_answer`;
CREATE TABLE `sp_edu_exam_answer` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `communist_no` varchar(255) DEFAULT NULL COMMENT '人员编号',
  `exam_id` varchar(255) DEFAULT NULL COMMENT '试卷id',
  `questions_id` varchar(255) DEFAULT NULL COMMENT '考题id',
  `answer_item` varchar(255) DEFAULT NULL COMMENT '我的答案',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8 COMMENT='考试答题表';

-- ----------------------------
-- Table structure for sp_edu_exam_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_exam_log`;
CREATE TABLE `sp_edu_exam_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评分id',
  `communist_no` varchar(50) DEFAULT NULL COMMENT '谁考的',
  `exam_id` varchar(255) DEFAULT NULL COMMENT '试卷编号id 考的那一次',
  `log_score` float(10,0) DEFAULT NULL COMMENT '考试得分',
  `log_date` date DEFAULT NULL COMMENT '考试时间',
  `log_integral` varchar(255) DEFAULT NULL COMMENT '本次考试所得积分',
  `add_communist` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `time_lave` varchar(25) DEFAULT NULL COMMENT '考试用时',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='考试记录表';

-- ----------------------------
-- Table structure for sp_edu_groupdata
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_groupdata`;
CREATE TABLE `sp_edu_groupdata` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_title` varchar(255) DEFAULT NULL,
  `group_img` varchar(50) DEFAULT NULL COMMENT '图片',
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '专题介绍',
  `group_type` int(2) DEFAULT NULL COMMENT '1对应群体  2资料标签',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='对应群体/资料标签';

-- ----------------------------
-- Table structure for sp_edu_material
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_material`;
CREATE TABLE `sp_edu_material` (
  `material_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文字ID',
  `material_cat` int(11) DEFAULT NULL COMMENT '文章分类id',
  `material_topic` int(11) DEFAULT NULL COMMENT '专题id',
  `material_title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `material_keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `material_desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `material_content` mediumtext COMMENT '文章内容',
  `material_thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `material_img` varchar(255) DEFAULT NULL,
  `material_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `material_sourcs` varchar(255) DEFAULT NULL COMMENT '来源',
  `material_duration` varchar(255) DEFAULT NULL COMMENT '学习时长',
  `material_duration_integral` tinyint(4) DEFAULT NULL COMMENT '积分计算时间',
  `video_duration` varchar(255) DEFAULT NULL COMMENT '视频时长',
  `material_integral` varchar(255) DEFAULT '0' COMMENT '资料积分',
  `material_vedio` text COMMENT '视频代码',
  `is_read` int(11) DEFAULT '0' COMMENT '已读状态（0：未读    1：已读）',
  `read_num` int(11) DEFAULT '0' COMMENT '阅读数量',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_staff` varchar(255) DEFAULT '9999' COMMENT '添加人',
  `status` varchar(5) DEFAULT '1' COMMENT '状态',
  `memo` text COMMENT '备注',
  `period_score` varchar(25) DEFAULT NULL COMMENT '二次积分 与 integral_two表关联',
  `material_group` varchar(255) DEFAULT NULL COMMENT '对应群体',
  `material_data` varchar(255) DEFAULT NULL COMMENT '资料标签',
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8 COMMENT='学习资料';

-- ----------------------------
-- Table structure for sp_edu_material_category
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_material_category`;
CREATE TABLE `sp_edu_material_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `cat_name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `cat_pid` int(11) DEFAULT NULL COMMENT '所属父级id',
  `cat_type` int(11) DEFAULT NULL COMMENT '类型与文章的标识',
  `cat_content` text COMMENT '文章内容',
  `cat_title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `cat_keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `cat_desc` varchar(255) DEFAULT NULL COMMENT '文章描述',
  `share_party` text,
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='学习资料类别表';

-- ----------------------------
-- Table structure for sp_edu_material_communist
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_material_communist`;
CREATE TABLE `sp_edu_material_communist` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文字ID',
  `material_id` int(11) DEFAULT NULL COMMENT '资料id',
  `communist_no` varchar(255) DEFAULT NULL COMMENT '党员编号',
  `memo` text COMMENT '备注',
  `status` varchar(5) DEFAULT '1' COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `is_read` int(255) DEFAULT '0' COMMENT '是否已读（0：未学习   1：已学习）',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1747 DEFAULT CHARSET=utf8 COMMENT='学习人员表';

-- ----------------------------
-- Table structure for sp_edu_material_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_material_log`;
CREATE TABLE `sp_edu_material_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `communist_no` int(11) NOT NULL COMMENT '党员编号',
  `material_no` int(11) NOT NULL COMMENT '学习资料的编号',
  `log_type` int(2) DEFAULT '1' COMMENT '记录类型 1 学习资料记录  2 写学习笔记记录',
  `add_time` date NOT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='党员学习记录';

-- ----------------------------
-- Table structure for sp_edu_material_period
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_material_period`;
CREATE TABLE `sp_edu_material_period` (
  `period_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `material_id` int(11) DEFAULT NULL COMMENT '学习资料ID',
  `communist_no` int(11) DEFAULT NULL COMMENT '党员编号',
  `period_score` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '二次积分',
  `add_staff` varchar(35) CHARACTER SET utf8mb4 DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` int(15) DEFAULT '1',
  `period_year` varchar(25) DEFAULT NULL COMMENT '年份',
  `period_month` varchar(25) DEFAULT NULL COMMENT '月份',
  PRIMARY KEY (`period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='学习时段';

-- ----------------------------
-- Table structure for sp_edu_notes
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_notes`;
CREATE TABLE `sp_edu_notes` (
  `notes_id` int(11) NOT NULL AUTO_INCREMENT,
  `notes_thumb` varchar(255) DEFAULT NULL COMMENT '笔记缩略图',
  `notes_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `material_id` int(11) DEFAULT NULL COMMENT '所属文章的id',
  `notes_type` varchar(255) DEFAULT '' COMMENT '笔记类型（关联type表）',
  `notes_content` text COMMENT '内容',
  `add_staff` varchar(255) DEFAULT NULL,
  `notes_add` text COMMENT '地址定位',
  `status` varchar(5) DEFAULT '1' COMMENT '状态',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `memo` text COMMENT '备注',
  `alert_time` datetime DEFAULT NULL COMMENT '提醒时间',
  `is_alert` varchar(3) DEFAULT NULL COMMENT '是否提醒',
  `topic_type` varchar(255) DEFAULT NULL COMMENT '专题类型',
  `notes_group` tinyint(4) DEFAULT '1' COMMENT '对应群体',
  `notes_label` tinyint(4) DEFAULT '1' COMMENT '资料标签',
  PRIMARY KEY (`notes_id`)
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8 COMMENT='笔记表（包含日常笔记、学习笔记等所有笔记记录在此）';

-- ----------------------------
-- Table structure for sp_edu_questions
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_questions`;
CREATE TABLE `sp_edu_questions` (
  `questions_id` int(11) NOT NULL AUTO_INCREMENT,
  `questions_title` varchar(255) DEFAULT NULL COMMENT '考题标题',
  `questions_item` text COMMENT '题目选项',
  `questions_answer` varchar(255) DEFAULT NULL COMMENT '正确答案',
  `questions_score` varchar(255) DEFAULT NULL COMMENT '分值',
  `questions_type` int(11) DEFAULT NULL COMMENT '选题类型（1：单选  2：多选 3：判断）',
  `add_communist` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '1' COMMENT '状态 (0：删除   1：未使用  2：已考过)已经考过的试题不可更改与删除',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`questions_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='试题库表';

-- ----------------------------
-- Table structure for sp_edu_questions_import
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_questions_import`;
CREATE TABLE `sp_edu_questions_import` (
  `questions_id` int(11) NOT NULL AUTO_INCREMENT,
  `questions_title` varchar(255) DEFAULT NULL COMMENT '考题标题',
  `questions_item` text COMMENT '题目选项',
  `questions_answer` varchar(255) DEFAULT NULL COMMENT '正确答案',
  `questions_score` varchar(255) DEFAULT NULL COMMENT '分值',
  `questions_type` int(11) DEFAULT NULL COMMENT '选题类型（1：单选  2：多选 3：判断）',
  `add_communist` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '1' COMMENT '状态 (0：删除   1：未使用  2：已考过)已经考过的试题不可更改与删除',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否是错误数据。默认为0，不是。1表示是错误数据',
  PRIMARY KEY (`questions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试题库表';

-- ----------------------------
-- Table structure for sp_edu_topic
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_topic`;
CREATE TABLE `sp_edu_topic` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_title` varchar(255) DEFAULT NULL COMMENT '专题的名称',
  `topic_img` varchar(255) DEFAULT NULL COMMENT 'appbanner图',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '专题介绍',
  PRIMARY KEY (`topic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='学习专题表';

-- ----------------------------
-- Table structure for sp_edu_topic_details
-- ----------------------------
DROP TABLE IF EXISTS `sp_edu_topic_details`;
CREATE TABLE `sp_edu_topic_details` (
  `topic_id` int(11) DEFAULT NULL COMMENT '专题ID',
  `relation_type` int(11) DEFAULT NULL COMMENT '关联类型  区分文章、会议、考试',
  `relation_no` varchar(50) DEFAULT NULL COMMENT '关联文章、考试、或三会一课',
  `memo` text COMMENT '专题介绍',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学习专题明细表表';

-- ----------------------------
-- Table structure for sp_hr_dept
-- ----------------------------
DROP TABLE IF EXISTS `sp_hr_dept`;
CREATE TABLE `sp_hr_dept` (
  `dept_id` int(50) NOT NULL AUTO_INCREMENT,
  `dept_no` int(11) DEFAULT NULL COMMENT '部门编号',
  `dept_name` varchar(255) DEFAULT NULL COMMENT '部门名称',
  `dept_pno` int(11) DEFAULT NULL COMMENT '上级部门',
  `dept_type` varchar(255) DEFAULT NULL COMMENT '类型',
  `dept_manager` varchar(255) DEFAULT NULL COMMENT '部门主管',
  `dept_comprise` varchar(255) DEFAULT NULL COMMENT '组成（单位：综治中心）',
  `dept_area` varchar(255) DEFAULT NULL COMMENT '所在地（综治中心）',
  `dept_addr` varchar(400) DEFAULT NULL COMMENT '所在地详细地址',
  `add_user` varchar(255) DEFAULT NULL COMMENT '添加人',
  `update_user` varchar(255) DEFAULT NULL COMMENT '修改人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `dept_level` varchar(255) DEFAULT NULL COMMENT '部门层级（综治）',
  `dept_phone` varchar(50) DEFAULT NULL COMMENT '部门联系方式（综治中心）',
  `dept_record` varchar(255) DEFAULT NULL COMMENT '位置',
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='部门（组织结构）表';

-- ----------------------------
-- Table structure for sp_hr_party_change_flow
-- ----------------------------
DROP TABLE IF EXISTS `sp_hr_party_change_flow`;
CREATE TABLE `sp_hr_party_change_flow` (
  `flow_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `flow_name` varchar(100) NOT NULL COMMENT '流程名称',
  `flow_alert_msg` varchar(255) DEFAULT NULL COMMENT '流程提示语',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '	状态',
  `update_time` datetime DEFAULT NULL COMMENT '	修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`flow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='党组织换届流程表';

-- ----------------------------
-- Table structure for sp_hr_party_change_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_hr_party_change_log`;
CREATE TABLE `sp_hr_party_change_log` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `change_party_no` int(11) NOT NULL COMMENT '换届的组织编号',
  `change_party_name` varchar(100) DEFAULT NULL COMMENT '换届的组织名称',
  `change_code` int(11) NOT NULL COMMENT '换届步骤编号',
  `change_code_name` varchar(100) NOT NULL COMMENT '换届步骤名称',
  `change_file` varchar(255) DEFAULT NULL COMMENT '换届文件编号',
  `change_time` date NOT NULL COMMENT '换届时间',
  `change_file_upload_time` date NOT NULL COMMENT '上传时间',
  `status` int(11) DEFAULT '0' COMMENT '状态 0 未上传 1 已上传',
  `add_staff` varchar(100) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `type` int(11) DEFAULT '1' COMMENT '添加方式  1 默认 流程添加  2.通过换届历史添加',
  PRIMARY KEY (`change_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='党组织换届记录表';

-- ----------------------------
-- Table structure for sp_hr_post
-- ----------------------------
DROP TABLE IF EXISTS `sp_hr_post`;
CREATE TABLE `sp_hr_post` (
  `post_no` int(255) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `post_name` varchar(255) DEFAULT NULL COMMENT '岗位名称',
  `post_require` text COMMENT '任职要求',
  `post_recruit_num` int(11) DEFAULT NULL COMMENT '招聘人数',
  `post_explain` text COMMENT '任职说明',
  `post_recruitname` varchar(255) DEFAULT NULL COMMENT '发布岗位名称(招聘名称)',
  `post_recruit_start_time` datetime DEFAULT NULL COMMENT '开始招聘时间',
  `post_recruit_end_time` datetime DEFAULT NULL COMMENT '截止招聘时间',
  `add_user` varchar(255) DEFAULT NULL COMMENT '添加人',
  `update_user` varchar(255) DEFAULT NULL COMMENT '修改人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`post_no`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='岗位表';

-- ----------------------------
-- Table structure for sp_hr_staff
-- ----------------------------
DROP TABLE IF EXISTS `sp_hr_staff`;
CREATE TABLE `sp_hr_staff` (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `staff_no` varchar(50) NOT NULL COMMENT '工号',
  `recruit_no` varchar(50) DEFAULT NULL COMMENT '招聘时岗位',
  `trialcontract_no` varchar(255) DEFAULT NULL COMMENT '试用协议合同号',
  `contract_no` varchar(255) DEFAULT NULL COMMENT '正式合同编号',
  `staff_name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `staff_dept_no` int(255) NOT NULL COMMENT '部门  存dept_no',
  `staff_post_no` varchar(255) NOT NULL COMMENT '职位',
  `staff_avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `staff_sex` varchar(255) DEFAULT NULL COMMENT '性别 1男 0女',
  `staff_birthday` varchar(255) DEFAULT NULL COMMENT '生日',
  `staff_islunar` varchar(255) DEFAULT NULL COMMENT '农历',
  `staff_leapmonth` varchar(255) DEFAULT NULL COMMENT '闰月',
  `staff_diploma` varchar(255) DEFAULT NULL COMMENT '学历',
  `staff_school` varchar(255) DEFAULT NULL COMMENT '学校',
  `staff_specialty` varchar(255) DEFAULT NULL COMMENT '专业',
  `staff_nation` varchar(255) DEFAULT NULL COMMENT '民族',
  `staff_political_status` varchar(255) DEFAULT NULL COMMENT '政治面貌',
  `staff_idnumber` varchar(255) DEFAULT NULL COMMENT '身份证',
  `staff_tel` varchar(15) DEFAULT NULL COMMENT '电话',
  `staff_mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `staff_email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `staff_qq` varchar(255) DEFAULT NULL COMMENT 'QQ',
  `staff_msn` varchar(255) DEFAULT NULL COMMENT 'msn',
  `staff_homepage` varchar(255) DEFAULT NULL COMMENT '主页',
  `staff_blog` varchar(255) DEFAULT NULL COMMENT '博客',
  `staff_address` text COMMENT '地址',
  `staff_paddress` text COMMENT '籍贯',
  `staff_contact1` varchar(255) DEFAULT NULL COMMENT '联系人1',
  `staff_relationship1` varchar(255) DEFAULT NULL COMMENT '联系人1与本人关系',
  `staff_ctel1` varchar(255) DEFAULT NULL COMMENT '联系人1电话',
  `staff_cmobil1` varchar(255) DEFAULT NULL COMMENT '联系人1手机',
  `staff_caddress1` varchar(255) DEFAULT NULL COMMENT '联系人1地址',
  `staff_contact2` varchar(255) DEFAULT NULL COMMENT '联系人2',
  `staff_relationship2` varchar(255) DEFAULT NULL COMMENT '与本人关系',
  `staff_ctel2` varchar(255) DEFAULT NULL COMMENT '联系人2电话',
  `staff_cmobil2` varchar(255) DEFAULT NULL COMMENT '联系人2手机',
  `staff_caddress2` varchar(255) DEFAULT NULL COMMENT '联系人2地址',
  `staff_bank` varchar(255) DEFAULT NULL COMMENT '开户行名称',
  `staff_bankname` varchar(255) DEFAULT NULL COMMENT '开户名',
  `staff_level` varchar(255) DEFAULT NULL COMMENT '职称（级别）',
  `staff_duties` varchar(255) DEFAULT NULL COMMENT '职务',
  `staff_bankaccount_no` varchar(255) DEFAULT NULL COMMENT '账号',
  `staff_fee` varchar(255) DEFAULT '0' COMMENT 'gov-党费',
  `staff_into_date` varchar(255) DEFAULT NULL COMMENT '录入日期',
  `setting` text COMMENT '储存json',
  `add_user` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT '1' COMMENT '0 离职 1在职 2非员工',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `system_type` varchar(255) DEFAULT NULL COMMENT '用户使用机型  Android / iOS',
  `is_hide` varchar(5) DEFAULT '0' COMMENT '隐藏通讯录中的个人资料(0:显示 1:隐藏)',
  `rong_id` varchar(255) DEFAULT NULL COMMENT '融云ID',
  `staff_vlan` varchar(15) DEFAULT NULL COMMENT '虚拟网号码',
  `other` varchar(255) DEFAULT NULL COMMENT '其他联系方式',
  `staff_entrytime` varchar(255) DEFAULT NULL COMMENT '正式入职时间',
  `staff_positivetime` varchar(255) DEFAULT NULL COMMENT '转正日期',
  `staff_qualification` text COMMENT '个人资质',
  `staff_attachid` varchar(255) DEFAULT NULL COMMENT '资质附件',
  `staff_graduationdate` varchar(255) DEFAULT NULL COMMENT '毕业时间',
  `is_post` varchar(5) DEFAULT NULL COMMENT '是否转岗交接',
  `is_promotion` varchar(5) DEFAULT NULL COMMENT '是否晋升交接',
  `is_volunteer` varchar(255) DEFAULT NULL,
  `is_identity` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1087 DEFAULT CHARSET=utf8 COMMENT='员工表';

-- ----------------------------
-- Table structure for sp_life_bbs_post
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_bbs_post`;
CREATE TABLE `sp_life_bbs_post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '帖子id',
  `post_theme` varchar(255) DEFAULT NULL COMMENT '帖子主题',
  `party_no` int(11) DEFAULT NULL COMMENT '党支部编号（关联party表）',
  `communist_no` int(11) DEFAULT NULL COMMENT '发起人（党员）编号（关联communist表）',
  `post_content` text COMMENT '帖子内容',
  `post_img` varchar(255) DEFAULT NULL COMMENT '帖子图片',
  `post_auth_staff` varchar(255) DEFAULT NULL COMMENT '审核人编号',
  `post_auth_opinion` text COMMENT '审核意见',
  `add_staff` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` int(255) DEFAULT '0' COMMENT '0:未审核  1:已审核  2：驳回',
  `cat_id` int(11) DEFAULT NULL COMMENT '类型ID',
  `visitor_volume` varchar(255) DEFAULT '0' COMMENT '访问量',
  `add_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`post_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='论坛帖子表';

-- ----------------------------
-- Table structure for sp_life_bbs_post_category
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_bbs_post_category`;
CREATE TABLE `sp_life_bbs_post_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `cat_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `cat_pid` int(11) DEFAULT NULL COMMENT '上级ID',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '关联 communist 表',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `status` varchar(255) DEFAULT '1' COMMENT '状态',
  `memo` text COMMENT '分类简介',
  PRIMARY KEY (`cat_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='村务论坛类型表';

-- ----------------------------
-- Table structure for sp_life_bbs_post_comment
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_bbs_post_comment`;
CREATE TABLE `sp_life_bbs_post_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '留言与回复公用（id）',
  `comment_content` text COMMENT '评论内容',
  `communist_no` int(255) DEFAULT NULL COMMENT '评论人（党员）编号（关联communist表）',
  `comment_time` datetime DEFAULT NULL COMMENT '帖子的评论时间',
  `comment_type` int(11) DEFAULT '0' COMMENT '0 回复 1评论',
  `status` varchar(255) DEFAULT '0' COMMENT '0 待审核 1通过 2驳回',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL COMMENT '帖子id',
  `add_staff` varchar(255) DEFAULT NULL,
  `leave_id` int(11) DEFAULT NULL COMMENT '留言id',
  `comment_pid` int(11) DEFAULT '0' COMMENT '回复id  0是默认的评论，其他是回复',
  PRIMARY KEY (`comment_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='帖子评论表';

-- ----------------------------
-- Table structure for sp_life_bbs_post_fav
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_bbs_post_fav`;
CREATE TABLE `sp_life_bbs_post_fav` (
  `fav_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL COMMENT '论坛帖子表id',
  `communist_no` int(11) DEFAULT NULL COMMENT '点赞人（收藏人） 关联党员表',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `fav_type` varchar(255) DEFAULT NULL COMMENT '1.点赞  2收藏',
  PRIMARY KEY (`fav_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='论坛帖子点赞表';

-- ----------------------------
-- Table structure for sp_life_condition
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_condition`;
CREATE TABLE `sp_life_condition` (
  `condition_id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_title` varchar(255) DEFAULT NULL COMMENT '民情标题',
  `condition_thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `condition_content` text COMMENT '民情详情',
  `condition_content1` text,
  `condition_personnel` varchar(255) DEFAULT NULL COMMENT '民情提交人',
  `condition_personnel_mobile` varchar(255) DEFAULT NULL COMMENT '民情提交人电话',
  `condition_area` varchar(255) DEFAULT NULL COMMENT '提交人所在地址',
  `type_no` int(11) DEFAULT NULL COMMENT '民情类型（关联bd_type表）',
  `status` int(11) DEFAULT '0' COMMENT '状态 （0:待处理 10:已指派 20:处理中  30:完成）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `add_staff` char(11) DEFAULT NULL,
  PRIMARY KEY (`condition_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='民意调查表（民生O2O）';

-- ----------------------------
-- Table structure for sp_life_condition_category
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_condition_category`;
CREATE TABLE `sp_life_condition_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `cat_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `cat_pid` int(11) DEFAULT NULL COMMENT '上级ID',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '关联 communist 表',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `status` varchar(255) DEFAULT '1' COMMENT '状态',
  `memo` text COMMENT '分类简介',
  PRIMARY KEY (`cat_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='搜集民情类型表';

-- ----------------------------
-- Table structure for sp_life_condition_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_condition_log`;
CREATE TABLE `sp_life_condition_log` (
  `condition_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_id` int(11) DEFAULT NULL COMMENT '民情id',
  `condition_log_content` text COMMENT '日志内容',
  `status` int(11) DEFAULT '0' COMMENT '处理状态 （0:未处理  10:待接受  20:处理中  30：处理完成）  为了匹配主表状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`condition_log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='民意调查记录表（民生O2O日志）';

-- ----------------------------
-- Table structure for sp_life_condition_personal
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_condition_personal`;
CREATE TABLE `sp_life_condition_personal` (
  `condition_personal_id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_id` int(11) DEFAULT NULL COMMENT '民情id',
  `condition_delegate_no` text COMMENT '指派人编号（关联党员表）',
  `condition_delegate_time` datetime DEFAULT NULL COMMENT '指派时间',
  `condition_accept_no` text COMMENT '接受人编号（关联党员表）',
  `condition_content` text COMMENT '任务指派内容',
  `status` int(11) DEFAULT '0' COMMENT '处理状态 （0:未处理  10:待接受  20:处理中  30：处理完成）  为了匹配主表状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`condition_personal_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COMMENT='民意调查任务指派表（民生O2O日志）';

-- ----------------------------
-- Table structure for sp_life_guestbook
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_guestbook`;
CREATE TABLE `sp_life_guestbook` (
  `guestbook_id` int(11) NOT NULL AUTO_INCREMENT,
  `communist_name` varchar(255) DEFAULT NULL COMMENT '提交人',
  `communist_phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号',
  `guestbook_content` text COMMENT '审核内容(评语)',
  `guestbook_pid` varchar(11) DEFAULT '0' COMMENT '回复的id',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人  no',
  `status` varchar(11) DEFAULT '1' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `staff_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`guestbook_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COMMENT='留言建议表';

-- ----------------------------
-- Table structure for sp_life_survey
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_survey`;
CREATE TABLE `sp_life_survey` (
  `survey_id` int(11) NOT NULL AUTO_INCREMENT,
  `party_no` varchar(255) DEFAULT NULL COMMENT '发起问卷调查的部门编号（关联party表）',
  `survey_title` varchar(255) DEFAULT NULL COMMENT '问卷标题',
  `survey_join_num` varchar(255) DEFAULT NULL COMMENT '参与问卷调查人数',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '状态 （0：未使用   1：使用中）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`survey_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='问卷表';

-- ----------------------------
-- Table structure for sp_life_survey_answer
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_survey_answer`;
CREATE TABLE `sp_life_survey_answer` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `communist_no` int(11) DEFAULT NULL COMMENT '人员编号',
  `survey_id` int(11) DEFAULT NULL COMMENT '问卷id',
  `questions_id` int(11) DEFAULT NULL COMMENT '问题id',
  `answer_item` varchar(255) DEFAULT NULL COMMENT '我的答案（匹配survey_questions表questions_item）',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`answer_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='问卷调查结果表';

-- ----------------------------
-- Table structure for sp_life_survey_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_survey_log`;
CREATE TABLE `sp_life_survey_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) DEFAULT NULL COMMENT '问卷ID',
  `communist_no` int(11) DEFAULT NULL COMMENT '发起问卷调查的部门编号（关联party表）',
  `log_date` date DEFAULT NULL COMMENT '参加问卷日期',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '状态 （0：未使用   1：使用中）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='问卷表';

-- ----------------------------
-- Table structure for sp_life_survey_questions
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_survey_questions`;
CREATE TABLE `sp_life_survey_questions` (
  `questions_id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) DEFAULT NULL COMMENT '问卷id（关联cms_survey表）',
  `questions_title` varchar(255) DEFAULT NULL COMMENT '问题标题',
  `questions_item` text COMMENT '问题选项',
  `questions_type` int(11) DEFAULT NULL COMMENT '选题类型（1：单选  2：多选）',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '1' COMMENT '状态 (0：删除   1：未答  2：已答)',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`questions_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='问卷调查问题表\r\n';

-- ----------------------------
-- Table structure for sp_life_volunteer
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_volunteer`;
CREATE TABLE `sp_life_volunteer` (
  `volunteer_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '志愿者id',
  `volunteer_no` int(11) DEFAULT NULL COMMENT '志愿者编号',
  `communist_no` int(11) DEFAULT NULL COMMENT '党员编号（关联communist表）',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号（关联party表）',
  `volunteer_integral` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '志愿者活动获得的积分',
  `volunteer_content` text CHARACTER SET utf8 COMMENT '志愿者申请原因',
  `volunteer_audit_opinion` text CHARACTER SET utf8 COMMENT '审核意见',
  `add_staff` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` varchar(5) CHARACTER SET utf8 DEFAULT NULL COMMENT '状态 （1：待审核  2：已审核  3:已驳回）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text CHARACTER SET utf8 COMMENT '备注',
  `volunteer_audit_man` varchar(255) DEFAULT NULL COMMENT '审核人',
  `volunteer_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  PRIMARY KEY (`volunteer_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COMMENT='志愿者表';

-- ----------------------------
-- Table structure for sp_life_volunteer_activity
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_volunteer_activity`;
CREATE TABLE `sp_life_volunteer_activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '志愿者活动id',
  `activity_thumb` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动缩略图',
  `activity_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '志愿者活动标题',
  `activity_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动地点',
  `activity_host` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动主持人',
  `activity_integral` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动积分',
  `activity_organizer` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动组织者',
  `activity_status` varchar(0) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动状态',
  `activity_description` text CHARACTER SET utf8 COMMENT '活动简介',
  `activity_starttime` date DEFAULT NULL COMMENT '活动开始时间',
  `activity_endtime` date DEFAULT NULL COMMENT '活动结束时间',
  `party_no` text COMMENT '逗号分隔的党支部编号',
  `add_staff` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` varchar(5) CHARACTER SET utf8 DEFAULT '11' COMMENT '状态 （11：未审核  12：已审核）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text CHARACTER SET utf8 COMMENT '备注',
  PRIMARY KEY (`activity_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COMMENT='志愿者活动表';

-- ----------------------------
-- Table structure for sp_life_volunteer_activity_apply
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_volunteer_activity_apply`;
CREATE TABLE `sp_life_volunteer_activity_apply` (
  `apply_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '志愿者活动id',
  `communist_no` int(11) DEFAULT NULL COMMENT '志愿者对应党员编号（关联communist表）',
  `activity_id` int(11) DEFAULT NULL COMMENT '活动id',
  `apply_desc` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '申请说明',
  `add_staff` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` varchar(5) CHARACTER SET utf8 DEFAULT '1' COMMENT '状态 （1：申请  2：通过  3：驳回）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text CHARACTER SET utf8 COMMENT '备注',
  `party_no` int(11) DEFAULT NULL COMMENT '党支部编号',
  `apply_audit_man` varchar(255) DEFAULT NULL COMMENT '审核人',
  `apply_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `apply_description` text CHARACTER SET utf8 COMMENT '审核意见',
  PRIMARY KEY (`apply_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 COMMENT='志愿者活动申请表';

-- ----------------------------
-- Table structure for sp_life_vote
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_vote`;
CREATE TABLE `sp_life_vote` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `vote_theme` varchar(255) DEFAULT NULL COMMENT '投票主题',
  `vote_content` text COMMENT '投票内容',
  `vote_starttime` varchar(255) DEFAULT NULL,
  `vote_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `vote_auditor` date DEFAULT NULL COMMENT '审核人',
  `vote_endtime` date DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL COMMENT '投票状态      结束  开始  进行中  ',
  `memo` text,
  `add_staff` varchar(255) DEFAULT NULL,
  `is_multiple` varchar(5) DEFAULT NULL COMMENT '是否多选',
  PRIMARY KEY (`vote_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COMMENT='投票表决表';

-- ----------------------------
-- Table structure for sp_life_vote_option
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_vote_option`;
CREATE TABLE `sp_life_vote_option` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(255) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `add_staff` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`option_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=392 DEFAULT CHARSET=utf8 COMMENT='投票选项表';

-- ----------------------------
-- Table structure for sp_life_vote_result
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_vote_result`;
CREATE TABLE `sp_life_vote_result` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) DEFAULT NULL COMMENT '投票主题ID',
  `option_id` int(11) DEFAULT NULL COMMENT '选项id',
  `communist_no` int(11) DEFAULT NULL COMMENT '得分',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`result_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3486 DEFAULT CHARSET=utf8 COMMENT='投票结果查看表';

-- ----------------------------
-- Table structure for sp_life_vote_subject
-- ----------------------------
DROP TABLE IF EXISTS `sp_life_vote_subject`;
CREATE TABLE `sp_life_vote_subject` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `vote_id` int(11) DEFAULT NULL COMMENT '审核人',
  `subject_content` text COMMENT '投票内容',
  `is_multiple` varchar(5) DEFAULT NULL COMMENT '是否多选',
  `subject_option` varchar(255) DEFAULT NULL COMMENT '选项',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `subject_starttime` datetime DEFAULT NULL COMMENT '开始时间',
  `subject_endtime` datetime DEFAULT NULL COMMENT '结束时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `status` varchar(255) DEFAULT NULL COMMENT '投票状态      结束  开始  进行中  ',
  `memo` text,
  PRIMARY KEY (`subject_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COMMENT='投票主题表';

-- ----------------------------
-- Table structure for sp_oa_approval
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_approval`;
CREATE TABLE `sp_oa_approval` (
  `approval_id` int(11) NOT NULL AUTO_INCREMENT,
  `approval_no` int(11) DEFAULT NULL COMMENT '审批编号',
  `approval_name` varchar(255) DEFAULT NULL COMMENT '审批名称',
  `approval_content` varchar(255) DEFAULT NULL COMMENT '审批介绍  或内容',
  `approval_attach` varchar(255) DEFAULT NULL COMMENT '附件ID  关联upload表',
  `approval_apply_man` varchar(255) DEFAULT NULL COMMENT '申请人或发起人',
  `approval_time` datetime DEFAULT NULL COMMENT '申请时间',
  `approval_template` varchar(255) DEFAULT NULL COMMENT '模板 关联模板表',
  `node_staff` varchar(255) DEFAULT NULL COMMENT '当前审批所处的节点具有权限的所有审核人的编号',
  `approval_table_name` varchar(255) DEFAULT NULL COMMENT '表名  如oa_missive',
  `approval_table_field` varchar(255) DEFAULT NULL COMMENT '关联字段名  如missive_no',
  `approval_table_field_value` varchar(255) DEFAULT NULL COMMENT '关联字段的值',
  `node_id` int(11) DEFAULT NULL COMMENT '当前审批所处的节点id  关联approval_node  表node_id ',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人 communist_no',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `status` varchar(255) DEFAULT NULL COMMENT '状态 （待审、审核中、已完成、已驳回）',
  `memo` varchar(255) DEFAULT NULL COMMENT '简介',
  `approval_callfunction` varchar(255) DEFAULT NULL COMMENT '对调函数格式为"方法名称（''参数''）;"',
  `approval_rewrite_field` varchar(255) DEFAULT NULL COMMENT '回写字段',
  PRIMARY KEY (`approval_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8 COMMENT='审批表';

-- ----------------------------
-- Table structure for sp_oa_approval_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_approval_log`;
CREATE TABLE `sp_oa_approval_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `approval_no` int(11) DEFAULT NULL COMMENT '审批单编号',
  `node_name` varchar(255) DEFAULT NULL COMMENT '节点名称',
  `log_staff` varchar(255) DEFAULT '0' COMMENT '操作人关联communist_no',
  `log_type` varchar(255) DEFAULT NULL COMMENT '流转类型  [同意][退回]等',
  `log_content` text COMMENT '流转意见内容',
  `log_time` datetime DEFAULT NULL COMMENT '流转时间',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人 communist_no',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `status` int(11) DEFAULT '0' COMMENT '1 已审核   0未审核',
  `memo` varchar(255) DEFAULT NULL COMMENT '简介',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8 COMMENT='审批流转（记录/日志）表';

-- ----------------------------
-- Table structure for sp_oa_approval_node
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_approval_node`;
CREATE TABLE `sp_oa_approval_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `approval_no` int(11) DEFAULT NULL COMMENT '审批单编号',
  `node_no` int(11) DEFAULT NULL,
  `node_name` varchar(255) DEFAULT NULL COMMENT '节点名称',
  `node_staff` varchar(255) DEFAULT NULL COMMENT '拥有当前节点审核权限的人员编号,逗号分隔',
  `node_staff_real` varchar(255) DEFAULT NULL COMMENT '当前节点的实际审核人',
  `node_post` varchar(255) DEFAULT NULL COMMENT '节点审批人所属的岗位(由岗位选择审批人）',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人 communist_no',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `status` int(11) DEFAULT '0' COMMENT '21已审核  11未审核',
  `memo` varchar(255) DEFAULT NULL COMMENT '简介',
  PRIMARY KEY (`node_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=433 DEFAULT CHARSET=utf8 COMMENT='审批流转（记录/日志）表';

-- ----------------------------
-- Table structure for sp_oa_approval_template
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_approval_template`;
CREATE TABLE `sp_oa_approval_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_no` int(11) DEFAULT NULL COMMENT '模板编号',
  `template_name` varchar(255) DEFAULT NULL COMMENT '模板名称',
  `template_content` text COMMENT '模板说明/内容',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人 communist_no',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `status` varchar(255) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL COMMENT '简介',
  PRIMARY KEY (`template_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='模板表';

-- ----------------------------
-- Table structure for sp_oa_approval_template_node
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_approval_template_node`;
CREATE TABLE `sp_oa_approval_template_node` (
  `node_no` int(11) NOT NULL,
  `template_no` int(11) DEFAULT NULL COMMENT '模板编号 外键关联',
  `node_order` int(11) DEFAULT '0' COMMENT '节点排序，即审批顺序 数字越大越 排后',
  `node_name` varchar(255) DEFAULT NULL COMMENT '节点名称',
  `node_return_no` int(11) DEFAULT NULL COMMENT '退回节点',
  `node_staff` varchar(255) DEFAULT '' COMMENT '节点的审批人关联communist_no',
  `node_post` varchar(255) DEFAULT NULL COMMENT '节点审批人所属的岗位(由岗位选择审批人）',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人 communist_no',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `status` varchar(255) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL COMMENT '简介',
  PRIMARY KEY (`node_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='审批模板节点';

-- ----------------------------
-- Table structure for sp_oa_att_legwork
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_att_legwork`;
CREATE TABLE `sp_oa_att_legwork` (
  `legwork_no` varchar(255) NOT NULL COMMENT '外勤签到编号',
  `legwork_datetime` datetime DEFAULT NULL COMMENT '外勤签到日期（年月日）',
  `legwork_area` varchar(255) DEFAULT NULL COMMENT '签到地址',
  `legwork_content` text COMMENT '总结',
  `legwork_attach` varchar(255) DEFAULT NULL COMMENT '附件id 关联upload表',
  `legwork_type` varchar(255) DEFAULT NULL COMMENT '外勤类型（1.第一数据签到2.双联双创签到3.扶贫）',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '默认为：1（使用状态）',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `memo` text,
  `cust_no` varchar(225) DEFAULT NULL COMMENT '拜访客户编号（关联crm_cust表）',
  PRIMARY KEY (`legwork_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考勤外勤签到表';

-- ----------------------------
-- Table structure for sp_oa_att_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_att_log`;
CREATE TABLE `sp_oa_att_log` (
  `att_id` int(11) NOT NULL AUTO_INCREMENT,
  `att_no` int(11) NOT NULL COMMENT '考勤编号',
  `att_date` date DEFAULT NULL COMMENT '打卡日期',
  `att_time` time DEFAULT NULL COMMENT '打卡时间',
  `check_time` datetime DEFAULT NULL,
  `att_machine` varchar(255) DEFAULT NULL COMMENT '机器号',
  `att_manner` varchar(5) DEFAULT NULL COMMENT '对比方式  指纹/密码/个人申请补签/后台补签(1为指纹，12为手机)',
  `att_address` char(20) DEFAULT NULL COMMENT '签到地址',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '默认为：1（使用状态）',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `memo` text,
  PRIMARY KEY (`att_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7946 DEFAULT CHARSET=utf8 COMMENT='考勤流水表';

-- ----------------------------
-- Table structure for sp_oa_att_machine
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_att_machine`;
CREATE TABLE `sp_oa_att_machine` (
  `machine_id` int(11) NOT NULL AUTO_INCREMENT,
  `machine_no` varchar(50) DEFAULT NULL COMMENT '考勤机编号',
  `machine_name` varchar(50) DEFAULT NULL COMMENT '考勤机名称',
  `party_no` varchar(11) DEFAULT NULL COMMENT '部门名称',
  `machine_addr` varchar(50) DEFAULT NULL COMMENT '考勤机地点',
  `add_staff` varchar(50) DEFAULT NULL COMMENT '添加人',
  `status` varchar(50) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `command_status` varchar(255) DEFAULT NULL COMMENT '考勤机命令执行状态（0:无指令;1:执行考勤机上传指令;2:执行下载到考勤机指令）',
  `machine_type` varchar(255) DEFAULT NULL COMMENT '考勤机签到类型（1.会议签到2.上班签到）',
  `att_machine_num` int(11) DEFAULT '0' COMMENT '考勤机参数',
  PRIMARY KEY (`machine_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='考勤机';

-- ----------------------------
-- Table structure for sp_oa_att_setting_extratime
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_att_setting_extratime`;
CREATE TABLE `sp_oa_att_setting_extratime` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `time_no` varchar(255) NOT NULL COMMENT '额外工作时间编号',
  `time_title` varchar(255) DEFAULT NULL COMMENT '节日名称',
  `time_date` date DEFAULT NULL COMMENT '额外工作时间（年月日）',
  `time_startdate` date DEFAULT NULL COMMENT '节日开始时间',
  `time_enddate` date DEFAULT NULL COMMENT '节日结束时间',
  `time_type` varchar(5) DEFAULT NULL COMMENT '类型 1：额外工作 2：节假日',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '默认为：1（使用状态）',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `memo` text,
  PRIMARY KEY (`time_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='考勤额外工作时间表';

-- ----------------------------
-- Table structure for sp_oa_att_setting_time
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_att_setting_time`;
CREATE TABLE `sp_oa_att_setting_time` (
  `time_no` varchar(255) NOT NULL COMMENT '时间设置编号',
  `time_type` varchar(5) DEFAULT NULL COMMENT '时间设置类型 1：早晚上下班，中午不考勤 2：分上午/下午进行考勤 ',
  `time_morning_time` time DEFAULT NULL COMMENT '上午上班时间',
  `time_nooning_endtime` time DEFAULT NULL COMMENT '上午下班时间',
  `time_nooning_starttime` time DEFAULT NULL COMMENT '下午上班时间',
  `time_night_time` time DEFAULT NULL COMMENT '下午下班时间',
  `time_rest` varchar(5) DEFAULT NULL COMMENT '单双休设置 1：单休 2：双休',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '默认为：1（使用状态）',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `memo` text,
  PRIMARY KEY (`time_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考勤时间设置表';

-- ----------------------------
-- Table structure for sp_oa_meeting
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_meeting`;
CREATE TABLE `sp_oa_meeting` (
  `meeting_no` int(11) NOT NULL COMMENT '会议编号',
  `meeting_name` varchar(50) DEFAULT NULL COMMENT '会议名称',
  `meeting_camera` varchar(225) DEFAULT NULL,
  `meeting_integral` varchar(225) DEFAULT NULL COMMENT '会议积分',
  `meeting_type` int(11) DEFAULT NULL COMMENT '会议类型(关联type表)',
  `meeting_start_time` datetime DEFAULT NULL COMMENT '预计会议开始时间',
  `meeting_end_time` datetime DEFAULT NULL COMMENT '预计会议结束时间',
  `meeting_real_start_time` datetime DEFAULT NULL COMMENT '实际会议开始时间',
  `meeting_real_end_time` datetime DEFAULT NULL COMMENT '实际会议结束时间',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号',
  `meeting_addr` varchar(255) DEFAULT NULL COMMENT '会议地址',
  `meeting_host` text COMMENT '会议主持人',
  `machine_no` varchar(50) DEFAULT NULL COMMENT '考勤机编号',
  `add_staff` varchar(50) DEFAULT NULL COMMENT '添加人',
  `status` varchar(50) DEFAULT NULL COMMENT '状态(11为未召开，21为开会中，23为已召开   关联bd_status表meeting_status分组)',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '简介',
  `meeting_video` varchar(255) DEFAULT NULL COMMENT '视频',
  `meetting_thumb` varchar(255) DEFAULT NULL,
  `meetting_mien` varchar(255) DEFAULT NULL COMMENT 'huiyifengcai',
  `meetting_attach` varchar(255) DEFAULT NULL COMMENT '会议文件',
  PRIMARY KEY (`meeting_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='三会一课会议表';

-- ----------------------------
-- Table structure for sp_oa_meeting_communist
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_meeting_communist`;
CREATE TABLE `sp_oa_meeting_communist` (
  `communist_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `meeting_no` int(11) DEFAULT NULL COMMENT '会议编号',
  `sign_time` datetime DEFAULT NULL COMMENT '签到时间',
  `sign_addr` varchar(200) DEFAULT NULL COMMENT '签到地点',
  `party_no` int(11) DEFAULT NULL COMMENT '参会人数所属部门编号',
  `communist_no` int(11) DEFAULT NULL COMMENT '参会人员编号',
  `meeting_attach` varchar(100) DEFAULT NULL COMMENT '附件编号',
  `sign_type` varchar(50) DEFAULT NULL COMMENT '签到方式(1:指纹机签到，2:手机签到)',
  `meeting_notes` text COMMENT '学习心得',
  `add_staff` varchar(50) DEFAULT NULL COMMENT '添加人',
  `status` varchar(50) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`communist_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1874 DEFAULT CHARSET=utf8 COMMENT='会员相关党员表';

-- ----------------------------
-- Table structure for sp_oa_meeting_minutes
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_meeting_minutes`;
CREATE TABLE `sp_oa_meeting_minutes` (
  `meeting_minutes_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `meeting_minutes_title` varchar(255) DEFAULT NULL COMMENT '名称',
  `meeting_minutes_content` text COMMENT '内容',
  `meeting_minutes_feedback` text COMMENT '意见建议',
  `meeting_minutes_improvement` text COMMENT '整改方案',
  `meeting_minutes_thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号',
  `add_staff` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '作者关联 communist表',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL,
  `memo` text COMMENT '备注',
  `meeting_minutes_type` varchar(255) DEFAULT '1' COMMENT '1 会议记录 2 民主评议记录',
  `meeting_id` int(11) DEFAULT NULL COMMENT '关联会议ID ',
  PRIMARY KEY (`meeting_minutes_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='会议/民主评议会议记录表';

-- ----------------------------
-- Table structure for sp_oa_meeting_room
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_meeting_room`;
CREATE TABLE `sp_oa_meeting_room` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `room_no` varchar(50) NOT NULL COMMENT '房间编号',
  `appId` varchar(50) NOT NULL COMMENT 'appId',
  `room_name` varchar(200) NOT NULL COMMENT '房间名称',
  `room_type` varchar(50) NOT NULL COMMENT '房间标示 用于区分属于哪个项目 （存储项目地址）',
  `room_creater` int(11) NOT NULL COMMENT '房间创建者',
  `streamTitle` varchar(100) NOT NULL COMMENT '直播流编号',
  `add_communist` int(11) DEFAULT NULL COMMENT '添加人',
  `status` int(11) DEFAULT '1' COMMENT '状态 1 正常',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '变更时间',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `meeting_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='视频 会议房间表（七牛云）';

-- ----------------------------
-- Table structure for sp_oa_meeting_video
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_meeting_video`;
CREATE TABLE `sp_oa_meeting_video` (
  `video_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `video_room` varchar(255) DEFAULT NULL COMMENT '房间号',
  `staff_name` varchar(255) DEFAULT NULL COMMENT '人员名称',
  `type_no` varchar(255) DEFAULT NULL COMMENT '区分服务大厅和三会一课 视频会议',
  `party_name` varchar(255) DEFAULT NULL COMMENT '组织名称',
  `is_hide` varchar(255) DEFAULT '0' COMMENT '是否举手  0/1',
  `status` varchar(5) DEFAULT '1',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `communist_no` int(11) DEFAULT NULL COMMENT '人员编号 ',
  PRIMARY KEY (`video_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='会议视频房间表';

-- ----------------------------
-- Table structure for sp_oa_missive
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_missive`;
CREATE TABLE `sp_oa_missive` (
  `missive_id` int(11) NOT NULL AUTO_INCREMENT,
  `missive_no` varchar(255) DEFAULT NULL COMMENT '编号',
  `missive_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `missive_type` varchar(255) DEFAULT NULL COMMENT '公文类型',
  `missive_content` text COMMENT '内容',
  `missive_communist` varchar(255) DEFAULT NULL COMMENT '拟稿人',
  `missive_corporation` varchar(255) DEFAULT NULL COMMENT '所在单位（公司）',
  `missive_date` datetime DEFAULT NULL COMMENT '发送时间',
  `missive_receiver` text COMMENT '主送单位（接收人） 多选',
  `missive_cc` text COMMENT '抄送单位（接收人） 多选',
  `is_app_pc` int(11) DEFAULT '1' COMMENT '判断pc还是app   1pc  2app',
  `missive_attach` varchar(255) DEFAULT NULL COMMENT '附件，多个upload_id',
  `approval_no` varchar(50) DEFAULT NULL COMMENT '对应审批单编号    2017-11-20新增',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(5) DEFAULT '0' COMMENT ' 关联status表  \r\n0:未成功1：发送成功 3已读 4未读',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `memo` text COMMENT '备注',
  PRIMARY KEY (`missive_id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8 COMMENT='公文表';

-- ----------------------------
-- Table structure for sp_oa_missive_inbox
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_missive_inbox`;
CREATE TABLE `sp_oa_missive_inbox` (
  `inbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `missive_receiver` varchar(255) DEFAULT NULL COMMENT '收件人',
  `missive_id` int(11) DEFAULT NULL COMMENT '邮件内容',
  `is_read` varchar(5) DEFAULT NULL COMMENT '是否已读',
  `is_del` varchar(5) DEFAULT NULL COMMENT '是否已删除',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`inbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='邮件收件箱';

-- ----------------------------
-- Table structure for sp_oa_missive_outbox
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_missive_outbox`;
CREATE TABLE `sp_oa_missive_outbox` (
  `missive_id` int(11) NOT NULL AUTO_INCREMENT,
  `missive_sender` varchar(255) DEFAULT NULL COMMENT '发件人',
  `missive_receivers` varchar(255) DEFAULT NULL COMMENT '接收人',
  `missive_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `missive_content` text COMMENT '内容',
  `missive_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `is_del` varchar(5) DEFAULT NULL COMMENT '是否已删除',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `people_type` int(255) DEFAULT NULL COMMENT '员工类型（0：员工   1：客户   2：供应商）',
  PRIMARY KEY (`missive_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='邮件发件箱';

-- ----------------------------
-- Table structure for sp_oa_missive_sign
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_missive_sign`;
CREATE TABLE `sp_oa_missive_sign` (
  `sign_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公文签字表',
  `missive_no` varchar(25) NOT NULL COMMENT '公文编号',
  `sign_communist` int(11) NOT NULL COMMENT '公文签字人编号',
  `sign_communist_name` varchar(255) DEFAULT NULL COMMENT '公文签字人姓名',
  `sign_content` text COMMENT '签字内容',
  `sign_time` datetime DEFAULT NULL COMMENT '签字时间',
  `add_staff` varchar(255) DEFAULT NULL COMMENT 't添加人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '查看时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`sign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8 COMMENT='公告查看记录表';

-- ----------------------------
-- Table structure for sp_oa_notes
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_notes`;
CREATE TABLE `sp_oa_notes` (
  `notes_id` int(11) NOT NULL AUTO_INCREMENT,
  `notes_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `notes_content` varchar(255) DEFAULT NULL COMMENT '内容',
  `notes_type` varchar(255) DEFAULT NULL COMMENT '备注类别',
  `is_alert` varchar(255) DEFAULT NULL COMMENT '是否提醒',
  `alert_time` datetime DEFAULT NULL COMMENT '提醒时间',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `memo` text COMMENT '备注',
  PRIMARY KEY (`notes_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='备忘录表';

-- ----------------------------
-- Table structure for sp_oa_notice
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_notice`;
CREATE TABLE `sp_oa_notice` (
  `notice_id` int(11) NOT NULL AUTO_INCREMENT,
  `notice_title` varchar(255) DEFAULT NULL COMMENT '公告标题',
  `notice_content` text COMMENT '内容',
  `notice_communist` text COMMENT '人员编号',
  `is_app_pc` int(11) DEFAULT '1' COMMENT '判断是app或者pc   1pc    2app',
  `notice_attach` varchar(50) DEFAULT NULL COMMENT '附件id',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '1' COMMENT '状态 0 删除 1 显示数据',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`notice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8 COMMENT='公告表';

-- ----------------------------
-- Table structure for sp_oa_notice_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_notice_log`;
CREATE TABLE `sp_oa_notice_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `notice_id` int(11) DEFAULT NULL,
  `communist_no` int(11) DEFAULT NULL COMMENT '人员编号',
  `is_read` int(5) DEFAULT '1' COMMENT '是否查看0未查看1已查看',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '查看时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1993 DEFAULT CHARSET=utf8 COMMENT='公告查看记录表';

-- ----------------------------
-- Table structure for sp_oa_willdo
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_willdo`;
CREATE TABLE `sp_oa_willdo` (
  `willdo_id` int(11) NOT NULL AUTO_INCREMENT,
  `communist_no` int(11) DEFAULT NULL COMMENT '员工工号',
  `willdo_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `willdo_content` longtext COMMENT '内容',
  `willdo_cycle` varchar(255) DEFAULT NULL COMMENT '执行周期',
  `willdo_operdate` varchar(50) DEFAULT NULL COMMENT '执行日期',
  `willdo_start_time` varchar(255) DEFAULT NULL COMMENT '开始时间',
  `willdo_end_time` varchar(255) DEFAULT NULL COMMENT '结束时间',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `is_alert` int(11) DEFAULT NULL COMMENT '是否提醒',
  `willdo_time_type` datetime DEFAULT NULL COMMENT '提醒时间',
  PRIMARY KEY (`willdo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8 COMMENT='必做任务表';

-- ----------------------------
-- Table structure for sp_oa_willdo_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_willdo_log`;
CREATE TABLE `sp_oa_willdo_log` (
  `willdolog_id` int(11) NOT NULL AUTO_INCREMENT,
  `willdo_id` int(11) DEFAULT NULL COMMENT '必做任务id',
  `communist_no` int(11) DEFAULT NULL COMMENT '员工工号',
  `willdolog_summary` varchar(255) DEFAULT NULL COMMENT '总结',
  `willdolog_operdate` varchar(255) DEFAULT NULL COMMENT '执行时间',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`willdolog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1254 DEFAULT CHARSET=utf8 COMMENT='必做任务记录表';

-- ----------------------------
-- Table structure for sp_oa_worklog
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_worklog`;
CREATE TABLE `sp_oa_worklog` (
  `worklog_id` int(11) NOT NULL AUTO_INCREMENT,
  `worklog_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `worklog_type` varchar(255) DEFAULT NULL COMMENT '类型',
  `worklog_date` date DEFAULT NULL COMMENT '总结日期',
  `worklog_plan` text COMMENT '工作计划',
  `worklog_results` text COMMENT '工作结果',
  `worklog_summary` text COMMENT '总结内容',
  `worklog_staff` varchar(50) DEFAULT NULL COMMENT '总结人',
  `worklog_audit_man` varchar(50) DEFAULT NULL COMMENT '审核人',
  `worklog_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `worklog_audit_content` text COMMENT '审核内容',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `address` varchar(255) DEFAULT NULL,
  `worklog_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  PRIMARY KEY (`worklog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='工作日志表';

-- ----------------------------
-- Table structure for sp_oa_workplan
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_workplan`;
CREATE TABLE `sp_oa_workplan` (
  `workplan_id` int(11) NOT NULL AUTO_INCREMENT,
  `workplan_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `workplan_overview` text COMMENT '简介概述',
  `workplan_content` longtext COMMENT '内容',
  `workplan_summary` text COMMENT '总结',
  `workplan_executor_man` varchar(50) DEFAULT NULL COMMENT '执行人',
  `workplan_arranger_man` varchar(50) DEFAULT NULL COMMENT '安排人/审核人',
  `workplan_expectstart_time` datetime DEFAULT NULL COMMENT '计划预计开始时间',
  `workplan_expectend_time` datetime DEFAULT NULL COMMENT '计划预计结束时间',
  `workplan_start_time` datetime DEFAULT NULL COMMENT '实际开始时间',
  `workplan_end_time` datetime DEFAULT NULL COMMENT '实际截止时间',
  `workplan_audit_man` varchar(50) DEFAULT NULL COMMENT '审核人',
  `workplan_audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `workplan_audit_content` varchar(255) DEFAULT NULL COMMENT '审核内容',
  `workplan_reject_man` varchar(50) DEFAULT NULL COMMENT '驳回人',
  `workplan_reject_time` datetime DEFAULT NULL COMMENT '驳回时间',
  `workplan_reject_reasons` varchar(255) DEFAULT NULL COMMENT '驳回原因',
  `workplan_stop_man` varchar(255) DEFAULT NULL COMMENT '中止人(20160524因p5项目需要添加)',
  `workplan_stop_time` datetime DEFAULT NULL COMMENT '中止时间(20160524因p5项目需要添加)',
  `workplan_stop_reasons` varchar(255) DEFAULT NULL COMMENT '中止原因(20160524因p5项目需要添加)',
  `workplan_read_time` datetime DEFAULT NULL COMMENT '查看时间',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `workplan_attach` varchar(255) DEFAULT NULL,
  `workplan_delay_time` datetime DEFAULT NULL COMMENT '延期时间',
  `workplan_delay_content` text CHARACTER SET utf8mb4 COMMENT '延期原因',
  `workplan_audit_score` varchar(20) DEFAULT NULL COMMENT '工作审核评分',
  PRIMARY KEY (`workplan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='工作计划表';

-- ----------------------------
-- Table structure for sp_oa_workplan_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_oa_workplan_log`;
CREATE TABLE `sp_oa_workplan_log` (
  `planlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `workplan_id` int(11) DEFAULT NULL COMMENT '工作计划id',
  `planlog_staff` varchar(50) DEFAULT NULL COMMENT '员工编号：审核人',
  `planlog_summary` varchar(255) DEFAULT NULL COMMENT '总结',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '执行人',
  `status` varchar(5) DEFAULT NULL COMMENT '1:新增，2：开始，3：申请延期，4：同意延期，5：当日工作总结，6：审核，7：中止，8：完成，9：驳回',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`planlog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COMMENT='工作计划日志';

-- ----------------------------
-- Table structure for sp_perf_assess
-- ----------------------------
DROP TABLE IF EXISTS `sp_perf_assess`;
CREATE TABLE `sp_perf_assess` (
  `assess_no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL COMMENT '考核项',
  `assess_relation_no` int(11) DEFAULT NULL COMMENT '绩效所属（人/部门）编号',
  `assess_relation_type` varchar(255) DEFAULT NULL COMMENT '绩效所属类型（人/部门）',
  `assess_date` date DEFAULT NULL COMMENT '实际绩效考核日期',
  `assess_score` varchar(255) DEFAULT NULL COMMENT '绩效分',
  `assess_year` varchar(255) DEFAULT NULL COMMENT '绩效考核年份',
  `assess_cycle` varchar(255) DEFAULT NULL COMMENT '当前周期数（第几周，第几月，第几季度等）',
  `assess_cycle_type` varchar(255) DEFAULT NULL COMMENT '绩效考核周期类型（部门为年，季度，月；人员为月，周）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`assess_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8 COMMENT='绩效考核表';

-- ----------------------------
-- Table structure for sp_perf_assess_entering
-- ----------------------------
DROP TABLE IF EXISTS `sp_perf_assess_entering`;
CREATE TABLE `sp_perf_assess_entering` (
  `entering_id` int(11) NOT NULL AUTO_INCREMENT,
  `communist_id` int(11) DEFAULT NULL COMMENT '绩效所属人',
  `item_id` int(11) DEFAULT NULL COMMENT '考核相',
  `entering_date` varchar(255) DEFAULT NULL COMMENT '绩效月份',
  `entering_plan` varchar(255) DEFAULT NULL COMMENT '完成情况',
  `assess_id` int(11) DEFAULT NULL,
  `entering_score` varchar(255) DEFAULT NULL COMMENT '绩效分',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `entering_group` varchar(255) DEFAULT NULL COMMENT '党组织：party  党员 communist',
  PRIMARY KEY (`entering_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=286 DEFAULT CHARSET=utf8 COMMENT='绩效录入表';

-- ----------------------------
-- Table structure for sp_perf_assess_item
-- ----------------------------
DROP TABLE IF EXISTS `sp_perf_assess_item`;
CREATE TABLE `sp_perf_assess_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `item_score` varchar(255) DEFAULT NULL COMMENT '分值',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `item_type` tinyint(4) DEFAULT NULL COMMENT '1组织2党员',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组织考核评分项';

-- ----------------------------
-- Table structure for sp_perf_assess_score
-- ----------------------------
DROP TABLE IF EXISTS `sp_perf_assess_score`;
CREATE TABLE `sp_perf_assess_score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL COMMENT '关联',
  `relation_no` varchar(255) DEFAULT NULL COMMENT '党员/党组织编号',
  `item_type` tinyint(4) DEFAULT NULL COMMENT '1党组织2党员',
  `assess_date` date DEFAULT NULL COMMENT '实际绩效考核日期',
  `year` varchar(255) DEFAULT NULL COMMENT '绩效考核年份',
  `month` varchar(255) DEFAULT NULL COMMENT '当前月分',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='绩效评分';

-- ----------------------------
-- Table structure for sp_perf_assess_template
-- ----------------------------
DROP TABLE IF EXISTS `sp_perf_assess_template`;
CREATE TABLE `sp_perf_assess_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '模板id',
  `template_name` varchar(255) DEFAULT NULL COMMENT '模板名称',
  `template_relation_no` varchar(255) DEFAULT NULL COMMENT '考核部门',
  `template_relation_type` varchar(255) DEFAULT NULL COMMENT '模板所属类型（人/部门）',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`template_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COMMENT='考核模板表';

-- ----------------------------
-- Table structure for sp_perf_assess_template_item
-- ----------------------------
DROP TABLE IF EXISTS `sp_perf_assess_template_item`;
CREATE TABLE `sp_perf_assess_template_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL COMMENT '模板id',
  `item_name` varchar(255) DEFAULT NULL COMMENT '考核项',
  `item_date` varchar(255) DEFAULT NULL,
  `item_proportion` varchar(255) DEFAULT NULL COMMENT '比重',
  `item_type` int(11) DEFAULT NULL COMMENT '考核方式',
  `item_cycle` varchar(255) DEFAULT NULL COMMENT '周期数（第几周期）',
  `item_cycle_type` int(11) DEFAULT NULL COMMENT '考核周期类型',
  `item_manager` varchar(255) DEFAULT NULL COMMENT '责任人',
  `post_no` varchar(255) DEFAULT NULL COMMENT '所属岗位',
  `max_score` varchar(255) DEFAULT NULL COMMENT '目标设定(得分上限)',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(255) DEFAULT NULL COMMENT '状态',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `memo` text COMMENT '备注',
  `item_group` varchar(255) DEFAULT NULL COMMENT '考核项分组（communist/party）',
  `item_pid` varchar(255) DEFAULT '0',
  `item_manual_type` varchar(255) DEFAULT NULL COMMENT '自动录入的类型',
  PRIMARY KEY (`item_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=utf8 COMMENT='考核项表';

-- ----------------------------
-- Table structure for sp_poor_household
-- ----------------------------
DROP TABLE IF EXISTS `sp_poor_household`;
CREATE TABLE `sp_poor_household` (
  `poor_household_id` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT '贫困户ID编号',
  `poor_household_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '户主姓名',
  `poor_household_identity` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '身份证号',
  `poor_household_phone` varchar(255) DEFAULT NULL COMMENT '户主联系方式',
  `poor_village_type` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '所属村',
  `man_num` int(255) DEFAULT NULL COMMENT '人口总数',
  `assist_dutys` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '帮扶责任人',
  `poor_village_property` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '贫困户属性',
  `is_poor_village` int(1) DEFAULT NULL COMMENT '是否为贫困户 0否  1是',
  `poor_cause` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '致贫原因',
  `poor_status` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '脱贫状态',
  `poor_manner` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '脱贫方式',
  `poor_wage_income` int(11) DEFAULT NULL COMMENT '工资收入',
  `production_manage_income` int(11) DEFAULT NULL COMMENT '生产经营性收入',
  `other_income` int(11) DEFAULT NULL COMMENT '其他收入',
  `year_income` int(11) DEFAULT NULL COMMENT '年收入',
  `percapita_income` varchar(255) DEFAULT NULL COMMENT '人均收入',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  `staff_no` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `updata_time` datetime DEFAULT NULL COMMENT '修改时间',
  `memo` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`poor_household_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='贫困户';

-- ----------------------------
-- Table structure for sp_poor_household_member
-- ----------------------------
DROP TABLE IF EXISTS `sp_poor_household_member`;
CREATE TABLE `sp_poor_household_member` (
  `member_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `member_identity` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '身份证号',
  `member_sex` int(11) DEFAULT NULL COMMENT '性别  1男  2女',
  `member_relation` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '与户主关系',
  `update_time` datetime DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `add_staff` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `household_id` int(11) DEFAULT NULL COMMENT '贫困户',
  `status` int(11) DEFAULT '1',
  `memo` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='贫困户家庭成员';

-- ----------------------------
-- Table structure for sp_poor_village
-- ----------------------------
DROP TABLE IF EXISTS `sp_poor_village`;
CREATE TABLE `sp_poor_village` (
  `poor_village_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_poor` int(11) DEFAULT '0' COMMENT '是否贫困',
  `poor_village_name` varchar(255) DEFAULT NULL COMMENT '贫困村名称',
  `poor_village_charge` varchar(255) DEFAULT NULL COMMENT '负责人',
  `poor_village_phone` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `poor_village_keywork` text COMMENT '村简介',
  `poor_village_content` text COMMENT '基本信息',
  `boor_num` int(11) DEFAULT NULL COMMENT '户总数',
  `man_num` int(11) DEFAULT NULL COMMENT '人口总数',
  `poor_man_num` int(11) DEFAULT NULL COMMENT '人口总数',
  `poor_chance` varchar(255) DEFAULT NULL COMMENT '贫困发生率',
  `percapita_income` varchar(255) DEFAULT NULL COMMENT '人均收入',
  `poor_village__all` varchar(255) DEFAULT NULL COMMENT '村集体收入',
  `memo` text COMMENT '备注',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`poor_village_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='贫困村列表';

-- ----------------------------
-- Table structure for sp_supervise_case
-- ----------------------------
DROP TABLE IF EXISTS `sp_supervise_case`;
CREATE TABLE `sp_supervise_case` (
  `case_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '案件id',
  `case_title` varchar(255) DEFAULT NULL COMMENT '案件标题',
  `case_content` text COMMENT '案件内容',
  `case_ource` varchar(255) DEFAULT NULL COMMENT '来源',
  `case_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `case_situation` text COMMENT '调查情况',
  `case_h_view` varchar(255) DEFAULT NULL COMMENT '处理意见',
  `case_r_view` varchar(255) DEFAULT NULL COMMENT '驳回意见',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `case_situation_attach` varchar(255) DEFAULT NULL COMMENT '调查情况附件',
  `punish_type` varchar(255) DEFAULT NULL COMMENT '处分级别 关联type',
  PRIMARY KEY (`case_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='案件表';

-- ----------------------------
-- Table structure for sp_supervise_case_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_supervise_case_log`;
CREATE TABLE `sp_supervise_case_log` (
  `ce_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `case_id` int(11) DEFAULT NULL COMMENT '案件id',
  `communist_id` int(11) DEFAULT NULL COMMENT '涉案人员编号',
  `ce_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `ce_h_view` varchar(255) DEFAULT NULL COMMENT '处理意见',
  `punish_type` varchar(255) DEFAULT NULL COMMENT '处分标准',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `judge` varchar(5) DEFAULT NULL COMMENT '是否处分',
  PRIMARY KEY (`ce_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COMMENT='涉案人员表';

-- ----------------------------
-- Table structure for sp_supervise_chat
-- ----------------------------
DROP TABLE IF EXISTS `sp_supervise_chat`;
CREATE TABLE `sp_supervise_chat` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '约谈id',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号',
  `communist_no` varchar(200) DEFAULT NULL COMMENT '约谈人编号',
  `chat_type` varchar(255) DEFAULT NULL COMMENT '分类1为约谈人员2为约谈部门',
  `chat_content` varchar(255) DEFAULT NULL COMMENT '约谈内容',
  `update_staff` varchar(255) DEFAULT NULL COMMENT '审核人',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '推荐人',
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `chat_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `chat_time` datetime DEFAULT NULL COMMENT '约谈时间',
  `chat_address` varchar(255) DEFAULT '' COMMENT '约谈地址',
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='纪检约谈表';

-- ----------------------------
-- Table structure for sp_supervise_outstanding
-- ----------------------------
DROP TABLE IF EXISTS `sp_supervise_outstanding`;
CREATE TABLE `sp_supervise_outstanding` (
  `outstanding_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `party_no` int(11) DEFAULT NULL COMMENT '部门编号',
  `communist_no` int(11) DEFAULT NULL COMMENT '人编号',
  `outstanding_type` int(11) DEFAULT NULL COMMENT '分类1为优秀人员2为优秀部门',
  `outstanding_content` varchar(255) DEFAULT NULL COMMENT '内容',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '推荐人',
  `update_staff` varchar(255) DEFAULT NULL COMMENT '审核人',
  `status` varchar(5) DEFAULT '0' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `outstanding_attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `month` varchar(255) DEFAULT NULL COMMENT '月',
  PRIMARY KEY (`outstanding_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='优秀党员表';

-- ----------------------------
-- Table structure for sp_sys_ad
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_ad`;
CREATE TABLE `sp_sys_ad` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `ad_title` varchar(255) DEFAULT NULL COMMENT '广告标题',
  `ad_url` varchar(255) DEFAULT NULL COMMENT '广告链接地址',
  `ad_img` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `ad_location` varchar(255) DEFAULT NULL COMMENT '广告位置   编码',
  `add_staff` varchar(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `memo` text COMMENT '广告简介',
  `ad_sort` int(11) DEFAULT '0' COMMENT '广告展示排序',
  PRIMARY KEY (`ad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='广告表';

-- ----------------------------
-- Table structure for sp_sys_ad_location
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_ad_location`;
CREATE TABLE `sp_sys_ad_location` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) DEFAULT NULL COMMENT '位置名称',
  `location_code` varchar(255) DEFAULT NULL COMMENT '位置 编码',
  `location_description` text COMMENT '位置描述',
  `location_width` double DEFAULT NULL,
  `location_height` double DEFAULT NULL,
  `add_staff` varchar(255) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `memo` text,
  `project_type` varchar(255) DEFAULT NULL COMMENT '项目类别（gm为街道大厅）',
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='广告位置表';

-- ----------------------------
-- Table structure for sp_sys_auth
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_auth`;
CREATE TABLE `sp_sys_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `role_id` varchar(255) DEFAULT NULL COMMENT '角色id',
  `function_id` varchar(255) DEFAULT NULL COMMENT '功能模块id',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191032 DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Table structure for sp_sys_blogroll
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_blogroll`;
CREATE TABLE `sp_sys_blogroll` (
  `blogroll_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '导航id',
  `blogroll_name` varchar(255) DEFAULT NULL COMMENT '导航名称',
  `blogroll_url` varchar(255) DEFAULT NULL COMMENT '导航链接地址',
  `blogroll_type` int(11) DEFAULT NULL COMMENT '1 系统内 2系统外',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT '1' COMMENT '状态  1正常显示 0停用',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`blogroll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='门户友情链接菜单表（用于门户首页友情链接菜单的维护）';

-- ----------------------------
-- Table structure for sp_sys_config
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_config`;
CREATE TABLE `sp_sys_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `config_code` varchar(255) DEFAULT NULL COMMENT '英文编码',
  `config_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `config_value` text COMMENT '值',
  `config_type` varchar(255) DEFAULT NULL COMMENT '配置类型',
  `config_group` varchar(255) DEFAULT NULL COMMENT '配置组',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=272 DEFAULT CHARSET=utf8 COMMENT='系统配置';

-- ----------------------------
-- Table structure for sp_sys_function
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_function`;
CREATE TABLE `sp_sys_function` (
  `function_id` varchar(11) NOT NULL COMMENT '功能ID',
  `function_name` varchar(255) DEFAULT NULL COMMENT '功能名称',
  `function_action` varchar(255) DEFAULT NULL COMMENT 'java专用方法路径',
  `function_url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `function_pid` varchar(255) DEFAULT NULL COMMENT '父级id',
  `group_code` varchar(255) DEFAULT 'app' COMMENT '用于区分菜单（communist:基础菜单 cust:客户菜单 app:手机端菜单）',
  `function_code` varchar(255) DEFAULT NULL COMMENT '编码',
  `function_type` int(11) DEFAULT '0' COMMENT '类型   0代表菜单   1代表功能',
  `function_icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `function_order` int(11) DEFAULT NULL COMMENT '排序',
  `add_user` varchar(50) DEFAULT NULL COMMENT '添加人',
  `status` int(11) DEFAULT '1' COMMENT '状态  (0为不用,1为启用,默认值为1)',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='功能菜单表';

-- ----------------------------
-- Table structure for sp_sys_function_copy
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_function_copy`;
CREATE TABLE `sp_sys_function_copy` (
  `function_id` varchar(11) NOT NULL COMMENT '功能ID',
  `function_name` varchar(255) DEFAULT NULL COMMENT '功能名称',
  `function_action` varchar(255) DEFAULT NULL COMMENT 'java专用方法路径',
  `function_url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `function_pid` varchar(255) DEFAULT NULL COMMENT '父级id',
  `group_code` varchar(255) DEFAULT 'app' COMMENT '用于区分菜单（communist:基础菜单 cust:客户菜单 app:手机端菜单）',
  `function_code` varchar(255) DEFAULT NULL COMMENT '编码',
  `function_type` int(11) DEFAULT '0' COMMENT '类型   0代表菜单   1代表功能',
  `function_icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `function_order` int(11) DEFAULT NULL COMMENT '排序',
  `add_user` varchar(50) DEFAULT NULL COMMENT '添加人',
  `status` int(11) DEFAULT '1' COMMENT '状态  (0为不用,1为启用,默认值为1)',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='功能菜单表';

-- ----------------------------
-- Table structure for sp_sys_log
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_log`;
CREATE TABLE `sp_sys_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `function_code` varchar(255) DEFAULT NULL COMMENT '功能模块id',
  `log_type` varchar(255) DEFAULT NULL COMMENT '操作类型 1新增2修改3删除4登陆 5数据库初始化',
  `log_oldcontent` text COMMENT '操作前内容',
  `log_newcontent` text COMMENT '操作后内容',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT '1' COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3951 DEFAULT CHARSET=utf8 COMMENT='系统日志';

-- ----------------------------
-- Table structure for sp_sys_nav
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_nav`;
CREATE TABLE `sp_sys_nav` (
  `nav_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '导航id',
  `nav_code` varchar(20) DEFAULT NULL COMMENT '导航栏标示',
  `nav_pid` int(11) DEFAULT '0' COMMENT '导航上级ID',
  `nav_name` varchar(255) DEFAULT NULL COMMENT '导航名称',
  `nav_url` varchar(255) DEFAULT NULL COMMENT '导航链接地址',
  `is_header` int(1) DEFAULT '1' COMMENT '是否在头部显示',
  `is_left` int(1) DEFAULT '1' COMMENT '是否在左侧显示',
  `nav_order` int(11) DEFAULT '0' COMMENT '排序',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT '1' COMMENT '状态  1正常显示 0停用',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `class_login` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='门户导航菜单表（用于门户首页导航菜单的维护）';

-- ----------------------------
-- Table structure for sp_sys_role
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_role`;
CREATE TABLE `sp_sys_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主\r\n\r\n键',
  `role_name` varchar(255) DEFAULT NULL COMMENT '角色名称',
  `role_type` int(11) DEFAULT '1' COMMENT '角色类型（1.党建后台，2.微信，3.智慧街道）',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `role_all` int(11) DEFAULT NULL COMMENT '全局角色(智慧平桥：1.是，2.不是)',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100014 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Table structure for sp_sys_user
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_user`;
CREATE TABLE `sp_sys_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `user_nickname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `user_pwd` varchar(255) DEFAULT NULL COMMENT '密码',
  `user_role` varchar(255) DEFAULT NULL COMMENT '用户角色',
  `user_relation_no` varchar(255) DEFAULT NULL COMMENT '员工',
  `user_relation_type` varchar(11) DEFAULT '2' COMMENT '1:用户 2.智慧街道工作人员 3 群众',
  `last_login_time` datetime DEFAULT NULL COMMENT '登录时间',
  `add_staff` varchar(255) DEFAULT NULL COMMENT '添加人',
  `status` varchar(5) DEFAULT '1' COMMENT '状态 1 可用 -1禁用',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  `period_validity` varchar(255) DEFAULT NULL COMMENT '有效期',
  `charge_party` text COMMENT '负责党支部',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1035 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Table structure for sp_sys_user_auth
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_user_auth`;
CREATE TABLE `sp_sys_user_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '数据权限id',
  `communist_no` varchar(20) DEFAULT NULL COMMENT '用户id',
  `type_no` varchar(50) DEFAULT NULL COMMENT '类型表id',
  `select_value` text COMMENT '选中的结果',
  `auth_type` int(5) DEFAULT NULL COMMENT '类型（1、党建，2、网格）',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8 COMMENT='用户权限表';

-- ----------------------------
-- Table structure for sp_sys_user_auth_type
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_user_auth_type`;
CREATE TABLE `sp_sys_user_auth_type` (
  `type_no` varchar(11) NOT NULL COMMENT '数据集权限类型id',
  `type_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `type_code` varchar(255) DEFAULT NULL COMMENT '类型编码',
  `auth_type` int(11) DEFAULT NULL COMMENT ' 类型（1、党建 2、网格）',
  `function_id` int(11) DEFAULT NULL COMMENT '功能模块id',
  `type_range` varchar(255) DEFAULT NULL COMMENT '数据范围',
  `add_staff` varchar(255) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL COMMENT '状态',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`type_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户权限类型';

-- ----------------------------
-- Table structure for sp_sys_vcode
-- ----------------------------
DROP TABLE IF EXISTS `sp_sys_vcode`;
CREATE TABLE `sp_sys_vcode` (
  `vcode_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) DEFAULT NULL COMMENT '电话',
  `vcode_type` int(11) DEFAULT NULL COMMENT '验证码类型（1：数字，2：图形）',
  `vcode` varchar(255) DEFAULT NULL COMMENT '验证码',
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`vcode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='验证码';
