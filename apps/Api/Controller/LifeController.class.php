<?php

/**
 * 民生平台
 * User: liubingtao
 * Date: 2018/1/27
 * Time: 下午2:42
 */

namespace Api\Controller;

use Api\Validate\CommunistNoValidate;
use Api\Validate\IDValidate;
use Api\Validate\NumberValidate;
use Api\Validate\RequireValidate;
use Life\Model\LifeBbsPostCatModel;
use Life\Model\LifeBbsPostCommentModel;
use Life\Model\LifeBbsPostFavModel;
use Life\Model\LifeBbsPostModel;
use Life\Model\LifeConditionModel;
use Life\Model\LifeGuestbookModel;
use Life\Model\LifeConditionPersonalModel;
use Life\Model\LifeVolunteerActivityModel;

class LifeController extends Api
{
    /*********************  帖子*****************************/
    /**
     *  get_bbs_post_cat
     * @desc 获取论坛类型
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function get_bbs_post_cat()
    {
        $db = M('life_bbs_post_category');
        $cat_list = $db->field('cat_id,cat_name')->select();
        if ($cat_list) {
            $this->send('获取成功', $cat_list, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_bbs_post_list
     * @desc 获取论坛列表
     * @param int communist_no 党员编号
     * @param int cat 类型ID
     * @param int page
     * @param int pagesize
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function get_bbs_post_list()
    {
//         (new CommunistNoValidate())->goCheck();
//         (new NumberValidate(['page', 'pagesize']))->goCheck();

        $communist_no = I('post.communist_no');
        $cat = I('post.cat');
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;

        $db = new LifeBbsPostModel();
        $list = $db->getBbsPostList($communist_no, $cat, $page, $pagesize);
        if ($list['list']) {
            $this->send('获取成功', $list['list'], 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_bbs_post
     * @desc 增加帖子
     * @param int communist_no 党员编号
     * @param int cat_id 类型ID
     * @param int post_theme 主题
     * @param int post_content 内容
     * @param int post_img 图片ID
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function set_bbs_post()
    {
        $rows = I('post.');
        (new IDValidate('life_bbs_post_category', 'cat_id', '帖子类型不存在'))->goCheck();
        $db = new LifeBbsPostModel();
        $result = $db->Post($rows);
        if ($result) {
            $this->send('添加成功', null, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_bbs_post_info
     * @desc 获取帖子详情
     * @param int communist_no 党员编号
     * @param int post_id 帖子ID
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function get_bbs_post_info()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['post_id']))->goCheck();

        $db = new LifeBbsPostModel();
        $communist_no = I('post.communist_no');
        $post_id = I('post.post_id');
        $info = $db->getBbsPostInfo($communist_no, $post_id);
        if ($info) {
            $db->volumeAdd($post_id);
            $this->send('获取成功', $info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_bbs_post_fav
     * @desc 点赞
     * @param int post_id 帖子ID
     * @param int communist_no 党员编号
     * @param int fav_type 点赞或收藏
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function set_bbs_post_fav()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['post_id', 'fav_type']))->goCheck();
        (new IDValidate('life_bbs_post', 'post_id', '帖子不存在'))->goCheck();

        $rows = I('post.');
        $fav_type = I('post.fav_type');
        $db = new LifeBbsPostFavModel();
        $result = $db->Post($rows);
        if ($result) {
            if($fav_type == '1'){
                $data = '点赞成功';
            }else{
                $data = '收藏成功';
            }
            $this->send($data, null, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_bbs_post_comment
     * @desc 回复
     * @param int communist_no 党员编号
     * @param int post_id 帖子ID
     * @param int comment_content 内容
     * @param int comment_pid 父ID
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function set_bbs_post_comment()
    {
        (new IDValidate('life_bbs_post', 'post_id', '帖子不存在'))->goCheck();
        $rows = I('post.');
        $db = new LifeBbsPostCommentModel();
        $result = $db->Post($rows);
        if ($result) {
            $this->send('回复成功', null, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  del_bbs_post_fav
     * @desc 取消赞
     * @param int post_id 帖子ID
     * @param int communist_no 党员编号
     * @param int fav_type 点赞或收藏
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function del_bbs_post_fav()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['post_id', 'fav_type']))->goCheck();
        $rows = I('post.');
        $db = new LifeBbsPostFavModel();
        $result = $db->where($rows)->delete();
        if ($result) {
            $this->send('取消成功', null, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_comment_list
     * @desc 获取评论列表
     * @param int comment_id 评论ID
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    public function get_comment_list()
    {
        (new NumberValidate(['comment_id']))->goCheck();
        $comment_id = I('post.comment_id');
        $db = new LifeBbsPostModel();
        $list = $db->getBbsPostCommentList('comment_pid', $comment_id);
        $info = $db->getBbsPostCommentList('comment_id', $comment_id);
        if ($info) {
            $this->send('获取成功', ['info' => $info[0], 'list' => $list], 1);
        } else {
            $this->send();
        }
    }

    /*********************  end*****************************/

    /*********************  民生O2O*****************************/

