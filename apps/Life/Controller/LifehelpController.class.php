<?php
namespace Life\Controller;
use Common\Controller\BaseController;
class LifehelpController extends BaseController{
    
    /**************************************精准扶贫*****************************************/
    /**
     * @name:life_help_index
     * @desc：扶贫列表
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-25
     * @version：V1.0.0
     **/
    public function life_help_index(){
        checkAuth(ACTION_NAME);
        $this->display("Lifehelp/life_help_index");
    }
    /**
     * @name:life_help_index_data
     * @desc：扶贫首页数据
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_index_data(){
        $help_cadres = I('get.help_cadres');
        // dump($help_cadres);die;
        $help_name = I('get.help_name');
        if(!empty($help_cadres)){
            $where['help_cadres'] = $help_cadres;
        }
        if(!empty($help_name)){
            $where['help_name'] = array('like',"%".$help_name."%");
        }
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        $help_data['data'] = M('ccp_communist_help')->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
        $help_data['count'] = M('ccp_communist_help')->where($where)->count();
        foreach ($help_data['data'] as &$help){
            if($help['help_cadres']=='1'){
                $help['help_cadres'] = '是';
            }elseif($help['help_cadres']=='2'){
                $help['help_cadres'] = '否';
            }else{
                $help['help_cadres'] = '未选择';

            }
            $help['help_name'] = "<a class='fcolor-22' href='".U('life_help_info',array('help_id' => $help['help_id']))."'>".$help['help_name']." </a>";
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
     * @author：刘长军
     *         @updatetime 2018年10月26日
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
     * @desc：扶贫添加编辑保存操作
     * @param：
     * @return：
     * @author：刘长军
     *         @updatetime 2017年10月18日:08:34
     * @version：V1.0.0
     **/
    public function life_help_do_save(){
        $post = I("post.");
        $ccp_communist_help = M('ccp_communist_help');//实例化
        if(empty($post['help_id'])){
            if($ccp_communist_help->add($post)){
                showMsg('success','操作成功',U('life_help_index'),1);
            }else{
                showMsg('error','操作失败','');
            }
        }else{
            if($ccp_communist_help->where(['help_id'=> $post['help_id']])->save($post)){
                showMsg('success','编辑成功',U('life_help_index'),1);
            }else{
                showMsg('error','编辑失败','');
            }
        }
            
    }
    /**
     * @name:life_help_info
     * @desc：精准扶贫详情
     * @param：
     * @return：
     * @author：刘长军
     *         @updatetime 2018年10月26日
     * @version：V1.0.0
     **/
    public function life_help_info(){
        checkAuth(ACTION_NAME);
        $help_id = I("get.help_id");
        $help_data = getHelpInfo($help_id);
        $this->assign("help_data",$help_data);
        $this->display("Lifehelp/life_help_info");
    }
    /**
     * @name:life_help_do_del
     * @desc：帮扶人员删除
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_do_del(){
        $help_id = I("get.help_id");
        //dump($help_id);
        $db_help = M("ccp_communist_help");
        $help_data = $db_help->where(array('help_id'=>$help_id))->delete();
        if($help_data){
            showMsg('success','操作成功',U('life_help_index'));
        }else{
            showMsg('error','操作失败',U('life_help_index'));
        }
    }
    

    /**************************************帮扶队伍*****************************************/
    /**
     * @name:life_help_team_index
     * @desc：帮扶队伍管理
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_team_index()
    {
        checkAuth(ACTION_NAME);
        $this->display("Lifehelp/life_help_team_index");
    }
    /**
     * @name:life_help_team_index
     * @desc：帮扶队伍管理首页数据
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_team_index_data()
    {
        $help_team_name = I("get.keyword");
        $help_team_type = I("get.help_team_type");
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        if($help_team_name){
            $map['help_team_name'] = array('like',"%$help_team_name%");
        }
        if(!empty($help_team_type)){
            $map['help_team_type'] = $help_team_type;
        }
        $db_help = M("ccp_communist_help_team");
        if(!empty($pagesize)){
            $life_help_team_data = $db_help->where($map)->order('add_time DESC')->limit($page,$pagesize)->select();
            $count = $db_help->where($map)->count();
        }else{
            $life_help_team_data = $db_help->where($map)->order('add_time DESC')->select();
        }
        foreach ($life_help_team_data as &$help) {
            $db_communist = M("ccp_communist_help");
            $where['help_id'] = $help['help_team_head'];
            $help_team_data = $db_communist->where($where)->find();
            if($help_team_data){
                $help['help_team_head']=$help_team_data['help_name'];
            }
            $help['help_team_type']  = getBdTypeInfo($help['help_team_type'],'team_type');
            // if(!empty($help_team_type)){
                // $help['help_team_type'] = $help_team_type[0]['type_name'];  
            // } else {
                // $help['help_team_type'] = '暂无类型';
            // }
        }
        if(!empty($pagesize)){
            $data['data'] = $life_help_team_data;
            $data['count'] = $count;
            $data['code'] = 0;
            $data['msg'] = '获取数据成功';
        } else {
            $data = $life_help_team_data;
        }
        ob_clean();$this->ajaxReturn($data);
    }
    /**
     * @name:life_help_team_index
     * @desc：帮扶队伍管理添加编辑
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_team_edit()
    {
        checkAuth(ACTION_NAME);
        $help_team_id = I("get.help_team_id");
        $db_communist = M("ccp_communist_help_team");
        $db_help_communist = M("ccp_communist_help")->select();
        $db_help_communist_name = [];
        foreach($db_help_communist as $help){
            $db_help_communist_name[$help['help_id']] =$help['help_name']; 
        }
        if(!empty($help_team_id)){
            $where['help_team_id'] = $help_team_id; 
            $help_team_data = $db_communist->where($where)->find();
            $this->assign("help_team_data",$help_team_data);
        }
        $this->assign("db_help_communist_name",$db_help_communist_name);
        $this->display("Lifehelp/life_help_team_edit");
    }
    /**
     * @name:life_help_team_prople
     * @desc：ajax获取负责人
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2019-3-5
     * @version：V1.0.0
     **/
    public function life_help_team_prople(){
        $value = I('post.value');
        $db_team =M('ccp_communist_help_team');
        $where['help_team_id'] = $value;
        $data = $db_team->where($where)->find();
        $help_id = $data['help_team_head'];
        if($help_id){
            $map['help_id'] = $help_id;
            $help = M('ccp_communist_help')->where($map)->find();
        }
        $this->ajaxReturn($help);
    }
    /**
     * @name:life_help_team_phone
     * @desc：ajax获取负责人联系方式
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2019-3-5
     * @version：V1.0.0
     **/
    public function life_help_team_phone(){
        $value = I('post.value');
        $map['help_id'] = $value;
        $help = M('ccp_communist_help')->where($map)->find();
        $this->ajaxReturn($help);
    }
    /**
     * @name:life_help_team_index
     * @desc：帮扶队伍管理保存操作
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_team_save()
    {
        $post = I("post.");
        $post['update_time'] = date('Y-m-d H:i;s');
        $post['add_time'] = date('Y-m-d H:i;s');
        $post['staff_no'] = session('staff_no');
        $ccp_communist_help_team = M('ccp_communist_help_team');//实例化
        if(empty($post['help_team_id'])){
            if($ccp_communist_help_team->add($post)){
                showMsg('success','操作成功',U('life_help_team_index'),1);
            }else{
                showMsg('error','操作失败','');
            }
        }else{
            if($ccp_communist_help_team->where(['help_team_id'=> $post['help_team_id']])->save($post)){
                  showMsg('success','编辑成功',U('life_help_team_index'),1);
            }else{
                  showMsg('error','编辑失败','');
            }
        }
    }
    /**
     * @name:life_help_team_info
     * @desc：帮扶队伍详情
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_team_info()
    {
        checkAuth(ACTION_NAME);
        $help_team_id = I("get.help_team_id");
        $db_help = M("ccp_communist_help_team");
        $where['help_team_id'] = $help_team_id;
        $help_team_data = $db_help->where($where)->find();
        $help_team_type = getBdTypeList('team_type',$help_team_data['help_team_type']);
        $help_team_data['help_team_type'] = $help_team_type[0]['type_name'];
        $help_team_members = explode(',',$help_team_data['help_team_members']);
        $db_communist = M("ccp_communist_help");
        $help_team_name = [];
        foreach($help_team_members as $help_id){
            $where['help_id'] = $help_id;
            $help_data = $db_communist->where($where)->find();
            $help_team_name[$help_id] = $help_data['help_name'];
        }
        $where['help_id'] = $help_team_data['help_team_head'];
        $help_data_head = $db_communist->where($where)->find();
        $this->assign("help_team_data",$help_team_data);
        $this->assign("help_data_head",$help_data_head);
        $help_team_name = implode(',',$help_team_name);
        $this->assign("help_team_name",$help_team_name);
        $this->display("Lifehelp/life_help_team_info");
    }
    /**
     * @name:life_help_team_del
     * @desc：帮扶队伍删除
     * @param：
     * @return：
     * @author：刘长军
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_team_del()
    {
        $help_team_id = I("get.help_team_id");
        $db_measures = M("ccp_help_measures");
        $where['measures_team'] = $help_team_id;
        $measures_data = $db_measures->where($where)->find();
        $db_help_team = M("ccp_communist_help_team");
        if(!empty($measures_data)){
            showMsg('error','不允许删除',U('life_help_team_index'));
        }else{
            $help_team_data = $db_help_team->where(array('help_team_id'=>$help_team_id))->delete();
            if($help_team_data){
                showMsg('success','操作成功',U('life_help_team_index'));
            }else{
                showMsg('error','操作失败',U('life_help_team_index'));
            }
        }
        
    }
    /**********************************帮扶台账****************************************************/
        /**
     * @name:life_help_measures_index
     * @desc：人员帮扶台账列表
     * @param：
     * @return：
     * @author：wangzongbin
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_measures_index()
    {
        if($_GET['team'] == '1'){
            $team = '_1';
        }else{
            $team = '_2';
        }
        $this->assign('team',$_GET['team']);
        session('team',$team);
        checkAuth(ACTION_NAME.session('team'));

        $this->display("Lifehelp/life_help_measures_index");
    }

    /**
     * @name:life_help_index_data
     * @desc：人员帮扶台账首页数据
     * @param：
     * @return：
     * @author：wangzongbin--liuchagnjun
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_measures_index_data()
    {
        $team = I('get.team');
        $measures_leader = I('get.measures_leader');
        $measures_name = I('get.measures_name');
        $measures_help = I('get.measures_help');
        if(!empty($measures_help)){
          $where['measures_help'] = array('like',"%".$measures_help."%");
        }
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        $db_measures = M("ccp_help_measures");
        if(!empty($team)){
            $where['type'] = $team;
        }
        if(!empty($measures_name)){
            $where['measures_name'] = array('like',"%".$measures_name."%");
        }
        if(!empty($measures_leader)){
            $where['measures_leader'] = array('like',"%".$measures_leader."%");
        }
        $measures_data['data'] = $db_measures->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
        $count = $db_measures->where($where)->count();
        $team_help = M("ccp_communist_help_team")->field('help_team_id,help_team_name')->select();
        $help_prpeo= M('ccp_communist_help')->select();
        foreach($measures_data['data'] as &$measures){
            foreach($team_help as $v){
                if($measures['measures_team']==$v['help_team_id']){
                   $measures['measures_team'] = $v['help_team_name'];
                }
            }
            foreach($help_prpeo as $v){
                if($measures['measures_help']==$v['help_id']){
                   $measures['measures_help'] = $v['help_name'];
                }
            }
            if($measures['measures_genre']==1){
              $measures['measures_genre'] = '贫困村';
            }
            if($measures['measures_genre']==2){
              $measures['measures_genre'] = '贫困户';
            }
        }
        $measures_data['count'] = $count;
        $measures_data['code'] = 0;
        $measures_data['msg'] = '获取数据成功';
        ob_clean();$this->ajaxReturn($measures_data);
    }
    /**
     * @name:life_help_measures_edit
     * @desc：人员帮扶台账添加编辑
     * @param：
     * @return：
     * @author：wangzongbin
     * @addtime:2018-10-26
     * @version：V1.0.0
     **/
    public function life_help_measures_edit()
    {
        checkAuth(ACTION_NAME.session('team'));
        $measures_id = I("get.measures_id");
        $team = I('get.team');
        $this->assign('team',$team);
        $db_measures = M("ccp_help_measures");

        if(!empty($measures_id)){
            $where['measures_id'] = $measures_id; 
            $measures_data = $db_measures->where($where)->find();
            $this->assign("measures_data",$measures_data);
        }
        $help_communist_data = M('ccp_communist_help')->select();
        $this->assign("help_communist_data",$help_communist_data);
        $db_help_team = M('ccp_communist_help_team')->select();
        $this->assign("db_help_team",$db_help_team);
        $this->display("Lifehelp/life_help_measures_edit");
    }
    /**
     * @name:life_help_do_save
     * @desc：人员帮扶台账保存操作
     * @param：
     * @return：
     * @author：wangzongbin
     *         @updatetime 2017年10月18日:08:34
     * @version：V1.0.0
     **/
    public function life_help_measures_save()
    {
        $post = I("post.");
        $post['update_time'] = date('Y-m-d H:i:s');
        $post['status'] = 1;
        $post['add_staff'] = session('staff_no');
        $post['add_time'] = date('Y-m-d H:i:s');
        $db_measures = M('ccp_help_measures');//实例化
        if(!empty($post['measures_id'])){
            $where['measures_id'] = $post['measures_id'];
            $res = $db_measures->where($where)->save($post);
        }else{
            $res = $db_measures->add($post);
        }
        if(!empty($res)){
            showMsg('success','操作成功',U('life_help_measures_index',array('team'=>$post['type'])),1);
        }else{
            showMsg('error','操作失败');
        }
    }
     /**
     * @name:life_help_info
     * @desc：人员帮扶台账详情
     * @param：
     * @return：
     * @author：刘长军
     * @version：V1.0.0
     **/
    public function life_help_measures_info(){
        checkAuth(ACTION_NAME.session('team'));
        $measures_id = I("get.measures_id");
        $team = I('get.team');
        $db_measures = M("ccp_help_measures");
        $where['measures_id'] = $measures_id;
        $measures_data = $db_measures->where($where)->find();
        $help_prpeo= M('ccp_communist_help')->select();
        foreach($help_prpeo as $v){
            if($measures_data['measures_help']==$v['help_id']){
               $measures_data['measures_help'] = $v['help_name'];
            }
        }
        $measures_data['measures_type'] = getBdTypeInfo($measures_data['measures_type'],'help_measures');
        $team_help = M('ccp_communist_help_team')->select();
        foreach($team_help as $team){
            if($team['help_team_id']==$measures_data['measures_team']){
                $measures_data['measures_team'] = $team['help_team_name'];
            }
        }

        if($measures_data['measures_genre']==1){
            $array_measures_genre = strToArr($measures_data['measures_genre_no'],',');
            $poor_village = M("poor_village")->where(['is_poor'=>1])->select();
            foreach($array_measures_genre as &$measures){
                foreach($poor_village as &$village){
                    if($measures == $village['poor_village_id']){
                       $measures =  $village['poor_village_name'];
                    }
                }
            }
            $b = implode(',',$array_measures_genre);
            $measures_data['measures_genre_no'] = $b;
        }
        if($measures_data['measures_genre']==2){
            $array_measures_genre = strToArr($measures_data['measures_genre_no'],',');
            $poor_household = M("poor_household")->where(['is_poor_village'=>1])->select();
            foreach($array_measures_genre as &$measures){
                foreach($poor_household as &$household){
                    if($measures==$household['poor_household_id']){
                       $measures =  $household['poor_household_name'];
                    }
                }
            }
        $b = implode(',',$array_measures_genre);
            $measures_data['measures_genre_no'] = $b;
        }
        $this->assign("measures_data",$measures_data);
        $this->display("Lifehelp/life_help_measures_info");
    }
    /**
     * @name:life_help_do_save
     * @desc：人员帮扶台账删除
     * @param：
     * @return：
     * @author：wangzongbin
     * @version：V1.0.0
     **/
    public function life_help_measures_del()
    {
        $measures_id = I("get.measures_id");
        $team = I('get.team');
        $db_measures = M("ccp_help_measures");
        $where['measures_id'] = $measures_id;
        $measures_data = $db_measures->where($where)->delete();
        if($measures_data){
            showMsg('success','操作成功',U('life_help_measures_index',array('team'=>$team)));
        }else{
            showMsg('error','操作失败');
        }
    }

}
