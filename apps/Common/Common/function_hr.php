<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/8/17
 * Time: 10:46
 */

function getStaffAuth($staff_no, $type_no)
{

    $sys_user_auth = M('sys_user_auth');

    if (empty($type_no)) {
        $auth_map['staff_no'] = $staff_no;
        $auth_list            = $sys_user_auth->where($auth_map)->order("type_no desc")->select();
    } else {
        $auth_map['staff_no'] = $staff_no;
        $auth_map['type_no']  = $type_no;
        $auth_list            = $sys_user_auth->where($auth_map)->find();
    }
    if ($auth_list) {
        return $auth_list;
    } else {
        return null;
    }
}

function getStaffInfo($staff_no, $field = 'staff_name', $num = '')
{
    if (!empty($staff_no)) {
        //范喆修改2017-4-20 增加staff_no支持字母类型
        $staff_nos = explode(',', $staff_no);
        $db_staff              = M('hr_staff');
        $staff_map['staff_no'] = array('in', $staff_nos);
        if ($field == 'all') {
            if (empty($num)) {
                $staff_value = $db_staff->where($staff_map)->find();
            } else {
                $staff_value = $db_staff->where($staff_map)->select();
            }
        } else {
            //拆分
            $staff_value = $db_staff->where($staff_map)->field($field)->select();
            $stvalue     = "";
            foreach ($staff_value as &$value) {
                if ($stvalue == '') {
                    $stvalue = $value[$field];
                } else {
                    $stvalue = $stvalue . "," . $value[$field];
                }
            }
            $staff_value = $stvalue;
        }
    }
    if ($staff_value) {
        return $staff_value;
    } else {
        return null;
    }
}

function getStaffSelect($selected_no)
{
    $db_staff        = M('hr_staff');
    $selected_no_arr = explode(',', $selected_no); //分割成数组

    $staff_list    = $db_staff->where('status=1')->select();
    $staff_options = "";
    foreach ($staff_list as &$staff) {
        $selected = "";
        if (in_array($staff['staff_no'], $selected_no_arr)) {
//判断角色id是否存在于数组中
            $selected = "selected=true";
        }
        $staff_options .= "<option $selected value='" . $staff['staff_no'] . "'>" . $staff['staff_name'] . "</option>";
    }
    if (!empty($staff_options)) {
        return $staff_options;
    } else {
        return "<option value=''>无数据</option>";
    }
}

function getDeptSelect($selected_no)
{
    $db_dept         = M('hr_dept');
    $selected_no_arr = explode(',', $selected_no); //分割成数组

    $dept_list    = $db_dept->where('status=1')->select();
    $dept_options = "";
    foreach ($dept_list as &$dept) {
        $selected = "";
        if (in_array($dept['dept_no'], $selected_no_arr)) {
//判断角色id是否存在于数组中
            $selected = "selected=true";
        }
        $dept_options .= "<option $selected value='" . $dept['dept_no'] . "'>" . $dept['dept_name'] . "</option>";
    }
    if (!empty($dept_options)) {
        return $dept_options;
    } else {
        return "<option value=''>无数据</option>";
    }
}

function getDeptInfo($dept_no, $field = 'dept_name')
{
    if (!empty($dept_no)) {
        $db_dept             = M('hr_dept');
        $dept_list = explode(',', $dept_no);
        if ($field == 'all') {
            $dept_map['dept_no'] = array('in',$dept_list);
            $dept_field          = $db_dept->where($dept_map)->find();
        } else {
            $dept_field = "";
            foreach ($dept_list as &$dept_id) {
                $dept_info = $db_dept->where("dept_no='$dept_id'")->field($field)->find();
                if ($dept_info) {
                    if (empty($dept_field)) {
                        $dept_field = $dept_info[$field];
                    } else {
                        $dept_field .= ',' . $dept_info[$dept_field];
                    }
                }
            }
        }
    }
    if ($dept_field) {
        return $dept_field;
    } else {
        return null;
    }
}

