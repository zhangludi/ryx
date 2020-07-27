<?php
/**
 * Created by PhpStorm.
 * User: wangzongbin
 * Date: 2018/4/4
 * Time: 11:06
 */

namespace Index\Controller;


use Think\Controller;

class NewsController extends Controller
{
	/**
	 *  _initialize
	 * @desc 构造函数
	 * @user liubingtao
	 * @date 2018/4/8
	 * @version 1.0.0
	 */
	public function _initialize()
	{
		$door_communist_no = session('door_communist_no');
		if (!empty($door_communist_no)) {
			$this->assign('is_login', 1);
		} else {
			$this->assign('is_login', 0);
		}
        $where['status'] = '1';
        $nav_list = M('sys_nav')->where($where)->order('nav_order asc')->select();
        foreach ($nav_list as  $key=>&$list) {
            if($key==4){
                $list['class_login'] = 'see-details';
            }
            $list['nav_url'] = U($list['nav_url']);
        }
        $this->assign('nav_list',$nav_list);
        $where['status'] = '1';
        $blogroll_list = M('sys_blogroll')->where($where)->select();
        foreach ($blogroll_list as  &$list) {

            if($list['blogroll_type'] == '1'){
                $list['code'] = "<li><a href='".$list['blogroll_url']."'>".$list['blogroll_name']."</a> </li>";
            }else{
                $list['code'] =  "<li><a href='".U($list['blogroll_url'])."' target='_blank'>".$list['blogroll_name']."</a></li>";
            }
        }
        $this->assign('blogroll_list',$blogroll_list);
	}
	/**
     * @name:ipam_news_index
     * @desc：新闻中心首页
     * @param：null
     * @return：
     * @author：王宗彬
     * @addtime:2018-04-11
     * @updatetime:2018-04-11
     * @version：V1.0.0
     **/
    public function ipam_news_index()
    {
    	// 获取新闻动态列表
    	$news_list = getArticleList('30','','10');
   	 	foreach ($news_list['data'] as &$news) {
   	 		$news['article_thumb'] = getUploadInfo($news['article_thumb']);
		}
        // dump($news_list);die;
		$this->assign('news_list',$news_list['data']);
		// 获取高层动态列表
        $cat_list = M('cms_article_category')->where("cat_type = 4")->field('cat_id,cat_name')->select();
//    	$top_list = getArticleList('43','','10');
//   	 	foreach ($top_list['data'] as &$top) {
//   	 		$top['article_thumb'] = getUploadInfo($top['article_thumb']);
//   	 		$top['add_time'] = getFormatDate($top['add_time'], 'Y-m-d');
//   	 		$top['add_staff_name'] = getStaffInfo($top['add_staff']);
//		}
		$this->assign('cat_list',$cat_list);
    	// 获取公告列表
    	$notice_list = getNoticeList('','','','5');
   	 	foreach ($notice_list as &$notice) {
   	 		$notice['notice_attach'] = getUploadInfo($notice['notice_attach']);
            $notice['notice_content'] = mb_substr(removeHtml($notice['notice_content']), 0,200,'utf-8');
			$notice['add_time'] = getFormatDate($notice['add_time'], 'Y-m-d');
		}
		$this->assign('notice',$notice_list[0]);
		$this->assign('notice_list',$notice_list);
    	//党员声音
		$article_list = getArticleList('46','','5'); 
		foreach ($article_list['data'] as &$article) {
			$article['article_thumb'] = getUploadInfo($article['article_thumb']);
			$article['add_time'] = getFormatDate($article['add_time'], 'Y-m-d');
		}
		$this->assign('communist_voice',$article_list['data'][0]);
		$this->assign('article_list',$article_list['data']);
		//新闻快讯
		$newsflash_list = getArticleList('47','','5');
		foreach ($newsflash_list['data'] as &$newsflash) {
			$newsflash['article_thumb'] = getUploadInfo($newsflash['article_thumb']);
		}
		$this->assign('newsflash_list',$newsflash_list['data']);
		//热点会议
		$meetting_list = getMeetingList();
		foreach ($meetting_list['data'] as &$meetting) {
			$meetting['meetting_thumb'] = getUploadInfo($meetting['meetting_thumb']);
		}
        // dump($meetting_list);die;
		$this->assign('meetting_list',$meetting_list['data']);
		//精选热文
		$article_choiceness = getArticleList('44','','5');
		foreach ($article_choiceness['data'] as &$choiceness) {
			$choiceness['article_thumb'] = getUploadInfo($choiceness['article_thumb']);
		}
		$this->assign('choiceness',$article_choiceness['data'][0]);
		$this->assign('choiceness_list',$article_choiceness['data']);
		//经典回顾
		$classic_review = getArticleList('45','','5');
		foreach ($classic_review['data'] as &$review) {
			$review['article_thumb'] = getUploadInfo($review['article_thumb']);
		}
		$this->assign('waiming',$classic_review['data'][0]);
		$this->assign('classic_review',$classic_review['data']);

        $this->display('ipam_news_index');
    }

