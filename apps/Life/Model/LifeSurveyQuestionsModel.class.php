<?php
 namespace Life\Model;

 use Common\Model\PublicModel;

 class LifeSurveyQuestionsModel extends PublicModel{
    
    protected $_validate = array(
         array('questions_id','require','操作失败',self::MODEL_UPDATE),
         array('survey_id','require','操作失败',self::MODEL_INSERT),
         array('questions_title','require','请输入标题',self::MODEL_BOTH,self::EXISTS_VALIDATE),
         array('questions_item','require','请输入选项',self::MODEL_BOTH,self::EXISTS_VALIDATE),
     ); 
    
     protected $_auto = array(
         array(add_staff,'session',self::MODEL_INSERT,'function','communist_no'),
         array('status',1,self::MODEL_BOTH),
         array('update_time','date',self::MODEL_UPDATE,'function','Y-m-d H:i:s'),
         array('add_time','date',self::MODEL_INSERT,'function','Y-m-d H:i:s')
     );
 }