function getWilldoLog($willdo_id, $choosedate, $staff_no, $status)
{

    $willdo_log                    = M('oa_willdo_log');
    $log_map['willdo_id']          = $willdo_id;
    $log_map['willdolog_operdate'] = array('like', '%' . $choosedate . '%');
    $log_map['add_staff']          = $staff_no;
    $log_map['status']             = $status;
    $willdo_log                    = $willdo_log->where($log_map)->find();
    return $willdo_log;
}

function getPostSelect($selected_no, $field = "post_name")
{
    $db_post = M('hr_post');
    if ($field == 'post_recruitname') {
        $post_list = $db_post->where("status=2")->field('post_no,' . $field)->select();
    } else {
        $post_list = $db_post->where("status in (1,2)")->field('post_no,' . $field)->select();
    }
    $selected_no_arr = strToArr($selected_no); //分割成数组
    $post_options    = "";
    foreach ($post_list as &$post) {
        $selected = "";
        foreach ($selected_no_arr as &$arr) {
            if ($arr == $post['post_no']) {
                $selected = "selected=true";
            }
        }
        $post_options .= "<option $selected value='" . $post['post_no'] . "'>" . $post[$field] . "</option>";
    }
    if (!empty($post_options)) {
        return $post_options;
    } else {
        return "<option value=''>无数据</option>";
    }
}

function getPost($post_no, $field = 'post_name')
{
    if (!empty($post_no)) {
        $db_post   = M('hr_post');
        $post_list = explode(',', $post_no);
        if ($field == 'all') {
            $post_map['post_no'] = $post_no;
            $post_field          = $db_post->where($post_map)->find();
        } else {
            $post_field = "";
            foreach ($post_list as &$post_id) {
                $post_info = $db_post->where("post_no='$post_id'")->field($field)->find();
                if ($post_info) {
                    if (empty($post_field)) {
                        $post_field = $post_info[$field];
                    } else {
                        $post_field .= ',' . $post_info[$field];
                    }
                }
            }
        }
    }
    if ($post_field) {
        return $post_field;
    } else {
        return '无';
    }
}

function getDeptList($dept_no, $returntype = 'arr')
{
    $hr_dept              = M('hr_dept');
    $dept_array           = array();
    $dept_str             = "";
    $dept_map['dept_pno'] = $dept_no;
    $dept_list            = $hr_dept->where($dept_map)->select();
    foreach ($dept_list as $list) {
        $dept_array[] = $list;
        $dept_str .= $list['dept_no'] . ",";
        if ($returntype == 'str') {
            $dept_str .= getDeptList($list['dept_no'], 'str');
        } else if ($returntype == 'arr') {
            $dept_sonlist = getDeptList($list['dept_no']);
            foreach ($dept_sonlist as $sonlist) {
                $dept_array[] = $sonlist;
            }
        }
    }
    if ($returntype == 'arr') {
        return $dept_array;
    } else {
        return $dept_str;
    }
}

