<?php
namespace Home\Model;
use Think\Model;

class WangxiaoModel extends Model{
    protected $autoCheckFields =false; 
    /**
     * getOrder取得当前用户订单信息
     * @param $userid 用户id
     * @return 订单号
     */
    public function getOrder($userid){
        return $this->table('tbl_Netplatform_Students_OrderStudents')->where("student_id='".$userid."'")->getField('order_id');
    }
    
    /**
     * getClasses 根据订单号取得用户班级id
     * @param str $orderid 用户订单号
     * @return array 班级id数组
     */
    public function getClasses($orderid){
        $packages_class=$this->table('tbl_Netplatform_Students_OrderPackages')->
            join('tbl_Netplatform_Courses_PackageClasses ON tbl_Netplatform_Students_OrderPackages.package_id = tbl_Netplatform_Courses_PackageClasses.package_id')->
            field('tbl_Netplatform_Courses_PackageClasses.class_id')->
            where("order_id='$orderid'")->
            select(); 
        $common_class=$this->table('tbl_Netplatform_Students_OrderClasses')->
            where("order_id='$orderid'")->
            field('class_id')->
            select();
        if($packages_class){
            return array_unique(array_merge(array_column($packages_class, 'class_id'),array_column($common_class,'class_id')));
        }
        return array_column($common_class,'class_id');
    }
    
    /**
     * 取得用户相关班级信息
     * @param array $classes 班级id
     * @param $validate ture 取得有效班级 flase 过期班级
     * @return array 用户班级信息 
     */
    public function classData($classes,$validate){
        $where=' and datediff(endTime,Now())>0';
        if(!$validate){$where=' and  datediff(endTime,Now())<=0';}
        $ids = $this->classidsStr($classes);
        return $this->table('tbl_Netplatform_Courses_Classes')->where("agency_id='".session('agency_id')."' and id in ($ids)".$where)->select();
    }
    
    /**
     * 转换字符串ID 用户IN查询
     * @param $array id数组
     */
    private function classidsStr($array){
        $arr=array();
        for($i=0;$i<=count($array);$i++){
            if(!empty($array[$i])){
              $arr[] = "'".$array[$i]."'";  
            }
        }
        return implode($arr, ',');
    }
    
    /**
     * 根据ID取得一个班级的信息
     * @param str $classid 班级ID
     */
    public function OneClassData($classid){
        return $this->table('tbl_Netplatform_Courses_Classes as t1')->where("t1.id='$classid' and t1.agency_id='".session('agency_id')."'")->
                join("left join tbl_netplatform_teacherclasses t2 ON t2.class_id=t1.id")->
                join('left join tbl_netplatform_teachers t3 ON t2.teacher_id=t3.id')->
                field('t1.subject_id,t1.name as classname,t1.starttime,t1.endtime,t1.imgUrl as classimg,t3.name,t3.description,t3.title,t3.imgUrl')->
                find();
    }
    
    /**
     * 取得用户指定班级最后一次观看记录
     * @param string $classid 班级id
     * @param string $uid 用户id
     */
    public function LastListen($classid,$uid){
        return $this->table('vw_netplatform_courses_lessonview as t1')->
                join("inner join (select * from tbl_netplatform_students_learning where student_id='$uid' order by createTime desc) t2 ON t1.id=t2.lesson_id")->
                order('t2.createTime desc')->
                where("classId='$classid'")->
                getField('t2.lesson_id');
    }
    
    /**
     * 取得班级章节信息
     * @param str $subjectid 科目id
     */
    public function GetChapters($subjectid){
        $chapter = array();
        $list=$this->table('tbl_netplatform_settings_chapters')->where("subject_id='$subjectid' and pid is null")->find();
        return $this->getChildChapters($list['id'],$chapter);
        //show_bug($this->getChildChapters($list['id'],$chapter));
        //die();
    }
    
