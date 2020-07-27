<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/1/24
 * Time: 上午10:09
 */

namespace Oa\Controller;

use Common\Controller\BaseController;

class OanotesController extends BaseController
{
    /**
     * *************************** 备忘录管理开始 ***********************
     */
    /**
     * 备忘录首页
     *
     * @name oa_notes_index()
     * @param
     *
     * @return
     *
     * @author yangluhai-lixiangchao
     *         @addtime 2016年8月30日上午11:51:12
     *         @updatetime 2016年9月20日上午11:51:12
     * @version 0.01
     */

    public function oa_notes_index()
    {
        checkAuth(ACTION_NAME);
        $is_hunt = I('get.is_hunt');
        if (!empty($is_hunt)) {
            // 公告管理搜索条件

            // 标题及内容
            $notes_content = I('post.notes_content');
            if (!empty($notes_content)) {
                $this->assign('notes_content', $notes_content);
            }
            // 分类
            $notes_classify = I('post.notes_classify');
            if (!empty($notes_classify)) {
                $this->assign('notes_classify', $notes_classify);
            }
        }
        $this->display("oa_notes_index");
    }
    // 备忘录数据加载

    /**
     * 备忘录首页数据加载
     *
     * @name oa_notes_index_data()
     * @param
     *
     * @return
     *
     * @author yangluhai-lixiangchao
     *         @addtime 2016年8月30日上午11:51:44
     *         @updatetime 2016年9月20日上午11:51:44
     * @version 0.01
     */
    public function oa_notes_index_data()
    {
        $staff_no = $this->staff_no;
        $notes_classify = I('get.notes_classify');
        $notes_content = I('get.notes_content');
        $start = I('get.start');
        $end = I('get.end');
		$page = I('get.page');
		$limit = I('get.limit');
        $notes_list = getOaNotesList($staff_no,$notes_content, $start, $end,$notes_classify);
		foreach($notes_list as $key=>&$value){
			if(empty($value['alert_time'])){
				$value['alert_time']='暂无提醒时间';
			}
		}
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$count = M('oa_notes')->where($notes_list[0]['where'])->count();
        ob_clean();
		$notes_list = [
			'code'=>0,
			'msg'=>0,
			'count'=>$count,
			'data'=>$notes_list
		];
		$this->ajaxReturn($notes_list);
    }

    /**
     * 备忘录数据修改/添加
     *
     * @name oa_notes_edit()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:52:20
     *         @updatetime 2016年8月30日上午11:52:20
     * @version 0.01
     */
    public function oa_notes_edit()
    {
        checkAuth(ACTION_NAME);
        $notes_id    = I('get.notes_id'); // I方法获取数据、
        if ($notes_id) {
            $notes_info = getOaNotesInfo($notes_id, 'all');
            if ($notes_info['alert_time'] != null) {
                $notes_info['is_alert'] = 1;
            }
            $this->assign('notes_info', $notes_info);
        }
        $this->display("Oanotes/oa_notes_edit");
    }
    // 备忘录数据修改/添加 存储

