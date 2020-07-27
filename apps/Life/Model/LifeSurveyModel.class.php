<?php
 namespace Life\Model;

 use Common\Model\PublicModel;

 class LifeSurveyModel extends PublicModel{
     
    protected $_validate = array(
         array('survey_id','require','操作失败',self::MODEL_UPDATE),
         array('party_no','require','请选择部门',self::MODEL_BOTH,self::EXISTS_VALIDATE),
         array('survey_title','require','请输入标题',self::MODEL_BOTH,self::EXISTS_VALIDATE),
         array('survey_join_num','require','操作失败',self::MODEL_UPDATE,self::EXISTS_VALIDATE)
     ); 
    
     protected $_auto = array(
         array('status',0,self::MODEL_INSERT),
         array('update_time','date',self::MODEL_UPDATE,'function','Y-m-d H:i:s'),
         array('add_time','date',self::MODEL_INSERT,'function','Y-m-d H:i:s')
     );
 }