    /**
     * 递归取得章节信息
     * @param string $chapterid 章节id
     * @param array $array 空数组
     */
    private function getChildChapters($chapterid,&$array){
        $db=M('settings_chapters');
        $data=$db->field('tbl_Netplatform_settings_chapters.id,tbl_Netplatform_settings_chapters.name,pid,tbl_Netplatform_settings_chapters.orderno,t2.lesson_id,t3.time,t4.status')->
                join('LEFT JOIN tbl_Netplatform_Courses_LessonChapters t2 ON t2.chapter_id=tbl_Netplatform_Settings_Chapters.id')->
                join('LEFT JOIN tbl_netplatform_courses_lessons t3 ON t3.id=t2.lesson_id')->
                join("LEFT JOIN (select * from tbl_netplatform_students_learning where student_id='".session('userid')."') t4 ON t4.lesson_id=t2.lesson_id")->
                where("pid='$chapterid'")->order('orderno')->select();
        if($data){
            for($i=0;$i<count($data);$i++){
              $tempcount=$db->where("pid='".$data[$i]['id']."'")->count('id'); //统计子记录个数  
              array_push($array,array(
                'name'        => $data[$i]['name'],
                'id'          => $data[$i]['id'],
                'pid'         => $data[$i]['pid'],
                'orderid'     => $data[$i]['orderno'],
                'child'       => $tempcount,
                'order'       => $i+1,
                'lesson_id'   => $data[$i]['lesson_id'],
                'time'        => $data[$i]['time'],
                'status'      => $data[$i]['status']  
                )
              );   
              $this->getChildChapters($data[$i]['id'], $array);
            }
        }
        return $array;
    }
    
    public function getExaminfo($subject){
        $db=M('settings_subjects');
        $ids = $this->classidsStr($subject);
        return $db->where("tbl_netplatform_settings_subjects.id in ($ids)")->field('tbl_netplatform_settings_subjects.id,exam_id,tbl_netplatform_settings_subjects.name as subname,t2.name')->
               join('LEFT JOIN tbl_netplatform_settings_exams t2 ON tbl_netplatform_settings_subjects.exam_id=t2.id')->
               select();
    }
    
    /**
     * 取得课时资源
     * @param string $chapterid 章节ID
     * @return array 课时信息
     */
    public function GetLessons($chapterid){
        $db=M('courses_lessons');
        $class_id=$this->classidsStr(session('class_id'));
        return $db->field('id,name,t2.chapter_id')->join('left join tbl_Netplatform_Courses_LessonChapters t2 ON t2.lesson_id=tbl_Netplatform_Courses_Lessons.id')->
        where("class_id in ($class_id) and t2.chapter_id='$chapterid'")->select();
    }
    /**
     * 添加用户提问信息
     * @param array $data 提问信息
     * @return mixd 保存是否成功
     */
    public function AddQuestion($data){
        $db=M('teachers_answerquestiontopics');
        $Lesson=$db->table('vw_netplatform_courses_lessonview as t1')->
                field('subject.name as subname,exam.name as examname,t1.name')->
                where("t1.id='".$data['lesson_id']."'")->
                join("left join tbl_netplatform_settings_subjects subject ON subject.id=t1.subjectId")->
                join('left join tbl_netplatform_settings_exams exam ON exam.id=t1.examId')->
                find();
        $data['title']=$Lesson['examname'].' - '.$Lesson['subname'].' - '.$Lesson['name'];
        return $db->add($data);
    }
    
    /**
     * 用户提问列表
     */
    public function GetQuestion($limit){
       $db=M('teachers_answerquestiontopics');
       $count=$this->QuestionCount(0,  session('userid'));
       $page=new \Think\Page($count,10);
       $pagebar=$page->show();
       $data=$db->field('title,context,student_id,t2.nickname')->
               where("student_id='".session('userid')."'")->
               join('left join tbl_netplatform_security_users t2 ON t2.id=tbl_netplatform_teachers_answerquestiontopics.student_id')->
               order('createTime desc')->
               limit($limit)->
               select();
       return array(
           'data' => $data,
           'page' => $pagebar
       );
    }
    
    private function QuestionCount($status=0,$userid){
        $db=M('teachers_answerquestiontopics');
        return $db->where("student_id='$userid' and status=$status")->count('id');
    }
    /**
     * 取得课程相关信息
     * @param string $lesson_id
     */
    public function GetLessonInfo($lesson_id){
        $data = $this->table('tbl_netplatform_courses_lessons')->where("id='$lesson_id'")->find();
        if($data){
            $data['pre'] = $this->GetRelateLesson($data['id'], $data['class_id'],'createtime asc');
            $data['next'] = $this->GetRelateLesson($data['id'], $data['class_id'],'createtime desc');
            $temp_videourl = play_set($data['videourl']);   //播放地址加密
            $temp_highvideourl = play_set($data['highvideourl']);  //播放地址加密
            $data['videourl'] = $temp_videourl;
            $data['highvideourl'] = $temp_highvideourl;
        }
        return $data;
    }
    
