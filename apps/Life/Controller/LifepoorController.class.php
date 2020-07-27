<?php
namespace Life\Controller;
 // 命名空间
use Common\Controller\BaseController;

class LifepoorController extends BaseController // 继承Controller类
{
	/**
	 * @name:life_poor_village_index
	 * @desc：贫困村列表
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-3
	 * @version：V1.0.0
	 **/
    public function life_poor_village_index(){
		checkAuth(ACTION_NAME);
		$this->display("life_poor_village_index");
	}
	/**
	 * @name:life_poor_village_data
	 * @desc：贫困村列表数据
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2017-12-12
	 * @version：V1.0.0
	 **/
	public function life_poor_village_data(){
	    
	    $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
	    $keyword = I('get.keyword');
	    $is_poor = I('get.is_poor');
	    // dump($is_poor);die;
	    $poor_village = M('poor_village');
	    if(!empty($is_poor)){
	    	$where['is_poor'] = $is_poor;
	    }
	    if (!empty($keyword)){
	        $where['poor_village_name'] = array('like',"%$keyword%");
	    }
	    $poor_village_data['data'] = $poor_village->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
	    $count = $poor_village->where($where)->count();
	    
	    
	    $poor_village_data['count'] = $count;
		$num = 1;
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($poor_village_data['data'] as &$poor_village) {
		    if ($poor_village['is_poor'] == 2){
		        $poor_village['is_poor'] = '否';
		    }else if($poor_village['is_poor'] == 1){
		        $poor_village['is_poor'] = '是';
		    }
		}
		$poor_village_data['code'] = 0;
        $poor_village_data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($poor_village_data); // 返回json格式数据
	}
	/**
	 * @name:life_poor_village_edit
	 * @desc：贫困村列表添加修改
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-21
	 * @version：V1.0.0
	 **/
	public function life_poor_village_edit()
	{
	    checkAuth(ACTION_NAME);
        $poor_village_id = I("get.poor_village_id");
        $db_communist = M("poor_village");
        if(!empty($poor_village_id)){
        	$where['poor_village_id'] = $poor_village_id;
        	$poor_village_data = $db_communist->where($where)->find();
            $this->assign("poor_village_data",$poor_village_data);
        }
	    $this->display("Lifepoor/life_poor_village_edit");
	}
	/**
	 * @name:life_poor_village_save
	 * @desc：贫困村保存
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-21
	 * @version：V1.0.0
	 **/
	public function life_poor_village_save()
	{
	   	$post = I('post.');
	   	$post['update_time'] = date('Y-m-d H:i;s');
        $post['add_time'] = date('Y-m-d H:i;s');
        $post['staff_no'] = session('staff_no');
	   	$db_sp_poor_village = M('poor_village');
	    if(!empty($post['poor_village_id'])){
	    	if($db_sp_poor_village->where(['poor_village_id'=> $post['poor_village_id']])->save($post)){
                  showMsg('success','编辑成功',U('life_poor_village_index'),1);
            }else{
                  showMsg('error','编辑失败',U('life_poor_village_index'),1);
            }
	    }else{
	    	if($db_sp_poor_village->add($post)){
                showMsg('success','操作成功',U('life_poor_village_index'),1);
            }else{
                showMsg('error','操作失败',U('life_poor_village_index'),1);
            }
	    }
        
	}
	/**
	 * @name:life_poor_village_info
	 * @desc：贫困村详情
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-21
	 * @version：V1.0.0
	 **/
	public function life_poor_village_info()
	{
	    checkAuth(ACTION_NAME);
	   	$poor_village_id = I('get.poor_village_id');
        $this->assign("poor_village_id",$poor_village_id);
	   	$db_sp_poor_village = M('poor_village');
	    if(!empty($poor_village_id)){
	    	$where['poor_village_id'] = $poor_village_id;
	    	$poor_village_data = $db_sp_poor_village->where($where)->find();
	    }
        $this->assign("poor_village_data",$poor_village_data);
        $this->display("Lifepoor/life_poor_village_info");
	}
	public function life_poor_village_info_data()
	{
		$poor_village_id = I('get.poor_village_id');
		if(!empty($poor_village_id)){
			$where['poor_village_type'] = $poor_village_id;
		}
		$poor_village_data['data'] = M('poor_household')->where($where)->select();
		//
		foreach($poor_village_data['data'] as &$household){
			//所属村
	    	if(!empty($household['poor_village_type'])){
	    		$poor_village = M('poor_village')->select();
	    		foreach($poor_village as $village){
	    			if($household['poor_village_type']==$village['poor_village_id']){
	    				$household['poor_village_type'] = $village['poor_village_name'];
	    			}
	    		}
	    	}
	    	//脱贫状态
	    	if(!empty($household['poor_status'])){
	    		$household['poor_status'] = getStatusName('state_of_poverty',$household['poor_status']);
	    	}
	    	//贫困户属性
	    	if(!empty($household['poor_village_property'])){
	    		$household['poor_village_property'] = getBdTypeInfo($household['poor_village_property'],'poor_households');
	    	}
		}
		
		//
		$poor_village_data['code'] = 0;
        $poor_village_data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($poor_village_data); // 返回json格式数据
	}
	/**
	 * @name:life_poor_village_del
	 * @desc：贫困村删除
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-21
	 * @version：V1.0.0
	 **/
	public function life_poor_village_del()
	{
	   	checkAuth(ACTION_NAME);
        $poor_village_id = I("get.poor_village_id");
        $poor_village= M("poor_village");
        $poor_village_data  = $poor_village->where(array('poor_village_id'=>$poor_village_id))->delete();
        if($poor_village_data){
            showMsg('success','操作成功',U('life_poor_village_index'));
        }else{
            showMsg('error','操作失败',U('life_poor_village_index'));
        }
	}