    /**
     *  set_life_condition
     * @desc 提交民情
     * @param string condition_title 标题
     * @param int type_no 民情类型
     * @param string condition_content 内容
     * @param string condition_personnel 提交人
     * @param string condition_personnel_mobile 提交人手机号
     * @param string condition_area 地址
     * @param int condition_thumb 图片
     * @param int add_staff 党员编号
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function set_life_condition()
    {
        $data = I('post.');
		$data['add_time'] = date('Y-m-d H:i:s');
		$data['update_time'] = date('Y-m-d H:i:s');		
        $result = M('life_condition')->add($data);
        if ($result) {
            $this->send('添加成功', null, 1);
        } else {
            $this->send('添加失败');
        }
    }

    /**
     *  get_life_condition_cat_list
     * @desc 获取民情分类列表
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_life_condition_cat_list()
    {
        $type_list = M('life_condition_category')->where("status = 1")->select();
        if ($type_list) {
            $this->send('添加成功', $type_list, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  set_life_condition
     * @desc 我的任务
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_life_my_condition()
    {
        (new CommunistNoValidate())->goCheck();
        $db_condition = new LifeConditionPersonalModel();
        $communist_no = I('post.communist_no');
        $condition_map['condition_accept_no'] = $communist_no;
        $condition_log_data = $db_condition->getConditionPersonalList($condition_map);
        $db_condition = new LifeConditionModel();
        $status_name_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_color');
        foreach ($condition_log_data as &$conditionlog){
           /* $conditionlog['condition_accept_no'] = getCommunistInfo($conditionlog['condition_accept_no']);
            $conditionlog['condition_delegate_no'] = getStaffInfo($conditionlog['condition_delegate_no']);*/
            $conditionlog['status'] = "<font color='" . $status_color_arr[$conditionlog['status']] . "'>" . $status_name_arr[$conditionlog['status']] . "</font> ";
            $con_data = $db_condition->getConditionInfo($conditionlog['condition_id']);
            $conditionlog['condition_personnel'] = $con_data['condition_personnel']; //提交人
            $conditionlog['condition_personnel_mobile'] = $con_data['condition_personnel_mobile']; //提交人电话
            $conditionlog['add_time'] = $con_data['add_time'];
            $conditionlog['condition_thumb'] = getUploadInfo($con_data['condition_thumb']);
            $conditionlog['condition_area'] = $con_data['condition_area']; 
            $conditionlog['condition_title'] = $con_data['condition_title']; 
            $conditionlog['condition_content'] = $con_data['condition_content'];
            $conditionlog['condition_content1'] = $con_data['condition_content1'];

            
        }
        if ($condition_log_data) {
            $this->send('操作成功', $condition_log_data, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  life_condition_list
     * @desc 获取民生O2O列表
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function life_condition_list()
    {
        $db = new LifeConditionModel();
        $status = I('post.status');
        $condition_personnel = I('post.condition_personnel');
        $type_no = I('post.type_no');
		if(!empty($status) || $status == '0'){
			$where['status'] = array('eq',$status);
		}
        if(!empty($condition_personnel) ){
        	$where['condition_personnel'] = array('like',"%$condition_personnel%");
        }
        if(!empty($type_no)){
        	$where['type_no'] = array('eq',$type_no);
        }
        $result = $db->getConditionList($where);
        foreach ($result['data'] as &$list){
        	$list['add_time'] = getFormatDate($list['add_time'] , 'Y-m-d');
        }
        if ($result) {
            $data['status'] = 1;
            $data['msg'] = '获取成功';
            $data['data'] = $result['data'];
            $data['count'] = count($result['data']);
            $this->ajaxReturn($data);
        } else {
            $this->send();
        }
    }
    /**
     *  life_condition_info
     * @desc 获取民生O2O列表
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function life_condition_info()
    {
        (new CommunistNoValidate())->goCheck();
    	(new NumberValidate(['condition_id']))->goCheck();
    	$condition_id = I('post.condition_id');
    	$communist_no = I('post.communist_no');
        $db = new LifeConditionModel();

        $result = $db->getConditionInfo($condition_id);
        $map['condition_accept_no'] = $communist_no;
        $map['condition_id'] = $condition_id;
        $result['condition_delegate_no'] = M('life_condition_personal')->where($map)->getField('condition_delegate_no');
        $result['condition_delegate_no'] = getStaffInfo($result['condition_delegate_no']);
        $upload_id = str_replace ( "`", ",", $result['condition_thumb'] );
        $no_arr = strToArr($upload_id);
        $i = 0;
        $status_name_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where("status_group = 'condition_status'")->getField('status_no,status_color');
        foreach ($no_arr as &$arr) {
        	 $result['thumb'][$i++] = getUploadInfo($arr);
             //$result['status'] = "<font color='" . $status_color_arr[$result['status']] . "'>" . $status_name_arr[$result['status']] . "</font> ";
        }
        if ($result) {
            $this->send('操作成功', $result, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  life_condition_take_in
     * @desc 获取民生O2O状态修改
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function life_condition_take_in()
    {
        (new NumberValidate(['condition_id']))->goCheck();
        $condition_id = I('post.condition_id');
        $add_staff = I('post.add_staff');
        $status = I('post.status');
        $where['condition_id'] = $condition_id;
        $data['status'] = $status;
        $data['condition_content1'] = I('post.condition_content1');
        $result = M('life_condition')->where($where)->save($data); 
        $where['condition_id'] = $condition_id;
        M('life_condition_personal')->where($where)->save($data);        
        $condition_title = M('life_condition')->where($where)->getField('condition_title');    
        $post['condition_id'] = $condition_id;
        $post['add_time'] = date('Y-m-d H:i:s');
        $post['update_time'] = date('Y-m-d H:i:s');
        $post['status'] = $status;
        if($status == '20'){
            $post['condition_log_content'] = getCommunistInfo($add_staff)."接收".$condition_title;
        }else{
            $post['condition_log_content'] = getCommunistInfo($add_staff)."完成".$condition_title;
        }
        $post['status'] = $status;
        M('life_condition_log')->add($post);

        if ($result) {
            $this->send('操作成功', $result, 1);
        } else {
            $this->send();
        }
    }
    
    
    
    
    /*********************  end*****************************/


    /*********************留言建议*****************************/

    /**
     *  set_guestbook
     * @desc 写入留言建议
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function set_guestbook()
    {
        $db_guestbook = M('life_guestbook');
        $rows = I('post.');
        $rows['communist_name'] = $rows['communist_no'];
        $rows['add_staff'] = $rows['communist_no'];
        $rows['add_time'] = date('Y-m-d H:i:s');
        $rows['update_time'] = date('Y-m-d H:i:s');
        $rows['party_no'] = getCommunistInfo($rows['communist_no'],'party_no');
        $result = $db_guestbook->add($rows);
        if ($result) {
            $this->send('添加成功', null, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_guestbook_list
     * @desc 获取列表
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_guestbook_list()
    {
        (new NumberValidate(['communist_no']))->goCheck();
        $communist_no = I('post.communist_no');
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;
        $db = M('life_guestbook');
        $map['communist_name'] = $communist_no;
        $result = $db->where($map)->order('add_time desc')->limit($page,$pagesize)->select();
        foreach ($result as &$list) {
            $list['communist_name'] = getCommunistInfo($list['communist_name']);
            $list['add_staff'] = getCommunistInfo($list['add_staff']);
            $list['party_no'] = getPartyInfo($list['party_no']);
            if( $list['status'] == '1'){
                $list['status'] = '未回复';
            }else{
                $list['status'] = '已回复';
            }
        }
        if ($result) {
            $this->send('获取成功', $result, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_guestbook_info
     * @desc 获取详情
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_guestbook_info()
    {
        (new NumberValidate(['guestbook_id']))->goCheck();

        $guestbook_id = I('post.guestbook_id');

        $db = new LifeGuestbookModel();

        $result = $db->getGuestnookInfo($guestbook_id);

        if ($result) {
            $this->send('获取成功', $result, 1);
        } else {
            $this->send();
        }
    }
    /*********************end*****************************/

    /*********************调查问卷*****************************/

    /**
     *  get_life_survey_list
     * @desc 获取我能参加的调查问卷列表
     * @param int communist_no 党员编号
     * @param int status 0可参加 1公示
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_life_survey_list()
    {
        (new CommunistNoValidate())->goCheck();

        $status = I('post.status');

        $communist_no = I('post.communist_no');
        $party_no = getCommunistInfo($communist_no, 'party_no');

        $where = $status == 0 ? ' and communist_no is null' : '';
        $show_status = $status == 0 ? 1 : 0;
        if($status == 0){
            $survey_list = M('life_survey')
            ->join("(select * from sp_life_survey_log where communist_no = $communist_no) as l on sp_life_survey.survey_id=l.survey_id", 'LEFT')
            ->where("sp_life_survey.status=$show_status and find_in_set($party_no,sp_life_survey.party_no) $where")
            ->field('sp_life_survey.survey_id,sp_life_survey.survey_title,sp_life_survey.add_time')->order('sp_life_survey.add_time desc')->select();
        } else {
            $survey_list = M('life_survey_log')
            ->join('left join sp_life_survey on sp_life_survey.survey_id=sp_life_survey_log.survey_id')
            ->where("find_in_set($party_no,sp_life_survey.party_no)")
            ->field('sp_life_survey.survey_id,sp_life_survey.survey_title,sp_life_survey_log.communist_no,sp_life_survey.add_time')->group('sp_life_survey.survey_id')->order('sp_life_survey.add_time desc')->select();
        }
        if ($survey_list) {
            $this->send('获取成功', $survey_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_life_survey_info
     * @desc 获取调查问卷详情
     * @param int survey_id 问卷ID
     * @param int status 0可参加 1公示
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function get_life_survey_info()
    {
        (new NumberValidate(['survey_id']))->goCheck();

        $status = I('post.status');
        $survey_id = I('post.survey_id');

        $survey_info = getSurveyInfo($survey_id, $status);

        if ($survey_info) {
            foreach ($survey_info['questions_list'] as &$list) {
                $list['questions_type'] = $list['questions_type'] == '1' ? '单选' : '多选';
            }
            $this->send('获取成功', $survey_info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_life_survey
     * @desc 写入调查问卷
     * @param int communist_no 党员编号
     * @param int survey_id 问卷ID
     * @param int answer_list 选项 json
     * @user liubingtao
     * @date 2018/2/6
     * @version 1.0.0
     */
    public function set_life_survey()
    {
        (new CommunistNoValidate())->goCheck();

        (new IDValidate('life_survey', 'survey_id'))->goCheck();

        $communist_no = I('post.communist_no');
        $survey_id = I('post.survey_id');
        $answer_list = $_POST['answer_list'];
        $answer_list = json_decode($answer_list, true);
        if (!$answer_list) : $this->send('格式错误'); endif;

        M()->startTrans();
        $survey_list['survey_id'] = $survey_id;
        $survey_list['communist_no'] = $communist_no;
        $survey_list['log_date'] = date('Y-m-d');
        $survey_list['add_staff'] = $communist_no;
        // $survey_list['add_staff'] = peopleNo($communist_no,1);
        $survey_list['status'] = '1';
        $survey_list['add_time'] = date('Y-m-d H:i:s');
        $result = M('life_survey_log')->add($survey_list);
        $arr = array();
        foreach ($answer_list as $item) {
            if (stripos($item['answer_item'], ",") !== false) {
                $answer_item = explode(',', $item['answer_item']);
                foreach ($answer_item as $value) {
                    $list['communist_no'] = $communist_no;
                    $list['survey_id'] = $survey_id;
                    $list['questions_id'] = $item['questions_id'];
                    $list['answer_item'] = $value;
                    $list['add_staff'] = $communist_no;
                    // $list['add_staff'] =  peopleNo($communist_no,1);
                    $list['status'] = '1';
                    $list['add_time'] = date('Y-m-d H:i:s');
                    $arr[] = $list;
                }
            } else {
                $list['communist_no'] = $communist_no;
                $list['survey_id'] = $survey_id;
                $list['questions_id'] = $item['questions_id'];
                $list['answer_item'] = $item['answer_item'];
                $list['add_staff'] = $communist_no;
                // $list['add_staff'] =  peopleNo($communist_no,1);
                $list['status'] = '1';
                $list['add_time'] = date('Y-m-d H:i:s');
                $arr[] = $list;
            }

        }
        $flag = M('life_survey_answer')->addAll($arr);
        if ($result && $flag) {
            M()->commit();
            $num = M('life_survey_log')->where("survey_id=$survey_id")->count();
            $join_num = $num;
            if(empty($join_num)){
              $join_num = $num['0'];
            }
            if(empty($join_num)){
              $join_num = $num['tp_count'];
            }
            $this->send('提交成功', ['num' => $join_num], 1);
        } else {
            M()->rollback();
            $this->send('提交失败');
        }
    }
    /*********************end*****************************/

    /*********************困难帮扶*****************************/

    /**
     *  get_life_help_list
     * @desc 获取帮扶人员列表
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function get_life_help_list()
    {
    	(new NumberValidate(['page', 'pagesize']))->goCheck();
    	$pagesize = I('post.pagesize');
    	$page = (I('post.page') - 1) * $pagesize;
        $measures_data = M("ccp_help_measures")->where("type=2")->order('add_time desc')->limit($page,$pagesize)->select();
        $team_help = M("ccp_communist_help_team")->getField('help_team_id,help_team_name');
        $help_prpeo= M('ccp_communist_help')->getField('help_id,help_name');
        $poor_household = M("poor_household")->where("is_poor_village=1")->getField("poor_household_id,poor_household_name");


        $poor_village = M("poor_village")->where("is_poor=1")->getField("poor_village_id,poor_village_name");
        foreach($measures_data as &$measures){
            $measures['measures_team'] = $team_help[$measures['measures_team']];
            $measures['measures_help'] = $help_prpeo[$measures['measures_help']];
            $measures_genre_no = explode(',',$measures['measures_genre_no']);
            $measures['measures_genre_name'] = '';
            foreach ($measures_genre_no as $data) {
                if($measures['measures_genre'] == '2'){
                    if(!empty($measures['measures_genre_name'])){
                        $measures['measures_genre_name'] .= ",".$poor_household[$data];
                    }else{
                        $measures['measures_genre_name'] = $poor_household[$data];
                    }
                }else{
                    if(!empty($measures['measures_genre_name'])){
                        $measures['measures_genre_name'] .= ",".$poor_village[$data];
                    }else{
                        $measures['measures_genre_name'] = $poor_village[$data];
                    }
                }
            }
            $measures['title'] = $measures['measures_help']."帮扶了".$measures['measures_genre_name'];
        }
        if(!empty($measures_data)){
            $this->send('获取成功', $measures_data, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_life_help_info
     * @desc 获取帮扶详情
     * @param int help_id 帮扶id
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function get_life_help_info()
    {
        (new NumberValidate(['measures_id']))->goCheck();
        $measures_id = I('post.measures_id');
        $map['measures_id'] = $measures_id;
        $map['type'] = 2;
        $measures_data = M("ccp_help_measures")->where($map)->order('add_time desc')->find();
        $team_help = M("ccp_communist_help_team")->getField('help_team_id,help_team_name');
        $help_prpeo= M('ccp_communist_help')->getField('help_id,help_name');
        $poor_household = M("poor_household")->where("is_poor_village=1")->getField("poor_household_id,poor_household_name");

        $poor_village = M("poor_village")->where("is_poor=1")->getField("poor_village_id,poor_village_name");
        $measures_data['measures_team'] = $team_help[$measures_data['measures_team']];
        $measures_data['measures_help'] = $help_prpeo[$measures_data['measures_help']];
        //$measures_data['measures_genre_no'] = $poor_household[$measures_data['measures_genre_no']];
        $measures_genre_no = explode(',',$measures_data['measures_genre_no']);
        $measures_data['measures_genre_name'] = '';
        foreach ($measures_genre_no as $data) {
            if($measures_data['measures_genre'] == '2'){
                if(!empty($measures_data['measures_genre_name'])){
                    $measures_data['measures_genre_name'] .= ",".$poor_household[$data];
                }else{
                    $measures_data['measures_genre_name'] = $poor_household[$data];
                }
            }else{
                if(!empty($measures_data['measures_genre_name'])){
                    $measures_data['measures_genre_name'] .= ",".$poor_village[$data];
                }else{
                    $measures_data['measures_genre_name'] = $poor_village[$data];
                }
            }

            
        }
        $measures_data['title'] = $measures_data['measures_help']."帮扶了".$measures_data['measures_genre_name'];

        if ($measures_data) {
            $this->send('获取成功', $measures_data, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_life_help
     * @desc 申请帮扶
     * @param int communist_no 党员编号
     * @param int difficulty_content 内容
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function set_life_help()
    {
        (new CommunistNoValidate())->goCheck();
        (new RequireValidate(['difficulty_content']))->goCheck();

        $post = I("post.");
        $communist_no = $post['communist_no'];
        $communist_data = getCommunistInfo($communist_no, 'communist_name,communist_sex,communist_birthday,
                communist_idnumber,communist_paddress,communist_address,communist_tel,communist_mobile,communist_qq');
        $post['help_name'] = $communist_data['communist_name'];
        $post['help_no'] = $communist_no;
        $post['help_sex'] = $communist_data['communist_sex'];
        $post['help_birthday'] = $communist_data['communist_birthday'];
        $post['help_idnumber'] = $communist_data['communist_idnumber'];
        $post['help_nativeplace'] = $communist_data['communist_paddress'];
        $post['help_address'] = $communist_data['communist_address'];
        $post['help_tel'] = $communist_data['communist_tel'];
        $post['help_mobile'] = $communist_data['communist_mobile'];
        $post['help_qq'] = $communist_data['communist_qq'];
        $post['communist_no'] = ''; 
        $post['source_type'] = 3;
        $help_data = saveHelpCommunist($post);
        if ($help_data) {
            $this->send('添加成功', null, 1);
        } else {
            $this->send('写入失败');
        }
    }
    /*********************end*****************************/

    /*********************志愿者*****************************/

    /**
     *  get_life_volunteer_list
     * @desc 获取我能参加的志愿者活动列表
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function get_life_volunteer_list()
    {
        (new CommunistNoValidate())->goCheck();

        $db_volunteer_activity = M("life_volunteer_activity");
        $communist_no = I("post.communist_no");

        $party_no = getCommunistInfo($communist_no, "party_no");
        $activity_data = $db_volunteer_activity->where("FIND_IN_SET('$party_no',party_no)")->order('add_time desc')->select();

        if ($activity_data) {
			$where['communist_no'] = $communist_no;
            $is_volunteer = M("life_volunteer")->where($where)->getField('status');
            if($is_volunteer == '2'){
                $is_volunteer = 1;
            }else{
                $is_volunteer = 0;
            }
            foreach ($activity_data as &$activity) {
                $activity['activity_thumb'] = getUploadInfo($activity['activity_thumb']);				
				$activity['activity_time'] = $activity['activity_starttime'].' - '.$activity['activity_endtime'];            }

            $this->send('', $activity_data, 1,'is_volunteer',$is_volunteer);
        } else {
            $this->send();
        }
    }

    /**
     *  get_life_volunteer_activity_list
     * @desc 获取我参加的志愿者活动列表
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function get_life_volunteer_activity_list()
    {
        (new CommunistNoValidate())->goCheck();

        $communist_no = I("post.communist_no");

        $db_activity = M("life_volunteer_activity");
        $db_activity_communist = M("life_volunteer_activity_apply");

        $activity_apply_list = $db_activity_communist->where("communist_no= $communist_no and status = 2")->getField('activity_id', true);
        if (empty($activity_apply_list)) : $this->send(); endif;
        $activity_ids = implode(',', $activity_apply_list);//获取我参加的活动id
        //查询活动列表
        $activity_list = $db_activity->where("activity_id in ($activity_ids)")->order('add_time desc')->select();
        if ($activity_list) {
            foreach ($activity_list as &$list) {
                $list['activity_thumb'] = getUploadInfo($list['activity_thumb']);
            }
            $this->send('获取成功', $activity_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_life_volunteer_activity_info
     * @desc 获取活动详情
     * @param int activity_id 活动ID
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function get_life_volunteer_activity_info()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['activity_id']))->goCheck();

        $activity_id = I("post.activity_id");
        $communist_no = I("post.communist_no");
        $activity = new LifeVolunteerActivityModel();
        $activity_data = $activity->getActivityinfo($activity_id);
        if ($activity_data) {
            $db_activity_apply = M("life_volunteer_activity_apply");
            $apply_data = $db_activity_apply->where("communist_no = '$communist_no' and activity_id=$activity_id")->field('status,apply_desc')->find();
            //判断申请表没有数据或者数据状态不是已通过状态，视为未申请状态
            if (empty($apply_data)) {
                $activity_data['apply_status'] = "";
                $activity_data['apply_desc'] = "";
            } else {
                $activity_data['apply_status'] = $apply_data['status'];
                $activity_data['apply_desc'] = $apply_data['apply_desc'];

            }
            $this->send('获取成功', $activity_data, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_life_volunteer
     * @desc 志愿者申请
     * @param int communist_no 党员编号
     * @param string volunteer_content 申请内容
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function set_life_volunteer()
    {
        (new CommunistNoValidate())->goCheck();
        (new RequireValidate(['volunteer_content']))->goCheck();
        $data = I('post.');
        $communist_no = $data['communist_no'];
        $db_volunteer = M('life_volunteer');
        $num = $db_volunteer->where("communist_no = $communist_no and status=1")->count();
        if ($num != 0) : $this->send('请勿重复申请'); endif;
        $data['communist_no'] = $communist_no;
        $data['party_no'] = getCommunistInfo($communist_no, "party_no");
        //状态为未审核
        $data['status'] = "1";
        $data['update_time'] = date("Y-m-d H:i:s");
        $data['add_time'] = date("Y-m-d H:i:s");
        $result  = $db_volunteer->add($data);
        if ($result) {
            $this->send('申请成功', null, 1);
        } else {
            $this->send('申请失败');
        }
    }

    /**
     *  get_life_volunteer_status
     * @desc 获取志愿者状态
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/2/9
     * @version 1.0.0
     */
    public function get_life_volunteer_status()
    {
        (new CommunistNoValidate())->goCheck();

        $communist_no = I('post.communist_no');

        $db_volunteer = M('life_volunteer');

        $status = $db_volunteer->where("communist_no = $communist_no")->getField('status');
        $volunteer_content = $db_volunteer->where("communist_no = $communist_no")->getField('volunteer_content');
        
        $this->send('获取成功', $status, 1,'volunteer_content',$volunteer_content);
    }

    /**
     *  get_life_volunteer_activity_status
     * @desc 获取志愿者活动申请状态
     * @param int communist_no 党员编号
     * @param int activity_id 活动ID
     * @user liubingtao
     * @date 2018/2/9
     * @version 1.0.0
     */
    public function get_life_volunteer_activity_status()
    {
        (new CommunistNoValidate())->goCheck();
        (new IDValidate('life_volunteer_activity', 'activity_id'))->goCheck();

        $communist_no = I('post.communist_no');
        $activity_id = I('post.activity_id');

        $db = M('life_volunteer_activity_apply');

        $apply_info = $db->where("communist_no = '$communist_no' and activity_id='$activity_id'")->field('status,apply_desc')->find();
        if(!empty($apply_info)){
            $data['apply_status'] = $apply_info['status'];
            $data['apply_desc'] = $apply_info['apply_desc'];
        } else {
            $data['apply_status'] = 0;
            $data['apply_desc'] = '你未申请该活动';
        }
        $this->send('获取成功', $data, 1);
    }
    /**
     *  set_life_volunteer_activity
     * @desc 志愿者活动申请
     * @param int communist_no 党员编号
     * @param string apply_desc 申请内容
     * @param int party_no 支部编号
     * @param int activity_id 活动ID
     * @user liubingtao
     * @date 2018/2/7
     * @version 1.0.0
     */
    public function set_life_volunteer_activity()
    {
        (new CommunistNoValidate())->goCheck();
        (new IDValidate('life_volunteer_activity', 'activity_id'))->goCheck();
        (new RequireValidate(['apply_desc']))->goCheck();

        $db_activity_apply = M('life_volunteer_activity_apply');
        $data = I('post.');
        $communist_no = $data['communist_no'];
        $activity_id = $data['activity_id'];

        $num = $db_activity_apply->where("communist_no = '$communist_no' and activity_id=$activity_id")->count();

        if ($num != 0) : $this->send('请勿重复申请'); endif;

        $data['update_time'] = date("Y-m-d H:i:s");
        $data['add_time'] = date("Y-m-d H:i:s");
        $data['status'] = "1";

        $result = $db_activity_apply->add($data);
        if ($result) {
            $this->send('申请成功', null, 1);
        } else {
            $this->send('申请失败');
        }
    }
    /**
     *  get_volunteer_activity_list
     * @desc 获取志愿者活动列表
     * @param int activity_id 活动ID
     * @user wangzongbin
     * @date 2018/2/9
     * @version 1.0.0
     */
    public function get_volunteer_activity_list()
    {
    	(new NumberValidate(['page', 'pagesize']))->goCheck();
    	$pagesize = I('post.pagesize');
    	$page = (I('post.page') - 1) * $pagesize;
    	$db = M('life_volunteer_activity');
    	$activity = $db->limit($page,$pagesize)->order('add_time desc')->select();
    	foreach($activity as &$list){
            $list['activity_thumb'] = getUploadInfo($list['activity_thumb']) ;
            $list['add_time'] = getFormatDate($list['add_time'],"Y-m-d H:i");
    	}
    	if ($activity) {
    		$this->send('获取成功', $activity, 1);
    	} else {
    		$this->send('获取失败');
    	}
    }
    /**
     *  get_volunteer_list
     * @desc 获取志愿者列表
     * @user wangzongbin
     * @date 2018/2/9
     * @version 1.0.0
     */
    public function get_volunteer_list()
    {
       $activity_id = I('post.activity_id');
        if($activity_id){
            //查询参见人员
            $volunteer = M('life_volunteer_activity_apply')->where("activity_id='$activity_id' and status=2")->order('add_time desc')->select();
            $count = M('life_volunteer_activity_apply')->where("activity_id='$activity_id' and status=2")->count();
            foreach ($volunteer as &$communist){
            	$communist['communist_name'] = getCommunistInfo($communist['communist_no'],"communist_name");
            	$communist['party_name'] = getPartyInfo($communist['party_no'],"party_name");
            	$communist_avatar = getCommunistInfo($communist['communist_no'],'communist_avatar');
                if(!empty($communist_avatar)){
                    $communist['communist_avatar']  = "/".$communist_avatar;
                }else {
                    $communist['communist_avatar']  = "";
                }
               
               
            }
        }else{
            $db_volunteer=M('life_volunteer');
            $volunteer = $db_volunteer->where("status = 2")->order('add_time desc')->select();
            $count = $db_volunteer->where("status = 2")->order('add_time desc')->count();
            foreach ($volunteer as &$list){
            	$list['communist_name'] = getCommunistInfo($list['communist_no'],'communist_name');
            	$list['party_no'] = getPartyInfo($list['party_no'],"party_name");
            	$communist_avatar = getCommunistInfo($list['communist_no'],'communist_avatar');
                if(!empty($communist_avatar)){
                    $list['communist_avatar']  = "/".$communist_avatar;
                }else {
                    $list['communist_avatar']  = "";
                }
               
                
            }
        }
    	if ($volunteer) {
    		$this->send('获取成功', $volunteer, 1,'count',$count);
    	} else if(empty($volunteer)){
            $data['status'] = 1;
            $data['msg'] = '获取成功';
            $data['count'] = 0;
            $data['list'] = [];
            $this->ajaxReturn($data);
        }{
    		$this->send('获取失败');
    	}
    }
    
    
    /*********************end*****************************/
    /**
     *  get_life_affairs_list
     * @desc 获取三务公开列表信息
     * @param int party_no 支部编号
     * @param int cat_id 分类ID
     * @user ljj
     * @date 2018/3/13
     * @version 1.0.0
     */
    public function get_life_affairs_list()
    {
        (new RequireValidate(['party_no']))->goCheck();
        //         (new NumberValidate(['page', 'pagesize']))->goCheck();
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;
        //(new RequireValidate(['cat_id']))->goCheck();
        $db_cms_affairs = M('cms_affairs');
        $party_no = I('post.party_no'); // 党组织编号
        $cat_id = I('post.cat_id'); // 分类编号
        $where['party_no'] = array('eq',$party_no);
        if($cat_id){
        	$where['article_cat'] = array('eq',$cat_id);
        }
        $affairs_list = $db_cms_affairs->where($where)->limit($page,$pagesize)->field('article_id,article_cat,article_title,article_keyword,article_description,article_content,party_no,add_time,article_thumb,article_img')->order('add_time desc')->select();
        foreach ($affairs_list as &$list) {
        	$list['add_time'] = getFormatDate($list['add_time'], 'Y-m-d');
        	$list['article_thumb'] = getUploadInfo($list['article_thumb']);
        	$list['article_img'] = getUploadInfo($list['article_img']);
        }
        if(!empty($affairs_list)){
             $this->send('获取成功', $affairs_list, 1);
        } else {
            $this->send('无更多数据');
        }
    }
    
    /**
     *  get_life_affairs_info
     * @desc 获取三务公开详情信息
     * @param int article_id 三务公开Id
     * @param int cat_id 分类ID
     * @user WANGZONGBIN
     * @date 2018/3/13
     * @version 1.0.0
     */
    public function get_life_affairs_info()
    {
    	$db_cms_affairs = M('cms_affairs');
    	$party_no = I('post.party_no');
    	$article_id = I('post.article_id',1);
    	$cat_id = I('post.cat_id');
    	if(!empty($party_no)){
    		$map['party_no'] = array('eq',$party_no);
    	}
    	if(!empty($article_id)){
    		$map['article_id'] = array('eq',$article_id);
    	}
    	if(!empty($cat_id)){
    		$map['cat_id'] = array('eq',$cat_id);
    	}
    	$affairs_info = $db_cms_affairs->where($map)->field('article_id,article_cat,article_title,article_keyword,article_description,article_content,party_no,add_time,article_thumb,article_img,add_staff')->find();
    	if(!empty($affairs_info)){
    		$affairs_info['article_thumb'] = getUploadInfo($affairs_info['article_thumb']);
    		$affairs_info['article_img'] = getUploadInfo($affairs_info['article_img']);
    		$affairs_info['add_staff'] = getStaffInfo($affairs_info['add_staff']);
    		$this->send('获取成功', $affairs_info, 1);
    	} else {
    		$this->send('无更多数据');
    	}
    }
    /**
     *  get_imail_inbox_list
     * @desc 获取书记信箱列表信息
     * @param int article_id 三务公开Id
     * @user WANGZONGBIN
     * @date 2018/3/13
     * @version 1.0.0
     */
    public function get_imail_list()
    {
        //(new RequireValidate(['communist_no']))->goCheck();
        $communist_no = I('post.communist_no') ? I('post.communist_no') : 9999; // 获取党员编号
        $type = I('post.type'); // 获取类型
        $keywords=I('post.keyword'); // 获取关键字
        $pagesize = I('post.pagesize') ? I('post.pagesize') : 10; // 第几页
        $page = I('post.page') ? I('post.page') :1; // 数据条数
        $page = $page - 1;
        $where = "1=1";
        if($type == 1){
            if(!checkDataAuth($communist_no)){
                $where .= " and o.imail_sender = '$communist_no' and i.add_staff = '$communist_no'";
            }
            if($keywords){
                $where.=" and (o.imail_content like '%$keywords%' or o.imail_title like '%$keywords%')";
            }
            $where .= " and i.imail_receiver = '$communist_no'";
            $imail_data = M('com_imail_inbox as i')->field("i.inbox_id,i.imail_receiver,i.imail_receiver,i.add_staff,i.add_time,i.is_read,o.imail_sender,o.imail_title,o.imail_content,o.imail_id")->join("sp_com_imail_outbox as o on i.imail_contentid = o.imail_id")->order('add_time desc')->where($where)->limit($page,$pagesize)->select();
            foreach ($imail_data as $key => &$imail_value) {
                $imail_value['imail_sender'] = getCommunistInfo($imail_value['imail_sender']);
                $imail_value['add_time'] = getFormatDate($imail_value['add_time'] , 'Y-m-d');
            }
        } else {
            $db_imail_outbox = M("com_imail_outbox");
            if($keywords){
                $where.=" and (imail_content like '%$keywords%' or imail_title like '%$keywords%')";
            }
            if(!checkDataAuth($communist_no)){
                $where .= " and imail_sender = '$communist_no' and add_staff = '$communist_no'";
            }
            $where .= " and imail_sender = '$communist_no'";
            $outbox_sql = "select * from sp_com_imail_outbox  where $where order by add_time desc ";
            $imail_data = $db_imail_outbox->query($outbox_sql);
            foreach ($imail_data as &$outbox) {
                $imail_id  = $outbox['imail_id'];
                $outbox['inbox_id'] = $imail_id;
                $outbox['imail_sender'] = getCommunistInfo($outbox['imail_sender']);
                // $outbox['imail_content'] = mb_substr($outbox['imail_content'], 0, 15, 'utf-8') . "...";
                $outbox['add_time'] = getFormatDate($outbox['add_time'] , 'Y-m-d');
            }
        }
        if(!empty($imail_data)){
             $this->send('获取成功', $imail_data, 1);
        } else {
            $this->send('无更多数据');
        }
    }

    /**
     * @name  get_imail_info()
     * @desc  内部邮件详情
     * @param 
     * @return 
     * @author 王彬  王宗彬
     * @version 版本 V1.0.0
     * @updatetime  2017年10月20日 
     * @addtime   2016-08-04
     */
    public function get_imail_info()
    {
        $db_imail = M("com_imail_outbox");
        $db_imail_inbox = M("com_imail_inbox");
        $imail_id = I('post.imail_id');
        $inbox_id = $imail_id;
        $type = I('post.type') ? I('post.type') : 1;
        $communist_no = I('post.communist_no') ? I('post.communist_no') : 9999; // I方法获取党员编号;
        if($type == 2){
            if (! empty($imail_id)) {
                $status = array(
                        "is_read" => 1,
                        "imail_read_time" => date("Y-m-d H:i:s")
                );
                $db_imail_inbox->where("imail_receiver = '$communist_no' and imail_contentid = $imail_id")->save($status);
                $imail_data = $db_imail->where("imail_id = $imail_id")->find();
                $imail_data['send_name'] = getCommunistInfo($imail_data['imail_sender'], 'communist_name');
            
                $communist_arr = strToArr($imail_data['imail_receivers']);
                $imail_receivers = "";
                foreach($communist_arr as $arr){
                    $imail_receivers .= getCommunistInfo($arr,'communist_name').',';
                }
                $imail_data['receive_name'] = rtrim($imail_receivers,',');
                //$imail_data['receive_name'] = getCommunistInfo($imail_data['imail_receivers'], 'communist_name');
            }
        }else{
            if (!empty($inbox_id)){
                $imail_data = M('com_imail_inbox as i')->field("i.inbox_id,i.imail_receiver,i.imail_receiver,i.add_staff,i.add_time,i.is_read,o.imail_sender,o.imail_title,o.imail_content,o.imail_id,o.imail_attach")->join("sp_com_imail_outbox as o on i.imail_contentid = o.imail_id")->order('add_time desc')->where("inbox_id = '$inbox_id'")->find();
                $imail_data['send_name'] = getCommunistInfo($imail_data['imail_sender'], 'communist_name');
                $imail_data['receive_name'] = getCommunistInfo($imail_data['imail_receiver']);
            }
        }
        
        if(!empty($imail_data)){
             $this->send('获取成功', $imail_data, 1);
        } else {
            $this->send('无更多数据');
        }
    }

    /**
     * @name  com_imail_save()
     * @desc  内部邮件发送执行操作
     * @param 
     * @return 
     * @author 王彬
     * @version 版本 V1.0.0
     * @updatetime   
     * @addtime   2016-08-04
     */
    public function com_imail_save()
    {
        $post = $_POST;
        if (empty($post['people_type'])) {
            $post['people_type'] = 0;
        }
        $post['imail_receivers'] = rtrim($post['imail_receivers'],',');
        $post['imail_sender'] = $post['communist_no'];
        $post['imail_attach'] = '';
        $type = "1";
        $send_imail = setSendImail($post['imail_receivers'], $post['imail_content'], $post['imail_title'],$post['imail_attach'], $post['people_type'], $type,$post['communist_no']);
        $url = U('com_imail_outbox');
        if ($send_imail) {
            $imail_title = "您有一封邮件未读！请查看！！";
            $alert_url = "System/Com/com_imail_info/type/1/inbox_id/".$send_imail;
            //U('com_imail_info',array('imail_id'=>$send_imail));
            saveAlertMsg('22', $post['imail_receivers'],$alert_url, $imail_title,'','', '', session('communist_no'));
            $this->send('发送成功', $send_imail, 1);
        } else {
            $this->send('发送失败');
        }
    }
    /*********************投票*****************************/

    /**
     * @life_vote_list
     * @desc: 投票管理
     * @author:王宗彬
     * @addtime:2018-05-15
     * @version:V1.0.0
     **/
    public function get_life_vote_list()
    {
        $communist_no = I('post.communist_no');
        $type = 1; //1正开始  2结束（投票/时间结束）
        $map['subject_starttime'] = array('lt',date('y-m-d H:i:s'));
        $map['subject_endtime'] = array('gt',date('y-m-d H:i:s'));
        //$volist_list = M('life_vote_subject as s')->join("sp_life_vote_result as r on r.subject_id = s.subject_id", 'LEFT')->where("s.subject_starttime < '".date('y-m-d H:i:s')."' AND s.subject_endtime > '".date('y-m-d H:i:s')."' AND communist_no is null ")->field('s.subject_id,s.subject_content,r.communist_no')->group('s.subject_id')->select();
        $data =M('life_vote_subject')->where("subject_starttime < '".date('y-m-d H:i:s')."' AND subject_endtime > '".date('y-m-d H:i:s')."'")->select();
        $volist_list = array();
        foreach ($data as $list) {

            $where['subject_id'] = $list['subject_id'];
            $where['communist_no'] = $communist_no;
            $res = M('life_vote_result')->where($where)->getField('result_id');
            if(!$res){
                $volist_list[] = $list;
            }
        }
        if ($volist_list) {
            $this->send('获取成功', $volist_list, 1);
        } else {
            $this->send('获取失败');
        }
    }
    /**
     * @life_vote_info
     * @desc: 投票管理
     * @author:王宗彬
     * @addtime:2018-05-15
     * @version:V1.0.0
     **/
    public function get_life_vote_info()
    {
        $subject_id = I('post.subject_id');
        $life_vote_subject = M('life_vote_subject');
        $life_vote_option = M('life_vote_option');
        $life_vote_result = M('life_vote_result');
        $subject_info = $life_vote_subject->where("subject_id='$subject_id'")->field('subject_id,vote_id,subject_content,is_multiple')->order('add_time desc')->find();
        if(!empty($subject_info)){
            $subject_list['subject_info'] = $subject_info;
            $option_list = $life_vote_option->where("subject_id=" . $subject_info['subject_id'])->select();
            if(!empty($option_list)){
                foreach ($option_list as &$option) {
                    $option['result_num'] = $life_vote_result->where("option_id=" . $option['option_id'])->count('option_id');
                }
            }
            $subject_list['option_list'] = $option_list;
        }
        if ($subject_list) {
            $this->send('获取成功', $subject_list, 1);
        } else {
            $this->send('获取失败');
        }
    }
    /**
     * @life_vote_do_save
     * @desc: 投票
     * @author:王宗彬
     * @addtime:2018-05-15
     * @version:V1.0.0
     **/
    public function life_vote_do_save()
    {
        $post = I('post.');
        $post['communist_no'] = $post['communist_no'];
        $post['add_time'] = date("Y-m-d H:i:s");
        $post['update_time'] = date("Y-m-d H:i:s");
        $result = M('life_vote_result')->add($post);
        if ($result) {
            $this->send('提交成功', $result, 1);
        } else {
            $this->send('提交失败');
        }
    }


    /*********************end*****************************/
}
