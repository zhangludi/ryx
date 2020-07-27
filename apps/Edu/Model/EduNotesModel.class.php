<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/1/30
 * Time: 上午11:32
 */

namespace Edu\Model;

use Think\Model;

class EduNotesModel extends Model
{
    protected $_validate = [];

    public function getNotesList($communist_no, $page, $pagesize, $keyword, $topic_id, $type, $material_id,$note_time)
    {
        $maps = [];
        if (!empty($communist_no))
            $maps['add_staff'] = ['eq', $communist_no];
        if (!empty($keyword)) {
            $maps['notes_title'] = ['like', "%$keyword%"];
            $maps['notes_content'] = ['like', "%$keyword%"];
        }
        if (!empty($topic_id))
            $maps['topic_type'] = ['eq', $topic_id];

        if (!empty($type))
            $maps['notes_type'] = ['eq', $type];
        if (!empty($material_id))
            $maps['material_id'] = ['eq', $material_id];
        if(!empty($note_time)){
           $start = $note_time." 00:00:000";
           $end = $note_time." 23:59:59";
           $maps['add_time'] = array('between',array($start,$end));
        }
        $notes_list = $this->where($maps)->limit($page, $pagesize)->order('add_time desc')->select();

        return $notes_list;
    }

    public function getNotesInfo($notes_id, $communist_no, $type, $material_id)
    {
        if (!empty($notes_id)) {
            $maps['notes_id'] = ['eq', $notes_id];
        } elseif (!empty($communist_no) && !empty($type) && !empty($material_id)) {
            $maps['add_communist'] = ['eq', $communist_no];
            $maps['notes_type'] = ['eq', $type];
            $maps['material_id'] = ['eq', $material_id];
        }
        else {
            return false;
        }

        $notes_list = $this->where($maps)->find();

        return $notes_list;
    }
}