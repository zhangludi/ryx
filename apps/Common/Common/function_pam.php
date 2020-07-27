<?php
    /**
     * 获取学校数据
     * @param    int    $school_id   学校id
     * @param    string    $field    字段 school_name all
     * @return   array string     返回一个字段或数组
     * @author：刘丙涛
     * @addtime:2017-06-19
     * @version：V1.0.0
     */
    function getSchoolInfo($school_id,$field = 'school_name'){
        $db_school = new \Dj\Model\DjSchoolModel();
        $map = "school_id=$school_id";
        if ($field == 'all'){
            $school_info = $db_school->selectData('0',$map);
        }else{
            $school_info = $db_school->selectData('0',$map,$field);
            $school_info = $school_info[$field];
        }
        return $school_info;
    }
    /**
     * 获取学校数据
     * @return   array string     返回一个字段或数组
     * @author：刘丙涛
     * @addtime:2017-06-19
     * @version：V1.0.0
     */
    function getSchoollist(){
        $db_school = new \Dj\Model\DjSchoolModel();
        $map = "status=1";
        $school_list = $db_school->selectData('1',$map);
        return $school_list;
    }
/**
 * 获取班级数据
 * @param    int    $school_id   学校id
 * @param    string    $returntype   返回类型 arr数组 str id字符串
 * @return   array string     返回一个字段或数组
 * @author：刘丙涛
 * @addtime:2017-06-19
 * @version：V1.0.0
 */
