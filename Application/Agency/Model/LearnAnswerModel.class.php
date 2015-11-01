<?php
namespace Agency\Model;
use Think\Model;

class LearnAnswerModel extends Model{
    
    public function insert_answer($data = array()){
        return $this->add($data);
    }
    /**
     * 查询提问表数据
     */
    public function query_ask(){
        return $this->where('t2.jgid='.session('JGID'))
                ->join('left join hk_user t2 ON t2.userid = hk_learn_ask.uid')
                ->join('left join hk_class_resources t3 ON t3.lessonid = hk_learn_ask.lessonid')
                ->field('t2.username,t3.cnname,hk_learn_ask.*')->select();
    }
    
    /**
     * 查询提问答疑
     * @param  $askid 提问id
     */     
    public function query_ask_answer($askid){
        return $this->where('hk_learn_ask.askid='.$askid)
                ->join('left join hk_user t2 ON t2.userid = hk_learn_ask.uid')
                ->join('left join hk_learn_answer t3 ON t3.askid = hk_learn_ask.askid')
                ->join('left join hk_jigou_admin t4 ON t4.uid = t3.jguid')
                ->field('t2.username,t4.username as jgusername,t3.content as anstext,t3.create_time as anstime,hk_learn_ask.*')->find();
    }
     /**
     * 删除提问答疑信息
     * @param int $askid 提问id
     */
    public function delete_ask($askid){
        $answerid = $this->where('hk_learn_ask.askid='.$askid)                
            ->join('left join hk_learn_answer t3 ON t3.lessonid = hk_learn_ask.lessonid')
            ->field('t3.answerid')->select();
        $model = M('Learn_Answer');
        $model->delete($answerid);
        return $this->delete($askid);
    }
}