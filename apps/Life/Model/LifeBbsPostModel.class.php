<?php

namespace Life\Model;

use Common\Model\PublicModel;

class LifeBbsPostModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
        array('post_theme','require','请写入帖子主题',self::MODEL_INSERT),
        array('communist_no','require','写入失败',self::MODEL_INSERT),
        array('post_content','require','请写入内容',self::MODEL_INSERT),
//        array('post_img','require','请写入图片',self::MODEL_INSERT),
        array('cat_id','require','请选择类型',self::MODEL_INSERT),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array(add_staff, 'session', self::MODEL_INSERT, 'function','communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
    	array('update_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s')
    );

    /**
	 * @name:getbbsList
	 * @desc：获取民情列表
	 * @param：
	 * @return：
	 * @author:王桥元
	 * @addtime:2017-10-27
	 * @version：V1.0.0
	 **/
	function getbbsList($where="",$page,$pagesize){
		if(empty($where)){
			$where = "1=1";
		}
		$bbs_data['data'] = $this->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
        $bbs_data['count'] = $this->where($where)->count();
		return $bbs_data;
	}
	/**
	 * @name:getbbsInfo
	 * @desc：获取民情详情
	 * @param：
	 * @return：
	 * @author:王桥元
	 * @addtime:2017-10-27
	 * @version：V1.0.0
	 **/
	function getbbsInfo($bbs_id){

		$bbs_data = $this->where("post_id = $bbs_id")->find();
		if($bbs_data){
			return $bbs_data;
		}else{
			return null;
		}
	}


    /**
     * getBbsPostList
     * @desc 获取帖子列表
     * @param $communist_no int 党员编号
     * @param $cat int 帖子类型
     * @param $page int 页数
     * @param $pagesize int 条数
     * @return array
     * @author 刘丙涛
     * @addtime:2018-01-09
     * @version：V1.0.0
     **/
    public function getBbsPostList($communist_no, $cat, $page = 0, $pagesize = 10)
    {
        $fav = new LifeBbsPostFavModel();

        $comment = new LifeBbsPostCommentModel();

        if (!isset($communist_no)) : return; endif;

        $hotspot = $this->where('status=1')->order('add_time desc')->field('post_id,post_theme,post_img,post_content,visitor_volume,communist_no,add_time')->find();

        $where = 'status=1';

        if (!empty($cat)) : $where .= " and cat_id=$cat"; endif;

        $list = $this->where($where)->limit($page,$pagesize)->order('add_time desc')->field('post_id,post_theme,post_img,post_content,visitor_volume,communist_no,add_time')->select();
        if (!$hotspot && !$list) : return; endif;

        $hotspot['img_url'] =  getImageNum($hotspot['post_img'], 1);

        foreach ($list as &$item)
        {
            $item['fav_num'] = $fav->getFavNum($item['post_id']);

            $communist_avatar = getCommunistInfo($item['communist_no'],'communist_avatar');
            
            if(!$communist_avatar || !file_exists(SITE_PATH.$communist_avatar)){
	        	$item['communist_avatar'] = "/statics/public/images/default_photo.jpg";
	        }
            $item['comment_name'] = getCommunistInfo($item['communist_no']);
            $item['add_time'] = getFormatDate($item['add_time'], 'Y-m-d H:i');
            $item['comment_num'] = $comment->getCommentNum('post_id', $item['post_id']);

            $item['is_fav']  = empty($fav->isFav($communist_no, $item['post_id'])) ? 0:1;

            $item['img_url']  = getImageNum($item['post_img'], 1);
        }

        return ['hotspot' => $hotspot, 'list' => $list];
    }

    /**
     * getBbsPostInfo
     * @desc 获取帖子详情
     * @param $communist_no int 党员编号
     * @param $post_id int 帖子id
     * @return array
     * @author 刘丙涛
     * @addtime:2018-01-09
     * @version：V1.0.0
     **/
    public function getBbsPostInfo($communist_no, $post_id)
    {

        if (!isset($post_id) || !isset($communist_no)) : return; endif;

        $comment = new LifeBbsPostCommentModel();

        $fav = new LifeBbsPostFavModel();

        $post_info = $this->field('post_id,post_theme,communist_no,post_content,post_img,visitor_volume,add_time')->find($post_id);

        $comment_list = $this->getBbsPostCommentList('post_id', $post_id);

        if (!is_array($post_info)) : return; endif;

        $post_info['is_fav']  = empty($fav->isFav($communist_no, $post_id)) ? 0:1;

        $post_info['is_collect']  = empty($fav->isFav($communist_no, $post_id, 2)) ? 0:1;

        $post_info['fav_num'] = $fav->getFavNum($post_info['post_id']);

        $post_info['comment_num'] = $comment->getCommentNum('post_id', $post_info['post_id']);
		
        $post_info['img_url']  = getImageNum($post_info['post_img'], 1);

        $post_info['communist_name'] = getCommunistInfo($post_info['communist_no']);
        
        $communist_avatar = getCommunistInfo($post_info['communist_no'],'communist_avatar');
        if(!empty($communist_avatar)){
            $post_info['communist_avatar'] = "/".$communist_avatar;
        }
        return ['post_info' => $post_info, 'comment_list' => $comment_list];
    }


    /**
     * volumeAdd
     * @desc 查看人数增加
     * @param $post_id int 帖子id
     * @author 刘丙涛
     * @addtime:2018-01-09
     * @version：V1.0.0
     **/
    public function volumeAdd($post_id)
    {
        if (empty($post_id)) : return; endif;

        $this->where(['post_id' => $post_id])->setInc('visitor_volume');
    }

    /**
     * getBbsPostCommentList
     * @desc 获取评论列表
     * @param $field int 字段名
     * @param $id int 字段值
     * @return string
     * @author 刘丙涛
     * @addtime:2018-01-11
     * @version：V1.0.0
     **/
    public function getBbsPostCommentList($field, $id)
    {

        if (empty($field) && empty($id)) : return; endif;

        $comment = new LifeBbsPostCommentModel();

        $comment_list = $comment->getCommentList([$field => $id]);

        if (is_array($comment_list) && !empty($comment_list))        {
			foreach ($comment_list as &$item){
				if ($item['comment_pid'] != 0)	{
					$p_comment = $comment->find($item['comment_pid']);

					$item['comment_content'] .= '  //@'.getCommunistInfo($p_comment['communist_no'])
						.':  '.$p_comment['comment_content'];
				}
				$item['comment_name'] = getCommunistInfo($item['communist_no']);
				$item['comment_num'] = $comment->getCommentNum('comment_pid', $item['comment_id']);
				$item['communist_avatar'] = getCommunistInfo($item['communist_no'],'communist_avatar');
				if(!$item['communist_avatar'] || !file_exists(SITE_PATH.$item['communist_avatar'])){
					$item['communist_avatar'] = "/statics/public/images/default_photo.jpg";
				}
				$item['party_name'] = getPartyInfo(getCommunistInfo($item['communist_no'], 'party_no'));
				$item['comment_time'] = date('m-d H:i:s', strtotime($item['comment_time']));
			}
            return $comment_list;
        }else{
            return null;
        }
    }
}