    /*取得当前lesson上一节课和或者下一节课*/
    private function GetRelateLesson($lesson_id,$class_id,$order){
        return $this->table('tbl_netplatform_courses_lessons')->
                where("id<>'$lesson_id' and class_id='$class_id'")->
                order($order)->
                limit(1)->
                getField('id');
    }
    
    /**
     * 记录学员学习情况
     * @param string $lesson_id 课时id
     */
    public function Learning($lesson_id){
        $db=M('students_learning');
        $student_id=session('userid');
        $data=$db->where("lesson_id='$lesson_id' and student_id='$student_id'")->find();
        if(!$data){
            $this->add_Learn($lesson_id,session('userid')); 
        }
    }
    
    /**
     * 添加学员学习记录
     * @param string $lesson_id 课时id
     */
    private function add_Learn($lesson_id,$student_id){
        $db=M('students_learning');
        $agency_id=session('?agency_id')?session('agency_id'):C('default_agenices');
        $data=array();
        $data['student_id']=$student_id;
        $data['lesson_id']=$lesson_id;
        $data['agency_id']=$agency_id;
        $data['status']=0;
        $db->add($data);
    }
    
    /**
     * 更新用户学习进度
     * @param string $lesson_id 课时id
     * @param string $student_id 学员id
     */
    public function update_Learn($lesson_id,$student_id){
        $db=M('students_learning');
        $db->where("lesson_id='$lesson_id' and student_id='$student_id'")->setField('status', 1);
    }
    
    /**取得用户消息信息*/
    public function GetMsg(){
        $db=M('settings_msgusers');
        $data=$db->field('msgbody.title,msgbody.content,status,tbl_netplatform_settings_msgusers.createTime,msgbody.type,id')->
                where("tbl_netplatform_settings_msgusers.user_id='".session('userid')."'")->
                join("LEFT JOIN tbl_netplatform_settings_msgbody msgbody ON msgbody.id=tbl_netplatform_settings_msgusers.msg_id")->
                order('tbl_netplatform_settings_msgusers.createTime desc')->
                select();
        return $this->GroupMsg($data);
    }
    
    /**
     * 用户消息分组 已读，未读，所有消息
     * @param array $Msg 消息数组
     * @return array 分组后的消息
     */
    private function GroupMsg($Msg=array()){
        $array_on=array();
        $array_off=array();
        foreach ($Msg as $v){
            if($v['status']==1){
                $array_on[]=$v;
            }else{
                $array_off[]=$v;
            }
        }
        return array(
            'un_read'   => $array_off,
            'readed'    => $array_on,
            'allmsg'    => array_merge($array_on,$array_off)
        );
    }


    /**
     * 取得一条消息相关
     * @param string $msg_id 消息id
     * @return array 消息相关信息
     */
    public function GetOneMsg($msg_id){
        $db=M('settings_msgusers');
        $data=$db->where("msg_id='$msg_id' And tbl_netplatform_settings_msgusers.user_id='".session('userid')."'")->
                field('status,t2.title,t2.content,createTime')->
                join('tbl_netplatform_settings_msgbody t2 ON t2.id=tbl_netplatform_settings_msgusers.msg_id')->
                find();
        $db->where("msg_id='$msg_id' AND user_id='".session('userid')."'")->setField('status',1);   //更新消息为已读状态
        $data['relate']=$this->RelateMsg($msg_id, $data['createtime']);
        return $data;
    }
    
    private function RelateMsg($msg_id,$msg_time){
        $db=M('settings_msgusers');
        $next=$db->where("msg_id<>'$msg_id' And user_id='".session('userid')."' And createTime > '$msg_time'")->
                limit(1)->
                getField('msg_id');
        $pre=$db->where("msg_id<>'$msg_id' And user_id='".session('userid')."' And createTime < '$msg_time'")->
                limit(1)->
                getField('msg_id');
        return array(
            'pre'   => U('wangxiao/readmsg',array('msg_id'  => $pre)),
            'next'  => $next
        );
    }

