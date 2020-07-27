<?php
 namespace Edu\Model;

 use Common\Model\PublicModel;

 class EduExamModel extends PublicModel{
     
    protected $_validate = array(
//          array('exam_id','require','操作失败',self::MODEL_UPDATE),
         array('exam_title','require','请输入标题',self::MODEL_BOTH,self::EXISTS_VALIDATE),
         array('exam_time','require','请输入考试时长',self::MODEL_BOTH,self::EXISTS_VALIDATE),
         array('exam_party','require','请选择部门',self::MODEL_BOTH,self::EXISTS_VALIDATE),
         array('exam_date','require','请输入考试时间',self::MODEL_BOTH,self::EXISTS_VALIDATE),
     ); 
    
     protected $_auto = array(
        array('add_staff','getSessionstaff',1,'callback'),
        array('status',11,self::MODEL_INSERT),
        array('update_time','date',self::MODEL_UPDATE,'function','Y-m-d H:i:s'),
        array('add_time','date',self::MODEL_INSERT,'function','Y-m-d H:i:s')
     );

    protected function getSessionstaff(){
        return session('staff_no');
    }

 }