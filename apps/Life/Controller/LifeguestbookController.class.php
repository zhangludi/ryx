<?php
namespace Life\Controller;
use Life\Model\LifeGuestbookModel;
use Common\Controller\BaseController;
class LifeguestbookController extends BaseController{
/*******************************留言建议********************************/
	/*
	 * @name  life_guestbook_index()
	 * @desc  留言建议首页
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime  2017-12-2
	 */
 	public function life_guestbook_index(){
		checkAuth(ACTION_NAME);
		$child_nos_n = session('party_no_auth');//取本级及下级组织
        $p_map['party_no'] = array('in',$child_nos_n);
        $party_list = M('ccp_party')->where($p_map)->field('party_no,party_pno,party_name,party_avatar')->select();
		$this->assign('party_list',$party_list);
 		$this->display("Lifeguestbook/life_guestbook_index");
	}
	/**
	 * @name  life_guestbook_index_data()
	 * @desc  留言建议首页数据
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 */
	public function life_guestbook_index_data(){
		$pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
		$party_no = I('get.party_no');
		$guestbook_data = getGuestbookList($party_no, $page, $pagesize);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$party_name_arr = M('ccp_party')->getField('party_no,party_name');
		foreach ($guestbook_data['data'] as &$data) {
			$guestbook_id = $data['guestbook_id'];
			$data['add_time'] = getFormatDate($data['add_time'], 'Y-m-d');
			$data['communist_name'] = getCommunistInfo($data['communist_name']);
			$data['party_no'] = $party_name_arr[$data['party_no']];
            $data['status'] = $data['status'] == 1 ? "<span style='color:red;'>未回复</span>" : "<span style='color:green;'>已回复</span>";
			$data['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('life_guestbook_info', array('guestbook_id' =>$guestbook_id)) . "'>查看</a>"."<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('life_guestbook_do_del', array('guestbook_id' =>$guestbook_id)) . "' $confirm>删除</a>";
 		}
 		$guestbook_data['code'] = 0;
		$guestbook_data['msg'] = "获取数据成功";

		ob_clean();$this->ajaxReturn($guestbook_data);
	}
	/**
	 * @name  life_guestbook_info()
	 * @desc  留言建议详情
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 * */
	public function life_guestbook_info(){
		checkAuth(ACTION_NAME);

		$guestbook_id = I('get.guestbook_id');

		$db = new LifeGuestbookModel();
		$hr_staff_arr = M('hr_staff')->getField('staff_no,staff_name');
		$data = $db->getGuestnookInfo($guestbook_id);
		
		$data['staff_no'] = $hr_staff_arr[$data['staff_no']];
		$this->assign('data',$data);
		$this->display("Lifeguestbook/life_guestbook_info");
	}
	/**
	 * @name  life_guestbook_do_save()
	 * @desc  留言建议详情回复保存
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 * */
	public function life_guestbook_do_save(){
		$post = I('post.');
		$db = M('life_guestbook');
		$post['status'] = 2;
		$post['staff_no'] = session('staff_no');
		$post['update_time'] = date('Y-m-d H:i:s');
		$where['guestbook_id'] = $post['guestbook_id'];
		$result1 = $db->where($where)->save($post);
		if ( $result1){
			showMsg("success", "操作成功！！", U('life_guestbook_index'));
		}
		else
        {
			showMsg("error", "操作失败！！");
		}
	}
	/**
	 * @name  life_guestbook_do_del()
	 * @desc  留言建议删除
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 */
	public function life_guestbook_do_del(){
		checkAuth(ACTION_NAME);
		$guestbook_id = I("get.guestbook_id");
		$life_guestbook = M("life_guestbook");
		$gues_map['guestbook_id'] = $guestbook_id;
		$guestbook_data = $life_guestbook->where($gues_map)->delete();
		$guestbook_map['guestbook_pid'] = $guestbook_id;
		$life_guestbook->where($guestbook_map)->delete();
		$url = U('life_guestbook_index');
		if ($guestbook_data) {
			showMsg("success", "操作成功！！", $url);
		} else {
			showMsg("error", "操作失败！！",$url);
		}
	}

}
