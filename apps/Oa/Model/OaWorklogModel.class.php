<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/2/1
 * Time: 上午10:51
 */

namespace Oa\Model;


use Common\Model\PublicModel;

class OaWorklogModel extends PublicModel
{
//    protected $_validate = [
//        ['worklog_title', 'require', '请填写标题', self::MODEL_INSERT],
//        ['worklog_type', 'require', '请选择类型', self::MODEL_INSERT],
//        ['worklog_summary', 'require', '请输入总结内容']
//    ];

    protected $_auto = [
        array('status', 0, self::MODEL_INSERT, 'int'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
        array('comment_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
        // array('worklog_date', 'date', self::MODEL_INSERT, 'function','Y-m-d'),
    	array('update_time', 'date', self::MODEL_BOTH, 'function','Y-m-d H:i:s')
    ];

    public function getWorklogList($communist_no, $worklog_type, $keyword, $page, $pagesize)
    {
        $maps['add_staff'] = ['eq', $communist_no];

        $maps['worklog_type'] = ['eq', $worklog_type];
        if(!empty($keyword)){
            $maps['worklog_title'] = ['like', "%$keyword%"];
        }
        $page = ($page-1) *10;
        $worklog_list = $this->where($maps)->limit($page, $pagesize)->order('add_time desc')->select();

        return $worklog_list;
    }
}