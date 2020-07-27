<?php
/***********************************党组织地图、网格地图**********************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
class CcpmapController extends BaseController{
   /**
     * @name:ccp_party_map
     * @desc：部门地图
     * @param：
     * @author：刘丙涛
     * @addtime:2017-04-26
     * @version：V1.0.0
     **/
    public function ccp_party_map(){
        $map = getConfig('pam_map');
        if (empty($map)) {
            showMsg('success', '请设置地图', U('System/Sysconfig/sys_config_index'));
        }
        checkAuth(ACTION_NAME);
      
        $this->display('Ccpmap/ccp_party_map');
    }
    /**
     * @name:ccp_party_map_data
     * @desc：部门地图
     * @param：
     * @author：刘丙涛
     * @addtime:2017-04-26
     * @version：V1.0.0
     **/
    public function ccp_party_map_data(){
        $db_party = M('ccp_party');
        $child_nos = session('party_no_auth');//取本级及下级组织
        $p_map['status'] = 1;
        $p_map['party_no'] = array('in',$child_nos);
        $party_list = $db_party->where($p_map)->select();
        $num = 1;
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($party_list as &$list){
            $list['num'] = $num++;
            if ($list['gc_lng'] == "" || $list['gc_lat'] == ""){
                $list['is_record'] = '未设置';
                $button = "<a class='layui-btn  layui-btn-xs layui-btn-f60 ' href='" . U('ccp_party_map_set',array('party_no' => $list['party_no'],'page_type'=>'edit')) . "'>设置 </a>";
            }else{
                $list['is_record'] = '已设置';
                $button = " <a class='layui-btn layui-btn-primary layui-btn-xs ' href='" . U('ccp_party_map_set',array('party_no' => $list['party_no'],'page_type'=>'info')) . "'> 查看 </a>
                            <a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('ccp_party_map_do_del',array('party_no' => $list['party_no'])) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>";
            }
            $list['add_user'] = $staff_name_arr[$list['add_staff']];
            $list['operate'] = $button;
        }
        if(!empty($party_list)){
            $party_map['code'] = 0;
            $party_map['msg'] = '获取数据成功';
            $party_map['data'] = $party_list;
            ob_clean();$this->ajaxReturn($party_map);
        } else {
            $party_map['code'] = 0;
            $party_map['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($party_map);
        }
    }


    /**
     * @name:ccp_party_map_set
     * @desc：部门地图
     * @param：
     * @return：
     * @author：刘丙涛   王桥元
     * @updatetime:2017-09-15
     * @addtime:2017-04-26
     * @version：V1.0.0
     **/
    public function ccp_party_map_set(){
        checkAuth(ACTION_NAME);
        $db_party = M('ccp_party');
        $db_communist = M('ccp_communist');
        $party_no = I('get.party_no');
        //判断编辑页面还是详情页面
        $page_type = I('get.page_type');
        $this->assign('page_type',$page_type);
        $map_list = $db_party->where("party_pno='0'")->find();
//         $map_list = $db_party->select();
//         if (!empty($map_list)){
//         	foreach ($map_list as &$list){
//         		$pos = explode(',',$list['party_map']);
//         		//$list['party_map'] = "[".$pos['0'].",".$pos['1']."]";
//         		$list['party_map'] = $pos['0'].",".$pos['1'];
//         	}
//         	$this->assign('map_list',$map_list);
//         }
        $party_no_auth = session('party_no_auth');//取下级组织
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M("ccp_party")->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->order('party_no asc')->select();
        $this->assign('party_list',$party_list);
        $web_address = getConfig('web_address');
        $this->assign('web_address',$web_address);
        $this->assign('map_list',$map_list);
        $this->assign('party_no',$party_no);
        $this->display('Ccpmap/ccp_party_map_set');
    }
    /**
     * @name:ccp_party_map_do_save
     * @desc：党组织地图记录点保存
     * @param：
     * @return：
     * @author：刘丙涛   王桥元  
     * @updatetime:2017-09-15
     * @addtime:2017-3-24
     * @version：V1.0.
     **/
    public function ccp_party_map_do_save(){
        $db_party = M('ccp_party');
        $party_no = I('post.party_no');	//部门编号
        $lng = I('post.lng');	//横坐标
        $lat = I('post.lat');	//纵坐标
        $party_address = I('post.party_address');	//地址
        //$map['party_map'] = $lng . ',' . $lat;
        $map['gc_lng'] = $lng;
        $map['gc_lat'] = $lat;
        $map['update_time'] = date('Y-m-d H:i:s');
        $map['party_address'] = $party_address;
        $party_map['party_no'] = $party_no;
        $result = $db_party->where($party_map)->save($map);
        if ($result) {
            echo 'ccp_party_map';
        } else {
            echo 1;
        }
    }
    /**
     * @name:ccp_party_map_do_del
     * @desc：党组织地图记录点删除
     * @param：
     * @return：
     * @author：刘丙涛
     * @addtime:2017-3-24
     * @version：V1.0.0
     **/
    public function ccp_party_map_do_del(){
        checkLogin();
        $db_party = M('ccp_party');
        $party_no = I('get.party_no');
        //$map['party_map'] = null;
        $map['gc_lng'] = null;
        $map['gc_lat'] = null;
        $map['update_time'] = date('Y-m-d H:i:s');
        $party_map['party_no'] = $party_no;
        $result = $db_party->where($party_map)->save($map);
        if ($result) {
            showMsg('success', '操作成功！', U('Ccpmap/ccp_party_map'));
        } else {
            showMsg('error', '操作失败！');
        }
    }
    /**
     * @name:ccp_party_grid
     * @desc：部门地图网格列表
     * @author：靳邦龙
     * @addtime:2017-10-13
     * @version：V1.0.0
     **/
    public function ccp_party_grid(){
        checkAuth(ACTION_NAME);
        $db_party = M('ccp_party');
        $db_grid = M('ccp_party_grid');
        $party_list = $db_party->select();
        $num = 1;
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($party_list as &$list){
            $list['num'] = $num++;
            $button = '';
            $party_no=$list['party_no'];
            $grid_map['party_no'] = $party_no;
            $count=$db_grid->where($grid_map)->count();
            if ($count==0){
                $list['is_record'] = '未设置';
                $button = "<a class='btn btn-xs yellow-crusta btn-outline' href='" . U('ccp_party_grid_set',array('party_no' => $list['party_no'],'page_type'=>'edit')) . "'><i class='glyphicon glyphicon-plus'></i> 设置 </a>";
            }else{
                $list['is_record'] = '已设置';
                $button = " <a class='btn btn-xs green btn-outline' href='" . U('ccp_party_grid_set',array('party_no' => $list['party_no'],'page_type'=>'info')) . "'> 详情 </a>
                            <a class='btn btn-xs red btn-outline' href='" . U('ccp_party_grid_do_del',array('party_no' => $list['party_no'])) . "'$confirm><i class='fa fa-trash-o'></i> 重置 </a>";
            }
            $list['add_user'] = $staff_name_arr[$list['add_staff']];
            $list['operate'] = $button;
        }
        $this->assign('party_list',$party_list);
        $this->display('Ccpmap/ccp_party_grid');
    }
    /**
     * @name:ccp_party_grid_set
     * @desc：网格地图设置
     * @author：靳邦龙
     * @addtime:2017-10-13
     * @version：V1.0.0
     **/
    public function ccp_party_grid_set(){
        checkAuth(ACTION_NAME);
        $db_party = M('ccp_party');
        $db_communist = M('ccp_communist');
        $party_no = I('get.party_no');
        //判断编辑页面还是详情页面
        $page_type = I('get.page_type');
        $this->assign('page_type',$page_type);
        $grid_list = $db_party->select();
        if (!empty($grid_list)){
            foreach ($grid_list as &$list){
                $pos = explode(',',$list['party_grid']);
                $list['party_grid'] = $pos['0'].",".$pos['1'];
            }
            $this->assign('grid_list',$grid_list);
        }
        $this->assign('party_no',$party_no);
        $this->display('Ccpmap/ccp_party_grid_set');
    }
    /**
     * @name:ccp_party_grid_do_save
     * @desc：网格地图
     * @author：靳邦龙
     * @updatetime:2017-09-15
     * @version：V1.0.0
     **/
    public function ccp_party_grid_do_save(){
        $db_grid = M('ccp_party_grid');
        $party_no = I('post.party_no');	//部门编号
        $lng = I('post.lng');	//横坐标
        $lat = I('post.lat');	//纵坐标
        $grid['party_no'] = $party_no;
        $grid['gc_lng'] = $lng;
        $grid['gc_lat'] = $lat;
        $grid['update_time'] = date('Y-m-d H:i:s');
        $result = $db_grid->add($grid);
        if ($result) {
            echo 'ccp_party_grid';
        } else {
            echo 1;
        }
    }
    /**
     * @name:ccp_party_grid_do_del
     * @desc：党组织地图记录点删除
     * @author：靳邦龙
     * @addtime:2017-10-13
     * @version：V1.0.0
     **/
    public function ccp_party_grid_do_del(){
        checkLogin();
        $db_grid = M('ccp_party_grid');
        $party_no = I('get.party_no');
        $grid_map['party_no'] = $party_no;
        $result = $db_grid->where($grid_map)->delete();
        if ($result) {
            showMsg('success', '操作成功！', U('ccp_party_grid'));
        } else {
            showMsg('error', '操作失败！');
        }
    }
}
