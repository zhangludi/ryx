<?php
/**
 * Created by PhpStorm.
 * User: wangzongbin
 * Date: 2018/4/4
 * Time: 11:06
 */

namespace Allinone\Controller;


use Think\Controller;

class GovernmentController extends Controller
{
	/**
	 *  _initialize
	 * @desc 政务首页
	 * @user liubingtao
	 * @date 2018/4/8
	 * @version 1.0.0
	 */
	public function ipam_gov_index()
	{
	    $cat_list = M('cms_article_category')->where("cat_pid=14 and status=1")->field('cat_id,cat_name')->select();
	    $this->assign('cat_list', $cat_list);
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
		$this->display();
	}

    /**
     *  _initialize
     * @desc 政务列表
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function ipam_gov_list()
    {
        // $db = M('cms_article_category');
        // $cms_article = M('cms_article');
        // $cat_id = I('get.cat_id');

        // $id = I('get.id');
        // $cat_map['cat_pid'] = $cat_id;
        // $cat_map['status'] = 1;
        // $cat_list = $db->where($cat_map)->field('cat_id,cat_pid,cat_name')->select();

        // $cat_name_map['cat_id'] = $cat_id;
        // $cat_name = $db->where($cat_name_map)->getField('cat_name');
        // if (empty($cat_id)) {
        //     $id = $cat_list[0]['cat_id'];
        // }
        // $article_map['article_cat'] = $cat_id;
        // $article_map['status'] = 1;
        // $article_list = $cms_article->where($article_map)->field('article_id,article_title,article_keyword,article_thumb')->order('add_time desc')->select();

        // if(!empty($cat_list)){
        //     $type = '1';
        // }else{
        //     $type = '2';
        // }
        // $this->assign('type', $type);
        // $this->assign('article_list', $article_list);
        // $this->assign('cat_name', $cat_name);
        // $this->assign('cat_list', $cat_list);
        // $this->assign('id', $id);
        // $this->display('Government/ipam_gov_list');
    }




    public function ipam_gov_info()
    {
        $article_id = I('get.article_id');
        $article_info = getArticleInfo($article_id, 'all');
        $article_info['article_attach'] = getUploadInfo($article_info['article_attach']);
        $article_thumb = getUploadInfo($article_info['article_thumb']);
        $article_attach = explode('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $article_info['article_attach']);
        $arr = [];
        for ($i = 0; $i<count($article_attach); $i++) {
            $arr[$i]['num'] = $i +1;
            $arr[$i]['url'] = $article_attach[$i];
        }
       

        $this->assign('article_thumb', $article_thumb);
        $this->assign('article_info', $article_info);
        $this->assign('url', $arr);
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
        $this->display();
    }
}