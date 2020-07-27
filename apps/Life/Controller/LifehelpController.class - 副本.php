<?php
namespace Life\Controller;
use Common\Controller\BaseController;
class LifehelpController extends BaseController{
	
	/**************************************精准扶贫*****************************************/
    /**
     * @name:life_help_index
     * @desc：精准扶贫首页
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-10-24
     * @version：V1.0.0
     **/
    public function life_help_index(){
      	checkAuth(ACTION_NAME);
        $this->display("Lifehelp/life_help_index");
    }
    /**
     * @name:life_help_index_data
     * @desc：精准扶贫首页数据
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-10-24
     * @version：V1.0.0
     **/
    public function life_help_index_data(){
    	$help_name = I("get.start");
		$staff_no = session('staff_no');
		$help_data['data'] = getHelpList($help_name);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		$type_name_arr = M('bd_type')->where("type_group = 'help_source_type'")->getField('type_no,type_name');
		$type_names_arr = M('bd_type')->where("type_group = 'lifehelp_type'")->getField('type_no,type_name');
		
		foreach ($help_data['data'] as &$help) {
			switch ($help['help_sex']) {
				case '1':
					$help['help_sex'] = "男";
					break;
				default:
					$help['help_sex'] = "女";
					break;
			}
			$help['source_type_name'] = $type_name_arr[$help['source_type']];
			$help['help_type'] = $type_names_arr[$help['help_type']];  //困难程度
			$help['communist_no'] = $communist_name_arr[$help['communist_no']];
			$help_delegate = "";
			if(empty($help['communist_no'])){
				$help_delegate = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='".U('life_help_edit', array('help_id' => $help['help_id'])) . "'>帮扶 </a>";
			}
			$help['help_no'] = "<a class='fcolor-22' href='".U('life_help_info',array('help_id' => $help['help_id']))."'>".$help['help_no']." </a>";
			$help['help_name'] = "<a class='fcolor-22' href='".U('life_help_info',array('help_id' => $help['help_id']))."'>".$help['help_name']." </a>";
			$help['operate'] = $help_delegate."<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('life_help_do_del', array('help_id'=>$help['help_id'])) . "'$confirm>删除</a> ";
				/* "<a class='btn btn-xs blue btn-outline' href='" . U('life_help_edit', array('help_id' => $help['help_id'])) . "'><i class='fa fa-edit'></i>编辑</a>". */
		}
        $help_data['code'] = 0;
        $help_data['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($help_data);
    }
    /**
     * @name:life_help_edit
     * @desc：精准扶贫添加/编辑
     * @param：
     * @return：
     * @author：王宗彬
     *         @updatetime 2017年10月18日:08:34
     * @version：V1.0.0
     **/
    public function life_help_edit(){
    	checkAuth(ACTION_NAME);
        $help_id = I("get.help_id");
        $db_communist = M("ccp_communist_help");
        if(!empty($help_id)){
            $help_data = getHelpInfo($help_id);
            $this->assign("help_data",$help_data);
        }
        $this->display("Lifehelp/life_help_edit");
    }
    /**
     * @name:life_help_do_save
     * @desc：精准扶贫编辑保存操作
     * @param：
     * @return：
     * @author：王宗彬
     *         @updatetime 2017年10月18日:08:34
     * @version：V1.0.0
     **/
    public function life_help_do_save(){
        $post = I("post.");
        if(!empty($post['communist_no'])){
            // 给党员加积分
            $communist_no = $post['communist_no'];
            $integral_help = getConfig('integral_help');
            $communist_integral = getCommunistInfo($communist_no,'communist_integral');
            updateIntegral(1,7,$communist_no,$communist_integral,$integral_help,'帮扶困难人员'); // 给党员加积分
            //saveCommunistLog($post['communist_no'],'17','','',$post['help_name']);
        }
        $help_data = saveHelpCommunist($post);
        if($help_data){
            showMsg('success','操作成功',U('life_help_index'));
        }else{
            showMsg('error','操作失败',U('life_help_index'));
        }
    }
    /**
     * @name:life_help_info
     * @desc：精准扶贫详情
     * @param：
     * @return：
     * @author：王宗彬
     *         @updatetime 2017年10月18日:08:34
     * @version：V1.0.0
     **/
    public function life_help_info(){
    	checkAuth(ACTION_NAME);
        $help_id = I("get.help_id");
        $help_data = getHelpInfo($help_id);
        $help_data['help_type'] = getBdTypeInfo($help_data['help_type'],'lifehelp_type', 'type_name');//困难程度
        $help_data['source_type_name'] = getBdTypeInfo($help_data['source_type'],'help_source_type','type_name');
        if(!empty($help_data['help_img'])){
            $help_data['help_img'] = getUploadInfo($help_data['help_img']);
        } else {
            $help_data['help_img'] = '';
        }
        if(!empty($help_data['help_avatar'])){
            $help_data['help_avatar'] = getUploadInfo($help_data['help_avatar']);
        } else {
            $help_data['help_avatar'] = '';
        }
        $this->assign("help_data",$help_data);
        $this->display("Lifehelp/life_help_info");
    }
    /**
     * @name:life_help_do_del
     * @desc：精准扶贫删除
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-10-24
     * @version：V1.0.0
     **/
    public function life_help_do_del(){
        $help_id = I("get.help_id");
        $db_help = M("ccp_communist_help");
        $help_map['help_id'] = $help_id;
        $help_data = $db_help->where($help_map)->delete();
        if($help_data){
            showMsg('success','操作成功',U('life_help_index'));
        }else{
            showMsg('error','操作失败',U('life_help_index'));
        }
    }
}
