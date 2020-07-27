<?php

namespace Common\Model;

use Think\Model;

class PublicModel extends Model
{
    /**
     *
     * @param    int    $type    查询多条与单条数据 0:单条1:多条
     * @param    array    $map    where语句数组形式
     * @param    string    $field    字段
     * @param    array    $order    排序
     * @param    string    $limit    分页
     * @return   array         数据列表
     */
    public function selectData($type,$map,$field = '*',$order = array('add_time'=>'desc'),$limit){
        if ($type == 0){
            $result = $this->where($map)->field($field)->order($order)->find();
        }else{
            $result = $this->where($map)->field($field)->order($order)->limit($limit)->select();
        }
        return $result;
    }
    /**
     * 更新数据
     * @param    array    $data      需要修改的数据
     * @param    string    $field    id字段的名称
     * @return   float        true或false
     */
    public function updateData($data = array(),$field)
    {
        if (!$this->create($data)){
            showMsg('error',$this->getError());
        }
        if (empty($data[$field])){
            $id = $this->add();
            if (!$id){
                return;
            }else{
                return $id;
            }
        }else{
            $status = $this->save();
            if ($status === false){
                return;
            }
        }
        return true;
    }

    /**
     * 删除数据
     * @param    string    $where    条件
     * @return   float        true或false
     */
    public function delData($where){
        if (!empty($where)){
            $result = $this->where($where)->delete();
        }else{
            $result = false;
        }

        return $result;
    }

    /**
     * 新增数据
     */
    public function Post($data)
    {
        if (!is_array($data)) : return; endif;

        $flag = $this->create($data);

        if (!$flag) : return; endif;

        $result = $this->add($flag);

        return $result;
    }

    /**
     * 修改数据
     */
    public function Put($data)
    {
        if (!is_array($data)) : return; endif;

        $flag = $this->create($data);

        $result = $this->save($flag);

        return $result;
    }
}