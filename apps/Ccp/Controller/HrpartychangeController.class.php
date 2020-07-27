<?php
/***********************************党员、党组织、职务**********************************/
namespace Ccp\Controller;
use Think\Controller;
use Hr\Model\HrCommunistModel;

class HrpartychangeController extends Controller{
    /**
     * @name:hr_party_change_index
     * @desc：组织换届首页
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_index(){
    	// checkAuth(ACTION_NAME);
        $child_nos = session('party_no_auth');//取本级及下级组织
        $p_map['status'] = 1;
        $p_map['party_no'] = array('in',$child_nos);
        $party_list = M('ccp_party')->where($p_map)->select();
        // $party_no = getCommunistInfo(session("staff_no"),'party_no');
        $party_no = $party_list[0]['party_no'];
        $this->assign('party_no',$party_no);
        $this->assign('party_list',$party_list);
        $this->assign('change_time',date("Y-m-d"));
        // 获取组织换届流程信息
        $flow_list = M('hr_party_change_flow')->order('memo asc')->select();
        if(!empty($flow_list)){
            $flow_count = count($flow_list);
        } else {
            $flow_count = 1;
        }
        $this->assign('flow_list',$flow_list);
        $this->assign('flow_count',$flow_count);
    	$this->display("Hrpartychange/hr_party_change_index");
    }
    /**
     * @name:hr_party_change_index_data
     * @desc：组织换届数据加载
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_index_data(){
        // 文件资料列表
        // $change_time = I('get.change_time');
        $party_no = I('get.party_no',1);
        $change_time = getPartyInfo($party_no,'party_change_time');
        $party_change_step_list = getBdCodeList('party_change_step_code');
        $this->assign('party_change_step_list',$party_change_step_list);
        $edit_num = 1; // 默认第一个需要上传
        $change_map['change_time'] = $change_time;
        $change_map['change_party_no'] = $party_no;
        foreach ($party_change_step_list as &$step) {
            // 判断这个日期内是否存在换届记录
            $change_map['change_code'] = $step['code_no'];
            $change_map['status'] = 1;
            $party_change_info = M('hr_party_change_log')->where($change_map)->field('change_code,change_party_no,change_file_upload_time,change_time,status')->find();
            if(!empty($party_change_info)){
                $step['change_file_upload_time'] = $party_change_info['change_file_upload_time'];
                $step['status'] = $party_change_info['status'];
                $step['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('hr_party_change_info', array(
                    'party_no' => $party_no , 'code_no' => $step['code_no']
                )) . "'><i class='fa fa-edit'></i> 查看详情</a>  " . "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('hr_party_change_edit', array(
                    'party_no' => $party_no , 'code_no' => $step['code_no']
                )) . "'$confirm><i class='fa fa-trash-o'></i> 编辑 </a>  ";
                $edit_num = $step['code_no']+1;
                $step['status_name'] = '已上传';
            } else {
                $step['change_file_upload_time'] = '-';
                $step['status'] = 0;
                if($step['code_no'] == $edit_num){
                    $step['status_name'] = '可上传';
                    $step['operate'] = "<a id='chuan' onclick='shangchuan(".$party_no.",".$step['code_no'].")' class='layui-btn layui-btn-del layui-btn-xs'><i class='fa fa-edit' ></i> 上传资料</a>  ";
                } else {
                    $step['status_name'] = '-';
                    $step['operate'] = "<a disabled = 'disabled' class='btn btn-xs grey btn-outline'><i class='fa fa-edit'></i> 上传资料</a>  ";
                }
            }
        }
        $data['data'] = $party_change_step_list;
        $data['code'] = 0;
        $data['count'] = 0;
        $data['party_change_time'] = $change_time;
        $this->ajaxReturn($data); // 返回json格式数据
    }
    public function party_change_time(){
        $party_no = I('post.party_no',1);
        if($party_no){
            $where['party_no'] = $party_no;
            $data = M('ccp_party')->where($where)->find();
        }else{
            $data = M('ccp_party')->where($where)->find();
        }
        $this->ajaxReturn($data); // 返回json格式数据
    }
    /**
     * @name:hr_party_edit
     * @desc：上传资料
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_edit(){
        
        checkAuth(ACTION_NAME);
        $party_no = I('get.party_no');
        if (!empty($party_no)) {
            $party_row =getPartyInfo($party_no,'all');
            $party_row['party_level_name'] = getBdCodeInfo($party_row['pary_classification_code'],'pary_classification_code');
            if(empty($party_row['party_level_name'])){
                $party_row['party_level_name'] = '暂无层级信息';
            }
            if($party_row['party_pno'] == 0){
                $party_row['party_pno'] = "暂无上级组织";
            }else{
                $party_map['party_no'] = $party_row['party_pno'];
                $party_row['party_pno'] = M('ccp_party')->where($party_map)->getField('party_name');

            }
            if($party_row['party_level_code']){
                $party_row['party_level_code'] = getBdCodeInfo($party_row['party_level_code'],'party_level_code');
            }else{
                $party_row['party_level_code'] = '暂无党支部分级';
            }
            $this->assign("party_row", $party_row);
        }
        $code_no = I('get.code_no'); // 材料编号
        $this->assign("code_no",$code_no);
        $code_name = getBdCodeInfo($code_no,'party_change_step_code');
        $this->assign("code_name",$code_name); // 材料名称

        $change_time = I('get.change_time'); // 换届名称
        if(!empty($change_time)){
            $change_type = 'history';
            $change_time = $change_time;
        } else {
            $change_type = 'now';
            $change_time = $party_row['party_change_time'];
        }
        $this->assign("change_time",$change_time);
        $this->assign("change_type",$change_type);
        // 获取该组织的换届信息
        $log_map['change_party_no'] = $party_no;
        $log_map['change_code'] = $code_no;
        $log_map['change_time'] = $change_time;
        $change_log_info = M('hr_party_change_log')->where($log_map)->find();
        $this->assign("change_log_info",$change_log_info);
        $this->assign("type",$change_log_info['type']);
        $this->assign("party_no",$party_no);
        $this->display("Hrpartychange/hr_party_change_edit");
    }
    /**
     * @name:hr_party_info
     * @desc：部门详情
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function get_hr_party_info(){
        $party_no = I('post.party_no');
        if (! empty($party_no)) {
            $party_info =getPartyInfo($party_no,'all');
        }
        $this->ajaxReturn($party_info);
    }

    /**
     * @name:hr_party_change_info
     * @desc：部门换届详情
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_info(){
        checkAuth(ACTION_NAME);
        $party_no = I('get.party_no');
        if (!empty($party_no)) {
            $party_row =getPartyInfo($party_no,'all');
            $party_row['party_level_name'] = getBdCodeInfo($party_row['pary_classification_code'],'pary_classification_code');
            if(empty($party_row['party_level_name'])){
                $party_row['party_level_name'] = '暂无层级信息';
            }
            if($party_row['party_pno'] == 0){
                $party_row['party_pno'] = "暂无上级组织";
            }else{
                $party_map['party_no'] = $party_row['party_pno'];
                $party_row['party_pno'] = M('ccp_party')->where($party_map)->getField('party_name');

            }
            if($party_row['party_level_code']){
                $party_row['party_level_code'] = getBdCodeInfo($party_row['party_level_code'],'party_level_code');
            }else{
                $party_row['party_level_code'] = '暂无党支部分级';
            }
            $this->assign("party_row", $party_row);
        }
        $code_no = I('get.code_no'); // 材料编号
        $this->assign("code_no",$code_no);
        $code_name = getBdCodeInfo($code_no,'party_change_step_code');
        $this->assign("code_name",$code_name); // 材料名称

        $change_time = I('get.change_time'); // 换届名称
        if(!empty($change_time)){
            $change_time = $change_time;
        } else {
            $change_time = $party_row['party_change_time'];
        }
        $this->assign("change_time",$change_time);
        // 获取该组织的换届信息
        $log_map['change_party_no'] = $party_no;
        $log_map['change_code'] = $code_no;
        $log_map['change_time'] = $change_time;
        $change_log_info = M('hr_party_change_log')->where($log_map)->find();
        $this->assign("change_log_info",$change_log_info);
        $this->assign("party_no",$party_no);
        $this->display("Hrpartychange/hr_party_change_info");
    }
    /**
     * @name:hr_party_do_save
     * @desc：部门操作执行
     * @author：王宗彬
     * @addtime:2017-11-17
     * @version：V1.0.0
     **/
    public function hr_party_change_do_save(){
        checkLogin();
        $db_party = M('hr_party_change_log');
        $data = I('post.'); // I方法获取整个数组
        $party_no=$data['party_no'];
        $change_log['change_party_no'] = $party_no;
        $change_log['change_party_name'] = getPartyInfo($party_no);
        $change_log['change_code'] = $data['code_no'];
        $change_log['change_code_name'] = getBdCodeInfo($data['code_no'],'party_change_step_code');
        $change_log['change_time'] = $data['change_time'];
        $change_log['change_file'] = str_replace("`",",",$data['change_file']);
        $change_log['status'] = 1;
        $change_log['add_staff'] = session('staff_no');
        $change_log['change_file_upload_time'] = date("Y-m-d H:i:s");
        $change_log['update_time'] = date("Y-m-d H:i:s");
        // 判断该党支部在此时间此材料名称下是否有换届记录
        $is_has_log_map['change_party_no'] = $change_log['change_party_no'];
        $is_has_log_map['change_time'] = $change_log['change_time'];
        $is_has_log_map['change_code'] = $change_log['change_code'];
        if (empty($data['change_id'])) { // 保存
            $is_has_log = $db_party->where($is_has_log_map)->find();
        }
        $map['party_no'] = $change_log['change_party_no'];
        $party_manager = M("ccp_party")->where($map)->getField("party_manager");
        saveAlertMsg(11,$party_manager,"Ccp/Hrpartychange/hr_party_change_index","您有一条换届提醒",$change_log['change_time'],"","",session('staff_no'),'','');
        if(!empty($is_has_log)){
            showMsg('error', '该党支部在此时间此材料名称下有换届记录了！','');
        } else {
           if (!empty($data['change_id'])) { // 保存
                $log_map['change_id'] = $data['change_id'];
                $oper_res = $db_party->where($log_map)->save($change_log);
                $log_type_name='修改';//系统日志类型名称
                $log_type=2;
            } else { // 添加
                $change_log['add_time'] = date("Y-m-d H:i:s");
                $oper_res = $db_party->add($change_log);
                $log_type_name='添加';//系统日志类型名称
                $log_type=1;
            }
            if ($oper_res) {
                // 写进日志
                saveLog(ACTION_NAME,$log_type,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").$log_type_name.$change_log['change_party_name'].'党组织换届信息');
                if($data['type'] == 2 || $data['change_type'] == 'history'){ // 通过历史添加
                    showMsg('success', '操作成功！！！',  U('Hrpartychange/hr_party_change_history_index'));
                } else {
                    showMsg('success', '操作成功！！！',  U('Hrpartychange/hr_party_change_index'));
                }
            } else {
                showMsg('error', '操作失败','');
            } 
        }
        
    }

    /**
     * @name:hr_party_change_history_do_save
     * @desc：部门操作执行
     * @author：王宗彬
     * @addtime:2017-11-17
     * @version：V1.0.0
     **/
    public function hr_party_change_history_do_save(){
        $db_party = M('hr_party_change_log');
        $data = I('post.'); // I方法获取整个数组
        if (empty($data['change_id'])) { // 保存
            // 判断该党支部在此时间此材料名称下是否有换届记录
            $is_has_log_map['change_party_no'] = $data['party_no'];
            $is_has_log_map['change_time'] = $data['change_time'];
            $is_has_log = $db_party->where($is_has_log_map)->find();
        }
        if(!empty($is_has_log)){
            showMsg('error', '该党支部在此时间下有换届记录了！','');
        } else {
            $change_file_list = $data["change_file"];
            // 阶段换届文件信息
            $party_no=$data['party_no'];
            $change_log['change_party_no'] = $party_no;
            $change_log['change_party_name'] = getPartyInfo($party_no);
            $change_log['change_time'] = $data['change_time'];
            $change_log['status'] = 1;
            $change_log['add_staff'] = session('staff_no');
            $change_log['change_file_upload_time'] = date("Y-m-d H:i:s");
            $change_log['update_time'] = date("Y-m-d H:i:s");
            $change_log['add_time'] = date("Y-m-d H:i:s");
            $change_log['type'] = 2;
            foreach ($change_file_list as $file_key => $file_val) {
                $code_no = $file_key+1;
                $change_log['change_code'] = $code_no;
                $change_log['change_code_name'] = getBdCodeInfo($code_no,'party_change_step_code');
                $change_log['change_file'] = str_replace("`",",",$file_val);
                $oper_res = $db_party->add($change_log);
            }
            if ($oper_res) {
                // 写进日志
                saveLog(ACTION_NAME,$log_type,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").'添加'.$change_log['change_party_name'].'党组织换届信息');
                showMsg('success', '操作成功！！！',  U('Hrpartychange/hr_party_change_history_index'));
            } else {
                showMsg('error', '操作失败','');
            } 
        }
    }

    /**
     * @name:hr_party_change_flow_index
     * @desc：组织换届流程首页
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_flow_index(){
        checkAuth(ACTION_NAME);
        // 党组织列表
        $this->display("Hrpartychange/hr_party_change_flow_index");
    }

    /**
     * @name:hr_party_change_flow_index_data
     * @desc：组织换届流程首页数据
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_flow_index_data(){
        // 党组织换届流程
        $flow_list = M('hr_party_change_flow')->order('memo asc')->select();
        $data['count'] = M('hr_party_change_flow')->order('memo asc')->count();
        if(!empty($flow_list)){
            $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
            foreach ($flow_list as $flow_k => &$flow) {
                $flow['order_id'] = $flow_k + 1;
                $flow['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('hr_party_change_flow_edit', array(
                    'flow_id' => $flow['flow_id']
                )) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('hr_party_change_flow_del', array(
                    'flow_id' => $flow['flow_id']
                )) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
            }
        }
        $data['data'] = $flow_list;
        $data['code'] = 0;
        ob_clean();$this->ajaxReturn($data); // 返回json格式数据
    }

    /**
     * @name:hr_party_change_flow_edit
     * @desc：组织换届流程编辑/添加
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_flow_edit(){
        checkAuth(ACTION_NAME);
        // 党组织列表
        $flow_id = I('get.flow_id');
        if(!empty($flow_id)){
            // 编辑
            $flow_map['flow_id'] = $flow_id;
            $flow_info = M('hr_party_change_flow')->where($flow_map)->find();
            $this->assign("flow_info",$flow_info);
        }
        $this->display("Hrpartychange/hr_party_change_flow_edit");
    }

    /**
     * @name:hr_party_change_flow_edit
     * @desc：组织换届流程
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_flow_do_save(){
        // 党组织列表
        $flow_data = I('post.');
        $flow_data['add_staff'] = session('staff_no');
        $flow_data['update_time'] = date("Y-m-d H:i:s");
        $flow_data['status'] = 1;
        if (!empty($flow_data['flow_id'])) {
            $flow_map['flow_id'] = $flow_data['flow_id'];
            $oper_res = M('hr_party_change_flow')->where($flow_map)->save($flow_data);
        } else {
            $flow_data['add_time'] = date("Y-m-d H:i:s");
            $oper_res = M('hr_party_change_flow')->add($flow_data);
        }
        if ($oper_res) {
            showMsg('success', '操作成功！！！',  U('Hrpartychange/hr_party_change_flow_index'));
        } else {
            showMsg('error', '操作失败','');
        }
    }

    /**
     * @name:hr_party_change_flow_del
     * @desc：组织换届流程
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_flow_del(){
        checkAuth(ACTION_NAME);
        // 党组织列表
        $flow_id = I('get.flow_id');
        if (!empty($flow_id)) {
            $flow_map['flow_id'] = $flow_id;
            $oper_res = M('hr_party_change_flow')->where($flow_map)->delete();
        }
        if ($oper_res) {
            showMsg('success', '操作成功！！！',  U('Hrpartychange/hr_party_change_flow_index'));
        } else {
            showMsg('error', '操作失败','');
        }
    }


    /**
     * @name:hr_party_change_templete_index
     * @desc：组织换届流程模板首页
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_templete_index(){
        checkAuth(ACTION_NAME);
        // 党组织列表
        $this->display("Hrpartychange/hr_party_change_templete_index");
    }

    /**
     * @name:hr_party_change_templete_index
     * @desc：组织换届流程模板首页
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_templete_index_data(){
        // 党组织换届流程
        $code_map['code_group'] = 'party_change_step_code';
        $templete_list = M('bd_code')->where($code_map)->select();
        $data['count'] = M('bd_code')->where($code_map)->count();
        if(!empty($templete_list)){
            foreach ($templete_list as &$templete) {
                $templete['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('hr_party_change_templete_edit', array(
                    'code_id' => $templete['code_id'], 'type' => 'edit'
                )) . "'><i class='fa fa-edit'></i> 编辑</a>  ";
                if(!empty($templete['memo'])){
                    $templete['is_templete'] = '已有模板';
                    $templete['operate'] .= "<a class='btn btn-xs red btn-outline' href='" . U('hr_party_change_templete_edit', array(
                        'code_id' => $templete['code_id'], 'type' => 'info'
                    )) . "'$confirm><i ></i> 下载 </a>  ";
                } else {
                    $templete['is_templete'] = '无模板';
                }
            }
        }
        $data['data'] = $templete_list;
        $data['code'] = 0;
        ob_clean();$this->ajaxReturn($data); // 返回json格式数据
    }

    /**
     * @name:hr_party_change_templete_edit
     * @desc：组织换届流程编辑/添加
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_templete_edit(){
        checkAuth(ACTION_NAME);
        // 党组织列表
        $code_id = I('get.code_id');
        if(!empty($code_id)){
            // 编辑
            $code_map['code_id'] = $code_id;
            $templete_info = M('bd_code')->where($code_map)->find();
            $this->assign("templete_info",$templete_info);
        }
        $type = I('get.type');
        $this->assign("type",$type);
        $this->display("Hrpartychange/hr_party_change_templete_edit");
    }

    /**
     * @name:hr_party_change_flow_edit
     * @desc：组织换届流程
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_templete_do_save(){
        // 党组织列表
        $templete_data['memo'] = I('post.memo');
        $memo_data['memo'] = '';
        $code_id = I('post.code_id');
        if (!empty($templete_data['memo'])) {
            $memo_arr = explode('`', $templete_data['memo']);
            foreach ($memo_arr as $key => $val) { // 首先判断id对应的文件是否存在
                $upload_is_has = M('bd_upload')->where('upload_id = '.$val.'')->count();
                if($upload_is_has != 0){ // 存在重组保存数组
                    if(empty($memo_data['memo'])){
                        $memo_data['memo'] = $val;
                    } else {
                        $memo_data['memo'] .= '`'.$val;
                    }
                }
            }
            $code_map['code_id'] = $code_id;
            $oper_res = M('bd_code')->where($code_map)->save($memo_data);
        } else {
            $code_map['code_id'] = $code_id;
            $oper_res = M('bd_code')->where($code_map)->save($templete_data);
            showMsg('success', '操作成功！你未上传模板文件',  U('Hrpartychange/hr_party_change_templete_index'));
        }
        if ($oper_res) {
            showMsg('success', '操作成功！！！',  U('Hrpartychange/hr_party_change_templete_index'));
        } else {
            showMsg('error', '操作失败','');
        }
    }

    /**
     * @name:hr_party_change_history_index
     * @desc：组织换届历史首页
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_history_index(){
        // checkAuth(ACTION_NAME);
        // 党组织列表
        $child_nos = session('party_no_auth');//取本级及下级组织
        $p_map['status'] = 1;
        $p_map['party_no'] = array('in',$child_nos);
        $party_list = M('ccp_party')->where($p_map)->select();
        // $party_no = getCommunistInfo(session("communist_no"),'party_no');
        $party_no = $party_list[0]['party_no'];
        $this->assign('party_no',$party_no);
        $this->assign('party_list',$party_list);
        // 获取组织换届流程信息
        $flow_list = M('hr_party_change_flow')->order('flow_id asc')->select();
        if(!empty($flow_list)){
            $flow_count = count($flow_list);
        } else {
            $flow_count = 1;
        }
        $this->assign('flow_list',$flow_list);
        $this->assign('flow_count',$flow_count);
        $this->display("Hrpartychangehistory/hr_party_change_history_index");
    }

    /**
     * @name:hr_party_change_history_index_data
     * @desc：组织换届历史首页
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_history_index_data(){
        $party_no = I('get.party_no');
        if(empty($party_no)){
            $child_nos = session('party_no_auth');//取本级及下级组织
            $p_map['status'] = 1;
            $p_map['party_no'] = array('in',$child_nos);
            $party_list = M('ccp_party')->where($p_map)->select();
            $party_no = $party_list[0]['party_no'];
        }
        
        // 当前的党组织换届时间
        $party_change_time = getPartyInfo($party_no,'party_change_time');
        // 获取组织换届流程信息
        //$log_map['change_time'] = array('lt', $party_change_time);
        $log_map['change_party_no'] = $party_no;
        $log_list = M('hr_party_change_log')->where($log_map)->group('change_time')->order('change_time desc')->select();
        $communist_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($log_list as &$log) {
            $log['add_staff'] = $communist_name_arr[$log['add_staff']];
            $log['add_time'] = mb_substr($log['add_time'],0,10,'utf-8');
            $log['party_name'] = getPartyInfo($log['change_party_no']);
            $log['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('hr_party_change_history_list', array('party_no' => $log['change_party_no'] , 'change_time' => $log['change_time'], 'type' => 'edit')) . "'><i class='fa fa-edit'></i> 编辑</a>     <a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('hr_party_change_history_list', array('party_no' => $log['change_party_no'] , 'change_time' => $log['change_time'] , 'type' => 'info')) . "'><i class='fa '></i> 查看</a>";
        }
        $data['data'] = $log_list;
        $data['count'] = 0;
        $data['code'] = 0;
        ob_clean();$this->ajaxReturn($data);
    }

    /**
     * @name:hr_party_change_history_list
     * @desc：组织换届历史数据列表
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_history_list(){
        checkAuth(ACTION_NAME);
        // 党组织编号
        $party_no = I('get.party_no');
        $change_time = I('get.change_time');
        $type = I('get.type');
        $this->assign('party_no',$party_no);
        $this->assign('type',$type);
        $this->assign('change_time',$change_time);
        $this->display("Hrpartychangehistory/hr_party_change_history_list");
    }

    /**
     * @name:hr_party_change_history_list
     * @desc：组织换届历史数据列表
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_history_list_data(){
        // 党组织编号
        $party_no = I('get.party_no');
        $change_time = I('get.change_time');
        $type = I('get.type');
        // 换届流程列表
        $change_map['change_time'] = $change_time;
        $change_map['change_party_no'] = $party_no;
        $party_change_step_list = M('hr_party_change_log')->where($change_map)->field('change_code,change_code_name,change_party_no,change_file_upload_time,change_time,status')->select();
        $data['count'] = M('hr_party_change_log')->where($change_map)->count();
        foreach ($party_change_step_list as &$step) {
            $step['status_name'] = '已上传';
            // 判断这个日期内是否存在换届记录
            $step['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('hr_party_change_info', array(
                'party_no' => $party_no , 'code_no' => $step['change_code'],'change_time' => $step['change_time']
            )) . "'><i class='fa fa-edit'></i> 查看</a>  " ;
            if($type == 'edit'){
                $step['operate'] .= "<a class=' layui-btn  layui-btn-xs layui-btn-f60' href='" . U('hr_party_change_edit', array(
                'party_no' => $party_no , 'code_no' => $step['change_code'],'change_time' => $step['change_time']
                )) . "'$confirm><i class='fa fa-trash-o'></i> 编辑 </a>  ";
            }
        }
        $data['data'] = $party_change_step_list;
        $data['code'] = 0;
        ob_clean();$this->ajaxReturn($data);
    }

    /**
     * @name:hr_party_change_history_index
     * @desc：组织换届历史首页
     * @author：ljj
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_party_change_history_edit(){
        checkAuth(ACTION_NAME);
        // 党组织列表
        $party_no = I('get.party_no');
        if (!empty($party_no)) {
            $party_row =getPartyInfo($party_no,'all');
            $party_row['party_level_name'] = getBdCodeInfo($party_row['party_level_code'],'party_level_code');
            if(empty($party_row['party_level_name'])){
                $party_row['party_level_name'] = '暂无层级信息';
            }
            $this->assign("party_row", $party_row);
        }
        $this->assign('party_no',$party_no);
        $this->display("Hrpartychangehistory/hr_party_change_history_edit");
    }
}
