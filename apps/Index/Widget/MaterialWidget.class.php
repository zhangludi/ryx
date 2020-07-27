<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/8
 * Time: 09:58
 */

namespace Index\Widget;


use Think\Controller;

class MaterialWidget extends Controller
{
    /**
     *  material_article_list
     * @desc 学习资料列表组件
     * @param int $cat_id
     * @param int $page
     * @param int $pagesize
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function material_article_list($cat_id = 0, $page = 0, $pagesize = 8, $type = 'default')
    {
    	layout(false);
        $db_cat = M('edu_material_category');
        if (empty($cat_id)) {
            $cat_id = $db_cat->where('status=1 and cat_type=11')->getField('cat_id', true);
            $cat_id = implode(',', $cat_id);
        }
        $article_list = getMaterialList($cat_id, null, null, null, null, null, $page, $pagesize);
        foreach ($article_list as &$list) {
            $list['material_thumb'] = getUploadInfo($list['material_thumb']);
            $list['add_staff'] = getCommunistInfo($list['add_staff']);
            $list['add_time'] = date('Y-m-d', strtotime($list['add_time']));
        }
        $this->assign('article_list', $article_list);
        switch ($type) {
            case 'carousel' :
                $this->display('parts:ipam_material_article_carousel');
                break;
            default:
                $this->display('parts:ipam_material_article_list');
        }
    }

    /**
     *  material_video_list
     * @desc 视频组件
     * @param int $cat_id
     * @param int $page
     * @param int $pagesize
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function material_video_list($cat_id = 0, $page = 0, $pagesize = 3, $type = 'default')
    {
    	layout(false);
        $db_cat = M('edu_material_category');
        if (empty($cat_id)) {
            $cat_id = $db_cat->where('status=1 and cat_type=21')->getField('cat_id', true);
            $cat_id = implode(',', $cat_id);
        }
        $video_list = getMaterialList($cat_id, null, null, null, null, null, $page, $pagesize);
        foreach ($video_list as &$list) {
            $list['material_thumb'] = getUploadInfo($list['material_thumb']);
        }
        $this->assign('video_list', $video_list);
        switch ($type) {
            case 'carousel' :
                $this->display('parts:ipam_material_video_carousel');
                break;
            default:
                $this->display('parts:ipam_material_video_list');
        }
    }

    /**
     *  ipam_material_comment
     * @desc 文章评论保存
     * @param int $article_id
     * @param int $type
     * @user ljj
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function ipam_material_comment($article_id = 1, $type = 'article')
    {
        layout(false);
        $this->assign('article_id',$article_id);
        $comment_list = M('cms_article_comment')->where("article_id = '$article_id' and comment_type = '$type'")->order('add_time desc')->limit(10)->select();
        if(!empty($comment_list)){
            foreach ($comment_list as &$value) {
                $communist_no = $value['communist_no'];
                $value['communist_name'] = M('ccp_communist')->where("communist_no = '$communist_no'")->getField('communist_name');
                $value['communist_address'] = M('ccp_communist')->where("communist_no = '$communist_no'")->getField('communist_paddress');
                $value['communist_avatar'] = M('ccp_communist')->where("communist_no = '$communist_no'")->getField('communist_avatar');
            }
        }
        $this->assign('comment_list',$comment_list);
        $this->assign('comment_num',count($comment_list));
        $this->display('parts:ipam_material_comment');
    }
}