<?php
namespace System\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class BdController extends BaseController
{
	
	/**
	 * @name  bd_ad_list()
	 * @desc  广告列表页面
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_ad_list()
	{
		checkAuth(ACTION_NAME);
		$location_id = I('get.location_id');
		if(!empty($location_id)) {
			$this->assign("location_id", $location_id);
		}
		
		$this->display('Bd/bd_ad_list');
	}

	/**
	 * @name  bd_ad_list_data()
	 * @desc  广告数据列表
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_ad_list_data()
	{
		$location_id = I('get.location_id');
		$ad_data = getAdList($location_id);
		$data['data'] = $ad_data;
		$data['count'] = 0;
		$data['code'] = 0;
		$data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($data);
	}
	/**
	 * @name  bd_ad_edit()
	 * @desc  广告编辑/添加
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_ad_edit()
	{
		checkAuth(ACTION_NAME);
		$ad_id = I("get.ad_id");
		$ad_location = I("get.ad_location");
		if(!empty($ad_location)) {
			$ad_data['ad_location'] = $ad_location;
			$this->assign('ad_location', $ad_location);
		}
		if(!empty($ad_id)) {
			$ad_data = getAdInfo($ad_id);
		}
		$this->assign('ad_data', $ad_data);
		$this->display('Bd/bd_ad_edit');
	}
	/**
	 * @name  bd_ad_do_save()
	 * @desc  广告编辑/添加操作执行
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_ad_do_save()
	{
		checkLogin();
		$db_ad = M("sys_ad");
		$_POST['update_time'] = date("Y-m-d H:i:s");
		if(!empty($_POST["ad_id"])) {
			$ad_map['ad_id'] = $_POST['ad_id'];
			$ad_data = $db_ad->where($ad_map)->save($_POST);
		}else {
			$_POST['add_time'] = date("Y-m-d H:i:s");
			$_POST['add_staff'] = session('staff_no');
			$ad_data = $db_ad->add($_POST);
		}
		if ($ad_data) {
			echo "<script>alert('操作成功');location.href='" . U('bd_ad_list') . "'; parent.location.reload();</script>";
		} else {
			echo "<script>alert('操作失败');location.href='" . U('bd_ad_list') . "'; parent.location.reload();</script>";
		}
	}
	/**
	 * @name  bd_ad_del()
	 * @desc  广告删除操作
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-22
	 */
	public function bd_ad_del()
	{
		checkAuth(ACTION_NAME);
		$db_ad = M("sys_ad");
		$ad_id = I('get.ad_id');
		if(!empty($ad_id)) {
			$ad_map['ad_id'] = $ad_id;
			$ad_data = $db_ad->where($ad_map)->delete();
		}
		if ($ad_data){
			delFile($ad_dt['ad_img']);
			showMsg('success','操作成功！',U('bd_ad_list',array('location_id'=>$ad_dt['ad_location'])));
		} else {
			showMsg('error');
		}
	}
	/**
	 * @name  bd_localtion()
	 * @desc  广告位列表
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-11-13
	 */
	public function bd_location()
	{
	
		$this->display('Bd/bd_location');
	}
	/**
	 * @name  bd_localtion_data()
	 * @desc  广告位数据列表
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_localtion_data()
	{

		$db_location = M("sys_ad_location");
		$confirm = 'onclick="if(!confirm(' . "'确认删除？当前广告位下的所有广告也将被删除！'" . ')){return false;}"';
		$location_data = $db_location->where('status = 1')->select();
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ( $location_data as &$location ) {
		    $location ['add_staff'] = $staff_name_arr[$location ['add_staff']];

			$location ['ad_list'] = "<a href='" . U ( 'bd_ad_list', array (
					'location_id' => $location ['location_id'] 
			) ) . "'class=' layui-btn  layui-btn-xs layui-btn-f60' style='text-decoration: none;'>图片列表</a>";
			$location ['operate'] = "<a href='javascript:void(0)' class=' layui-btn  layui-btn-xs layui-btn-f60' target='_self' style='text-decoration: none;' onclick='select_receive_user(" . $location ['location_id'] . ")'><i class='fa fa-edit'></i>编辑</a>";
		}
		$data['data'] = $location_data;
		$data['count'] = 0;
		$data['code'] = 0;
		$data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($data);
	}
	/**
	 * @name  bd_location_edit()
	 * @desc  广告位编辑/添加页面
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_location_edit()
	{
		checkAuth(ACTION_NAME);
		$location_id = I('get.location_id');
		if(!empty($location_id)){
			$location_data = getLocationInfo($location_id);
			$this->assign('location_data', $location_data);
		}
		$this->display('Bd/bd_location_edit');
	}
	/**
	 * @name  bd_location_do_save()
	 * @desc  广告位编辑/添加执行
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_location_do_save()
	{
		checkLogin();
		$post = I('post.');
		$db_location = M('sys_ad_location');
		if(!empty($post['location_id'])) {
			$post['update_time'] = date('Y-m-d H:i:s');
			$location_map['location_id'] = $post['location_id'];
			$location_data = $db_location->where($location_map)->save($post);
		}else{
			$post['add_staff'] = session('staff_no');
			$post['add_time'] = date('Y-m-d H:i:s');
			$post['update_time'] = date('Y-m-d H:i:s');
			$location_data = $db_location->add($post);
		}
		if ($location_data) {
			echo "<script>alert('操作成功');location.href='" . U('bd_location') . "'; parent.location.reload();</script>";
		} else {
			echo "<script>alert('操作失败');location.href='" . U('bd_location') . "';parent.location.reload();</script>";
		}
	}
	/**
	 * @name  bd_location_do_del()
	 * @desc  广告位删除操作
	 * @param 
	 * @return 
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-13
	 */
	public function bd_location_do_del(){
		checkAuth(ACTION_NAME);
		$bd_ad_location = M('sys_ad_location');
		$bd_ad = M('sys_ad');
		$location_id = I('get.location_id');
		$location_map['location_id'] = $location_id;
		$location_data = $bd_ad_location->where($location_map)->delete();
		if($location_data){
			$ad_location_map['ad_location'] = $location_id;
			$ad_list = $bd_ad->where($ad_location_map)->select();
			foreach($ad_list as $list){
				$ad_map['ad_id'] = $list['ad_id'];
				$ad_dt = $bd_ad->where($ad_map)->delete();
				delFile($list['ad_img']);
			}
			showMsg('success','操作成功！',U('bd_location'));
		}else{
			showMsg('error');
		}
	}
	
