<?php

namespace Life\Model;

use Common\Model\PublicModel;

class LifeBbsPostCommentModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
        array('comment_content','require','请输入内容',self::MODEL_INSERT),
        array('communist_no','require','写入失败',self::MODEL_INSERT),
//        array('comment_type','require','写入失败1',self::MODEL_INSERT),
        array('post_id','require','写入失败',self::MODEL_INSERT),
//        array('comment_pid','require','写入失败',self::MODEL_INSERT),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array(add_staff, 'session', self::MODEL_INSERT, 'function','communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
        array('comment_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
    	array('update_time', 'date', self::MODEL_BOTH, 'function','Y-m-d H:i:s')
    );

    /**
     * getCommentNum
     * @desc 获取回复数量
     * @param $field string 字段
     * @param $id int 值
     * @return int
     * @author 刘丙涛
     * @addtime:2018-01-09
     * @version：V1.0.0
     **/
    public function getCommentNum($field, $id)
    {
        if (empty($field) && empty($id)) : return; endif;

        $num = $this->where([$field => $id])->count();

        return $num;
    }

    /**
     * getCommentList
     * @desc 获取回复列表
     * @param $where int 查询条件
     * @return int
     * @author 刘丙涛
     * @addtime:2018-01-09
     * @version：V1.0.0
     **/
    public function getCommentList($where)
    {
        $list = $this->where($where)->
                field('comment_id,comment_content,communist_no,comment_time,comment_type,comment_pid')->select();

        return $list;
    }
}