<?php
namespace Agency\Model;
use Think\Model;

class LearnAdviceModel extends Model{
    /**
     * 查询建议表数据
     * @param int  用户id
     */
    public function query_advice(){
        return $this->where('t2.jgid='.session('JGID'))
                ->join('left join hk_user t2 ON t2.userid = hk_learn_advice.uid')
                ->field('t2.username,t2.nickname,hk_learn_advice.*')->select();
    }
     /**
     * 删除机构班级信息
     * @param int $advid 要删除的班级id
     */
    public function delete_advice($advid){
        return $this->delete($advid);
    }
}