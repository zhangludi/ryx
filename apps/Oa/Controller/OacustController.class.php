<?php
namespace Oa\Controller;
use Common\Controller\BaseController;
class OacustController extends BaseController{
    /***************************理论中心组学习首页********************************/
	/**
	 * @name  oa_theoretical_centre_index()
	 * @desc  理论中心组学习首页
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_theoretical_centre_index(){
		checkAuth(ACTION_NAME);//判断越权
    	$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
    	$this->assign('party_list',$party_list);
		$this->display("Oacust/oa_theoretical_centre_index");
	}
	/**
	 * @name  oa_theoretical_centre_index_data()
	 * @desc  理论中心组学习首页数据
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_theoretical_centre_index_data(){
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
		$centre_title = I('get.centre_title');
        $party_no = I('get.party_no');
		if(!empty($centre_title)){
			$where['centre_title'] = array('like','%'.$centre_title.'%');
		}
		if(!empty($party_no)){
			$child_nos = getPartyChildNos($party_no,'arr');//党组织编号
			$where['party_no']=array('in',$child_nos);
		}else{
			$where['party_no'] = array('in',session('party_no_auth'));
		}
		
        $centre_data['data'] = M('oa_theoretical_centre')->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
		$centre_data['count'] = M('oa_theoretical_centre')->where($where)->count();
		foreach ($centre_data['data'] as &$centre) {
			$centre['add_staff'] = peopleNoName($centre['add_staff']);
			$centre['add_time']=getFormatDate($centre['add_time'], "Y-m-d");
			$logo = getUploadInfo($centre['centre_thumb']);
			if(empty($logo)){
				// 文章缩略图
				$centre['centre_thumb'] = '无';
			}else{
				// 文章缩略图
				$centre['centre_thumb'] = "<img src='$logo' width='50'>";
			}
		}		
		$centre_data['code'] = 0;
		ob_clean();$this->ajaxReturn($centre_data);
	}
	/**
	 * @name  oa_theoretical_centre_edit()
	 * @desc  理论中心组学习添加/编辑
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_theoretical_centre_edit(){
		checkAuth(ACTION_NAME);//判断越权
		$centre_id = I('get.centre_id');
		if (! empty($centre_id)) {
			$where['centre_id'] = $centre_id;
			$centre = M('oa_theoretical_centre')->where($where)->find();
			$this->assign("centre", $centre);
		}
		$this->display("Oacust/oa_theoretical_centre_edit");
	}
	/**
	 * @name  oa_theoretical_centre_do_save()
	 * @desc  理论中心组学习保存
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_theoretical_centre_do_save(){
		$post = $_POST;
		$post['add_time'] = date("Y-m-d H:i:s");
		if(!empty($post['centre_id'])){
			$post['update_time'] = date("Y-m-d H:i:s");
			$where['centre_id'] = $post['centre_id'];
			$res = M('oa_theoretical_centre')->where($where)->save($post);
		}else{
			$res = M('oa_theoretical_centre')->add($post);
		}
		if(!empty($res)){
			showMsg("success","操作成功",U('oa_theoretical_centre_index'),1);
		}else{
			showMsg("success","执行失败，请联系管理员。",U('oa_theoretical_centre_index'),1);
		}
	}
	/**
	 * @name  oa_theoretical_centre_info()
	 * @desc  理论中心组学习详情
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_theoretical_centre_info(){
		checkAuth(ACTION_NAME);//判断越权
		$centre_id = I('get.centre_id');
		$where['centre_id'] = $centre_id;
		$centre = M('oa_theoretical_centre')->where($where)->find();
		$centre['party_no'] = getPartyInfo($centre['party_no']);
		$centre['add_staff'] = peopleNoName($centre['add_staff']);
		$this->assign('centre',$centre);
		$this->display("Oacust/oa_theoretical_centre_info");
	}
	/**
	 * @name  oa_theoretical_centre_del()
	 * @desc  理论中心组学习删除
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.12.1
	 */
	public function oa_theoretical_centre_del(){
		checkAuth(ACTION_NAME);
        $map['centre_id'] = $_GET['centre_id'];
		$res = M('oa_theoretical_centre')->where($map)->delete();
		if($res){
			showMsg("success","操作成功",U('oa_theoretical_centre_index'));
		}else{
			showMsg("success","执行失败");
		}
	}	
	
	/***************************个人重大事项报告首页********************************/
	/**
	 * @name  oa_important_event_index()
	 * @desc  个人重大事项报告首页
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_important_event_index(){
		checkAuth(ACTION_NAME);//判断越权
    	$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
    	$this->assign('party_list',$party_list);
		$this->display("Oacust/oa_important_event_index");
	}
	/**
	 * @name  oa_important_event_index_data()
	 * @desc  个人重大事项报告首页数据
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_important_event_index_data(){
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
		
		
		$data['data'] = 0;
		$data['count'] = 0;
		$data['code'] = 0;
		ob_clean();$this->ajaxReturn($data);
	}
	
	/**
	 * @name  oa_important_event_edit()
	 * @desc  个人重大事项报告添加/编辑
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_important_event_edit(){
		checkAuth(ACTION_NAME);//判断越权
		
		$this->display("Oacust/oa_important_event_edit");
	}
	/**
	 * @name  oa_important_event_do_save()
	 * @desc  个人重大事项报告保存
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_important_event_do_save(){
		$post = $_POST;
		
		if(!empty($important_event)){
			showMsg("success","操作成功",U('oa_important_event_index'),1);
		}else{
			showMsg("success","执行失败，请联系管理员。",U('oa_important_event_index'),1);
		}
	}
	/**
	 * @name  oa_important_event_info()
	 * @desc  个人重大事项报告详情
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_important_event_info(){
		checkAuth(ACTION_NAME);//判断越权
		if($_GET['important_event_id']){
			$important_event_id = $_GET['important_event_id'];
            $minutes_map['important_event_id'] = $important_event_id;
			$data = M('oa_important_event')->where($minutes_map)->find();
			$data['important_event_content'] = removeHtml($data['important_event_content']);
			$data['party_no'] = getPartyInfo($data['party_no']);
            $data['add_staff'] = getStaffInfo($data['add_staff']);
		}
		$this->assign('data',$data);
		$this->display("Oacust/oa_important_event_info");
	}
	/**
	 * @name  oa_important_event_del()
	 * @desc  个人重大事项报告删除
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.12.1
	 */
	public function oa_important_event_del(){
		checkAuth(ACTION_NAME);
        $minutes_map['important_event_id'] = $_GET['important_event_id'];
		$res = M('oa_important_event')->where($minutes_map)->delete();
		if($res){
			showMsg("success","操作成功",U('oa_important_event_index',array('party_no'=>$post['party_nos'])));
		}else{
			showMsg("success","执行失败");
		}
	}
	
	/**
	 * @name  oa_important_event_print()
	 * @desc  个人重大事项报告打印
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_important_event_print(){
		checkAuth(ACTION_NAME);//判断越权
		
	}
}
