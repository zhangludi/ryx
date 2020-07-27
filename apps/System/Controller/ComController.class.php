<?php
namespace System\Controller;
 // 命名空间
use Think\Controller;
use Common\Controller\BaseController;
class ComController extends BaseController // 继承Controller类
{
	/**
	 * @name  com_imail_outbox()
	 * @desc  内部邮件发件箱
	 * @param
	 * @return
	 * @author  王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   2017年10月20日
	 * @addtime   2016-07-26
	 */
	public function com_imail_outbox()
	{
		checkAuth(ACTION_NAME);
		$staff_no = session('staff_no');
		$keywords=I('post.keywords');
		$imail_list = getImailList($staff_no,3,'0');
		$count = count($imail_list);
		$this->assign('count',$count);
		$industry = getConfig('industry');
		$this->assign('industry', $industry);
		$this->assign('keywords',$keywords);
		$this->display("Comimail/com_imail_outbox");
	}
	/**
	 * @name  com_imail_outbox_data()
	 * @desc  内部邮件发件箱数据
	 * @param 
	 * @return 
	 * @author 王彬  王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   2017年10月20日 
	 * @addtime   2016-07-26
	 */
	public function com_imail_outbox_data()
	{
		$db_imail_outbox = M("com_imail_outbox");
		$staff_no = session('staff_no');
		$keywords = $_GET['keywords'];
		$page = I('get.page');
		$limit = I('get.limit');
		if($keywords){
			$where['imail_title'] = array('like','%'.$keywords.'%');
		}
		if(!checkDataAuth($staff_no)){
			$out_map['imail_sender'] = $staff_no;
			$out_map['add_staff'] = $staff_no;
			$out_map['_logic'] = 'or';
		}
		if(!empty($where) && !empty($out_map)){
			$where['_complex'] = $out_map;
		} else if(empty($where) && !empty($out_map)){
			$where = $out_map;
		}
		$imail_outbox_data = $db_imail_outbox->where($where)->order('add_time desc')->limit(($page-1)*$limit,$limit)->select();

		$count = $db_imail_outbox->where($where)->count();
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($imail_outbox_data as &$outbox) {
		    $imail_id  = $outbox['imail_id'];
			if(!empty($outbox['imail_attach'])){
				$outbox['imail_attach'] = '<i class="fa fa-paperclip">';
			}else{
				$outbox['imail_attach'] = '';
			}
			$communist_arr = explode(',',$outbox['imail_receivers']);
			$imail_receivers = "";
			foreach($communist_arr as $arr){
			    $imail_receivers .= $staff_name_arr[$arr].',';
			}
			
			$outbox['add_time'] = getFormatDate($outbox['add_time'], 'Y-m-d H:i');
			
			$outbox['imail_receivers'] = rtrim($imail_receivers,',');
			$outbox['imail_sender'] = $staff_name_arr[$outbox['imail_sender']];
			$outbox['imail_content'] = mb_substr($outbox['imail_content'], 0, 15, 'utf-8') . "...";
			$outbox['operate'] = "<a href='" . U('com_imail_do_del', array(
											'imail_id' => $outbox['imail_id'],'type'=>2
									)) . "'class='btn btn-xs red btn-outline'>删除</a>";
            $outbox['imail_title'] = "<a onclick='info(".$outbox['imail_id'].")' class='fcolor-22'>".$outbox['imail_title']."</a>";
		}
		ob_clean();
		$imail_outbox_data = ['code'=>0,'msg'=>0,'count'=>$count,'data'=>$imail_outbox_data];
		$this->ajaxReturn($imail_outbox_data);
	}
	
