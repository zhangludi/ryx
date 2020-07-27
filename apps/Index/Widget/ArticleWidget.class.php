<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/4
 * Time: 15:14
 */
namespace Index\Widget;

use Think\Controller;

class ArticleWidget extends Controller
{
    /**
     *  article_list
     * @desc 文章列表
     * @param int $cat_id
     * @param int $pagesize
     * @user liubingtao
     * @date 2018/4/12
     * @version 1.0.0
     */
    public function article_list($cat_id = 0,$pagesize = 10)
    {
        layout(false);
        $article_list = getArticleList($cat_id, 0, $pagesize);
        $this->assign('article_list', $article_list['data']);
        $this->display('parts:ipam_article_list');
    }

    /**
     *  article_image_list
     * @desc 文章轮播
     * @param int $pagesize
     * @user liubingtao
     * @date 2018/4/12
     * @version 1.0.0
     */
    public function article_image_list($pagesize = 10)
    {
        layout(false);
        $article_list = getArticleList(0, 0, $pagesize);
        $list = array();
        foreach ($article_list['data'] as $item) {
            $value['article_id'] = $item['article_id'];
            $value['images_url'] = getUploadInfo($item['article_thumb']);
            $value['article_title'] = $item['article_title'];
            $list[] = $value;
        }
        $this->assign('article_list', $list);
        $this->display('parts:ipam_article_image_list');
    }

    /**
     *  ipam_banner_list
     * @desc 首页banner轮播
     * @param int $num banner数
     * @user liubingtao
     * @date 2018/4/12
     * @version 1.0.0
     */
    public function ipam_banner_list($num)
    {
        layout(false);
        $ad_list = M("sys_ad" )->where ("ad_location = '1'")->limit($num)->select ();
        foreach ($ad_list as &$ad_value) {
            $ad_value['ad_img'] = getUploadInfo($ad_value['ad_img']);
        }
        $this->assign('ad_list', $ad_list);
        $this->display('parts:ipam_banner_list');
    }
    /**
     *  ipam_banner_article_list
     * @desc 首页banner轮播
     * @param int $num banner数
     * @user liubingtao
     * @date 2018/4/12
     * @version 1.0.0
     */
    public function ipam_banner_article_list()
    {
        layout(false);
        $articl_list = M("cms_article")->where("article_point = 2")->order('update_time desc')->limit(7)->select ();
        foreach ($articl_list as &$list) {
            $list['article_thumb'] = getUploadInfo($list['article_thumb']);
        }
        $this->assign('articl_list', $articl_list);
        $this->display('parts:ipam_banner_article_list');
    }
    /**
     *  hotspot_issues
     * @desc 热点新闻
     * @user liubingtao
     * @date 2018/4/12
     * @version 1.0.0
     */
    public function hotspot_issues()
    {
        layout(false);
        $article_list = getArticleList(0, 0, '6');
        $this->assign('article_list', $article_list['data']);
        $this->display('parts:ipam_hotspot_issues');
    }

    public function news_list($cat_id, $num = 3)
    {
        layout(false);
        $cat_info = M('cms_article_category')->where("cat_id=$cat_id")->field('cat_id,cat_name')->find();
        $article_list =  M('cms_article')->where("article_cat='$cat_id'")->order("add_time desc")->limit(0,$num)->select();
        if(!empty($article_list)){
            foreach ($article_list as &$article_val) {
                $article_val['article_thumb'] = getUploadInfo($article_val['article_thumb']);
                $article_val['add_staff_name'] = peopleNoName($article_val['add_staff']);
            }
        }
        $this->assign('article_list', $article_list);
        $this->assign('cat_info', $cat_info);
        $this->display('parts:ipam_news_list');
    }
    /**
        通知公告
    */
    public function notice_index(){
        layout(false);
        $viewrecord_list = M('oa_notice')->field('notice_id,notice_title')->limit(9)->select();
        $this->assign('viewrecord_list',$viewrecord_list);
        $this->display('parts:ipam_notice_index');
    }
}