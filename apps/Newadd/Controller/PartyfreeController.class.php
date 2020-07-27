<?php
namespace System\Controller;//命名空间
use Think\Controller;
use Common\Controller\BaseController;

class PartyfreeController extends BaseController//继承Controller类
{
	//党费缴纳
	
	public function party_free_index(){
		checkAuth(ACTION_NAME);//判断越权
		
	}
}

