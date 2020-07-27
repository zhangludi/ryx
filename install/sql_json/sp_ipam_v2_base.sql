SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE `sp_bd_upload`;
TRUNCATE TABLE `sp_ccp_communist`;

INSERT INTO `sp_ccp_communist` VALUES ('384', '9999', '超级管理员', 'cjgly', '1', '101,102,201', '/uploads/ccp/5b63ef9d2afb3.jpg', '1', '2017-12-01', '', '', '11', '山东大学', '工商管理', '123456789987654321', '0531-1234781', '18769721792', '', '', '', '', '山东济南', '山东济南', '', '9999', '1', '21', '2018-08-03 14:01:27', '0000-00-00 00:00:00', '', '0', '', '0000-00-00', '0000-00-00', '2017-12-22', '', '', '0000-00-00', '5.00', '1', '1', '', '1', '0000-00-00 00:00:00', '', '1', '2017-12-22 00:00:00', '0000-00-00', '', '', 'chaojiguanliyuan', 'ccd9bfe60c510635e6141617f3c49f57','');

TRUNCATE TABLE `sp_ccp_communist_bio`;
TRUNCATE TABLE `sp_ccp_communist_change`;
TRUNCATE TABLE `sp_ccp_communist_change_log`;
TRUNCATE TABLE `sp_ccp_communist_comment`;
TRUNCATE TABLE `sp_ccp_communist_comment_details`;
TRUNCATE TABLE `sp_ccp_communist_help`;
TRUNCATE TABLE `sp_ccp_communist_log`;
TRUNCATE TABLE `sp_ccp_dues`;
TRUNCATE TABLE `sp_ccp_group`;
TRUNCATE TABLE `sp_ccp_group_communist`;
TRUNCATE TABLE `sp_ccp_integral_log`;
TRUNCATE TABLE `sp_ccp_party`;

INSERT INTO `sp_ccp_party` VALUES ('1', '济南市委', '市委', '0', '9999', '9999', '1', '1', '2018-07-30 11:22:50', '0000-00-00 00:00:00', '济南市委是中国共产党在济南市的领导核心，由中国共产党济南市代表大会选举产生，并在代表大会闭会期间，执行中国共产党中央委员会的指示和中国共产党北京市代表大会的决议，全面领导济南市的工作，定期向中国共产党中央委员会报告工作', '12.00', '', '2345`2432`2433`2434`2435', '117.125824', '36.654238', '2472', '2', '山东省济南市历下区旅游路','admin','7fef6171469e80d32c0559f88b377245');

TRUNCATE TABLE `sp_ccp_partyday_category`;
TRUNCATE TABLE `sp_ccp_partyday_plan`;
TRUNCATE TABLE `sp_ccp_partyday_plan_log`;
TRUNCATE TABLE `sp_ccp_party_duty`;

INSERT INTO `sp_ccp_party_duty` VALUES ('101', '市委书记', '9999', '1', '2018-07-24 10:35:35', '2011-01-05 12:07:25', '市委书记');
INSERT INTO `sp_ccp_party_duty` VALUES ('102', '区委书记', '9999', '1', '2018-06-02 17:01:32', '2011-01-05 12:07:40', '');
INSERT INTO `sp_ccp_party_duty` VALUES ('103', '支部副书记', '9999', '1', '2018-06-02 17:02:12', '2016-11-08 10:27:25', '123');

TRUNCATE TABLE `sp_ccp_party_grid`;
TRUNCATE TABLE `sp_ccp_secretary`;
TRUNCATE TABLE `sp_ccp_secretary_sign`;
TRUNCATE TABLE `sp_cms_affairs`;
TRUNCATE TABLE `sp_cms_affairs_category`;
TRUNCATE TABLE `sp_cms_article`;
TRUNCATE TABLE `sp_cms_article_category`;
TRUNCATE TABLE `sp_cms_article_comment`;
TRUNCATE TABLE `sp_cms_article_fav`;
TRUNCATE TABLE `sp_cms_contribute`;
TRUNCATE TABLE `sp_com_email_inbox`;
TRUNCATE TABLE `sp_com_email_outbox`;
TRUNCATE TABLE `sp_com_imail_inbox`;
TRUNCATE TABLE `sp_com_imail_outbox`;
TRUNCATE TABLE `sp_com_msg_log`;
TRUNCATE TABLE `sp_com_msg_template`;
TRUNCATE TABLE `sp_com_msg_type`;

TRUNCATE TABLE `sp_edu_exam`;
TRUNCATE TABLE `sp_edu_exam_answer`;
TRUNCATE TABLE `sp_edu_exam_log`;
TRUNCATE TABLE `sp_edu_material`;
TRUNCATE TABLE `sp_edu_material_category`;
TRUNCATE TABLE `sp_edu_material_communist`;
TRUNCATE TABLE `sp_edu_notes`;
TRUNCATE TABLE `sp_edu_questions`;
TRUNCATE TABLE `sp_edu_topic`;
TRUNCATE TABLE `sp_edu_topic_details`;
TRUNCATE TABLE `sp_hr_dept`;

INSERT INTO `sp_hr_dept` VALUES ('1', '10', '综治机构', '0', null, '201803020002', null, null, null, 'admin', null, '1', null, '2018-02-23 13:58:59', '123', '02', null, null);