	public function bd_banner_info(){
		$this->display();
	}
	/******************门户导航维护开始**********************************************/
	
	/**
	 * @name  bd_nav_index()
	 * @desc  导航列表
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_nav_index(){
	    $this->display();
	}
	/**
	 * @name  bd_nav_index_data()
	 * @desc  导航列表数据获取
	 * @return json
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_nav_index_data(){
	    $db_nav=M("sys_nav");
	    $nav_name = I('get.nav_name');
	    if(!empty($nav_name)){
	    	$where['nav_name'] = array('like',"%$nav_name%");
	    }
	    $nav_list=$db_nav->where($where)->select();
	    $confirm = 'onclick="if(!confirm(' . "'是否启用？'" . ')){return false;}"';
	    $confirm1 = 'onclick="if(!confirm(' . "'是否停用？'" . ')){return false;}"';
	    foreach($nav_list as &$nav){
	        $nav['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='javascript:;'  onclick='nav_edit(".$nav['nav_id'].")'>编辑</a>";
	        if($nav['status']==1){
	        	$nav['status_name'] ="<font color='#00FF00'>启用</font>";
	           $nav['operate'].= " <a class='layui-btn layui-btn-del layui-btn-xs' style='text-decoration: none;' href='" . U('bd_nav_do_del', array('nav_id' => $nav['nav_id'],'status'=> $nav['status']))."' $confirm1>停用 </a>";
	        }else{
	        	$nav['status_name'] ="<font color='red'>停用</font>";
	           $nav['operate'].= " <a class='layui-btn layui-btn-del layui-btn-xs' style='text-decoration: none;' href='" . U('bd_nav_do_del', array('nav_id' => $nav['nav_id'],'status'=> $nav['status']))."' $confirm>启用 </a>";
	        }

	    } 
		$nav_list_arr['code'] = 0;
		$nav_list_arr['msg'] = 0;
		$nav_list_arr['count'] = 0;
		$nav_list_arr['data'] = $nav_list;
	    ob_clean();$this->ajaxReturn($nav_list_arr);
	}
	/**
	 * @name  bd_nav_edit()
	 * @desc  导航编辑
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_nav_edit(){
	    $db_nav=M("sys_nav");
	    $nav_id=I('get.nav_id');
	    if($nav_id){
	    	$nav_map['nav_id'] = $nav_id;
	        $nav_row=$db_nav->where($nav_map)->find();
	        $this->assign('nav_row',$nav_row);
	    }
	    $this->display();
	}
	/**
	 * @name  bd_nav_do_save()
	 * @desc  导航保存执行
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_nav_do_save(){
	    $db_nav=M("sys_nav");
	    $nav_id=I('post.nav_id');
	    $nav=I('post.');
	    if($nav_id){
	        $nav['update_time']=date("Y-m-d H:i:s");
	        $nav_map['nav_id'] = $nav_id;
	        $nav_res=$db_nav->where($nav_map)->save($nav);
	    }else{
	        $nav['add_staff']=session('staff_no');
	        $nav['add_time']=date("Y-m-d H:i:s");
	        $nav['update_time']=date("Y-m-d H:i:s");
	        $nav['status']=1;
	        $nav_res=$db_nav->add($nav);
	    }
	    if($nav_res){
	        showMsg('success', '操作成功！',U('bd_nav_index'),1);
	    }else{
	        showMsg('error', '操作失败！');
	    }
	}
	/**
	 * @name  bd_nav_do_del()
	 * @desc  导航停用或启用
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_nav_do_del(){
	    $db_nav=M("sys_nav");
	    $nav_id=I('get.nav_id');
	    $status=I('get.status');
	    if($nav_id){
	        if($status==1){
	            $nav['status']=0;
	        }else{
	            $nav['status']=1;
	        }
	        $nav['update_time']=date("Y-m-d H:i:s");
	        $nav_map['nav_id'] = $nav_id;
	        $nav_res=$db_nav->where($nav_map)->save($nav);
	    }
	    if($nav_res){
	        showMsg('success', '操作成功！',U('bd_nav_index'));
	    }else{
	        showMsg('error', '操作失败！');
	    }
	}
	/******************门户导航维护结束**********************************************/
	/******************门户友情链接维护开始**********************************************/
	
