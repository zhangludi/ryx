<?php
/********************
     * 模块：系统设置
     * 作者：王彬
     * 时间：2016-07-04
     * 
     *********************/
namespace System\Controller;// 命名空间
use Think\Controller;
use Common\Controller\BaseController;
class SysconfigController extends BaseController // 继承Controller类
{
	/**
	 * @name  sys_config_index()
	 * @desc  系统设置页签页面
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.1.0
	 * @updatetime   2016-08-19
	 * @addtime   2016-07-04
	 */
	public function sys_config_index()
	{
		$sys_config = M('sys_config');
		$com_msg_template = M('com_msg_template');
		$com_msg_type = M('com_msg_type');
		$config_row = $sys_config->select();
		foreach($config_row as &$row){
			$this->assign($row['config_code'],$row['config_value']);
		}
		$template_list = $com_msg_template->select();
		$this->assign('template_list',$template_list);
		$type_list = $com_msg_type->select();
		foreach($type_list as $list){
			$this->assign($list['type_code'],$list['is_msg']);
		}
		$this->assign('type_list',$type_list);
		$this->display("Sysconfig/sys_config_index");
	}
	/**
	 * @name  sys_config_do_save()
	 * @desc  保存配置
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-04
	 */
	public function sys_config_do_save()
	{
		$sys_config = M('sys_config');
		$com_msg_type = M('com_msg_type');
		$config = $_POST;
		$data['update_time'] = date('Y-m-d H:i:s');
		$data['add_staff'] = session('staff_no');
		foreach($config as $config_code=>$config_value){
			if($config_value != ''){
                $config_map['config_code'] = array('like','%'.$config_code.'%');
                $is_config_code=$sys_config->where($config_map)->select();
                if ($is_config_code){
					$data['config_value'] = $config_value;
                    $config_map['config_code'] = $config_code;
                    $sys_config_save = $sys_config->where($config_map)->save($data);
                }else{
					$data['add_time'] = date('Y-m-d H:i:s');
					$data['config_code'] = $config_code;
					$data['config_value'] = $config_value;
                    $sys_config_save = $sys_config->add($data);
                }
			}
            $dt['is_msg'] = $config_value;
			$data['template_id'] = $config_value;

			switch($config_code){
                case 'notesmsg_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'notesmsg_communist'")->save($dt);
                    break;
                case 'notesmsg_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'notesmsg_communist'")->save($data);
                    break;
                case 'noticemsg_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'noticemsg_communist'")->save($dt);
                    break;
                case 'noticemsg_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'noticemsg_communist'")->save($data);
                    break;
                case 'oa_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'oa_communist'")->save($dt);
                    break;
                case 'oa_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'oa_communist'")->save($data);
                    break;
                case 'performance_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'performance_communist'")->save($dt);
                    break;
                case 'performance_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'performance_communist'")->save($data);
                    break;
                case 'communication_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'communication_communist'")->save($dt);
                    break;
                case 'communication_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'communication_communist'")->save($data);
                    break;
                case 'msg_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'msg_communist'")->save($dt);
                    break;
                case 'msg_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'msg_communist'")->save($data);
                    break;
				case 'recruitmsg_recruit':
					$com_msg_type_save = $com_msg_type->where("type_code = 'recruitmsg_recruit'")->save($dt);
					break;
                case 'recruitmsg_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'recruitmsg_recruit'")->save($data);
                    break;
                case 'auditionmsg_recruit':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'auditionmsg_recruit'")->save($dt);
                    break;
                case 'auditionmsg_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'auditionmsg_recruit'")->save($data);
                    break;
                case 'payment_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'payment_communist'")->save($dt);
                    break;
                case 'payment_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'payment_communist'")->save($data);
                    break;
                case 'project_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'project_communist'")->save($dt);
                    break;
                case 'project_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'project_communist'")->save($data);
                    break;
                case 'bug_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'bug_communist'")->save($dt);
                    break;
                case 'bug_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'bug_communist'")->save($data);
                    break;
                case 'cost_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'cost_communist'")->save($dt);
                    break;
                case 'cost_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'cost_communist'")->save($data);
                    break;
                case 'budget_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'budget_communist'")->save($dt);
                    break;
                case 'budget_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'budget_communist'")->save($data);
                    break;
                case 'officesupplies_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'officesupplies_communist'")->save($dt);
                    break;
                case 'officesupplies_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'officesupplies_communist'")->save($data);
                    break;
                case 'fixedassets_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'fixedassets_communist'")->save($dt);
                    break;
                case 'fixedassets_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'fixedassets_communist'")->save($data);
                    break;
                case 'digitalassets_communist':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'digitalassets_communist'")->save($dt);
                    break;
                case 'digitalassets_template':
                    $com_msg_type_save = $com_msg_type->where("type_code = 'digitalassets_communist'")->save($data);
                    break;

			}
		}
		createQrcode();//更换二维码链接
		showMsg('success','操作成功！',U('sys_config_index'));
	}
	/**
	 * @name  sys_config_msg_template_data()
	 * @desc  获取模版数据
	 * @param 
	 * @return 
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-08-17
	 */
	public function sys_config_msg_template_data($template_id){
		$com_msg_template = M('com_msg_template');
        $template_map['template_id'] = $template_id;
		$template_data = $com_msg_template->where($template_map)->field("template_content,template_name")->find();
		if($template_data){
		    if(!empty(trim($template_data['template_content']))){
		        ob_clean();$this->ajaxReturn($template_data['template_content']);
		    }else{
		        ob_clean();$this->ajaxReturn($template_data['template_name']);
		    } 
		}else{
			ob_clean();$this->ajaxReturn(0);
		}
	}
}