	/**
	 * @name  com_imail_inbox()
	 * @desc  内部邮件首页
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-26
	 */
	public function com_imail_inbox()
	{
		checkAuth(ACTION_NAME);
		$staff_no = session('staff_no');
		$imail_list = getImailList($staff_no,2,'0');
		$count = count($imail_list);
		$this->assign('count',$count);
		$industry = getConfig('industry');
		$this->assign('industry', $industry);
		$this->display("Comimail/com_imail_inbox");
	}
	/**
	 * @name  com_imail_inbox_data()
	 * @desc  内部邮件收件箱
	 * @param 
	 * @return 
	 * @author 王彬   王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   2017年10月20日
	 * @addtime   2016-07-26
	 */
	public function com_imail_inbox_data()
	{
		$staff_no = session('staff_no');
		$confirm = 'onclick="if(!confirm(' . "'确定公示该邮件？'" . ')){return false;}"';
		$page = I('get.page');
		$limit = I('get.limit');
		$keywords=I('get.keywords');
		$where = "1=1";
		if(!checkDataAuth($staff_no)){
		    $where .= " and FIND_IN_SET($staff_no,i.imail_receiver) or FIND_IN_SET($staff_no,o.imail_receivers)";
		}
		if($keywords){
			$where.=" and (o.imail_content like '%$keywords%' or o.imail_title like '%$keywords%')";
		}
		$imail_data = $imail_data = M('com_imail_inbox as i')->field("i.inbox_id,i.imail_receiver,o.imail_receivers,i.add_staff,i.add_time,i.is_read,o.imail_sender,o.imail_title,o.imail_content,o.imail_id")->join("sp_com_imail_outbox as o on i.imail_contentid = o.imail_id")->order('add_time desc')->where($where)->limit(($page-1)*$limit,$limit)->select();
		$count = M('com_imail_inbox as i')->join("sp_com_imail_outbox as o on i.imail_contentid = o.imail_id")->where($where)->limit(($page-1)*$limit,$limit)->count();
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($imail_data as &$imail) {
            $inbox_id = $imail['inbox_id'];
            $imail['imail_receivers'] = $staff_name_arr[$imail['imail_receiver']];
            $imail['imail_sender'] = $staff_name_arr[$imail['imail_sender']];
			
			$imail['add_time'] = getFormatDate($imail['add_time'],'Y-m-d');
			
            $imail['operate'] = "<a href='" . U('com_imail_do_del', array(
										'inbox_id' => $imail['inbox_id'],'type'=>1
								)) . "'class='btn btn-xs red btn-outline'>删除</a>";
            $imail['imail_title'] = "<a onclick='info(".$inbox_id.")' class='fcolor-22'>".$imail['imail_title']."</a>";
		}
		ob_clean();
		$imail_data = ['code'=>0,'msg'=>0,'count'=>$count,'data'=>$imail_data];
		$this->ajaxReturn($imail_data);
	}
	/**
	 * @name  com_imail_inbox_page_data()
	 * @desc  内部邮件收件箱-分页页数
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-13
	 */
	public function com_imail_inbox_page_data(){
		$db_imail_inbox = M();
		$staff_no = session('staff_no');
		$imail_sql = "select count(*) as count from sp_com_imail_outbox as o,sp_com_imail_inbox as inbox where inbox.imail_contentid = o.imail_id and o.status = 1 and inbox.imail_receiver = $staff_no and inbox.is_del = '0'";
		$inbox_count = $db_imail_inbox->query($imail_sql);
		$count = $inbox_count[0]['count'];
		$page = $count/6;
		if(is_int($page)){
			
		}else{
			$page = ceil($page);
		}
		ob_clean();$this->ajaxReturn($page);
	}
	/**
	 * @name  com_imail_inbox_count_data()
	 * @desc  内部邮件收件箱-分页总条数
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-13
	 */
	public function com_imail_inbox_count_data(){
		$db_imail_inbox = M();
		$staff_no = session('staff_no');
		$imail_sql = "select count(*) as count from sp_com_imail_outbox as o,sp_com_imail_inbox as inbox where inbox.imail_contentid = o.imail_id and o.status = 1 and inbox.imail_receiver = $staff_no and inbox.is_del = '0'";
		$inbox_count = $db_imail_inbox->query($imail_sql);
		$count = $inbox_count[0]['count'];
		ob_clean();$this->ajaxReturn($count);
	}
	/**
	 * @name  com_imail_edit()
	 * @desc  内部邮件编辑
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-04
	 */
	public function com_imail_edit()
	{
		checkAuth(ACTION_NAME);
		$com_imail_outbox = M('com_imail_outbox');
		$imail_id = I('get.imail_id');
		$staff_no = session('staff_no');
		$imail_list = getImailList($staff_no,2,'0');
		$count = count($imail_list);
		$this->assign('count',$count);
		if(!empty($imail_id)){
			$imail_map['imail_id'] = $imail_id;
			$outbox_data = $com_imail_outbox->where($imail_map)->find();
			$outbox_data['imail_sender'] = $outbox_data['imail_sender'].",";
			$this->assign('outbox_data',$outbox_data);
		}
		$this->display("Comimail/com_imail_edit");
	}
	/**
	 * @name  com_imail_do_save()
	 * @desc  内部邮件发送执行操作
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-04
	 */
	public function com_imail_do_save()
	{
		checkLogin();
		$post = $_POST;
		if (empty($post['people_type'])) {
			$post['people_type'] = 0;
		}
		$post['imail_receivers'] = rtrim($post['missive_receiver_no'],',');
		$post['imail_sender'] = session('staff_no');
		$post['imail_attach'] = $post['missive_attach'];
		$type = "1";
		$send_imail = setSendImail($post['imail_receivers'], $post['imail_content'], $post['imail_title'],$post['imail_attach'], $post['people_type'], $type,$post['imail_sender']);
		$url = U('com_imail_outbox');
		if ($send_imail) {
			$imail_title = "您有一封邮件未读！请查看！！";
			$alert_url = "System/Com/com_imail_info/type/1/inbox_id/".$send_imail;
			//U('com_imail_info',array('imail_id'=>$send_imail));
			saveAlertMsg('22', $post['imail_receivers'],$alert_url, $imail_title,'','', '', session('staff_no'));
			showMsg('success','发送成功！',$url);
		} else {
			showMsg('error','发送失败！');
		}
	}
	/**
	 * @name  com_imail_info()
	 * @desc  内部邮件详情
	 * @param 
	 * @return 
	 * @author 王彬  王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime  2017年10月20日 
	 * @addtime   2016-08-04
	 */
	public function com_imail_info()
	{
		checkAuth(ACTION_NAME);
		$db_imail = M("com_imail_outbox");
		$db_imail_inbox = M("com_imail_inbox");
		$imail_id = I('get.imail_id');
		$inbox_id = I('get.inbox_id');
		$this->assign('imail_id',$imail_id);
		$type = I('get.type');
		$staff_no = session('staff_no');
		$imail_list = getImailList($staff_no,2,'0');
		$count = count($imail_list);
		$this->assign('count',$count);
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		if($type == "2"){
			if (! empty($imail_id)) {
				$status = array(
						"is_read" => 1,
						"imail_read_time" => date("Y-m-d H:i:s")
				);
				$imail_map['imail_receiver'] = $staff_no;
				$imail_map['imail_contentid'] = $imail_id;
				$db_imail_inbox->where($imail_map)->save($status);
				$imail_data_map['imail_id'] = $imail_id;
				$imail_data = $db_imail->where($imail_data_map)->find();
				$imail_data['send_name'] = $staff_name_arr[$imail_data['imail_sender']];
				$staff_arr = strToArr($imail_data['imail_receivers']);
				$imail_receivers = "";
				foreach($staff_arr as $arr){
				    $imail_receivers .= $staff_name_arr[$arr].',';
				}
				$imail_data['imail_receivers'] = rtrim($imail_receivers,',');
				$imail_data['staff_avatar'] = getStaffInfo($staff_no,'staff_avatar');
				$upload_list = getUploadInfo($imail_data['imail_attach']);
				$upload_list = strToArr($upload_list);
				if($upload_list[0] != ""){
					$this->assign('upload_list',$upload_list);
				}
				$imail_attach = $imail_data['imail_attach'];
				if (! empty($imail_attach)) {
					$imail_attach = strToArr($imail_data['imail_attach'], ',');
					$imail_data['imail_attach'] = arrToStr($imail_attach, '`');
				}
				$imail_data['receive_name'] = $staff_name_arr[$imail_data['imail_receivers']];
			}
			$this->assign("imail_data", $imail_data);
		}else{
			if (!empty($inbox_id)){
				$imail_map['inbox_id'] = $inbox_id;
				$imail_data = M('com_imail_inbox as i')->field("i.inbox_id,i.imail_receiver,i.imail_receiver,i.add_staff,i.add_time,i.is_read,o.imail_sender,o.imail_title,o.imail_content,o.imail_id,o.imail_attach")->join("sp_com_imail_outbox as o on i.imail_contentid = o.imail_id")->order('add_time desc')->where($imail_map)->select();
				$imail_data[0]['imail_receivers'] = $staff_name_arr[$imail_data[0]['imail_receiver']];
				$imail_attach = $imail_data['imail_attach'];
				if (! empty($imail_attach)) {
					$imail_attach = strToArr($imail_data['imail_attach'], ',');
					$imail_data['imail_attach'] = arrToStr($imail_attach, '`');
				}
			}
			$this->assign("imail_data", $imail_data[0]);
		}
		$inbox = I('get.inbox');
		$outbox = I('get.outbox');
		$this->assign('inbox',$inbox);
		$this->assign('outbox',$outbox);
		
		$this->display("Comimail/com_imail_info");
	}
	/**
	 * @name  com_imail_isread_edit()
	 * @desc  内部邮件批量修改为已读状态
	 * @param 
	 * @return 
	 * @author 王彬 
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-04
	 */
	public function com_imail_isread_edit(){
		checkLogin();
		$com_imail_inbox = M('com_imail_inbox');
		$imail_receivers = I('get.imail_receivers');
		$imail_receivers = rtrim($imail_receivers,',');
		$staff_no = session('staff_no');
		$imail_receivers = strToArr($imail_receivers);
		$data['is_read'] = 1;
		foreach($imail_receivers as $receiver){
			$inbox_map['imail_receiver'] = $staff_no;
			$inbox_map['imail_contentid'] = $receiver;
			$imail_inbox_data = $com_imail_inbox->where($inbox_map)->save($data);
		}
		ob_clean();$this->ajaxReturn($imail_inbox_data);
	}
	/**
	 * @name  com_imail_inbox_do_del()
	 * @desc  内部邮件删除操作-收信箱
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-04
	 */
	public function com_imail_inbox_do_del()
	{
		checkAuth(ACTION_NAME);
		$com_imail_inbox = M('com_imail_inbox');
		$imail_receivers = I('get.imail_receivers');
		$imail_receivers = rtrim($imail_receivers,',');
		$staff_no = session('staff_no');
		$imail_receivers = strToArr($imail_receivers);
		foreach($imail_receivers as $receiver){
			$inbox_map['imail_receiver'] = $staff_no;
			$inbox_map['imail_contentid'] = $receiver;
			$imail_inbox_data = $com_imail_inbox->where($inbox_map)->save($data);
		}
		$inbox = I('get.inbox');
		if(!empty($inbox)){
			if($imail_inbox_data){
				showMsg('success','操作成功！',U('com_imail_inbox'));
			}else{
				showMsg('error','操作失败！');
			}
		}else{
			ob_clean();$this->ajaxReturn($imail_inbox_data);
		}
	}
	/**
	 * @name  com_imail_outbox_do_del()
	 * @desc  内部邮件删除操作-发信箱
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-04
	 */
	public function com_imail_do_del()
	{
		checkAuth(ACTION_NAME);
		$com_imail_outbox = M('com_imail_outbox');
		$com_imail_inbox = M('com_imail_inbox');
		$type = I('get.type');
		if($type == "2"){
			$imail_id = I('get.imail_id');
			$outbox_map['imail_id'] = $imail_id;
			$imail_data = $com_imail_outbox->where($outbox_map)->delete();
			$inbox_map['imail_contentid'] = $imail_id;
			$com_imail_inbox->where($inbox_map)->delete();
		}else {
			$inbox_id = I('get.inbox_id');
			$inbox_map['inbox_id'] = $inbox_id;
			$imail_data = $com_imail_inbox->where($inbox_map)->delete();
		}
		if($imail_data){
			if($type == "2"){
				showMsg('success','操作成功！',U('com_imail_outbox'));
			}else{
				showMsg('success','操作成功！',U('com_imail_inbox'));
			}
		}else{
			showMsg('error','操作失败！');
		}
	}
	/**
	 * @name  com_imail_receiveUser()
	 * @desc  部门,角色,分组显示列表
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-10-19
	 */
	public function com_imail_receiveUser()
	{
		$name = I('get.field_name');
		//部门
		$staff_dept_no = getStaffInfo(session('staff_no'),'staff_dept_no');
		$dept_list = getDeptChildNos($staff_dept_no,'str',1,'is_admin');
		$dept_list = strToArr($dept_list, ',');
		foreach($dept_list as &$dept){
			$dept = getDeptInfo($dept ,all);
		}
		$this->assign('dept_list',$dept_list);
		//职务
		$party_list = M('ccp_party')->field('party_no,party_pno,party_name')->select();
		$this->assign('party_list',$party_list);
		//分组
		$group_list = M('ccp_group')->select();
		$this->assign('group_list',$group_list);
		$this->assign('name',$name);
		$type = I('get.type');  //有type 为第一书记
		$this->assign('type',$type);

		$group = I('get.group');  
		$this->assign('group',$group);

		$hide = I('get.hide');  //不显示人员分组
		$this->assign('hide',$hide);
		$this->display("Comimail/com_imail_receiveUser");
	}
	