TRUNCATE TABLE `sp_hr_post`;

INSERT INTO `sp_hr_post` VALUES ('1', '总经理', null, null, null, null, null, null, '1001', null, '1', null, '2018-06-30 15:11:29', '负责全公司的管理');
INSERT INTO `sp_hr_post` VALUES ('2', '部门主任', null, null, null, null, null, null, '1001', null, '1', null, '2018-06-30 15:12:04', '负责该部门的工作安排及调度工作');
INSERT INTO `sp_hr_post` VALUES ('3', '职员', null, null, null, null, null, null, '1001', null, '1', null, '2018-06-30 15:12:32', '负责处理领导安排的工作');

TRUNCATE TABLE `sp_hr_staff`;
INSERT INTO `sp_hr_staff` VALUES ('1001', '9999', '', '', '', '超级管理员dj', '10', '3', '/dj_sp_djv2/uploads/hr/5ba0cce7ad28d.jpg', '1', '1981-03-02', '', '', '本科', '', '1', '汉族', '党员', '150429199503166557', '88888888', '17862806782', '', '', '', '', '', '本科', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', '1', '', '0', '', '', '', '1', '2018-09-18 18:01:15', '0000-00-00 00:00:00', '', '', '0', '', '', '', '', '', '', '', '', '', '');
INSERT INTO `sp_hr_staff` VALUES ('1068', '201803020002', null, null, null, '超级管理员', '10', '3', null, '1', '1981-03-02', null, null, '本科', null, '1', '汉族', '党员', '150429199503166557', '88888888', '17862806782', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1', '1', null, '0', null, null, null, '1', '2018-08-20 14:06:12', null, null, null, '0', null, null, null, null, null, null, null, null, null, null);

TRUNCATE TABLE `sp_life_bbs_post`;
TRUNCATE TABLE `sp_life_bbs_post_category`;
TRUNCATE TABLE `sp_life_bbs_post_comment`;
TRUNCATE TABLE `sp_life_bbs_post_fav`;
TRUNCATE TABLE `sp_life_condition`;
TRUNCATE TABLE `sp_life_condition_log`;
TRUNCATE TABLE `sp_life_condition_personal`;
TRUNCATE TABLE `sp_life_guestbook`;
TRUNCATE TABLE `sp_life_survey`;
TRUNCATE TABLE `sp_life_survey_answer`;
TRUNCATE TABLE `sp_life_survey_log`;
TRUNCATE TABLE `sp_life_survey_questions`;
TRUNCATE TABLE `sp_life_volunteer`;
TRUNCATE TABLE `sp_life_volunteer_activity`;
TRUNCATE TABLE `sp_life_volunteer_activity_apply`;
TRUNCATE TABLE `sp_life_vote`;
TRUNCATE TABLE `sp_life_vote_option`;
TRUNCATE TABLE `sp_life_vote_result`;
TRUNCATE TABLE `sp_life_vote_subject`;
TRUNCATE TABLE `sp_oa_approval`;
TRUNCATE TABLE `sp_oa_approval_log`;
TRUNCATE TABLE `sp_oa_approval_node`;
TRUNCATE TABLE `sp_oa_approval_template`;
TRUNCATE TABLE `sp_oa_approval_template_node`;
TRUNCATE TABLE `sp_oa_att_legwork`;
TRUNCATE TABLE `sp_oa_att_log`;
TRUNCATE TABLE `sp_oa_att_machine`;
TRUNCATE TABLE `sp_oa_att_setting_extratime`;
TRUNCATE TABLE `sp_oa_att_setting_time`;
TRUNCATE TABLE `sp_oa_meeting`;
TRUNCATE TABLE `sp_oa_meeting_communist`;
TRUNCATE TABLE `sp_oa_meeting_minutes`;
TRUNCATE TABLE `sp_oa_meeting_video`;
TRUNCATE TABLE `sp_oa_missive`;
TRUNCATE TABLE `sp_oa_missive_inbox`;
TRUNCATE TABLE `sp_oa_missive_outbox`;
TRUNCATE TABLE `sp_oa_missive_sign`;
TRUNCATE TABLE `sp_oa_notes`;
TRUNCATE TABLE `sp_oa_notice`;
TRUNCATE TABLE `sp_oa_notice_log`;
TRUNCATE TABLE `sp_oa_willdo`;
TRUNCATE TABLE `sp_oa_willdo_log`;
TRUNCATE TABLE `sp_oa_worklog`;
TRUNCATE TABLE `sp_oa_workplan`;
TRUNCATE TABLE `sp_oa_workplan_log`;
TRUNCATE TABLE `sp_perf_assess`;
TRUNCATE TABLE `sp_perf_assess_entering`;
TRUNCATE TABLE `sp_perf_assess_template`;
TRUNCATE TABLE `sp_perf_assess_template_item`;
TRUNCATE TABLE `sp_supervise_case`;
TRUNCATE TABLE `sp_supervise_case_log`;
TRUNCATE TABLE `sp_supervise_chat`;
TRUNCATE TABLE `sp_supervise_outstanding`;
TRUNCATE TABLE `sp_sys_vcode`;