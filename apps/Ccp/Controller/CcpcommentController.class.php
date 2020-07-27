<?php
/*****************************民主评议********************************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
use GuzzleHttp\Psr7\str;
use Think\Model;

class CcpcommentController extends BaseController{
    /**
     * @name:ccp_communist_comment_index
     * @desc：民主评议主页
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-12-26 
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_index(){
    	checkAuth(ACTION_NAME);
        $party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno')->select();
    	$this->assign('party_list',$party_list);
        if(checkDataAuth(session("staff_no"), 'is_comment')){
             $this->assign('type',1);
        }
        $this->display('ccp_communist_comment_index');
    }
    /**
     * @name:ccp_communist_comment_list_data
     * @desc：民主评议主页表格数据加载
     * @author：黄子正
     * @addtime:2017-10-24 15:54:00
     * @addtime:2017-10-24 15:54:00
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_list_data(){
        $party_no = I('get.party_no');
        if(!empty($party_no)){
            $party_no = $party_no;
        }
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;

        //判断是否有查询条件
        $db_communist=M('ccp_communist_comment');
		$where['status'] =  array('eq',"1"); 
		if(!empty($party_no)){// 部门
			$party_no = getPartyChildNos($party_no,'str');
			$where['party_no'] = array('in',"$party_no");
		}else{
			$where['party_no'] = array('in',session('party_no_auth'));
        }
		$time = I('get.start');
        if (!empty($time)){
            $time = explode(' - ',$time);
            $start = $time['0'];
            $end = $time['1'];
			$where['comment_date']=array(array('egt',$start),array('elt',$end),'and');
        }
		$result['data'] = $db_communist->where($where)->order('comment_date desc')->limit($page,$pagesize)->select();
		$result['count'] = $db_communist->where($where)->count();	
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');        
        foreach ($result['data'] as &$v) {
            //用户数据权限判断
            $staff_no = session('staff_no');
            $party_name = $party_name_arr[$v['party_no']];
            if (!empty($party_name)){
                $v['party_name'] = $party_name;
            }else{
                $v['party_name'] = '无';
            }
            $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
            $comment_no = $v['comment_no'];
            if (checkDataAuth($staff_no, 'is_comment')) {
                $v['is_comment'] = '1';
                //$v['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('ccp_communist_comment_edit', array('comment_no' => $comment_no, 'type' => 'show')) . "'>查看</a>"."<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('ccp_communist_comment_edit', array('comment_no' => $comment_no, 'type' => 'edit')) . "'><i class='fa fa fa-edit'></i>  修改</a>  " ."<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('ccp_communist_comment_do_del', array('comment_no' => $comment_no)) . "'$confirm><i class='fa fa-trash-o'></i>  删除</a>  ";
            }//else{
                 //$v['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('ccp_communist_comment_edit', array('comment_no' => $comment_no, 'type' => 'show')) . "'>查看</a>  ";
            //}
        }
		$result['code'] = 0;
		$result['msg'] = "获取数据成功";
        ob_clean();$this->ajaxReturn($result);
    }
    /**
     * @name:ccp_communist_comment_edit
     * @desc：民主评议添加/修改/详情
     * @param：
     * @return：
     * @author：黄子正
     * @addtime:2017-10-24 15:54:00
     * @addtime:2017-10-24 15:54:00
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_edit(){
//    	checkAuth(ACTION_NAME);
        $party_no = I('get.party_no');
        if(!empty($party_no)){
            $row['party_no'] = $party_no;
        }
        $type = I('get.type');
        $comment_no = I('get.comment_no');
        

        if(!empty($comment_no)){
            $row=getCommentInfo($comment_no);
            $row['is_record'] = 0; //是否写评议记录
            $this->assign('comment_no',$comment_no);
            $this->assign('type',$type);
        }else{
            //添加操作
            $row['comment_date']=date('Y-m-d');           
        }
        if($type == 'show'){
            $meeting_map['meeting_id'] = $comment_no;
            $meeting_map['meeting_minutes_type'] = 2;
        	$data = M('oa_meeting_minutes')->where($meeting_map)->find();
        	if($data){
        		$row['meeting_minutes_content'] = $data['meeting_minutes_content'];
        		$row['meeting_minutes_feedback'] = $data['meeting_minutes_feedback'];
        		$row['meeting_minutes_improvement'] = $data['meeting_minutes_improvement'];
        		$row['is_record'] = 1;
        	}
        }
        $this->assign('row',$row); 
        $this->display('ccp_communist_comment_edit');
    }
    /**
     * @name:ccp_communist_comment_details_data
     * @desc：民主评议添加/修改数据加载
     * @param：
     * @return：
     * @author：黄子正
     * @addtime:2017-10-24 15:54:00
     * @addtime:2017-10-24 15:54:00
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_details_data(){
    	
        $ccp_communist=M('ccp_communist');
        $comment_no=I('get.comment_no');
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        $party_no_arr = M('ccp_communist')->getField('communist_no,party_no');
        $post_no_arr = M('ccp_communist')->getField('communist_no,post_no');
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        if(!empty($comment_no)){
            //修改或查看操作
            $where1['comment_no']=(int)$comment_no;
            $data_list['data']=getCommentDetailsList($where1);
            foreach($data_list['data'] as &$v1){
                $v1['communist_name']=$communist_name_arr[$v1['communist_no']];
                $v1['party_name']=$party_name_arr[$party_no_arr[$v1['communist_no']]];
                $v1['post_no']=$post_no_arr[$v1['communist_no']];
                $v1['post_name']=getPartydutyInfo($v1['post_no']);
            };
        }else{
            //添加操作
            $party_no=I('get.party_no');
            $where['party_no']=$party_no;
            $where['status']=COMMUNIST_STATUS_OFFICIAL;
            $data_list['data']=$ccp_communist->where($where)->field('communist_no,communist_name,party_no,post_no')->select();
            foreach($data_list['data'] as &$v){
                $v['party_name']=$party_name_arr[$party_no];
                $v['post_name']=getPartydutyInfo($v['post_no']);
                $v['details_test_score']='0';
                $v['details_party_score']='0';
                $v['details_total_score']='0';
            }
        }
        $data_list['code'] = 0;
        $data_list['msg'] = '获取数据成功';
        $this->ajaxReturn($data_list);
    }
    /**
     * @name:ccp_communist_comment_do_save
     * @desc：民主评议保存
     * @author：黄子正
     * @addtime:2017-10-24 15:54:00
     * @addtime:2017-10-24 15:54:00
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_do_save(){
        $data=I('post.');
        $model = new Model();
        $model->startTrans();

        $comment_no = $data['comment_no'];
        //添加主表数据
        $data1=array();
        $data2=array();
        
        $comment['comment_title']=$data['comment_title'];
        $comment['party_no']=$data['party_no'];
        $comment['comment_date']=$data['comment_date'];
        $comment['comment_content']=$data['comment_content'];
        $comment['add_staff']=session('staff_no');
        $comment['status']='1';
        $comment['add_time']=date('Y-m-d H:i:s');
        $comment['update_time']=date('Y-m-d H:i:s');
        if(!empty($comment_no)){
            $comment_map['comment_no'] = $comment_no;
            $result = $model->table('sp_ccp_communist_comment')->where($comment_map)->save($comment);
        }else{
            $result = $model->table('sp_ccp_communist_comment')->add($comment);
        }
        $json = $data['json'];
        $dt_json = str_replace('&quot;','"',$json);
        $dt_json = (Array)json_decode($dt_json,true);
        foreach($dt_json as &$v){
            $details['communist_no']=$v['communist_no'];
            $details['details_test_score']=$v['details_test_score'];
            $details['details_party_score']=$v['details_party_score'];
            $details['details_total_score']=$v['details_total_score'];
            $details['status']='1';
            $details['update_time']=date('Y-m-d H:i:s');
            if(!empty($comment_no)){
                $details['comment_no']=$comment_no;
                $where1['details_no']=$v['details_no'];
                $flag=$model->table('sp_ccp_communist_comment_details')->where($where1)->save($details);
            }else{
				$details['add_time']=date('Y-m-d H:i:s');
				$details['add_staff']=session('staff_no');
                $details['comment_no'] = $result;
                $flag = $model->table('sp_ccp_communist_comment_details')->add($details);
            }
        }
        if($result && $flag){
            $model->commit();
            showMsg('success','操作成功',U('ccp_communist_comment_index'),1);
        }else{
            $model->rollback();
            showMsg('error','操作失败');
        }
    }
    /**
     * @name:ccp_communist_comment_do_del
     * @desc：民主评议删除
     * @param：
     * @return：
     * @author：黄子正
     * @addtime:2017-10-24 15:54:00
     * @addtime:2017-10-24 15:54:00
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_do_del(){
    	checkAuth(ACTION_NAME);
        $ccp_communist_comment=M('ccp_communist_comment');
        $ccp_communist_comment_details=M('ccp_communist_comment_details');
        $comment_no=I('get.comment_no');
        $where['comment_no']=$comment_no;
        $comm_del=$ccp_communist_comment->where($where)->delete();
        $comm_del=$ccp_communist_comment_details->where($where)->delete();
        if($comm_del&&$comm_del){
            showMsg("success",'操作成功',U('ccp_communist_comment_index'));
        }else{
            showMsg("error",'操作失败',U('ccp_communist_comment_index'));
        }
    }
    /**
     * @name:ccp_communist_comment_record
     * @desc：民主评议会议记录
     * @author：刘丙涛
     * @addtime:2017-11-22
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_record(){
        checkAuth(ACTION_NAME);
        $where['status']='1';
        $party_no = I('get.party_no');
        $party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->select();
        $this->assign('party_list',$party_list);
        $this->display('ccp_communist_comment_record');
    }
    /**
     * @name:ccp_communist_comment_record_data
     * @desc：民主评议会议记录
     * @author：王宗彬
     * @addtime:2017-12-06
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_record_data(){
        $meeting_minutes_title = I('get.keyword');
       
    	$party_no = I('get.party_no');
    	//$meeting_minutes_title = I('get.meeting_minutes_title');
    	$add_communist = I('get.add_communist');

    	$data = getMeetingminutesList($party_no,'2', $meeting_minutes_title, $add_communist);
    	$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
    	if(!empty($data['data'] && $data['data'] != 'null')){
            foreach ($data['data'] as &$list){
            $meeting_minutes_id = $list['meeting_minutes_id'];
            $list['add_time'] = getFormatDate($list['add_time'], 'Y-m-d') ;
            //$list['meeting_minutes_id'] = "<a class='fcolor-22' href='" . U('ccp_communist_comment_record_info', array('meeting_minutes_id' => $meeting_minutes_id)) . "' target='_self'>".$meeting_minutes_id."</a>";
           //$list['meeting_minutes_title'] = "<a class='fcolor-22' href='" . U('ccp_communist_comment_record_info', array('meeting_minutes_id' => $meeting_minutes_id)) . "' target='_self'>".$list['meeting_minutes_title']."</a>";
            $list['add_staff'] = $staff_name_arr[$list['add_staff']];
            //$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
            //$list['operate'] .= "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('ccp_communist_comment_record_edit', array('meeting_minutes_id' => $meeting_minutes_id)) . "' target='_self'>编辑</a><a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('ccp_communist_comment_record_del', array('meeting_minutes_id' => $meeting_minutes_id)) . "' $confirm>删除</a>";
            }
            $data['code'] = 0;
            $data['msg'] = "获取数据成功";
            ob_clean();$this->ajaxReturn($data);
        }else{
            $data['code'] = 0;
            $data['msg'] = "获取数据失败";
            ob_clean();$this->ajaxReturn($data);
        }
    	
    }
    /**
     * @name:ccp_communist_comment_record_edit
     * @desc：民主评议会议记录编辑/添加
     * @author：王宗彬
     * @addtime:2017-12-06
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_record_edit(){
    	checkAuth(ACTION_NAME);
    	$party_no = I('get.party_no');
        $this->assign('party_no',$party_no);
    	if(!empty($_GET['meeting_minutes_id'])){
    		$meeting_minutes_id = $_GET['meeting_minutes_id'];
            $minutes_map['meeting_minutes_id'] = $meeting_minutes_id;
    		$data = M('oa_meeting_minutes')->where($minutes_map)->find();
            $data['add_staff'] = getStaffInfo($data['add_staff']);
    	}
        $this->assign('data',$data);
    	$this->display('ccp_communist_comment_record_edit');
    }
    
    /**
     * @name:ccp_communist_comment_record_save
     * @desc：民主评议会议记录保存操作
     * @author：王宗彬
     * @addtime:2017-12-06
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_record_save(){
    	$post = $_POST;
		$oa_meeting_minutes = M('oa_meeting_minutes');
        $post['add_staff'] = session("staff_no");
		if(!empty($post['meeting_minutes_id'])){
			$meeting_minutes_id = $post['meeting_minutes_id'];
			$post['update_time'] = date('Y-m-d H:i:s');
            $comment_map['comment_no'] = $post['meeting_id'];
			$post['meeting_minutes_title'] = M('ccp_communist_comment')->where($comment_map)->getField('comment_title');
            $minutes_map['meeting_minutes_id'] = $meeting_minutes_id;
			$meeting_minutes = $oa_meeting_minutes->where($minutes_map)->save($post);
		}else{
			$post['add_time'] = date('Y-m-d H:i:s');
			$post['update_time'] = date('Y-m-d H:i:s');
			$post['meeting_minutes_type'] = '2';
            $comment_map['comment_no'] = $post['meeting_id'];
			$post['meeting_minutes_title'] = M('ccp_communist_comment')->where($comment_map)->getField('comment_title');
			$meeting_minutes = $oa_meeting_minutes->add($post);
		}
		if(!empty($meeting_minutes)){
			showMsg("success","操作成功",U('ccp_communist_comment_record'));
		}else{
			showMsg("success","执行失败，请联系管理员。",U('ccp_communist_comment_record'));
		}
    }
    /**
     * @name:ccp_communist_comment_record_info
     * @desc：民主评议会议记录详情
     * @author：王宗彬
     * @addtime:2017-12-06  
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_record_info(){
    	checkAuth(ACTION_NAME);
    	$meeting_minutes_id = $_GET['meeting_minutes_id'];
        $minutes_map['meeting_minutes_id'] = $meeting_minutes_id;
    	$data = M('oa_meeting_minutes')->where($minutes_map)->find();
    	$this->assign('data',$data);
    	$this->display('ccp_communist_comment_record_info');
    }
    /**
     * @name:ccp_communist_comment_record_del
     * @desc：民主评议会议记录删除
     * @author：王宗彬
     * @addtime:2017-12-06
     * @version：V1.0.0
     **/
    public function ccp_communist_comment_record_del(){
    	checkAuth(ACTION_NAME);
    	$meeting_minutes_id = $_GET['meeting_minutes_id'];
        $minutes_map['meeting_minutes_id'] = $meeting_minutes_id;
    	$meeting_minutes = M('oa_meeting_minutes')->where($minutes_map)->delete();
    	if(!empty($meeting_minutes)){
    		showMsg("success","操作成功",U('ccp_communist_comment_record',array('party_no'=>$post['party_nos'])));
    	}else{
    		showMsg("success","执行失败，请联系管理员。",U('ccp_communist_comment_record'));
    	}
    }
}