	/**
	 * @name  com_imail_receiveUser_data()
	 * @desc  获取当前部门及子级部门员工
	 * @param 
	 * @return 
	 * @author 王彬    王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   2017-10-19
	 * @addtime   2016-08-01
	 */
	public function com_imail_receiveUser_data(){
		$dept_no = I('get.staff_dept_no');//点击的党支部编号
		$party_no = I('get.party_no');	//点击的角色ID						
		$group_id = I('get.group_id');	//点击的分组ID
		if(!empty($dept_no)){   	//获取党支部里面的人员
		    $staff_dept=getDeptChildNos($dept_no,'str','1');
		    $comm_map['staff_dept_no'] = array('in',$staff_dept);
		    $staff_list=M('hr_staff')->where($comm_map)->select();
		}
		if(!empty($party_no)){
			$comm_map['party_no'] = $party_no;
			$comm_map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
			$staff_list = M('ccp_communist')->field('communist_no,communist_name')->where($comm_map)->select();
		}
		if(!empty($group_id)){
			$comm_map['group_staff_id'] = $group_id;
			$staff_no = M('ccp_group_staff')->where($comm_map)->getField('staff_no',true);
			$map['staff_no'] = array('in', $staff_no);
			$staff_list = M('hr_staff')->where($map)->select();
		}
		
		ob_clean();$this->ajaxReturn($staff_list);
	}
	/**
	 * @name  com_imail_staff_data()
	 * @desc  获取员工信息
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime  2017-10-19
	 */
	public function com_imail_staff_data(){
		$staff_no = I('get.staff_no');
		$staff_data = getStaffInfo($staff_no,'all');
		if($staff_data['staff_sex'] == 1){
			$staff_data['staff_sex'] = "男";
		}else{
			$staff_data['staff_sex'] = "女";
		}
		ob_clean();$this->ajaxReturn($staff_data);
	}
	/**
	 * @name  com_imail_table()
	 * @desc  站内邮件表格页面(员工类型传值)
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime  2017-10-19
	 */
	public function com_imail_table()
	{
		$db_staff = M("hr_staff");
		$dept_id = I('get.dept_id');
		$type_id = I('get.type_id');
		$supplier_id = I('get.supplier_id');
		$type = I('get.type');
		
		$this->assign("type", $type);
		if (! empty($dept_id)) {
			$this->assign("dept_id", $dept_id);
		}
		if (! empty($type_id)) {
			$this->assign("type_id", $type_id);
		}
		if (! empty($supplier_id)) {
			$this->assign("supplier_id", $supplier_id);
		}
		$this->display("Comimail/com_imail_table");
	}
	/**
	 * @name  com_imail_table_data()
	 * @desc  站内邮件表格数据页面(员工类型传值)
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime  2017-10-19
	 */
	public function com_imail_table_data()
	{
		$db_staff = M();
		$dept_id = I('get.dept_id');
		$comm_map['dept_no'] = $dept_id;
		$staff_data = $db_staff->where($comm_map)->select();
		foreach ($staff_data as &$staff) {
			switch ($staff['staff_sex']) {
				case 1:
					$staff['staff_sex'] = "男";
					break;
				case 0:
					$staff['staff_sex'] = "女";
					break;
			}
		}
		ob_clean();$this->ajaxReturn($staff_data);
	}
	/**
	 * @name  com_imail_supplier_table_data()
	 * @desc  站内邮件表格数据页面(供应商类型传值)
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime  2017-10-19
	 */
	public function com_imail_supplier_table_data()
	{
		$db_supplier = M("crm_supplier");
		$type = I('get.type');
		$supplier_id = I('get.supplier_id');
		$supplier_map['supplier_type'] = $supplier_id;
		$staff_data = $db_supplier->where($supplier_map)->select();
		$staff_data['staff_name'] = $staff_data['supplier_name'];
		ob_clean();$this->ajaxReturn($staff_data);
	}
}