    /**
     * 备忘录数据保存
     *
     * @name oa_notes_save()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:52:42
     *         @updatetime 2016年8月30日上午11:52:42
     * @version 0.01
     */
    public function oa_notes_save()
    {
        checkLogin();
        $staff_no = $this->staff_no;
        $notes     = M('oa_notes');
        $alert     = M('bd_alertmsg');
        $notes_id  = I('post.notes_id');
        $is_alert = I('post.is_alert');
        if($is_alert == 'on'){
            $is_alert = 1;
        } else {
            $is_alert = 0;
        }
        if (!empty($notes_id)) // 修改
        {
            $alerturl     = "notes_id/" . $notes_id;
            $alert_map['alert_param'] = $alerturl;
            if ($is_alert == 1) {
                $alertmsg_sel = $alert->where($alert_map)->find();
                if ($alertmsg_sel) {
                    $alertmsg_del = $alert->where($alert_map)->delete();
                }
                $is_notesmsg = getConfig('notesmsg_staff');
                if ($is_notesmsg == 1) {
                    $data['alert_type']     = "03";
                    $data['alert_man']      = $staff_no;
                    $data['alert_param']    = $notes_id;
                    $data['alert_title']    = I('post.notes_title');
                    $data['alert_content']  = I('post.notes_content');
                    $data['alert_time']     = I('post.alert_time');
                    $data['alert_nexttime'] = null;
                    $data['alert_cycle']    = "one";
                    $data['add_staff']      = $staff_no;
                    sendMultiMsg('notesmsg_staff', $staff_no, $staff_no, json_encode($data));
                }
            } else // 设置不提醒的时候把原来数据删除
            {
                $alertmsg_del = $alert->where($alert_map)->delete();
            }
            if ($is_alert == 1) {
                $notes_data['alert_time'] = I('post.alert_time');
                $notes_data['is_alert']   = 1;
            } else {
                $notes_data['alert_time'] = null;
                $notes_data['is_alert']   = 0;
            }
            $notes_data['notes_classify'] = $_POST['notes_classify'];
            $notes_data['notes_title']    = I('post.notes_title');
            $notes_data['notes_id']       = $notes_id;
            $notes_data['notes_content']  = $_POST['notes_content'];
            $notes_data['add_staff']      = $staff_no;
            $notes_data['update_time']    = date("Y-m-d H:i:s");
            $notes_data['add_time']       = date("Y-m-d H:i:s");
            $notes_data['memo']           = I('post.memo');
            $save_not                     = $notes->save($notes_data);

            if ($save_not) {
                saveLog(ACTION_NAME, 2, '', '操作员[' . session('communist_no') . ']于' . date("Y-m-d H:i:s") . '对备忘录编号 [' . $notes_data['notes_id'] . ']进行修改操作');
                showMsg('success', '操作成功！！！', U('oa_notes_index'),1);
            } else {
                showMsg('error', '操作失败！！！', '');
            }
        } else // 添加
        {
            $notes_data['notes_title']   = I('post.notes_title');
            $notes_data['notes_content'] = $_POST['notes_content'];
            $notes_data['notes_type']    = $_POST['notes_classify'];
            $notes_data['add_staff']     = $staff_no;
            $notes_data['update_time']   = date("Y-m-d H:i:s");
            $notes_data['add_time']      = date("Y-m-d H:i:s");
            $notes_data['memo']          = I('post.memo');
            $notes_data['status']        = 1;
            if ($is_alert == 1) {
                $notes_data['alert_time'] = I('post.alert_time');
                $notes_data['is_alert']   = 1;
                $add_not                  = $notes->add($notes_data);
                $is_notesmsg              = getConfig('notesmsg_staff');
                if ($is_notesmsg == 1) {
                    $data['alert_type']     = "03";
                    $data['alert_man']      = $staff_no;
                    $data['alert_param']    = $add_not;
                    $data['alert_title']    = I('post.notes_title');
                    $data['alert_content']  = I('post.notes_content');
                    $data['alert_time']     = I('post.alert_time');
                    $data['alert_nexttime'] = null;
                    $data['alert_cycle']    = "one";
                    $data['add_staff']      = $staff_no;
                    sendMultiMsg('notesmsg_staff', $staff_no, $staff_no, json_encode($data));
                }
                if ($add_not) {
                    saveLog(ACTION_NAME, 1, '', '操作员[' . session('communist_no') . ']于' . date("Y-m-d H:i:s") . '新增一条备忘录数据，编号为[' . $add_not . ']');
                    showMsg('success', '操作成功！！！', U('oa_notes_index'),1);
                } else {
                    showMsg('error', '操作失败！！！', '');
                }
            } else {
                $add_not = $notes->add($notes_data);
                if ($add_not) {
                    saveLog(ACTION_NAME, 1, '', '操作员[' . session('communist_no') . ']于' . date("Y-m-d H:i:s") . '新增一条备忘录数据，编号为[' . $add_not . ']');
                    showMsg('success', '操作成功！！！', U('oa_notes_index'),1);
                } else {
                    showMsg('error', '操作失败！！！', '');
                }
            }
        }
    }
    // notes备忘录详情

    /**
     * 获取备忘录信息
     *
     * @name oa_notes_info
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:53:08
     *         @updatetime 2016年8月30日上午11:53:08
     * @version 0.01
     */
    public function oa_notes_info()
    {
        checkAuth(ACTION_NAME);
        $db_notes                 = M('oa_notes');
        $db_alertmsg              = M('bd_alertmsg');
        $notes_id                 = I('get.notes_id'); // I方法获取数据
        $notes_info               = getOaNotesInfo($notes_id, 'all');
        $notes_info['alert_time'] = getFormatDate($notes_info['alert_time'], "Y-m-d H:i");
        $param                    = "notes_id/" . $notes_id;
        $alert_map['alert_param'] = $param;
        $alertmsg_info            = $db_alertmsg->where($alert_map)->find();
        if ($alertmsg_info) {
            $alertmsg_data['status'] = 1;
            $save_alertmsg           = $db_alertmsg->where($alert_map)->save($alertmsg_data);
            $this->assign('is_alert', 1);
        } else {
            $this->assign('is_alert', 0);
        }
        $this->assign('alertmsg_info', $alertmsg_info);
        $this->assign('notes_info', $notes_info);
        $this->display("Oanotes/oa_notes_info");
    }
    // 备忘录数据删除

    /**
     * 删除备忘录信息
     *
     * @name oa_notes_del()
     * @param
     *
     * @return
     *
     * @author yangluhai
     *         @addtime 2016年8月30日上午11:54:35
     *         @updatetime 2016年8月30日上午11:54:35
     * @version 0.01
     */
    public function oa_notes_del()
    {
        checkAuth(ACTION_NAME);
        $db_notes    = M('oa_notes');
        $db_alertmsg = M('bd_alertmsg');
        $notes_id    = I('get.notes_id'); // I方法获取数据
        if (!empty($notes_id)) {
            // 必要的非空判断需要增加
            $notes_map['notes_id'] = $notes_id;
            $notes_del = $db_notes->where($notes_map)->delete();
            if ($notes_del) {
                $url          = "notes_id/" . $notes_id;
                $alert_map['alert_param'] = $url;
                $alertmsg_del = $db_alertmsg->where($alert_map)->delete();
                if ($notes_del) {
                    saveLog(ACTION_NAME, 3, '', '操作员[' . session('communist_no') . ']于' . date("Y-m-d H:i:s") . '对备忘录编号 [' . $notes_id . ']进行删除操作');
                    showMsg('success', '操作成功！！！', U('oa_notes_index'));
                } else {
                    showMsg('error', '操作失败！！！', '');
                }
            }
        }
    }

    /**
     * *************************** 备忘录管理结束 ***********************
     */
}