    /**
     * @name:ipam_news_detail
     * @desc：新闻详情页面
     * @param：null
     * @return：
     * @author：王宗彬
     * @addtime:2018-04-11
     * @updatetime:2018-04-11
     * @version：V1.0.0
     **/
    public function ipam_news_detail() {
        //最新专题学习
        $db_edutopic=M('edu_topic');
        $edutopic_list = $db_edutopic->where("status=1")->order('add_time desc')->find();
        $edutopic_list['topic_img'] = getUploadInfo($edutopic_list['topic_img']);
        $this->assign('edutopic_list',$edutopic_list);
    	$article_id=I("get.article_id");
        $door_communist_no =  session('door_communist_no');
        //相关文章
        $article_cat = M('cms_article')->where("article_id=$article_id")->getField('article_cat');
        $article_data = M('cms_article')->where()->limit(5)->field('article_title,article_thumb,article_view')->select();
        foreach ($article_data as &$value) {
            $value['article_thumb'] = getUploadInfo($value['article_thumb']);
        }
        $this->assign('article_data',$article_data);
        //收藏
        $fav_map['article_id'] = $article_id;
        $fav_map['communist_no'] = $door_communist_no;
        $fav_mcount = M('cms_article_fav')->where($fav_map)->count('fav_id');
        $this->assign('fav_mcount',$fav_mcount);
        //评论
        $comment_data = M('cms_article_comment')->where("article_id=$article_id")->field("comment_content,communist_no,add_time,comment_id")->select();
        foreach ($comment_data as &$value) {
            $give_where['comment_id'] = $value['comment_id'];
            $give_where['communist_no'] = session('door_communist_no');
            $value['code'] = M('cms_article_give')->where($give_where)->count('give_id');
            $value['communist_no'] = getCommunistInfo($value['communist_no']);
            $comment_give_map['comment_id'] = $value['comment_id'];
            $comment_give_map['give_type'] = 2;
            $value['comment_give'] = M('cms_article_give')->where($comment_give_map)->count('give_id');
        }
        $last_names = array_column($comment_data,'comment_give');//重新排序
        array_multisort($last_names,SORT_DESC,$comment_data);
        $this->assign('comment_data',$comment_data);
        $comment_count = M('cms_article_comment')->where("article_id=$article_id")->count('comment_id');
        $this->assign('comment_count',$comment_count);
    	$notice_id=I("get.notice_id");
    	$data = "";
    	if(!empty($article_id)){
    		$data_info = getArticleInfo($article_id,'all');
            $data['article_id'] = $data_info['article_id'];
            $data['article_source'] = $data_info['article_source'];
            $data['article_description'] = $data_info['article_description'];
            $data['add_time'] = $data_info['add_time'];
    		$data['title'] = $data_info['article_title'];
    		$data['content'] = $data_info['article_content'];
            $data['add_staff'] = peopleNoName($data_info['add_staff']);
            $data['article_attach'] = $data_info['article_attach'];
            $article_map['article_id'] = $article_id;
    		M('cms_article')->where($article_map)->setInc('article_view');
    	}else{
            $notice_map['notice_id'] = $notice_id;
            $notice_map['status'] = 1;
    		$data_info = M('oa_notice')->where($notice_map)->find();
    		$data['title'] = $data_info['notice_title'];
    		$data['content'] = $data_info['notice_content'];
            $data['add_staff'] =  getStaffInfo($data_info['add_staff']);
            $data['article_attach'] = $data_info['notice_attach'];
    	}
    	$data['add_time'] =  getFormatDate($data_info['add_time'], 'Y-m-d');
    	$this->assign('data',$data);
        $db_give = M('cms_article_give');
        $count['article_id'] = $article_id;
        $count['give_type'] = 1;
        $give_count = $db_give->where($count)->count('give_id');
        $this->assign('give_count',$give_count);
        $give_map['article_id'] = $article_id;
        $give_map['give_type'] = 1;
        $give_map['communist_no'] = session("door_communist_no");
        $give_data = $db_give->where($give_map)->count('give_id');
        if($give_data<1){
            $msg = 1;
        }else{
            $msg = 2;
        }
        $this->assign('msg',$msg);
    	$this->display('ipam_news_detail');
    }
    /**
     * @name:news_collect
     * @desc：收藏
     * @param：null
     * @return：
     * @author：刘长军
     * @addtime:2019-08-27
     * @updatetime:2019-08-27
     * @version：V3.0.0
     **/
    public function news_collect(){
        $article_id = I('post.article_id');
        $db_fav = M('cms_article_fav');
        $where['communist_no'] = session('door_communist_no');
        $where['article_id'] = $article_id;
        $fav_count = $db_fav->where($where)->count('fav_id');
        if($fav_count == 0){
            $where['add_time'] = date("Y-m-d H:i:s");
            $where['add_staff'] = session('door_communist_no');
            $fav_data = $db_fav->add($where);
            $msg = 1;
        }else{
            $fav_data = $db_fav->where($where)->delete();
            $msg = 2;
        }
        $this->ajaxReturn($msg);
    }
    /**
     * @name:give_like
     * @desc：点赞
     * @param：null
     * @return：
     * @author：刘长军
     * @addtime:2019-08-26
     * @updatetime:2019-08-26
     * @version：V3.0.0
     **/
    public function news_give_like(){
        $msg = '';
        $give_type = I('post.give_type');
        $data['comment_id'] = I('post.comment_id');
        $db_give = M('cms_article_give');
        $data['article_id'] = I('post.article_id');
        $data['give_type'] = $give_type;
        $data['communist_no'] = session('door_communist_no');
        $give_data = $db_give->where($data)->count('give_id');
        $give_map['article_id'] = $data['article_id'];
        $give_map['give_type'] = $data['give_type'];
        $give_map['comment_id'] = $data['comment_id'];
        if($give_data == 0){
            $data['add_staff'] = session('door_communist_no');
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $give_add = $db_give->add($data);
            $msg['give_count'] = $db_give->where($give_map)->count('give_id');
            if($give_add){
                $msg['num'] = 1;
            }
        }else{
            $give_add = $db_give->where($data)->delete();
            $msg['give_count'] = $db_give->where($give_map)->count('give_id');
            if($give_add){
                $msg['num'] = 2;
            }
        }
        $this->ajaxReturn($msg);
    }
    /**
     * @name:ipam_news_list
     * @desc：新闻详情页面
     * @param：null
     * @return：
     * @author：王宗彬
     * @addtime:2018-04-11
     * @updatetime:2018-04-11
     * @version：V1.0.0
     **/
    public function ipam_news_list() {
    	$id = I('get.id');
    	$cat_name = getArticleCatInfo($id);
    	if($id == 1){
    		$cat_name = "通知公告";
    	}elseif ($id == 2){
    		$cat_name = "今日首推";
    	}
    	$this->assign('cat_name',$cat_name);
    	$this->assign('id',$id);
    	
    	switch ($id) {
    	 	case 1:
    	 		$count = $data_list = M('oa_notice')->where("status=1")->count();break;
    	 	case 2:
    	 		$count = M('cms_article')->where("article_point = 1")->count();break;
    	 	default:
                $article_map['status'] = 1;
                $article_map['article_cat'] = $id;
    	 		$count = M('cms_article')->where($article_map)->count();break;
    	 }
    	 $this->assign('count',$count);
    	 $this->display('ipam_news_list');
    }
    /**
     * @name:getNewsList
     * @desc：ajax分页
     * @param：null
     * @return：
     * @author：王宗彬
     * @addtime:2018-04-11
     * @updatetime:2018-04-11
     * @version：V1.0.0
     **/
    public function getNewsList()
    {
    	$id = I('post.id');
    	$pagesize = I('post.pagesize');
    	$page = (I('post.page') - 1) * $pagesize;
    	switch ($id) {
    		case 1:
                $communist_no = session('door_communist_no');
                $notice_map['status'] = 1;
                if(!empty($communist_no)){
                    $notice_map['_string'] = "find_in_set($communist_no,notice_communist)";
                }
    			$data_list = M('oa_notice')->where($notice_map)->order('add_time desc')->limit($page,$pagesize)->select();
    			;break;
    		case 2:
    			$data_list = M('cms_article')->where("article_point = 1")->order("update_time desc")->limit($page,$pagesize)->select();
    			;break;
    		default:
    			$list = getArticleList($id,$page,$pagesize);
    			$data_list = $list['data'];
    			;break;
    	}
    	$data = '';
    	foreach ($data_list as $list) {
	    		if($id == '1'){
	    			$list['notice_content'] = removeHtml($list['notice_content']);
	    			$data .= "<li class='public_word_lr_two mb-15'>
		    			<a class='see-details see_news_detail'  href='".U('ipam_news_detail', array('notice_id' => $list['notice_id']))."'>
			    			<div class='public_word_lr_two_img w-120'>
					    		<img class='w-all h-90' src='".getUploadInfo($list['notice_attach'])."' alt='{$list['notice_title']}'>
					    	</div>
					    	<div class='public_word_lr_two_content'>
					    		<div class='public_word_lr_two_content_title fsize-16 fcolor-11 material_over_point w-500'>{$list['notice_title']}</div>
					    		<div class='public_word_lr_two_content_date fsize-12 fcolor-b3b3b3'>{$list['add_time']}</div>
					    	
					    	</div>
				    	</a>
	    			</li>";
    			}else{
    				$data .= "<li class='public_word_lr_two mb-15'>
		    			<a  class='see-details' href='".U('ipam_news_detail', array('article_id' => $list['article_id']))."'>
			    			<div class='public_word_lr_two_img w-120'>
					    		<img class='w-all h-90' src='".getUploadInfo($list['article_thumb'])."' alt='{$list['article_title']}'>
					    	</div>
					    	<div class='public_word_lr_two_content'>
					    		<div class='public_word_lr_two_content_title fsize-16 fcolor-11 material_over_point w-500'>{$list['article_title']}</div>
					    		<div class='public_word_lr_two_content_date fsize-12 fcolor-b3b3b3'>{$list['add_time']}</div>
					    		<p class='fsize-14 fcolor-808080'>{$list['article_description']}</p>
					    	</div>
				    	</a>
	    			</li>";
	    		}
	    	}
            ob_clean();$this->ajaxReturn(['content' => $data]);
        }
    /**
     * @name:ipam_comment_save
     * @desc：文章评论保存
     * @param：null
     * @return：
     * @author：王宗彬
     * @addtime:2018-04-11
     * @updatetime:2018-04-11
     * @version：V1.0.0
     **/
    public function ipam_article_comment_save() {
        $comment['article_id']=I("post.article_id");
        $comment['comment_type']=I("post.comment_type");
        $comment['comment_content']=I("post.comment_content");
        $comment['communist_no'] = session('door_communist_no');
        $comment['status'] = 1;
        $comment['comment_time'] = date('Y-m-d H:i:s');
        $comment['add_time'] = date('Y-m-d H:i:s');
        $comment['update_time'] = date('Y-m-d H:i:s');
        $comment_result = M('cms_article_comment')->add($comment);
        if($comment_result){
            showMsg('success', '评论成功', U('ipam_news_detail',array('article_id'=>$comment['article_id'])));
        } else {
            showMsg('error', '评论失败', '');
            
        }
    }
}