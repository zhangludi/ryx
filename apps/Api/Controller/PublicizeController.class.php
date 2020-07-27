<?php
/**
 * 宣传平台
 * User: liubingtao
 * Date: 2018/1/27
 * Time: 下午2:33
 */

namespace Api\Controller;

use Api\Validate\CommunistNoValidate;
use Api\Validate\NumberValidate;
use Api\Validate\RequireValidate;
use Cms\Model\CmsArticleModel;

class PublicizeController extends Api
{
    /**
     *  get_bd_banner_list
     * @desc 获取相应位置的banner图片列表
     * @param int location_id ID
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_bd_banner_list(){
        (new NumberValidate(['location_id']))->goCheck();

        $maps['ad_location'] = ['eq', I("post.location_id")];
        $db_ad = M('sys_ad');
        $ad_list = $db_ad->where($maps)->field('ad_img,ad_title')->select();
        if ($ad_list) {
            foreach ($ad_list as &$ad) {
                $ad['ad_img'] = getUploadInfo($ad['ad_img']);
            }
            $this->send('获取成功', $ad_list, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_article_cat_list
     * @desc 获取文章栏目列表
     * @user 
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_cms_article_cat_list(){
        $cat_ids = I('post.cat_ids');
    	$cat_list = getArticleCatList($cat_ids);
    	if ($cat_list) {
    		$this->send('获取成功', $cat_list, 1);
    	} else {
    		$this->send();
    	}
    }
    /**
     *  get_article_list
     * @desc 获取文章列表
     * @param int cat_id 栏目ID
     * @param int page 页数
     * @param int pagesize 条数
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_cms_article_list(){
        (new NumberValidate(['cat_id', 'page', 'pagesize']))->goCheck();

        $pagesize = I('post.pagesize');

        $page = (I('post.page') - 1) * $pagesize;

        $cat_id = I('post.cat_id');
        $type = I('post.type');

        $article_list = getArticleList($cat_id, $page, $pagesize,'','','',$type);
        if ($article_list['data']) {

            foreach ($article_list['data'] as &$article) {
                $article['article_thumb'] = getUploadInfo($article['article_thumb']);
                //$article['add_staff'] = getStaffInfo($article['add_staff']);
                $article['add_staff'] = peopleNoName($article['add_staff']);
                $article['add_time'] = getFormatDate($article['add_time'], "Y-m-d");
            }
            $this->send('获取成功', $article_list['data'], 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_cms_article_info
     * @desc 获取文章详情
     * @param int article_id ID
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_cms_article_info(){
        (new NumberValidate(['article_id']))->goCheck();

        $article_id = I('post.article_id');
		$communist_no = I('post.communist_no');
        $article_info = getArticleInfo($article_id, "all");

        if ($article_info) {
            //$article_info['add_staff'] = getStaffInfo($article_info['add_staff'], "staff_name");
            $article_info['add_time'] = getFormatDate($article_info['add_time'], "Y-m-d");
            $article_info['add_staff'] = peopleNoName($article_info['add_staff']);
            $article_info['article_thumb'] = getUploadInfo($article_info['article_thumb']);
			
			$where['article_id'] = $article_id;
			$where['give_type'] = 1;
			$article_info['article_like_count'] = M('cms_article_give')->where($where)->count();//点赞数量
			$where['communist_no'] = peopleNo($communist_no,1);
			$article_like = M('cms_article_give')->where($where)->find();//是否点赞
			if(!empty($article_like)){
				$article_info['article_like'] = 1;
			}else{
				$article_info['article_like'] = 0;
			}			
			$where['give_type'] = 3;
			$article_collect = M('cms_article_give')->where($where)->count();//是否收藏
			if(!empty($article_collect)){
				$article_info['article_collect']  = 1;
			}else{
				$article_info['article_collect']  = 0;
			}
            M('cms_article')->where("article_id='$article_id'")->setInc('article_view');
            $this->send('获取成功', $article_info, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  set_cms_article
     * @desc 写入文章
     * @param int article_cat 栏目ID
     * @param string article_title 文章标题
     * @param string article_content 文章内容
     * @param int communist_no 党员编号
     * @param int article_thumb 附件ID
     * @user wangzongbin
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function set_cms_article(){

        (new NumberValidate(['article_cat']))->goCheck();
        (new RequireValidate(['article_title', 'article_content']))->goCheck();
        (new CommunistNoValidate())->goCheck();

        $communist_no = I('post.communist_no');

        $data = I('post.');

        $data['add_staff'] = peopleNo($communist_no,1);

        if($data['article_cat'] == 15){
            $data['status'] = 21; 
        } else {
            $data['status'] = 1; 
        }
        
        $resulrt = (new CmsArticleModel())->Post($data);

        if ($resulrt) {
            $this->send('添加成功', null, 1);
        } else {
            $this->send('添加失败');
        }
    }
    /**
     *  get_cms_article_info_url
     * @desc 获取文章详情地址
     * @user 王宗彬
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_cms_article_info_url(){
        (new NumberValidate(['article_id']))->goCheck();
        $article_id = I('post.article_id');
        $article_info = M('cms_article')->where("article_id = '$article_id'")->field("article_id,article_thumb,article_title,article_content")->find();
        if (!empty($article_info)) {
            $article_info['article_thumb'] = getUploadInfo($article_info['article_thumb']);
            $article_info['url'] = "http://dev.dangjian.co/index.php/Wechat/Cms/cms_article_info/article_id/".$article_id;
            $this->send('获取成功', $article_info, 1);
        }else {
            $this->send('获取失败');
        }
    }
    /**
     * 
     * @desc 文章点赞
     * @user wangzongbin
     * @date 2019/8/30
     * @version 1.0.0
     */
    public function get_give_like(){
		(new NumberValidate(['give_type', 'article_id', 'communist_no']))->goCheck();
        $give_type = I('post.give_type');// 1文章的点赞     2评论的点赞    3文章的收藏
        $article_id = I('post.article_id');
        $communist_no = I('post.communist_no');//点赞人
        $comment_id = I('post.comment_id');//评论的id
        switch ($give_type) {
            case 1:
                $where['give_type'] = $give_type;
                $where['article_id'] = $article_id;
                $where['communist_no'] = peopleNo($communist_no,1);
                $give_count = M('cms_article_give')->where($where)->count();
                if($give_count==1){
					M('cms_article_give')->where($where)->delete();
					$this->send('取消点赞成功',$give_count_num_new, 1,'is_exist',1);
                }else{
                    $where['add_time'] = date('Y-m-d H:i:s');
                    $where['update_time'] = date('Y-m-d H:i:s');
                    $where['add_staff'] = peopleNo($communist_no,1);
                    $give_count = M('cms_article_give')->add($where);
                    $map['article_id'] = $article_id;
                    $map['give_type'] = $give_type;
                    $give_count_num_new = M('cms_article_give')->where($map)->count();
                    $this->send('点赞成功',$give_count_num_new, 1);
                }
                break;
            case 2:
				$where['give_type'] = $give_type;
                $where['article_id'] = $article_id;
				$where['comment_id'] = $comment_id;
                $where['communist_no'] = peopleNo($communist_no,1);
                $give_count = M('cms_article_give')->where($where)->count();
                if($give_count==1){
                    M('cms_article_give')->where($where)->delete();
					$this->send('取消点赞成功',$give_count_num_new, 1,'is_exist',1);
                }else{
					$where['add_time'] = date('Y-m-d H:i:s');
                    $where['update_time'] = date('Y-m-d H:i:s');
                    $where['add_staff'] = peopleNo($communist_no,1);
                    $give_count = M('cms_article_give')->add($where);
                    $map['article_id'] = $article_id;
                    $map['give_type'] = $give_type;
					$map['comment_id'] = $comment_id;
                    $give_count_num_new = M('cms_article_give')->where($map)->count();			
                    $this->send('点赞成功',$give_count_num_new, 1);
				}				
                break;
			case 3:
				$where['give_type'] = $give_type;
                $where['article_id'] = $article_id;
                $where['communist_no'] = peopleNo($communist_no,1);
                $give_count = M('cms_article_give')->where($where)->count();
                if($give_count==1){
                    M('cms_article_give')->where($where)->delete();
					$this->send('取消收藏成功',$give_count_num_new, 1,'is_exist',1);
                }else{
                    $where['add_time'] = date('Y-m-d H:i:s');
                    $where['update_time'] = date('Y-m-d H:i:s');
                    $where['add_staff'] = peopleNo($communist_no,1);
                    $give_count = M('cms_article_give')->add($where);
                    $map['article_id'] = $article_id;
                    $map['give_type'] = $give_type;
					$map['communist_no'] = peopleNo($communist_no,1);
                    $give_count_num_new = M('cms_article_give')->where($map)->count();
                    $this->send('收藏成功',$give_count_num_new, 1);
                }
				break;
			
        }
    }
	/**
	* set_article_comment
	* @desc 文章评论
	* @user wangzongbin 
	* @date 2019/8/30
	* @version 1.0.0
	*/
    public function set_article_comment(){
		(new CommunistNoValidate())->goCheck();
		(new NumberValidate(['article_id']))->goCheck();
		$comment['article_id']=I("post.article_id");
        $comment['comment_type']=I("post.comment_type");//文章article  学习material
        $comment['comment_content']=I("post.comment_content");
		$comment['communist_no'] = I("post.communist_no");
        $comment['status'] = 1;
        $comment['comment_time'] = date('Y-m-d H:i:s');
        $comment['add_time'] = date('Y-m-d H:i:s');
        $comment['update_time'] = date('Y-m-d H:i:s');
        $comment_result = M('cms_article_comment')->add($comment);
        if($comment_result){
			$this->send('评论成功',null, 1);
        } else {
			$this->send('评论失败');
            
        }
	}
	/**
	* get_article_comment_list
	* @desc 文章评论列表
	* @user wangzongbin 
	* @date 2019/8/30
	* @version 1.0.0
	*/
    public function get_article_comment_list(){
		(new CommunistNoValidate())->goCheck();
		(new NumberValidate(['article_id', 'page', 'pagesize']))->goCheck();
		$comment_type=I("post.comment_type");//文章article  学习material
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;	
		$article_id=I("post.article_id");
		$communist_no = I("post.communist_no");
		$where['article_id'] = $article_id;
		$where['communist_no'] = $communist_no;
		$where['comment_type'] = $comment_type;
		$comment_list = M('cms_article_comment')->where($where)->field('comment_id,communist_no,communist_no,comment_content,add_time')->limit($page,$pagesize)->order('add_time  desc')->select();
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		$communist_avatar_arr = M('ccp_communist')->getField('communist_no,communist_avatar');
		foreach($comment_list as &$list){
			$list['communist_name'] = $communist_name_arr[$list['communist_no']];
			$list['communist_avatar'] = $communist_avatar_arr[$list['communist_no']];
			//点赞
			$map['give_type'] = 2;
			$map['comment_id'] = $list['comment_id'];
			$list['comment_like_count'] = M('cms_article_give')->where($map)->count();//点赞数量
			$map['communist_no'] = peopleNo($communist_no,1);
			$comment_like = M('cms_article_give')->where($map)->find();//是否点赞
			if(!empty($comment_like)){
				$list['comment_like'] = 1;
			}else{
				$list['comment_like'] = 0;
			}
		}
        if($comment_list){
			$this->send('获取成功',$comment_list, 1);
        } else {
			$this->send('获取失败');
            
        }
	}
}
