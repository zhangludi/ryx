<?php
/**
 * Created by PhpStorm.
 * User: wangzongbin
 * Date: 2018/4/4
 * Time: 11:06
 */

namespace Allinone\Controller;


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
	}
	/**
     * @name:party_news_index
     * @desc：党建要闻
     * @param：null
     * @return：
     * @author：王宗彬
     * @addtime:2019-06-20
     * @updatetime:2019-06-20
     * @version：V1.0.0
     **/
    public function party_news_index()
    {
        $cat_id = I('get.cat_id');
        $cat_name = getArticleCatInfo($cat_id);
        $this->assign('cat_name',$cat_name);
        $where['article_cat'] = $cat_id;
        $news_list = M('cms_article')->where($where)->order('add_time desc')->limit(0,9)->select();
        foreach ($news_list as &$list) {
            $list['add_staff'] = peopleNoName($list['add_staff']);
        }
        $this->assign('news_list',$news_list);
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
        $this->display('party_news_index');
    }
    /**
     * @name:party_news_info
     * @desc：党建要闻
     * @param：null
     * @return：
     * @author：王宗彬
     * @addtime:2019-06-20
     * @updatetime:2019-06-20
     * @version：V1.0.0
     **/
    public function party_news_info()
    {
        $article_id = I('get.article_id');
        $where['article_id'] = $article_id;
        $article_info = M('cms_article')->field('article_id,article_cat,article_title,article_keyword,article_content,add_time')->where($where)->find();
        $article_info['add_staff'] = peopleNoName($article_info['add_staff']);
        $cat_name = getArticleCatInfo($article_info['article_cat']);
        $this->assign('cat_name',$cat_name);
        $this->assign('article_info',$article_info);
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
        $this->display('party_news_info');
    }
    
}