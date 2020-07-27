<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/2/2
 * Time: 14:16
 */

namespace Oa\Model;

use Common\Model\PublicModel;

class OaWorkplanModel extends PublicModel
{

    protected $_auto = [
        ['status', 11, self::MODEL_INSERT, 'int'],
        ['add_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'],
        ['update_time', 'date', self::MODEL_BOTH, 'function', 'Y-m-d H:i:s']
    ];
    /**
     *  getWorkplanList
     * @desc 获取工作计划列表
     * @param int $communist_no 编号
     * @param int $isCheck 判断执行人与安排人
     * @param int $status 状态
     * @param string $kewords 关键词搜索
     * @param int $page 页数
     * @param int $pagesize 条数
     * @return array
     * @user liubingtao
     * @date 2018/2/2
     * @version 1.0.0
     */
    function getWorkplanList($communist_no, $isCheck, $status, $kewords, $page = 0, $pagesize = 10)
    {
        if (empty($communist_no) || empty($isCheck)) : return false; endif;
        if ($isCheck == 1) {
            $maps['workplan_executor_man'] = ['in', $communist_no];//执行人
        } else {
            $maps['workplan_arranger_man'] = ['in', $communist_no];//安排人
        }
        if (!empty($status)) {
            $maps['status'] = ['in', $status];
        }
        if (!empty($kewords)) {
            $maps['workplan_title'] = ['like', "%$kewords%"];
        }
        $result = $this->where($maps)->limit($page, $pagesize)->order('add_time desc')->
            field('workplan_id,workplan_title,workplan_content,workplan_executor_man,workplan_audit_man,workplan_expectstart_time')->select();
        return $result;
    }

    /**
     *  getWorkplanInfo
     * @desc 获取工作计划详情
     * @param int $id
     * @return bool
     * @user liubingtao
     * @date 2018/2/5
     * @version 1.0.0
     */
    function getWorkplanInfo($id)
    {
        if (empty($id)) : return false; endif;

        $maps['workplan_id'] = ['eq', $id];
        $result = $this->where($maps)->
        field('workplan_title,workplan_content,workplan_audit_man,workplan_expectstart_time,workplan_expectend_time,memo')->find();
        return $result;
    }
}