	/*************************************贫困户展示*****************************************/
	/**
	 * @name:life_poor_village_info
	 * @desc：贫困户展示列表
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-22
	 * @version：V1.0.0
	 **/
	public function life_poor_household_index()
	{
	   	checkAuth(ACTION_NAME);
	   	$poor_village_data = M('poor_village')->select();
		$this->assign("poor_village_data",$poor_village_data);
        $this->display("Lifepoor/life_poor_household_index");
	}
	/**
	 * @name:life_poor_village_info
	 * @desc：贫困户展示数据
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-22
	 * @version：V1.0.0
	 **/
	public function life_poor_household_data()
	{
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;

	    $poor_status = I('get.poor_status');
	    //dump($poor_status);die;
	    if(!empty($poor_status)){
	    	$where['poor_status'] = $poor_status;
	    }
	    $poor_household_name = I('get.keyword');
	    if(!empty($poor_household_name)){
	    	$where['poor_household_name'] = array('like',"%$poor_household_name%");
	    }
	    $poor_village_type = I('get.poor_village_type');
	    if(!empty($poor_village_type)){
	    	$where['poor_village_type'] = $poor_village_type;
	    }
	    $poor_village_property = I('get.poor_village_property');
	    if(!empty($poor_village_property)){
	    	$where['poor_village_property'] = $poor_village_property;
	    }
	    $poor_household = M('poor_household');
	    $poor_household_data['data'] = $poor_household->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
	    $count = $poor_household->where($where)->count();
	    $poor_household_data['count'] = $count;
	    foreach($poor_household_data['data'] as &$household){
	    	//所属村
	    	if(!empty($household['poor_village_type'])){
	    		$poor_village = M('poor_village')->select();
	    		foreach($poor_village as $village){
	    			if($household['poor_village_type']==$village['poor_village_id']){
	    				$household['poor_village_type'] = $village['poor_village_name'];
	    			}
	    		}
	    	}
	    	//脱贫状态
	    	if(!empty($household['poor_status'])){
	    		$household['poor_status'] = getStatusName('state_of_poverty',$household['poor_status']);
	    	}
	    	//贫困户属性
	    	if(!empty($household['poor_village_property'])){
	    		$household['poor_village_property'] = getBdTypeInfo($household['poor_village_property'],'poor_households');
	    	}
	    }
		$poor_household_data['code'] = 0;
        $poor_household_data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($poor_household_data); // 返回json格式数据
	}
	/**
	 * @name:life_poor_village_edit
	 * @desc：贫困户展示修改
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-22
	 * @version：V1.0.0
	 **/
	public function life_poor_household_edit()
	{
		checkAuth(ACTION_NAME);
		$poor_household_id = I('get.poor_household_id');
		$poor_household = M('poor_household');
		if(!empty($poor_household_id)){
			$poor_household_data = $poor_household->where(['poor_household_id'=>$poor_household_id])->find();
			$this->assign("poor_household_data",$poor_household_data);
		}
		// dump($poor_household_data);die;

		$poor_village_data = M('poor_village')->select();
		$this->assign("poor_village_data",$poor_village_data);
		// dump($poor_village_data);die;
       	$this->display("Lifepoor/life_poor_household_edit");
	}
	/**
	 * @name:life_poor_village_save
	 * @desc：贫困户展示保存
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-22
	 * @version：V1.0.0
	 **/
	public function life_poor_household_save()
	{
     	$post = I('post.');
     	$poor_household = M('poor_household');
     	if(!empty($post['poor_household_id'])){
	     	$where['poor_household_id'] = $post['poor_household_id'];
	     	$post['updata_time'] = date('Y-m-d H:i:s');
	     	$poor_household->save($post);
     	}else{
	     	$post['updata_time'] = date('Y-m-d H:i:s');
	     	$post['add_time'] = date('Y-m-d H:i:s');
	     	$post['staff_no'] = session('staff_no');
     		$poor_household->add($post);
     	}
     	if($poor_household){
            showMsg('success','操作成功',U('life_poor_household_index'),1);
        }else{
            showMsg('error','操作失败','');
        }
	}
	/**
	 * @name:life_poor_village_info
	 * @desc：贫困户展示详情
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-22
	 * @version：V1.0.0
	 **/
	public function life_poor_household_info()
	{
		checkAuth(ACTION_NAME);
       	$poor_household_id = I('get.poor_household_id');
		$this->assign("poor_household_id",$poor_household_id);
       	$poor_household = M('poor_household');
       	$poor_household_data = $poor_household->where(['poor_household_id'=>$poor_household_id])->find();
       	//所属村
       	if(!empty($poor_household_data['poor_village_type'])){
       		$poor_village_data = M('poor_village')->select();
       		// dump($poor_village_data);die;
       		foreach($poor_village_data as $village){
       			if($poor_household_data['poor_village_type']== $village['poor_village_id']){
       				$poor_household_data['poor_village_type'] = $village['poor_village_name'];
       			}
       		}
       	}
       	//脱贫状态
    	if(!empty($poor_household_data['poor_status'])){
    		$poor_household_data['poor_status'] = getStatusInfo('state_of_poverty',$poor_household_data['poor_status']);
    	}
    	//贫困户属性
    	if(!empty($poor_household_data['poor_village_property'])){
    		$poor_household_data['poor_village_property'] = getBdTypeInfo($poor_household_data['poor_village_property'],'poor_households');
    	}
    	//致命原因
    	if(!empty($poor_household_data['poor_cause'])){
    		$poor_household_data['poor_cause'] = getBdTypeInfo($poor_household_data['poor_cause'],'poverty_causes');
    	}
		$this->assign("poor_household_data",$poor_household_data);
		$this->display("Lifepoor/life_poor_household_info");
	}
	//添加成员
	public function life_poor_household_info_data()
	{
		$pagesize = I('get.limit');
    	$page = (I('get.page')-1)*$pagesize;
		$poor_household_id = I('get.poor_household_id');
		if(!empty($poor_household_id)){
			$where['household_id'] = $poor_household_id;
		}
		$data_list['data'] = M('poor_household_member')->limit($page,$pagesize)->where($where)->select();
		foreach($data_list['data'] as &$list){
			if($list['member_sex']==1){
				$list['member_sex'] = '男';
			}else{
				$list['member_sex'] = '女';
			}
		}
		$data_list['count'] = M('poor_household_member')->where($where)->count();
		$data_list['code'] = 0;
        $data_list['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($data_list); // 返回json格式数据
	}
	public function life_poor_household_info_edit()
	{
		$household_id = I('get.poor_household_id');
		// dump($household_id);die;
		$this->assign("household_id",$household_id);
        $this->display("Lifepoor/life_poor_household_info_edit");
	}
	public function life_poor_household_info_save()
	{
		$post = I('post.');
		$post['updata_time'] = date('Y-m-d H:i:s');
     	$post['add_time'] = date('Y-m-d H:i:s');
     	$post['staff_no'] = session('staff_no');
     	$poor_household = M('poor_household_member')->add($post);
     	if($poor_household){
            showMsg('success','操作成功',U('life_poor_household_info',array('poor_household_id'=>$post['household_id'])),1);
        }else{
            showMsg('error','操作失败','');
        }
 		
	}
	//台账
	public function life_poor_household_measures_info_data()
	{
		$pagesize = I('get.limit');
    	$page = (I('get.page')-1)*$pagesize;
		$poor_household_id = I('get.poor_household_id');
		// dump($poor_household_id);
		$db_measures = M("ccp_help_measures");
		$measures_list['data'] = $db_measures->where(['measures_genre'=>'2'])->limit($page,$pagesize)->select();
		$i = 1;
		$measures_genre_no = [];
		foreach($measures_list['data'] as &$measures){
			if(strpos($measures['measures_genre_no'],$poor_household_id)!==false){
				$measures_genre_no[] = $measures;
				$measures_list['count'] = $i++;
			}
		}
		$help_communist_data = M('ccp_communist_help')->select();
		$measures_list['data'] = $measures_genre_no;
		foreach($measures_list['data'] as &$list){
			$list['measures_type'] = getBdTypeInfo($list['measures_type'],'help_measures');
			if($list['type']==1){
				$list['type'] = '帮扶队伍';
				$list['measures_help'] = '-';
			}else{
				$list['type'] = '帮扶人员';
				$list['measures_name'] = '-';
			}
			foreach($help_communist_data as $help){
				if($list['measures_help']==$help['help_id']){
					$list['measures_help'] = $help['help_name'];
				}
			}

			
		}
		$measures_list['code'] = 0;
        $measures_list['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($measures_list);
	}
	/**
	 * @name:life_poor_village_del
	 * @desc：贫困户展示删除
	 * @param：
	 * @return：
	 * @author：刘长军
	 * @addtime:2018-11-22
	 * @version：V1.0.0
	 **/
	public function life_poor_household_del()
	{
       	checkAuth(ACTION_NAME);
        $poor_household_id = I("get.poor_household_id");
        //dump($help_id);
        $poor_household= M("poor_household");
        $poor_household_data  = $poor_household->where(array('poor_household_id'=>$poor_household_id))->delete();
        if($poor_household_data){
            showMsg('success','操作成功',U('life_poor_household_index'));
        }else{
            showMsg('error','操作失败',U('life_poor_household_index'));
        }
	}
}
