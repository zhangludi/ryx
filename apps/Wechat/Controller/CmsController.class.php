<?php
namespace Wechat\Controller;
use Think\Controller;
use Wxjssdk\JSSDK;
class CmsController extends Controller 
{

	/**
	 * @cms_article_list_total
	 * @desc：文章列表
	 * @author：王宗彬
	 * @addtime:2019-07-08
	 * @version：V1.0.0
	 **/
	public function cms_article_list_total(){
		$cat_list = getArticleCatList("15,36,37,38,39,40,41,42,46");
		$this->assign('cat_list',$cat_list);
		$cat_id = I('get.cat_id',46);
		$this->assign('cat_id',$cat_id);
		$this->assign('article_list',$article_list['data']);
		$this->display('cms_article_list_total');
	}
	/**
	 * @cms_article_list_total
	 * @desc：文章列表
	 * @author：王宗彬
	 * @addtime:2019-07-08
	 * @version：V1.0.0
	 **/
	public function cms_article_list_total_data(){
		$cat_id = I('post.cat_id');
		$article_list = getArticleList($cat_id,'0','50');
		foreach ($article_list['data'] as &$list) {
			//$list1['article_title'] = mb_substr(strip_tags($list1['article_title']),0,13,'utf-8');
			if($list['article_thumb']==null || $list['article_thumb'] == ''){
				$data .= "<li class='mb-12em w-95b over-h bb-3em-e6 pb-10em' style='position: relative;'>
					<a href='".U('Cms/cms_article_info', array('article_id' => $list['article_id']))."'>
						<div class='over-h ml-8em mr-8em'>								
		                    <div class=''>
		                    	<div class='f-16em color-33 f-w ' style='text-overflow: -o-ellipsis-lastline;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;'>".$list['article_title']."</div>
		                        <div class='pull-right color-a4 f-11em '>".peopleNoName($list['add_staff'])."</div>
		                        <div class='pull-left color-a4 f-11em'>".getFormatDate($list['add_time'], "Y-m-d")."</div>
		                    </div>
		                </div>
		            </a>
				</li>";
			}else{
				$data .= "<li class='mb-12em over-h bb-3em-e6 pb-10em' style='position: relative;'>
					<a href='".U('Cms/cms_article_info', array('article_id' => $list['article_id']))."'>
						<div class='pull-left w-120em h-90em'>
							<img class=' w-120em h-90em bor-ra-3' src='".getUploadInfo($list['meetting_thumb'])."'>
						</div>
						<div class='pull-left over-h w-230em ml-8em'>
						   <div class='pull-left w-216em mt-12em' >
						        <div class='f-16em color-33 f-w ' style='text-overflow: -o-ellipsis-lastline;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;'>".$list['article_title']."</div>
						        <div class='' style='position: absolute; bottom: 10%;width: 60%;'>
			                        <div class='pull-left color-a4 f-11em pt-5em' >".peopleNoName($list['add_staff'])."</div>
			                        <div class='pull-right color-a4 mt-5em f-11em' >".getFormatDate($list['add_time'], "Y-m-d")."</div>
			                    </div>
		                    </div>
		                </div>
		            </a>
				</li>"; 
			}
		}
		ob_clean();$this->ajaxReturn(['content' => $data]);
	}
	/**
	 * @cms_article_list
	 * @desc：文章列表
	 * @author：王宗彬
	 * @addtime:2018-05-03
	 * @version：V1.0.0
	 **/
	public function cms_article_list()
	{
		//checkLoginWeixin();
		$cat_id = I('get.cat_id');
		$article_list = getArticleList($cat_id,'0','1000000');
		foreach ($article_list['data'] as &$list) {
			$list['article_thumb'] = getUploadInfo($list['article_thumb']);
			$list['add_staff'] = peopleNoName($list['add_staff']);
			$list['add_time'] = date('Y-m-d',strtotime($list['add_time']));
		}
		
		$this->assign('article_list',$article_list['data']);
		$this->display('Cms/cms_article_list');
	}
	/**
	 * @cms_article_do_save
	 * @desc：文章列表
	 * @author：王宗彬
	 * @addtime:2018-05-03
	 * @version：V1.0.0
	 **/
	public function cms_article_do_save()
	{
		$post = I('post.');
		$post['communist_no'] = session('wechat_communist');
		$post['comment_type'] = 'article';
		$post['status'] = 1;
        $post['comment_time'] = date('Y-m-d H:i:s');
        $post['add_time'] = date('Y-m-d H:i:s');
        $post['update_time'] = date('Y-m-d H:i:s');
        $comment_result = M('cms_article_comment')->add($post);
        if(!empty($comment_result)){
        	showMsg('success', '评论成功！', U('Cms/cms_article_info',array('article_id'=>$post['article_id'])));
        }else{
        	showMsg('error', '评论失败', '');
        }
	}
	/**
	 * @name:cms_article_info
	 * @desc：文章详情
	 * @author：王宗彬
	 * @addtime:2018-05-03
	 * @version：V1.0.0
	 **/
	public function cms_article_info()
	{
		//checkAuthweixin(ACTION_NAME);
		$article_id = I('get.article_id');
		$article_info = getArticleInfo($article_id,'all');
		$article_info['add_staff'] = peopleNoName($article_info['add_staff']);
		$this->assign('article_info',$article_info);
		$where['article_id'] = $article_id;
		$where['comment_type'] = 'article';
		$comment_list = M('cms_article_comment')->where($where)->select();
		foreach ($comment_list as &$value) {
            $communist_no = $value['communist_no'];
            $value['communist_name'] = M('ccp_communist')->where("communist_no = '$communist_no'")->getField('communist_name');
            $communist_avatar = M('ccp_communist')->where("communist_no = '$communist_no'")->getField('communist_avatar');
			if(!$communist_avatar || !file_exists(SITE_PATH.$communist_avatar)){
                $value['communist_avatar'] = "/statics/public/images/default_photo.jpg";
            }
			
        }
        $this->assign('comment_list',$comment_list);

		$this->display('Cms/cms_article_info');
	}
	
	
}