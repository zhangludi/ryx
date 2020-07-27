<?php

namespace Ccp\Model;
use Think\Model\ViewModel;
class CcpCommunistViewModel extends ViewModel
{
    protected $viewFields = array(
        'ccp_communist'   =>array('communist_no','communist_name','_type'=>'left'),
        'bd_status' =>array('status_value','_on'=>'bd_status.status_no=ccp_communist.status'),
        'ccp_party'   =>array('party_name','_on'=>'ccp_communist.party_no=ccp_party.party_no'),
    );
}