    /**
     * 删除用户消息
     * @param array $msg_id 待删除的消息id
     * @return 删除条数
     */
    public function DelMsg($msg_id=array()){
        $ids=  $this->classidsStr($msg_id);
        return $this->table('tbl_netplatform_settings_msgusers')->where("msg_id in ($ids) and user_id='".session('userid')."'")->delete();
    }
    
    /**
     * 笔记列表
     * @param string $lesson_id 课时id
     * @param string $user_id 用户id
     * @param ing $limit 限制记录条数,用于非分页模块显示
     */
    public function NoteList($lesson_id,$user_id,$limit){
        $db=M('courses_notes');
        $where="user_id='$user_id'";
        if(!empty($lesson_id)){
            $where.=" And lesson_id='$lesson_id'";
        }
        if(is_numeric($limit)){
           return $db->where($where)->order('createtime desc')->limit($limit)->select(); 
        }else{
            $count=$db->where($where)->count();
            $page=  getpage($count);
            $data=$db->where($where)->
                    field('vw.name,content,playtime,subject.name as subname,exam.name as examname,tbl_netplatform_courses_notes.createtime,vw.id,tbl_netplatform_courses_notes.id as noteid')->
                    order('tbl_netplatform_courses_notes.createtime desc')->
                    join("left join vw_netplatform_courses_lessonview vw ON vw.id=tbl_netplatform_courses_notes.lesson_id")->
                    join('left join tbl_netplatform_settings_subjects subject ON vw.subjectId=subject.id')->
                    join('left join tbl_netplatform_settings_exams exam ON vw.examId=exam.id')->
                    limit($page->firstRow.','.$page->listRows)->select();
            return array(
                'data'  => $data,
                'page'  => $page->show()
            );
        }
    }


    /**
     * 添加笔记
     * @param string $lesson_id 课时id
     * @param string $uid 用户id
     * @return array 所添加的笔记信息
     */
    public function AddNote($lesson_id,$content,$playtime,$uid){
        $db=M('courses_notes');
        $data=array();
        $data['id']=uuid();
        $data['lesson_id']=$lesson_id;
        $data['playTime']=$playtime;
        $data['content']=$content;
        $data['user_id']=$uid;
        $result=$db->add($data);
        if($result){
            $data['playTime']= number_format(playtime($playtime),2,':',':');
            return $data;
        }
    }
    
    /**
     * 修改笔记
     * @param string $note_id 笔记id
     * @param string $uid 用户id
     * @param string $content 修改的内容
     */
    public function EditNote($note_id,$content,$uid){
        $db=M('courses_notes');
        $data=array('content' => $content,'lastTime' => date('Y-m-d', time()));
        return $db->where("id='$note_id' and user_id='$uid'")->setField($data);
    }
    
    /**
     * 删除笔记
     * @param string $note_id 笔记id
     * @param string $uid 用户id
     */
    public function DeleteNote($note_id,$uid){
        $db=M('courses_notes');
        return $db->where("id='$note_id' and user_id='$uid'")->delete();
    }
    
    /**
     * 提问回答详情
     * @param string $qid 问题id
     */
    public function answerInfo($qid){
        $db=M('teachers_answerquestiondetails');
        $question = $db->table('tbl_netplatform_teachers_answerquestiontopics')->field('title,content,createTime')->where("id='$qid'")->find();
        $data = $db->where("topic_id='$qid'")->
                join('left join tbl_netplatform_security_users t2 ON t2.id=tbl_netplatform_teachers_answerquestiondetails.user_id')->
                field('t2.name,t2.imgUrl,tbl_netplatform_teachers_answerquestiondetails.content,tbl_netplatform_teachers_answerquestiondetails.createTime')->
                order('tbl_netplatform_teachers_answerquestiondetails.createTime desc')->
                select();
        return array(
            'question'  => $question,
            'data'      => $data
        );
    }
    
    /**
     * 继续提问
     * @param string $data 问题信息
     * @param string $qid 问题id
     */
    public function addreply($data){
        $db=M('teachers_answerquestiondetails');
        return $db->add($data);
    }
}