function getClasseslist($school_id,$returntype='arr'){
    $db_classes = new \Dj\Model\DjSchoolClassesModel();
    $map = "school_id=$school_id and status=1";
    $school_list = $db_classes->selectData('1',$map);
    if ($returntype == 'str'){
        $classes_id = '';
        foreach($school_list as $data){
            $classes_id .= $data['classes_id'].",";
        }
        $school_list = substr($classes_id,0,-1);
    }
    return $school_list;
}
    /**
     * 获取班级数据
     * @param    int    $classes_id   班级id
     * @param    string    $field    字段 school_name all
     * @return   array string     返回一个字段或数组
     * @author：刘丙涛
     * @addtime:2017-06-19
     * @version：V1.0.0
     */
    function getClassesInfo($classes_id,$field = 'classes_name'){
        $db_classes = new \Dj\Model\DjSchoolClassesModel();
        $map = "classes_id=$classes_id";
        if ($field == 'all'){
            $classes_info = $db_classes->selectData('0',$map);
        }else{
            $classes_info = $db_classes->selectData('0',$map,$field);
            $classes_info = $classes_info[$field];
        }
        return $classes_info;
    }
    
    /**
     * @name  getCoumtryCount()
     * @desc  获取村民人数
     * @param:
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-06-08
     */
    function getCoumtryCount($party_no){
        $db_communist = M('ccp_communist');
        $where['is_identity'] = array('in','1,11,101,111');
        $where['status'] = '1';
        $where['party_no'] = $party_no;
        $num = $db_communist->where($where)->count();
        return $num;
    }

    /**
     * @name  getCoumtryscore()
     * @desc  获取为村指数
     * @param:
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-06-08
     */
    function getCoumtryscore($party_no){
        $db_party = M('ccp_party');
        $db_communist = M('ccp_communist');
        $party_list = $db_party->where('status=1 and is_village=3')->field('party_no')->select();
        $array = array();
        $map['is_identity'] = 1;
        $map['communist_post_no'] = 901;
        foreach ($party_list as $list){
            $map['party_no'] = $list['party_no'];
            $num = $db_communist->where($map)->count();
            $array[] = $num;
        }

        return $num;
    }

    
    /**
     * @name  setProve()
     * @desc  写入认证信息
     * @param int $type 认证类型 1:村民老师 2党员
     * @param array $apply_info 认证信息数组
     * @param array $user_role 权限
     * @param string $communist_no 党员编号
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-06-30
     */
    function setProve($type,$apply_info,$user_role,$communist_no){
        $db_apply = new \Dj\Model\DjApplyModel();
        $db_communist = M('ccp_communist');
        $db_user = M('sys_user');
        $weixin = json_decode($apply_info['apply_weixin'],true);
        $db_communist->startTrans();
        $communist_data['open_id'] = $weixin['openid'];//微信唯一id
        $communist_data['classes_id'] = $apply_info['classes_id'];//学校id
        $communist_data['is_auth'] = $apply_info['apply_type'];//权限
        if ($type == '1'){
            $no = buildOrderNo();
            $communist_data['communist_id'] = $no;//
            $communist_data['communist_no'] = $no;//工号
            $communist_data['communist_name'] = $apply_info['apply_name'];//姓名
            $communist_data['party_no'] = $apply_info['party_no'];//部门编号
            $communist_data['communist_avatar'] = $weixin['headimgurl'];//头像
            $communist_data['communist_sex'] = $weixin['sex'];//性别
            $communist_data['communist_idnumber'] = $apply_info['apply_identity'];//身份证
            $communist_data['add_staff'] = session('staff_no');//添加人
            $communist_data['status'] = 1;//状态
            $communist_data['add_time'] = date('Y-m-d H:i:s');//添加时间
            $result = $db_communist->add($communist_data);
            $user_data['user_nickname'] = $apply_info['apply_name'];//
            $user_data['user_role'] = $user_role;//村民权限
            $user_data['user_relation_no'] = $no;//员工编号
            $user_data['user_relation_type'] = '7';//类型
            $user_data['add_staff'] = session('staff_no');//添加人
            $user_data['status'] = '1';//状态
            $user_data['add_time'] = date('Y-m-d H:i:s');//添加时间
            $flag = $db_user->add($user_data);
        }else{
            $communist_data['update_time'] = date('Y-m-d H:i:s');//修改时间
            $where['communist_no'] = $communist_no;
            $result = $db_communist->where($where)->save($communist_data);
            $map['user_relation_no'] = $communist_no;
            $role = $db_user->where($map)->field('user_role')->find();
            $user_data['user_role'] = $role.','.$user_role;//村民权限
            $user_data['add_staff'] = session('staff_no');//添加人
            $flag = $db_user->where($map)->save($user_data);
        }

        $data['apply_id'] = $apply_info['apply_id'];
        $data['status'] = '2';
        $finally = $db_apply->updateData($data,'apply_id');
        if ($result && $flag && $finally){
            $db_communist->commit();
            $result = true;
        }else{
            $db_communist->rollback();
            $result = false;
        }
        return $result;
    }
    /**
     * @name  checkAuthCommunist()
     * @desc  判断人员权限
     * @param string $auth 权限
     * @param string $communist_no 党员编号
     * @param string $party_no 党组织或村编号
     * @param string $school_id 学校id
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-06-30
     */
    function checkAuthCommunist($communist_no,$auth,$party_no,$school_id){
        $db_communist = M('ccp_communist');
        $is_identity = getCommunistInfo($communist_no,'is_identity');
        $count = substr_count($auth,$is_identity);
        if ($count > 0){
            $result = true;
            $flag = true;
            $where['communist_no'] = $communist_no;
            if (!empty($party_no)){
                $where['village_no'] = $party_no;
                $result = $db_communist->where($where)->find();
            }
            if (!empty($school_id)){
                $where['school_id'] = $school_id;
                $flag = $db_communist->where($where)->find();
            }
            if ($result && $flag){
                return true;
            }else{
                false;
            }
        }else{
            return false;
        }
    }
    /**
     * @name  getDynamicList()
     * @desc  获取动态列表
     * @param string $party_no 党组织或村编号
     * @param string $communist_no 登录人编号
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function getDynamicList($party_no,$communist_no){
        $db_article = new \Dj\Model\DjArticleModel();
        $db_comment = new \Dj\Model\DjArticleCommentModel();
        $db_praise = new \Dj\Model\DjArticlePraiseModel();
        $map = "party_no=$party_no and status";
        $article_list = $db_article->selectData('1',$map);
        foreach ($article_list as &$list){
            $list['communist_name'] = getCommunistInfo($list['communist_no']);
            $list['communist_avatar'] = getCommunistInfo($list['communist_no'],'communist_avatar');
            if ($list['article_img']){
                $list['article_img'] = explode(',',getUploadInfo($list['article_img']));
            }
            $list['timeTran'] = timeTran($list['add_time']);
            //是否点赞
            if ($communist_no){
                $where = "communist_no=$communist_no and status=1 and article_id=".$list['article_id'];
                $is_praise = $db_praise->selectData('0',$where);
                if ($is_praise){
                    $list['is_praise'] = 1;
                }else{
                    $list['is_praise'] = 0;
                }
            }else{
                $list['is_praise'] = 0;
            }
            //点赞数量
            $praise_num = $db_praise->selectData('1',"status=1 and article_id=".$list['article_id']);
            $list['praise_num'] = count($praise_num);
            //点赞
            $praise_list = $db_praise->selectData('1','status=1 and article_id='.$list['article_id'],'communist_no',
                'add_time asc','5');
            $praise = '';
            foreach ($praise_list as $value){
                $praise .= getCommunistInfo($value['communist_no']).',';
            }
            $list['praise_name'] = substr($praise,0,-1);
            $list['comment_list'] = $db_comment->getCommentInfo($list['article_id']);
            if (empty($list['comment_list'])){
                $list['is_comment'] = 0;
            }else{
                $list['is_comment'] = 1;
            }
            if (!$is_praise && !$list['comment_list']){
                $list['is_all'] = 0;
            }else{
                $list['is_all'] = 1;
            }
        }
        return $article_list;
    }
    /**
     * @name  getPraise()
     * @desc  获取点赞
     * @param string $article_id 文章id
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function getPraise($article_id){
        $db_praise = new \Dj\Model\DjArticlePraiseModel();
        //点赞数量
        $praise_num = $db_praise->selectData('1',"status=1 and article_id=$article_id");
        $list['praise_num'] = count($praise_num);
        //点赞
        $praise_list = $db_praise->selectData('1',"status=1 and article_id=$article_id",'communist_no',
            'add_time asc','5');
        $praise = '';
        foreach ($praise_list as $value){
            $praise .= getCommunistInfo($value['communist_no']).',';
        }
        $list['praise_name'] = substr($praise,0,-1);
        return $list;
    }
    /**
     * @name  getIsexistence()
     * @desc  获取是否有点赞与评论
     * @param string $article_id 文章id
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function getIsexistence($article_id){
        $db_praise = new \Dj\Model\DjArticlePraiseModel();
        $db_comment = new \Dj\Model\DjArticleCommentModel();
        //点赞数量
        $praise_num = $db_praise->selectData('1',"status=1 and article_id=$article_id");
        $list['comment_list'] = $db_comment->getCommentInfo($article_id);
        if (!count($praise_num) && !$list['comment_list']){
            $is_all = 0;
        }else{
            $is_all = 1;
        }
        return $is_all;
    }
    /**
     * @name  getemaillist()
     * @desc  获取email
     * @param string $communist_no 人员编号
     * @param string $is_read 已读未读
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function getemaillist($communist_no,$is_read){
        $email_list = M()->query("select inbox_id,imail_id,imail_sender,imail_content,i.add_time from sp_com_email_inbox as i left join sp_com_email_outbox as o on i.email_contentid=o.imail_id where email_receiver=$communist_no and is_read=$is_read and i.is_del=0 and o.is_del=0");
        foreach ($email_list as &$list){
            $list['communist_name'] = getCommunistInfo($list['imail_sender']);
        }
        return $email_list;
    }
    /**
     * @name  setemailread()
     * @desc  email已读
     * @param string $communist_no 人员编号
     * @param string $imail_id 邮件id
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function setemailread($communist_no,$imail_id){
        $db_inbox = M('com_email_inbox');
        $data['is_read'] = '1';
        $data['email_read_time'] = date('Y-m-d H:i:s');
        $data['update_time'] = date('Y-m-d H:i:s');
        $where['email_contentid'] = $imail_id;
        $where['email_receiver'] = $communist_no;

        $db_inbox->where($where)->save($data);
    }
     /**
     * @name  get_article_catname()
     * @desc   
     * @param string $catid 取某个菜单的值
     * @param string $menu  取该下拉菜单列表
     * @return int
     * @author 王玮琪
     * @version 版本 V1.0.0
     * @addtime   2017-07-31
     */
     function get_article_catname($catid,$menu){
        $where['cat_id'] = $catid;
          $cat_data = M('cms_article_category')->where($where)->field("cat_name")->find();
          $category_data = M('cms_article_category')->where($where)->select();
          if(!empty($menu)){//取下拉菜单值
             if (!empty($category_data)) {
                echo "<ul class='down-meau'>";
                foreach ($category_data as $key => $value) {
                   echo " <li><a href='".U('Cms/cms_article_list',array('cat_id',$value['cat_id']))."''>".$value['cat_name']."</a></li>"; 
                }
                echo "</ul>" ;
             }
          }else{//取导航菜单
              if(empty($category_data)){
                 echo "<a href='".U('Cms/cms_article_list',array('cat_id',$catid))."' >".$cat_data['cat_name']."</a>";
              }else{
                 echo "<a  >".$cat_data['cat_name']."</a>";
              }
          }
    }
    /**
     * @name  getEmailInfo()
     * @desc  email已读
     * @param string $field 字段
     * @param string $imail_id 邮件id
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function getEmailInfo($imail_id,$field){
        $db_outbox = M('com_email_outbox');
        $where['imail_id'] = $imail_id;
        $where['is_del'] = '0';
        $email_list = $db_outbox->where($where)->find();
        if ($field != 'all'){
            $email_list = $email_list[$field];
        }
        return$email_list;
    }
    /**
     * @name  setnoticeread()
     * @desc  通知公告已读
     * @param string $communist_no 人员编号
     * @param string $notice_id 公告id
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function setnoticeread($communist_no,$notice_id){
        $db_log = M('oa_notice_log');
        $data['is_read'] = '1';
        $data['update_time'] = date('Y-m-d H:i:s');
        $where['notice_id'] = $notice_id;
        $where['communist_no'] = $communist_no;
        $db_log->where($where)->save($data);
    }

    /**
     * @name  checkcommunistidentity()
     * @desc  判断当前登录人的身份
     * @param string $communist_no 人员编号
     * @param string $notice_id 公告id
     * @return int
     * @author 刘丙涛
     * @version 版本 V1.0.0
     * @addtime   2017-07-11
     */
    function checkcommunistidentity($communist_no,$notice_id){
        $db_log = M('oa_notice_log');
        $data['is_read'] = '1';
        $data['update_time'] = date('Y-m-d H:i:s');
        $where['notice_id'] = $notice_id;
        $where['communist_no'] = $communist_no;
        $db_log->where($where)->save($data);
    }