function getStaffList($dept_no, $post_no, $returntype = 'str', $ischild = '1', $status, $keyword, $staff_level, $staff_positivetime, $staff_entrytime, $staff_post_no, $staff_dept_no, $staff_diploma, $staff_sex, $staff_name, $staff_no, $staff_year, $start, $end, $phone)
{
    $hr_dept      = M('hr_dept');
    $hr_staff     = M('hr_staff');
    $config_value = getConfig("industry");
    if (!empty($dept_no)) {
        $where    = "";
        $depts_no = strToArr($dept_no);
        if ($post_no != "") {
            $where .= " and staff_post_no = '$post_no'";
        }
        if (!empty($status)) {
            if ($status == 2) {
                $where .= " and status=0";

            } else {
                $where .= " and (status=1 or status=3)";
            }
        } else {
            $where .= "   and (status=1 or status=3)";
        }
        if (!empty($phone)) {
            $where .= " and (staff_cmobil1 like '%" . $phone . "%' or staff_cmobil2 like '%" . $phone . "%')";
        }
        if (!empty($keyword)) {
            $where .= " and (staff_name like '%" . $keyword . "%' or staff_sexname like '%" . $keyword . "%' or staff_age like '%" . $keyword . "%' or staff_cmobil1 like '%" . $keyword . "%' or staff_cmobil2 like '%" . $keyword . "%')";
        }
        if (!empty($start) && !empty($end)) {
            $where .= " and add_time between '$start' and '$end'";
        }
        $staffs_no  = "";
        $staff_list = array();
        foreach ($depts_no as &$no) {
            $staff_data = $hr_staff->where("staff_dept_no = '$no' and (is_hide=0 or is_hide = '' or is_hide is null) " . $where)->order("staff_no ")->select();
            if ($returntype == "str") {
                foreach ($staff_data as $data) {
                    $staffs_no .= $data['staff_no'] . ",";
                }
                if ($ischild == '1') {
                    $dept_data = $hr_dept->where("dept_pno = '$no'")->select();
                    foreach ($dept_data as $data) {
                        $staffs_no .= getStaffList($data['dept_no'], $post_no);
                    }
                }
            } else if ($returntype == "arr") {
                foreach ($staff_data as $data) {
                    //员工年龄
                    $data['staff_age']     = getStaffAge($data['staff_birthday']);
                    $data['staff_dept_no'] = getDeptInfo($data['staff_dept_no']);
                    $data['staff_post_no'] = getPost($data['staff_post_no']);
                    $staff_list[]          = $data;
                }
                if ($ischild == '1') {
                    $dept_data = $hr_dept->where("dept_pno = '$no'")->select();
                    foreach ($dept_data as $data) {
                        $staff_row = getStaffList($data['dept_no'], $post_no, 'arr', '1', $status, $keyword);
                        foreach ($staff_row as $row) {
                            $staff_list[] = $row;
                        }
                    }
                }
            }
        }
    } else {
        if (!empty($post_no)) {
            $post_no    = strToArr($post_no);
            $staff_list = array();
            $staffs_no  = "";
            $i          = 1;
            foreach ($post_no as $no) {
                if ($config_value == 'pam') {
                    $where = '';
                    if (!empty($status)) {
                        if ($status == 2) {
                            $status = 0;
                        }
                        $where .= " and status =" . $status;
                    } elseif ($config_value == "vote") {
                        $where .= " and (status=1 or status=3)";
                    } else {
                        $where .= " and status=1";
                    }
                    if (!empty($phone)) {
                        $where .= " and (staff_cmobil1 like '%" . $phone . "%' or staff_cmobil2 like '%" . $phone . "%')";
                    }
                    if (!empty($keyword)) {
                        if ($config_value == "vote") {
                            $where .= " and (staff_name like '%" . $keyword . "%' or staff_sexname like '%" . $keyword . "%' or staff_age like '%" . $keyword . "%' or staff_cmobil1 like '%" . $keyword . "%' or staff_cmobil2 like '%" . $keyword . "%')";
                        } else {
                            $where .= " and (staff_name like '%" . $keyword . "%' or staff_no like '%" . $keyword . "%')";
                        }
                    }
                    if (!empty($staff_level)) {
                        $where .= " and staff_level=" . $staff_level;
                    }
                    if (!empty($staff_positivetime)) {
                        $where .= " and staff_positivetime like '%" . $staff_positivetime . "%'";
                    }
                    if (!empty($staff_entrytime)) {
                        $where .= " and staff_entrytime like '%" . $staff_entrytime . "%'";
                    }
                    if (!empty($staff_post_no)) {
                        $where .= " and staff_post_no =" . $staff_post_no;
                    }
                    if (!empty($staff_dept_no)) {
                        $where .= " and staff_dept_no =" . $staff_dept_no;
                    }
                    if (!empty($staff_diploma)) {
                        $where .= " and staff_diploma like '%" . $staff_diploma . "%'";
                    }
                    if (!empty($staff_sex)) {
                        if ($staff_sex == 2) {
                            $where .= " and staff_sex=0";
                        } else {
                            $where .= " and staff_sex=1";
                        }
                    }
                    if (!empty($staff_name)) {
                        $staff_name = getStaffInfo($staff_name);
                        $where .= " and staff_name like '%" . $staff_name . "%'";
                    }
                    if (!empty($staff_no)) {
                        $where .= " and staff_no='" . $staff_no . "'";
                    }
                    if (!empty($staff_year)) {
                        $days = $staff_year * 365;
                        $where .= " and datediff(now(),staff_entrytime)=" . $days;
                    }
                    if (!empty($start) && !empty($end)) {
                        $where .= " and add_time between '$start' and '$end'";
                    }
                    $staff_data = $hr_staff->where("FIND_IN_SET($no,staff_post_no) and is_hide=0" . $where)->order(array('status', 'add_time' => 'desc'))->select();
                } else {
                    $staff_data = $hr_staff->where("FIND_IN_SET($no,staff_post_no) and is_hide=0 and (status=1 or status=3)")->select();
                }
                if ($returntype == "str") {
                    foreach ($staff_data as $data) {
                        if ($i == 1) {
                            $staffs_no = $data['staff_no'];
                        } else {
                            $staffs_no .= "," . $data['staff_no'];
                        }
                        $i++;
                    }
                } else {
                    foreach ($staff_data as $data) {
                        //员工年龄
                        $data['staff_age'] = getStaffAge($data['staff_birthday']);
                        $data['staff_dept_no'] = getDeptInfo($data['staff_dept_no']);
                        $data['staff_post_no'] = getPost($data['staff_post_no']);
                        $staff_list[] = $data;
                    }
                }
            }
        } else {
            //获取所有员工列表
            $staff_list = array();
            if ($config_value == "vote") {
                $where = " is_hide=0 ";
            } else {
                $where = "1=1";
            }
            if (!empty($status)) {
                if ($status == 2) {
                    $status = 0;
                }
                $where .= " and status =" . $status;
            } elseif ($config_value == "vote") {
                $where .= " and (status=1 or status=3)";
            } else {
                $where .= " and status=1";
            }
            if (!empty($phone)) {
                $where .= " and (staff_cmobil1 like '%" . $phone . "%' or staff_cmobil2 like '%" . $phone . "%')";
            }
            if (!empty($keyword)) {
                if ($config_value == "vote") {
                    $where .= " and (staff_name like '%" . $keyword . "%' or staff_sexname like '%" . $keyword . "%' or staff_age like '%" . $keyword . "%' or staff_cmobil1 like '%" . $keyword . "%' or staff_cmobil2 like '%" . $keyword . "%')";
                } else {
                    $where .= " and (staff_name like '%" . $keyword . "%' or staff_no like '%" . $keyword . "%')";
                }
            }
            if (!empty($staff_level)) {
                $where .= " and staff_level=" . $staff_level;
            }
            if (!empty($staff_positivetime)) {
                $where .= " and staff_positivetime like '%" . $staff_positivetime . "%'";
            }
            if (!empty($staff_entrytime)) {
                $where .= " and staff_entrytime like '%" . $staff_entrytime . "%'";
            }

            if (!empty($staff_post_no)) {
                $where .= " and staff_post_no =" . $staff_post_no;
            }
            if (!empty($staff_dept_no)) {
                $where .= " and staff_dept_no =" . $staff_dept_no;
            }
            if (!empty($staff_diploma)) {
                $where .= " and staff_diploma like '%" . $staff_diploma . "%'";
            }
            if (!empty($staff_sex)) {
                if ($staff_sex == 2) {
                    $where .= " and staff_sex=0";
                } else {
                    $where .= " and staff_sex=1";
                }
            }
            if (!empty($staff_name)) {
                /* $staff_name=getStaffInfo($staff_name);
                return $staff_name */
                $where .= " and staff_name like '%" . $staff_name . "%'";
            }
            if (!empty($staff_no)) {
                $where .= " and staff_no='" . $staff_no . "'";
            }
            if (!empty($staff_year)) {
                $days = $staff_year * 365;
                $where .= " and datediff(now(),staff_entrytime)=" . $days;
            }
            if (!empty($start) && !empty($end)) {
                $where .= " and add_time between '$start' and '$end'";
            }
            if ($config_value == 'pam') {
                $staff_data = $hr_staff->where($where)->order(array('status', 'add_time' => 'desc'))->select();
            } else {
                $staff_data = $hr_staff->where($where)->order("staff_no asc ")->select();
            }
            foreach ($staff_data as &$data) {
                //员工年龄
                $data['staff_age'] = getStaffAge($data['staff_birthday']);
                $data['staff_dept_no'] = getDeptInfo($data['staff_dept_no']);
                $data['staff_post_no'] = getPost($data['staff_post_no']);
                $staff_list[] = $data;
            }
        }
    }
    if ($returntype == 'str') {
        $staffs_no = substr($staffs_no, 0, -1);
        return $staffs_no;
    } else if ($returntype == 'arr') {
        return $staff_list;
    }
}

