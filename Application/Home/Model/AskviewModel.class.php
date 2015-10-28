<?php
namespace Home\Model;
use Think\Model\ViewModel;
/**
 * TP视图
 */
class AskviewModel extends ViewModel{
    protected $viewFields = array(
        'teachers_answerquestiontopics' => array('id' => 'qid','title','content','status','agency_id','lesson_id','student_id','createtime','_as' => 'q','_type' => 'LEFT'),
        'security_users' => array('_as' => 'u','id','nickname','_on' => 'q.student_id=u.id','_type' => 'LEFT'),
        'teachers_answerquestiondetails' => array('_as' => 'answer','_on' => 'q.id=answer.topic_id','count(answer.id)' => 'renums')
    );
    
    public function GetQuestion($user_id,$agency_id,$pagesize){
        $where=!empty($agency_id)?$where.=" and agency_id='$agency_id'":$where="student_id='$user_id'";
        $count=$this->where($where)->count();
        $page=getpage($count, $pagesize);
        $data=$this->where($where)->limit($page->firstRow.','.$page->listRows)->order('createTime desc')->select();
        return array(
            'data' => $data,
            'total'=> $count,
            'page' => $page->show()
        );
    }
    
    public function myquestion($num,$lesson_id=null){
        $where="q.student_id='".session('userid')."'";
        if(!empty($lesson_id)){
           $where.=" and lesson_id='$lesson_id'" ;
        }
        return $this->where($where)->limit($num)->order('createtime desc')->select();
    }
}
