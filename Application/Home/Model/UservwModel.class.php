<?php
namespace Home\Model;
use Think\Model;
/**
 * UservwModel 用户自定义视图模型
 * 可写public方法实例化视图表 
 */
class UservwModel extends Model{
    protected $autoCheckFields =false;
    
    /**
     * 根据课时id取得课程相关信息
     * @param string $lesson_id 课时id
     * @return array 课程信息
     */
    public function Lessonview($lesson_id){
        return $this->table('vw_netplatform_courses_lessonview')->
        join('LEFT JOIN tbl_netplatform_settings_subjects t2 ON vw_netplatform_courses_lessonview.subjectId=t2.id')->
        join('LEFT JOIN tbl_netplatform_settings_exams t3 ON vw_netplatform_courses_lessonview.examId=t3.id')->
                field('vw_netplatform_courses_lessonview.name,t2.name as subjectname,t3.name as examname')->
                where("id='$lesson_id'")->
                find();
    }
}

