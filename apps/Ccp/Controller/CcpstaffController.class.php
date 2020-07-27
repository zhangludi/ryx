<?php
/***********************************党员、党组织、职务**********************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
use Ccp\Model\CcpstaffModel;

class CcpstaffController extends BaseController{
   
   
    /**
     * @name:hr_dept_index
     * @desc：部门列表
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_dept_index(){
        checkAuth(ACTION_NAME);
		if(IS_AJAX){
			$category_list =$this->getDeptLists(0,-1);
			$category_list = array(
				'code'=>0,
				'msg'=>'',
				'count'=>0,
				'data'=>$category_list
			);
			$this->ajaxReturn($category_list);
		}
		$this->display("Ccpstaff/hr_dept_index");
    }
    
    /**
     * @name:hr_dept_edit
     * @desc：部门添加/编辑，添加为_add
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_dept_edit(){
        
        checkAuth(ACTION_NAME);
        $db_dept = M('hr_dept');
        $dept_no = I('get.dept_no');
        if (! empty($dept_no)) {
            $dept_row =getdeptInfo($dept_no,'all');
            $this->assign("dept_row", $dept_row);
        }
        $type = I('get.type');
        $this->assign("type",$type);
        $this->display("Ccpstaff/hr_dept_edit");
    }
    /**
     * @name:hr_dept_info
     * @desc：部门详情
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_dept_info(){
        checkAuth(ACTION_NAME);
        $this->display("Ccpstaff/hr_dept_info");
    }
     /**
     * @name:getDeptLists
     * @desc：获取部门列表
     * @param：$dept_pno(父级no) $num(制表符数量)
     * @return：
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    function getDeptLists($dept_pno = 0,$num = -1){
        $db_dept = M('hr_dept');
        $dept_map['dept_pno'] = $dept_pno;
        $dept_map['status'] = 1;
        $dept_list = $db_dept->where($dept_map)->select();
        $category_list = array();
        $symbol = "├─";
        $tabs = "";
        for($i = 0;$i <= $num; $i++){
            $tabs .= "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $tabs .= $symbol;
        $num++;
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        $dept_name_arr = M('hr_dept')->getField('dept_no,dept_name');
        foreach ($dept_list as &$dept) {
            if (!empty($dept['dept_manager'])){
                $manager_list = explode(",", $dept['dept_manager']); // 分割成数组
                $manager_name = "";
                foreach ($manager_list as & $manager) {
                    if (empty($manager_name)) {
                        $manager_name = $staff_name_arr[$manager];
                    } else {
                        $manager_name .= ',' .  $staff_name_arr[$manager];
                    }
                }
                $dept['dept_manager'] = $manager_name;
            } else {
                $dept['dept_manager'] = '-';
            }

            $dept['operate'] = "<a class='btn btn-xs blue btn-outline' href='" . U('Ccpstaff/hr_dept_edit', array('dept_no' => $dept['dept_no'])) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='btn btn-xs red btn-outline' href='" . U('Ccpstaff/hr_dept_do_del', array('dept_no' => $dept['dept_no'])) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
            $dept['dept_name'] = $tabs.$dept['dept_name'];
            $dept['dept_pno'] = $dept_name_arr[$dept['dept_pno']];
            if (!empty($dept['add_staff'])){
                $dept['add_staff'] =  $staff_name_arr[$dept['add_staff']];
            }
            
            $category_list[] = $dept;
            $article_category_sonlist =$this-> getDeptLists($dept['dept_no'],$num);
            foreach($article_category_sonlist as &$sonlist){
                $category_list[] = $sonlist;
            }
        }
        
        return $category_list;
    }
    /**
     * @name:hr_dept_do_save
     * @desc：部门操作执行
     * @author：王宗彬
     * @addtime:2017-11-17
     * @version：V1.0.0
     **/
    public function hr_dept_do_save(){
        $db_dept = M('hr_dept');
        $data = I('post.'); // I方法获取整个数组
        $dept_no=$data['dept_no'];
        $dept_name=$data['dept_name'];
        $type = $data['type']; //判断跳转的页面
        if (!empty($dept_no)) {
            $data['update_time'] = date("Y-m-d H:i:s");
            $oper_res = $db_dept->where("dept_no = '$dept_no'")->save($data);
            $log_type_name='修改';//系统日志类型名称
            $log_type=2;
        } else {
            if (! empty($dept_name)) {
            	$dept = getdeptList('');  
            	if(empty($dept)){   //是否存在支部  
            		$data['dept_no'] = 1;  //第一个支部编号为1
            	}else if(empty($data['dept_pno'])){
            		$data['dept_no'] = getFlowNo('', 'hr_dept', 'dept_no', '1');  //支部编号流水
            	}else{
            		$data['dept_no'] = getFlowNo($data['dept_pno'], 'hr_dept', 'dept_no', '2');  //支部编号流水
            	}
                $data['status'] = '1';
                $data['add_time'] = date("Y-m-d H:i:s");
                $data['add_staff'] = $this->staff_no;
                $oper_res = $db_dept->add($data);
 
                $log_type_name='新增';//系统日志类型名称
                $log_type=1;
            } else {
                showMsg('error', '请将内容填写完整！','');
            }
        }
        if ($oper_res) {
            saveLog(ACTION_NAME,$log_type,'','操作员['. session('staff_no').']于'.date("Y-m-d H:i:s").$log_type_name.'党组织数据，组织编号为['.$dept_no.']');
            showMsg('success', '操作成功！！！',  U('Ccpstaff/hr_dept_index'));
        } else {
            showMsg('error', '操作失败','');
        }
    }
    /**
     * @name:hr_dept_do_del
     * @desc：部门删除
     * @author：王宗彬-杨凯
     * @addtime:2017-11-3
     * @update:2018-01-02
     * @version：V1.0.0
     **/
    public function hr_dept_do_del(){
        checkAuth(ACTION_NAME);
        $db_dept = M('hr_dept');
        $dept_no = I('get.dept_no');
        $staff_nolist=getstaffList($dept_no,'str',1);//取当前部门下的所有员工编号
        if($staff_nolist){
            showMsg('error', '该部门下有员工，不可删除','');
        }
        $dept_nolist=getDeptChildNos($dept_no,'str',0);
        if($dept_nolist){
        	showMsg('error', '该部门下有其他，不可删除','');
        }
        if($dept_no){
            $dept_map['dept_no'] = $dept_no;
            $del_res = $db_dept->where($dept_map)->delete();
        }
        if ($del_res) {
            saveLog(ACTION_NAME,3,'','操作员['.session('staff_no').']于'.date("Y-m-d H:i:s").'对部门编号 ['.$dept_no.']进行删除操作');
            showMsg('success', '操作成功！！！',  U('Ccpstaff/hr_dept_index'));
        } else {
            showMsg('error', '操作失败','');
        }
    }

    /**
     * @name:hr_staff_index
     * @desc：通讯录/党员管理首页
     * @author：王宗彬
     * @addtime:2017-10-10
     * @version：V1.0.1
     **/
    public function hr_staff_index(){
    	checkAuth(ACTION_NAME);
        $dept_map['status'] = 1;
	    $dept_list = M('hr_dept')->field('dept_no,dept_name,dept_pno')->select();
        $this->assign('dept_list',$dept_list);
        $this->display("Ccpstaff/hr_staff_index");
    }
    /**
     * @name:hr_staff_index_data
     * @desc：通讯录/党员数据获取
     * @author：王宗彬
     * @updateime:2017-10-11
     * @version：V1.0.1
     **/
    public function hr_staff_index_data()
    {
    	$dept_no=I('get.dept_no');//点击的党支部编号
    	$staff_name=I('get.staff_name');
		$pagesize = I('get.limit');
		$page = (I('get.page')-1) * $pagesize;
     	$hr_staff = M('hr_staff');
        if(!empty($dept_no)){
            $map['staff_dept_no'] = $dept_no;
        }
        if(!empty($staff_name)){
            $map['staff_name'] = array('like','%'.$staff_name.'%');
        }
        $staff_list = M('hr_staff')->where($map)->limit($page,$pagesize)->select();
        $count =  M('hr_staff')->where($map)->count();
        $dept_name_arr = M('hr_dept')->getField('dept_no,dept_name');
        $post_name_arr = M('hr_post')->getField('post_no,post_name');
        foreach ($staff_list as &$staff) {
        	$staff['staff_name'] = "<a onclick='info(".$staff['staff_no'].','.$staff['staff_type'].")'  class=' fcolor-22 '>".$staff['staff_name']."</a>";
            if($staff['staff_sex'] == '1'){
                $staff['staff_sex'] = '男';
            }else{
                $staff['staff_sex'] = '女';
            }
            $staff['staff_dept_no'] = $dept_name_arr[$staff['staff_dept_no']];
            $staff['staff_post_no'] = $post_name_arr[$staff['staff_post_no']];
        	$confirm = 'onclick="if(!confirm(' . "'是否连账号一起删除？'" . ')){return false;}"';
        	$button="<a class='btn btn-xs red btn-outline' href='".U('hr_staff_do_del',array('staff_no'=>$staff['staff_no'],'staff_type'=>$staff['staff_type']))."'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
        	$staff['operate'] ="<a class='btn btn-xs blue btn-outline' href='" . U('hr_staff_edit', array('staff_no' => $staff['staff_no'],'staff_type'=>$staff['staff_type'])) . "'><i class='fa fa-edit'></i> 编辑</a>  ".$button;
        }
        $arr['data'] = $staff_list;
        $arr['count'] = $count;
		ob_clean();
		$staff_list = array(
			'code'=>0,
			'msg'=>'',
			'count'=>$count,
			'data'=>$staff_list
		);
		$this->ajaxReturn($staff_list); // 返回json格式数据
    }
    
    /**
     * @name:hr_staff_edit
     * @desc：人员添加/编辑，添加为_add
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_staff_edit()
    {
        checkAuth(ACTION_NAME);
        $staff_no = I('get.staff_no');
        $dept_no = I('get.dept_no');
        $hr_staff = M('hr_staff');
        if (! empty($staff_no)) {
            $staff = getstaffInfo($staff_no,'all');
            $staff['prefix_staff_no'] = $staff['staff_no'];
        }
        if(empty($staff['staff_no'])){
            if(!empty($dept_no)){
                $prefix_dept_no = $dept_no;
            } else {
                $dept_no_auth = session('dept_no_auth');//取本级及下级组织
                $dept_map['status'] = 1;
                $dept_map['dept_no'] = array('in',$dept_no_auth);
                $prefix_dept_no = M('hr_dept')->where($dept_map)->order('dept_no asc')->limit(1)->getField('dept_no');
            }
        	$staff['prefix_staff_no'] = getFlowNo($prefix_dept_no, 'hr_staff', 'staff_no', '4');
            $staff['dept_no'] = $prefix_dept_no;
        }
        $this->assign('staff', $staff);
        $this->display("Ccpstaff/hr_staff_edit");
    }

    /**
     * @name:hr_staff_info
     * @desc：人员详情
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_staff_info(){
        checkAuth(ACTION_NAME);
        $type = I('get.type');
        $this->assign('type',$type);
        $staff_no = I('get.staff_no');
        $hr_staff = M('hr_staff');
        if (! empty($staff_no)) {
            $staff = getStaffInfo($staff_no,'all');
            $staff['staff_post_no'] = getPost($staff['staff_post_no']);
            $staff['staff_dept_no'] = getDeptInfo($staff['staff_dept_no']);
            $staff['prefix_staff_no'] = $staff['staff_no'];
            $staff['staff_nation'] = getBdCodeInfo($staff['staff_nation'],'communist_nation_code');
            $staff['staff_diploma'] = getBdCodeInfo($staff['staff_diploma'],'diploma_level_code');
            $staff['staff_avatar'] = getUploadInfo($staff['staff_avatar']);
            $this->assign('staff',$staff);
        }
        $this->display("Ccpstaff/hr_staff_info");
    }
    /**
     * @name:hr_staff_do_save
     * @desc：人员执行操作
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_staff_do_save(){

        $staff_no = I('post.staff');	//判断添加还是修改
        $data = I("post.");
        $hr_staff = M('hr_staff');
        if (!empty($staff_no)) {//修改
        	$data['update_time'] = date('Y-m-d H:i:s');
            $comm_map['staff_no'] = $staff_no;
            $result = $hr_staff->where($comm_map)->save($data);
			$log_type_name='修改';//添加系统日志操作名称
            $log_type=2;//系统日志类型编号
        } else { 
        	//添加
            $staff_no=getFlowNo($data['staff_dept_no'], 'hr_staff', 'staff_no', '4');
            if (!empty($staff_no)) {
                if (! checkRepeat('hr_staff', 'staff_no', $data['staff_no'])) {
                    $data['add_time'] = date('Y-m-d H:i:s');
                    if(empty($data['staff_avatar'])){
                        $data['staff_avatar'] =null;
                    }
                    if(empty($data['staff_remindtime' ])){
                    	$data['staff_remindtime'] =null;
                    }
                    $data['staff_no'] = $staff_no;
                    $data['staff_initial'] = getFirstCharMul($data['staff_name']);
                    $data['add_staff'] = $this->staff_no;
                    $result = $hr_staff->add($data);
                    if($result){
                        savePeople($data['staff_no'],$data['staff_name'],$data['staff_idnumber'],2);//保存people表
                    }
                    $log_attach = 0 ;
                   	if(!empty($data['log_attach'])){
                   		$attach = $data['log_attach'];
                   		$log_attach =  str_replace('`','_',$data['log_attach']);
                   	}
                } else {
                    showMsg('error', '员工编号已存在！！,请重新添加','');
                }
            } else {
                showMsg('error', '必填字段为空！！,请重新添加','');
            }
        }
        if ($result) {
            //添加系统日志
            saveLog(ACTION_NAME,1,'','操作员['.getStaffInfo($this->staff_no).']于'.date("Y-m-d H:i:s").$log_type_name.'党员'.$fazh.'信息，编号为['.$staff_no.']，姓名为['.$data['staff_name'].']');
            showMsg('success', '操作成功！！！', U('Ccpstaff/hr_staff_index'),1);
        } else {
            showMsg('error', '操作失败！！！',U('Ccpstaff/hr_staff_index'));
        }
    }
   
     
    /**
     * @name:hr_staff_do_del
     * @desc：人员删除
     * @author：王宗彬
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function hr_staff_do_del(){

        checkAuth(ACTION_NAME);
        $staff_no = I('get.staff_no');
        $hr_staff = M('hr_staff');;
        if (! empty($staff_no)) {
            $comm_map['staff_no'] = $staff_no;
            $result = $hr_staff->where($comm_map)->delete();
        }
        if ($result) {
                $user_map['user_relation_no'] = $staff_no;
            	M('sys_user')->where($user_map)->delete();
            showMsg('success', '操作成功！！！',U('Ccpstaff/hr_staff_index'));
        } else {
            showMsg('error', '操作失败！！！','');
        }
    }
   
}