/**
 * @name  getDeptChildNos()
 * @desc  获取当前部门的下级部门列表
 * @param 当前部门no
 * @param noself 是否包含自己
 * @param format 返回格式str或array
 * @return array 以逗号隔开的当前部门及所有下级部门的dept_no字符串或数组
 * @author 靳邦龙
 * @time   2017-10-16
 */
function getDeptChildNos($dept_pno = '0', $format = 'str', $noself = '1', $is_admin)
{
    if ($is_admin == 'is_admin') {
        $staff_no                 = session('staff_no');
        $comm_map['communist_no'] = $staff_no;
        $comm_map['type_no']      = 'is_admin';
        $communist_auth           = M('sys_user_auth')->where($comm_map)->count();
    } else {
        $communist_auth = 0;
    }
    if ($communist_auth > 0) {
        // 判断是否是有超级管理员权限
        // 有权限查看全部党组织
        $dept_no_arr = M("hr_dept")->where('status = 1')->field('dept_no')->select();
        if (!empty($dept_no_arr)) {
            foreach ($dept_no_arr as $dept_val) {
                $dept_nos_arr[] = $dept_val['dept_no'];
            }
        }
    } else {
        // 无权限只允许查看本级及下级
        $dept_nos_arr = getDeptChildNoselfNos($dept_pno);
        if ($noself == 1) {
            //
            $dept_nos_arr[] = $dept_pno;
        }
    }
    if ($format == 'str') {
        $dept_nos_arr = arrToStr($dept_nos_arr);
    }
    return $dept_nos_arr;
}

/**
 * @name  getDeptChildNoselfNos()
 * @desc  获取当前部门的所有下级部门编号列表（供getdeptChildNos调用）
 * @param 当前部门no
 * @return 所有下级部门编号的一维数组
 * @author 靳邦龙
 * @time   2017-10-16
 */
function getDeptChildNoselfNos($dept_pno)
{
    $hr_dept              = M('hr_dept');
    $dept_map['dept_pno'] = $dept_pno;
    $dept_list            = $hr_dept->where($dept_map)->getField('dept_no', true);
    if ($dept_list) {
        foreach ($dept_list as $dept_no) {
            $next_nos_arr = getDeptChildNoselfNos($dept_no);
            if ($next_nos_arr) {
                $dept_list = array_merge($dept_list, $next_nos_arr);
            }
        }
    }
    return $dept_list;
}
