<?php
/**
 * Created by PhpStorm.
 * User: dklly
 * Date: 18-1-9
 * Time: 上午10:46
 */

namespace Life\Model;


use Common\Model\PublicModel;

class LifeGuestbookModel extends PublicModel
{
    protected $_validate = array(
        array('guestbook_id', 'require', '写入失败', 2),
        array('communist_phone', 'require', '请填写手机号', 1),
        array('guestbook_content', 'require', '请填写内容', 1),
        array('communist_no', 'require', '写入失败', 1)
    );

    protected $_auto = array(
        array('status', 1, 2),
        array('add_staff', 'session', 2, 'function','communist_no'),
        array('add_time', 'date', 1, 'function','Y-m-d H:i:s'),
        array('update_time', 'date', 2, 'function','Y-m-d H:i:s')
    );

    /*
	 * getGuestbookList
	 * @desc  获取留言建议列表
	 * @param $party_no string  党组织编号
	 * @return array
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime  2018-01-11
	 */
    public function getGuestbookList($party_no, $communist_no, $page = 1, $pagesize = 1000)
    {

        if (!empty($party_no)) : $where['party_no'] = $party_no; endif;

        if (!empty($communist_no)) : $where['communist_no'] = $communist_no; endif;

        $page = ($page - 1) * $pagesize;

        $list = $this->where($where)->
                field('guestbook_id,add_staff,communist_phone,status,add_time')->
                order('add_time desc')->limit($page, $pagesize)->select();

        if (!is_array($list)) : return; endif;

        foreach ($list as &$item) :
            $item['status'] = ($item['status'] ==1) ? '已回复':'未回复';
            $item['communist_name'] = getCommunistInfo($item['add_staff']);
        endforeach;

        return $list;
    }

    /*
	 * Post
	 * @desc  增加
	 * @param $data array  post 数据
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime  2018-01-11
	 */
    public function Post($data)
    {
        if (empty($data['communist_no'])) : return; endif;

        $data['party_no'] = getCommunistInfo($data['communist_no'], 'party_no');

        return parent::Post($data);
    }

    /*
    * getGuestnookInfo
    * @desc  获取留言建议详情
    * @param $id string  留言id
    * @return array
    * @author 刘丙涛
    * @version 版本 V1.0.0
    * @updatetime
    * @addtime  2018-01-11
    */
    public function getGuestnookInfo($id)
    {
        if (empty($id)) : return; endif;

        $info = $this->find($id);

        if (!is_array($info)) : return; endif;

        $info['status_name'] = $info['status'] == 1 ? "<span style='color:red;'>未回复</span>" : "<span style='color:green;'>已回复</span>";

        $info['communist_name'] = getCommunistInfo($info['communist_name']);

        $info['party_name'] = getPartyInfo($info['party_no']);

        return $info;
    }
}