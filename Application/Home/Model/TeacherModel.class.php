<?php
namespace Home\Model;
use Think\Model;

class TeacherModel extends Model{
    /**
     * 向系统教师表插入教师数据
     * @param array $data 所添加的教师信息
     */
	protected $trueTableName = 'HK_Class_Teacher';
	
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询系统教师表数据
     * @param int $uid 教师id
     */
    public function query_user($uid){
        if(isset($uid) && !empty($uid)){
            return $this->where('tid='.$uid)->find();
        }else{
            return $this->field('tid,TeachName,ZCXL,TeachPic,create_time,last_time')->select();
        }
    }
	public function query_examclass(){
		return $this->table('HK_ExamClass')->field('examid,CnName')->order('SortID')->select();
    }
    
    /**
     * 修改系统用户信息
     * @param array $data 所修改的用户信息，必须包含主键
     */
    public function update_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除系统教师信息
     * @param int $tid 要删除的教师id
     */
    public function delete_user($tid){
        return $this->where('tid='.$tid)->delete();
    }
}