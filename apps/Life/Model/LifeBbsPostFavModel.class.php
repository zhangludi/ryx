<?php

namespace Life\Model;

use Common\Model\PublicModel;

class LifeBbsPostFavModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
        array('post_no','require','写入失败',self::EXISTS_VALIDATE,self::MODEL_INSERT),
        array('communist_no','require','写入失败',self::EXISTS_VALIDATE,self::MODEL_INSERT),
        array('fav_type','require','写入失败',self::EXISTS_VALIDATE,self::MODEL_INSERT),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array(add_staff, 'session', self::MODEL_INSERT, 'function','communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
    	array('update_time', 'date', self::MODEL_BOTH, 'function','Y-m-d H:i:s')
    );

    /**
     * getFavNum
     * @desc 获取点赞或收藏数量
     * @param $post_id int 帖子id
     * @param $fav_type int 1：点赞 2：收藏
     * @return int
     * @author 刘丙涛
     * @addtime:2018-01-09
     * @version：V1.0.0
     **/
    public function getFavNum($post_id, $fav_type = 1)
    {
        if (!isset($post_id)) : return; endif;

        $num = $this->where(['post_id' => $post_id, 'fav_type' => $fav_type])->count();

        return $num;
    }

    /**
     * isFav
     * @desc 判断是否点赞
     * @param $communist_no int 党员编号
     * @param $post_id int 帖子id
     * @param $fav_type int 1：点赞 2：收藏
     * @return int
     * @author 刘丙涛
     * @addtime:2018-01-09
     * @version：V1.0.0
     **/
    public function isFav($communist_no, $post_id, $fav_type = 1)
    {
        if (!isset($post_id) || !isset($communist_no)) : return; endif;

        $result = $this->where(['communist_no' => $communist_no, 'post_id' => $post_id, 'fav_type' => $fav_type])->find();

        return $result;
    }

}