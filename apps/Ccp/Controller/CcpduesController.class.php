<?php
/******************   党费管理      *****************************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
class CcpduesController extends BaseController{
    /**
     * @name:ccp_dues_index
     * @desc：党费管理首页
     * @author：靳邦龙
     * @addtime:2017-10-10
     * @version：V1.0.0
     **/
    public function ccp_dues_index(){
        checkAuth(ACTION_NAME);
        $month=date("Y-m");
        $this->assign('month',$month);
        $party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
        $this->assign('party_list',$party_list);
        $party_no = I('get.party_no');
        if(!empty($party_no)){
            $this->assign('party_no',$party_no); 
        }else{
             $this->assign('party_no',$party_list[0]['party_no']);
        }
        $this->display("Ccpdues/ccp_dues_index");
    }
   /**
     * @name:ccp_dues_list_data
     * @desc：党费列表数据读取
     * @author：靳邦龙
     * @addtime:20171010
     * @version：V1.0.0
     **/
    public function ccp_dues_list_data(){
        $communist_no=I('get.communist_no');
        $communist_name=I('get.communist_name');
        $dues_month=I('get.dues_month');
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        
        $status=I('get.status');
        $party_no = I('get.party_no');
        $db_dues=M("ccp_dues");
        if(!empty($communist_no)){
            $duse_map['communist_no'] = $communist_no;
        }
        if(!empty($party_no)){
            $party_nos=getPartyChildNos($party_no);
        } else {
            $party_nos = session('party_no_auth');//取本级及下级组织
        }
        $duse_map['party_no'] = array('in',$party_nos);
        if(!empty($dues_month)){
            $duse_map['_string'] = "DATE_FORMAT(dues_time, '%Y-%m') = '$dues_month'";
        }
        if(!empty($status)){
            $duse_map['status'] = $status;
        }
        if(!empty($communist_name)){
            $duse_map['communist_name']  = array('like', '%'.$communist_name.'%');
        }
        $dues_list=$db_dues->where($duse_map)->limit($page,$pagesize)->order('add_time desc')->select();
        $count=$db_dues->where($duse_map)->count();
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $status_map['status_group'] = 'dues_status';
        $status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
        if(!empty($dues_list)){
            foreach($dues_list as &$dues){
                $dues['status_name'] = "<font color='" . $status_color_arr[$dues['status']] . "'>" . $status_name_arr[$dues['status']] . "</font> ";//getStatusName('dues_status', );
               // $dues['operate']="<a class='btn btn-xs red btn-outline' href='" . U('ccp_dues_do_del',array('dues_id' => $dues['dues_id'])) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>";
                $dues['update_time'] = getFormatDate($dues['update_time'],"Y-m-d");
            }
            $arr['data'] = $dues_list;
            $arr['count'] = $count;
            $arr['code'] = 0;
            $arr['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($arr);  
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($arr);  
        }
        
    }
	/**
	* @name:ccp_dues_edit
	* @desc：添加
	* @author：靳邦龙
	* @addtime:20171010
	* @version：V1.0.0
	**/
	public function ccp_dues_edit(){
		
		//获取党组织列表
		$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
       // print_r($party_list);
		$this->assign('party_list',$party_list);
		
		
		$this->display("Ccpdues/ccp_dues_edit");
	}
	
	//根据ajax 获取人员列表的数据
	public function communist_no_list_ajax(){
		$db_dues=M("ccp_dues");
        $data=I('post.');
		//print_r($data);
		if($data['party_no'] == ''){
			return false;
		}
		$getCommunistSelect = getCommunistSelect('',$data['party_no']);
		//print_r($getCommunistSelect);
		ob_clean();$this->ajaxReturn($getCommunistSelect); 
		
	}
	/**
	* @name:ccp_dues_status
	* @desc：添加
	* @author：靳邦龙
	* @addtime:20171010
	* @version：V1.0.0
	**/
	public function ccp_dues_status(){
		$dues_id=I('get.dues_id');
        $duse_map['dues_id'] = $dues_id;
		$data['status'] = 2;
		$dues_res = M('ccp_dues')->where($duse_map)->save($data);	
		if ($dues_res){
			showMsg('success', '缴纳成功', U('ccp_dues_index'));
		}else{
			showMsg('error', '缴纳失败！');
		}
	}
	
	
   /**
     * @name:ccp_dues_do_save
     * @desc：党费数据保存方法
     * @author：靳邦龙
     * @addtime:20171010
     * @version：V1.0.0
     **/
    public function ccp_dues_do_save(){
        $db_dues=M("ccp_dues");
        $data=I('post.');
		
        $map['dues_month'] = $data['dues_month'];
        $map['communist_no'] = $data['communist_no'];
        $res = $db_dues->where($map)->find();
        $title = getCommunistInfo($data['communist_no']).'本月缴费信息已存在，不可重复录入！';
        if(!empty($res)){
            showMsg('error',$title);
        }
        $data['add_staff']=session('staff_no');//添加人
        $data['add_staff_name']=M('hr_staff')->where("staff_no = ".$data['add_staff'])->getField('staff_name');//添加人姓名
       // $data['party_no']=getCommunistInfo($data['communist_no'],'party_no');//缴费人部门
        $data['party_name']=getPartyInfo($data['party_no'],'party_name');//缴费人部门名称
       $data['communist_name']=getCommunistInfo($data['communist_no']);//缴费人姓名
        $data['add_time']=date("Y-m-d H:i:s");
        $data['update_time']=date("Y-m-d H:i:s");
       
        if(!trim($data['dues_time'])){
            $data['dues_time']=null;
        }
        $dues_res=$db_dues->add($data);
		if ($dues_res){
			showMsg('success', '操作成功', '', 1);
		}else{
			showMsg('error', '数据有误，操作失败！');
		}
    }
    /**
     * @name:ccp_dues_list_export（）
     * @desc：流水导出操作
     * @author：靳邦龙
     * @addtime:2017-10-10
     * @version：V1.0.0
     **/
    public function ccp_dues_list_export(){
		$communist_no=I('get.communist_no','');
        $dues_month=I('get.dues_month','');
        $status=I('get.status','');
        $party_no = I('get.party_no');
        
        $db_dues=M("ccp_dues");
        $where='1=1';
        if(!empty($communist_no)){
            $where['communist_no'] = $communist_no;
        }
        if(!empty($party_no)){
            $where['party_no'] = $party_no;
        }
        if(!empty($dues_month)){
            $where['dues_month'] = $dues_month;
        }
        if(!empty($status)){
            $where['status'] = $status;
        }
        $status_name_arr = M('bd_status')->where("status_group = 'dues_status'")->getField('status_no,status_name');
        $dues_list=$db_dues->where($where)->field("communist_name,party_name,dues_time,dues_amount,add_staff_name,add_time,status")->select();
        foreach($dues_list as &$dues){
            $dues['status'] = $status_name_arr[$dues['status']];
        }
		$head['communist_name']='缴纳人';
		$head['party_name']='所属党组织';
		$head['dues_time']='缴纳时间';
		$head['dues_amount']='缴纳金额';
		$head['add_staff_name']='操作人';
		$head['add_time']='操作时间';
		$head['status']='缴纳状态';
		exportExcel('党费缴纳记录',$head,$dues_list);
   }
    /**
     * @name:ccp_dues_list_import（）
     * @desc：流水导入操作
     * @author：刘丙涛
     * @addtime:2017-05-27
     * @version：V1.0.0
     **/
    public function ccp_dues_list_import(){
        $file = $_FILES['file_free'];
        if (!empty($file['tmp_name']))
        {
            $upload = new \Think\Upload();
            $upload->maxSize = 0; // 不限制附件上传大小
            $upload->exts = array('xls','xlsx'); //
            $upload->rootPath = C('TMPL_PARSE_STRING')['__UPLOAD_PATH__']; // 上传文件所在文件夹
            $upload->savePath = 'ccp/fa/xls/'; // 设置附件上传目录
            $upload->autoSub = true; // 开启子目录保存
            $info = $upload->upload();
            if (!$info) {
                $this->error($upload->getError());
            }
            import("Org.Util.PHPExcel");
            //Vendor('PHPExcel.PHPExcel.IOFactory');
            import("Org.Util.PHPExcel.IOFactory");
            $objPHPExcel = new \PHPExcel();
            $file_name= C('TMPL_PARSE_STRING')['__UPLOAD_PATH__'].$info['file_free']['savepath'].$info['file_free']['savename'];
            
            $extension = strtolower( pathinfo($file_name, PATHINFO_EXTENSION) );
            if ($extension =='xlsx') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            } else if ($extension =='xls') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            }
            $path=BASE_PATH.$file_name;
            try {
                $objPHPExcel = $objReader->load($path);
            } catch (Exception $e) {
                showMsg('error', '文件格式与扩展名不匹配，请检查后重新上传！');
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            $db_dues=M("ccp_dues");
            for($i=2;$i<=$highestRow;$i++) {
                if(!empty($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))
                {
                    $dues_month =$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();//年月
                    $communist_no = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();//员工编号
                    $dues_amount = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();//金额
                    //$dues_content = getExcelTime($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue());//备注
                    $dues_content = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();//备注
                    
                    

                    $map['dues_month'] = $dues_month;
                    $map['communist_no'] = $communist_no;
                    $res = $db_dues->where($map)->find();

                    $data['dues_amount']=$dues_amount;//缴纳金额
                    $data['dues_content']=$dues_content;//内容
                    $data['update_time']=date("Y-m-d H:i:s");
                    $data['status']=1;
                    if(!empty($res)){
                        $res1 = $db_dues->where($map)->save($data);
                    }else{
                        $data['add_time']=date("Y-m-d H:i:s");
                        $data['dues_month']=$dues_month;//年月
                        $data['communist_no']=$communist_no;//党员
                        $data['add_staff']=session('staff_no');//添加人
                        $staff_map['staff_no'] = $data['add_staff'];
                        $data['add_staff_name']=M('hr_staff')->where($staff_map)->getField('staff_name');;//添加人姓名
                        $data['party_no']=getCommunistInfo($communist_no,'party_no');//缴费人部门
                        $data['party_name']= getPartyInfo($data['party_no'],'party_name');//缴费人部门名称
                        $data['communist_name']=getCommunistInfo($communist_no);//缴费人姓名
                        $res = $db_dues->add($data);
                    }
                    // if(checkRepeat('ccp_dues', 'dues_month', $dues_month, 'communist_no', $communist_no)){
                    //     showMsg('error',$communist_no.'编号重复，请检查后重新导入');
                    // }
                    //$dues[]=$data;
                }
            }
            //$dues_res=$db_dues->addAll($dues);
            if($res || $res1){
                showMsg(success, '导入成功',U('ccp_dues_index'));
            }else{
                showMsg(error, '导入失败');
            }
        }else{
            showMsg(error, '导入的文件为空，请从新导入数据');
        }
    }
 /**
     * @name       ccp_dues_summary（）
     * @desc       党费汇总
     * @author     靳邦龙
     * @addtime    2017-05-27
     * @version    V1.0.0
     **/
    public function ccp_dues_summary(){
        //checkAuth(ACTION_NAME);
        $db_dues=M("ccp_dues");
        $db_party = M("ccp_party");
        $dues_month=I('request.dues_month',date("Y-m"));
        $party_list=A('Ccp/ccpcommunist')->getPartyLists();
        foreach ($party_list as &$list){
            $party_nos=getPartyChildNos($list['party_no']);
            //当月全部
            $dues_map['party_no'] = array('in',$party_nos);
            $dues_map['dues_month'] = $dues_month;
           $all_arr = $db_dues->where($dues_map)->field("SUM(dues_amount) as dues_amount,count(*) as dues_communist")->find();
           $dues_map['status'] = 2;
           $had_arr= $db_dues->where($dues_map)->field("SUM(dues_amount) as dues_amount,count(*) as dues_communist")->find();
           if(!$had_arr['dues_amount']){
               $had_arr['dues_amount']=0;
           }
           if(!$all_arr['dues_amount']){
               $all_arr['dues_amount']=0;
           }
           if(!$had_arr['dues_communist']){
               $had_arr['dues_communist']=0;
           }
           if(!$all_arr['dues_communist']){
               $all_arr['dues_communist']=0;
           }
           $list['amount_rate']=$had_arr['dues_amount'].'/'.$all_arr['dues_amount'];//缴费比例
           $list['communist_rate']=$had_arr['dues_communist'].'/'.$all_arr['dues_communist'];//人数比例
        }
        $this->assign('month',$dues_month);
        $this->assign('party_list',$party_list);
        $this->display("Ccpdues/ccp_dues_summary");
    }
    /**
     * @name:ccp_dues_summary_export（）
     * @desc：汇总导出操作
     * @author：刘丙涛
     * @addtime:2017-5-26
     * @version：V1.0.0
     **/
    public function ccp_dues_summary_export(){
        $db_log=M('fa_bankaccount_log');
        $party_list = getPartyChildNos(0);
        foreach ($party_list as $item){
            $log_map['memo'] = $item['party_no'];
            $sum_cost = $db_log->where($log_map)->sum('log_amount');
            $list['party_no'] = $item['party_no'];
            $list['party_name'] = $item['party_name'];
            $list['sum_cost'] = $sum_cost;
            $log_array[] = $list;
        }
        $head['party_no']='编号';
        $head['party_name']='支部名称';
        $head['sum_cost']='缴纳金额';
        exportExcel('党费缴纳汇总',$head,$log_array);
    }
    /**
     * @name:ccp_dues_do_del
     * @desc：删除
     * @author：靳邦龙
     * @addtime:20171211
     * @version：V1.0.0
     **/
    public function ccp_dues_do_del(){
        $db_dues=M("ccp_dues");
        $dues_id=I('get.dues_id');
        $duse_map['dues_id'] = $dues_id;
        $dues_res=$db_dues->where($duse_map)->delete();
        if ($dues_res){
            showMsg('success', '操作成功', U('ccp_dues_index'));
        }else{
            showMsg('error', '数据有误，操作失败！');
        }
    }

}