	/**
	 * @name  bd_blogroll_index()
	 * @desc  友情链接列表
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_blogroll_index(){
	    $this->display();
	}
	/**
	 * @name  bd_blogroll_index_data()
	 * @desc  友情链接列表数据获取
	 * @return json
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_blogroll_index_data(){
	    $db_blogroll=M("sys_blogroll");
	    $blogroll_name = I('get.blogroll_name');
	    if(!empty($blogroll_name)){
	    	$where['blogroll_name'] = array('like',"%$blogroll_name%");
	    }
	    $blogroll_list=$db_blogroll->where($where)->select();
	    $confirm = 'onclick="if(!confirm(' . "'是否启用？'" . ')){return false;}"';
	    $confirm1 = 'onclick="if(!confirm(' . "'是否停用？'" . ')){return false;}"';
	    foreach($blogroll_list as &$blogroll){
	    	$blogroll['add_staff'] = getStaffInfo($blogroll['add_staff']);
	        $blogroll['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='javascript:;' onclick='blogroll_edit(".$blogroll['blogroll_id'].")'>编辑</a>";
	        if($blogroll['status']==1){
	        	$blogroll['status_name'] ="<font color='#00FF00'>启用</font>";
	           $blogroll['operate'].= " <a class='layui-btn layui-btn-del layui-btn-xs' style='text-decoration: none;' href='" . U('bd_blogroll_do_del', array('blogroll_id' => $blogroll['blogroll_id'],'status'=> $blogroll['status']))."' $confirm1> 停用 </a>";
	        }else{
	        	$blogroll['status_name'] ="<font color='red'>停用</font>";
	           $blogroll['operate'].= " <a class='layui-btn layui-btn-del layui-btn-xs' style='text-decoration: none;' href='" . U('bd_blogroll_do_del', array('blogroll_id' => $blogroll['blogroll_id'],'status'=> $blogroll['status']))."' $confirm>启用 </a>";
	        }
	    }
	    $blogroll_list_arr['code'] = 0;
	    $blogroll_list_arr['count'] = 0;
	    $blogroll_list_arr['msg'] = 0;
	    $blogroll_list_arr['data'] = $blogroll_list;
	    ob_clean();$this->ajaxReturn($blogroll_list_arr);
	}
	/**
	 * @name  bd_blogroll_edit()
	 * @desc  友情链接编辑
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_blogroll_edit(){
	    $db_blogroll=M("sys_blogroll");
	    $blogroll_id=I('get.blogroll_id');
	    if($blogroll_id){
	    	$blogroll_map['blogroll_id'] = $blogroll_id;
	        $blogroll_row=$db_blogroll->where($blogroll_map)->find();
	        $this->assign('blogroll_row',$blogroll_row);
	    }
	    $this->display();
	}
	/**
	 * @name  bd_blogroll_do_save()
	 * @desc  友情链接保存执行
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_blogroll_do_save(){
	    $db_blogroll=M("sys_blogroll");
	    $blogroll_id=I('post.blogroll_id');
	    $blogroll=I('post.');
	    if($blogroll_id){
	        $blogroll['update_time']=date("Y-m-d H:i:s");
	        $blogroll_map['blogroll_id'] = $blogroll_id;
	        $blogroll_res=$db_blogroll->where($blogroll_map)->save($blogroll);
	    }else{
	        $blogroll['add_staff']=session('staff_no');
	        $blogroll['add_time']=date("Y-m-d H:i:s");
	        $blogroll['update_time']=date("Y-m-d H:i:s");
	        $blogroll['status']=1;
	        $blogroll_res=$db_blogroll->add($blogroll);
	    }
	    if($blogroll_res){
	        showMsg('success', '操作成功！',U('bd_blogroll_index'),1);
	    }else{
	        showMsg('error', '操作失败！');
	    }
	}
	/**
	 * @name  bd_blogroll_do_del()
	 * @desc  友情链接停用或启用
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-04
	 */
	public function bd_blogroll_do_del(){
	    $db_blogroll=M("sys_blogroll");
	    $blogroll_id=I('get.blogroll_id');
	    $status=I('get.status');
	    if($blogroll_id){
	        if($status==1){
	            $blogroll['status']=0;
	        }else{
	            $blogroll['status']=1;
	        }
	        $blogroll['update_time']=date("Y-m-d H:i:s");
	        $blogroll_map['blogroll_id'] = $blogroll_id;
	        $blogroll_res=$db_blogroll->where($blogroll_map)->save($blogroll);
	    }
	    if($blogroll_res){
	        showMsg('success', '操作成功！',U('bd_blogroll_index'));
	    }else{
	        showMsg('error', '操作失败！');
	    }
	}
	/******************门户友情链接维护结束**********************************************/

