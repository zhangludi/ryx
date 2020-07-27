<?php

/********************************考勤相关基础层方法 开始*************************************/
/**
 * @name:getTimeSetting
 * @desc：获取时间设置
 * @param：
 * @return：
 * @author：王彬
 * @addtime:2016-12-03
 * @updatetime:2016-12-06
 * @version：V1.0.0
 **/
function getTimeSetting()
{
    $_att_setting_time = M('oa_att_setting_time');
    $data              = $_att_setting_time->find();
    //转换时间格式
    $data['time_morning_time']      = getFormatDate($data['time_morning_time'], "H:i");
    $data['time_nooning_starttime'] = getFormatDate($data['time_nooning_starttime'], "H:i");
    $data['time_nooning_endtime']   = getFormatDate($data['time_nooning_endtime'], "H:i");
    $data['time_night_time']        = getFormatDate($data['time_night_time'], "H:i");
    if ($data) {
        return $data;
    } else {
        return null;
    }
}

/**
 * @name    getMachineInfo
 * @desc：       获取考勤机表某一字段
 * @param：    $machine_no(考勤机编号)   $field指定参数
 * @return：
 * @author：黄子正
 * @addtime:2017-04-11
 * @updatetime:2017-05-09
 * @version：V1.0.0
 **/
function getMachineInfo($machine_no, $field = 'all')
{
    $_att_machine = M('oa_att_machine');
    $machine_map['machine_no'] = $machine_no;
    if (!empty($machine_no)) {
        if ($field == "all") {
            $data = $_att_machine->where($machine_map)->find();
            if ($data) {
                //添加部门名称
                $data['party_name'] = getPartyInfo($data['party_no']);
                return $data;
            } else {
                return null;
            }
        } else {
            $data = $_att_machine->where($machine_map)->field($field)->find();
            if ($data) {
                if (!empty($data['party_no'])) {
                    //添加部门名称
                    $data['party_name'] = getPartyInfo($data['party_no']);
                }
                return $data;
            } else {
                return null;
            }
        }
    } else {
        $data = $_att_machine->select();
        foreach ($data as &$v) {
            //添加部门名称
            $v['party_name'] = getPartyInfo($v['party_no']);
        }
        if ($data) {
            return $data;
        } else {
            return null;
        }
    }
}
/**
 * @name:getMachineSelect
 * @desc：获取考勤机下拉列表
 * @param：$machine_no:要选中的编号-选填
 * @return：
 * @author：黄子正
 * @addtime:2017-04-11
 * @updatetime:2011-04-11
 * @version：V1.0.0
 **/
function getMachineSelect($machine_no)
{
    $_att_machine = M('oa_att_machine');
    $machine_list = $_att_machine->where("machine_type = '1'")->field('machine_no,machine_name')->select();
    $type_options = "";
    $select_arr   = strToArr($machine_no);
    foreach ($machine_list as &$type) {
        $selected = "";
        foreach ($select_arr as $arr) {
            if ($arr == $type['machine_no']) {
                $selected = "selected=true";
            }
        }
        $type_options .= "<option $selected value='" . $type['machine_no'] . "'>" . $type['machine_name'] . "-" . $type['machine_no'] . "</option>";
    }
    if (!empty($type_options)) {
        return $type_options;
    } else {
        return "<option value=''>无数据</option>";
    }
}

/********************************考勤相关基础层方法 结束*************************************/
/********************************考勤相关业务层方法 开始*************************************/
/********************************考勤相关业务层方法 结束*************************************/