	/**
	 * @name  bd_alertmsg_list()
	 * @desc  消息提醒列表
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-30
	 */
	public function bd_alertmsg_list(){
		$this->display("Bd/bd_alertmsg_list");
	}
	/**
	 * @name  bd_alertmsg_data()
	 * @desc  消息提醒表格数据
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-30
	 */
	public function bd_alertmsg_data(){
		$page = I('get.page');
        $limit = I('get.limit');
        $alert_type = I('get.alert_type');
        $offset = ($page-1)*$limit;
        if(!empty($limit)){
        	$alertmsg_list = getAlertMsgList(session('staff_no'),$alert_type,'','','',$offset,$limit);
        	foreach ($alertmsg_list['data'] as &$mag_val) {
				$mag_val['operate'] = "<a class='btn btn-xs blue btn-outline' style='text-decoration: none;' href='" . U('bd_alertmsg_info', array('alert_id' => $mag_val['alert_id'])) . "'><i class='fa fa-trash-o'></i> 详情 </a>  ";
			}
        }else {
        	$alertmsg_list = getAlertMsgList(session('staff_no'));
        	foreach ($alertmsg_list as &$mag_val) {
				$mag_val['operate'] = "<a class='btn btn-xs blue btn-outline' style='text-decoration: none;' href='" . U('bd_alertmsg_info', array('alert_id' => $mag_val['alert_id'])) . "'><i class='fa fa-trash-o'></i> 详情 </a>  ";
			}
        }
		
		ob_clean();$this->ajaxReturn($alertmsg_list);
	}
	/**
	 * @name  bd_alertmsg_info()
	 * @desc  消息提醒详情页面
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-30
	 */
	public function bd_alertmsg_info(){
		$bd_alertmsg = M('bd_alertmsg');
		$bd_type = M('bd_type');
		$alert_id = I('get.alert_id');
		saveAlertStatus($alert_id);
		$alert_map['alert_id'] = $alert_id;
		$alertmsg_data = $bd_alertmsg->where($alert_map)->find();
		$http_host = $_SERVER['HTTP_HOST'].'/';
		//$type_data = $bd_type->where("type_group = 'alert_type' and type_no = ".$alertmsg_data['alert_type'])->find();
		$alertmsg_data['alert_type'] =getBdTypeInfo($alertmsg_data['alert_type'], 'alert_type') ;//$type_data['type_name'];
		$this->assign("alertmsg_data",$alertmsg_data);
		$this->assign("http_host",$http_host);
		$this->display("Bd/bd_alertmsg_info");
	}
}