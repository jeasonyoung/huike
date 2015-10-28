<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class WangxiaoController extends BaseController{
    public function Index(){
        $Model=D("Home/Wangxiao");
		if(session('class_id')){
			$myclass=$Model->classData(session('class_id'),true);
			/*缓存用户科目$myclass*/
			F('mysubject'.session('username'),array_unique($myclass));
			$myclass2=$Model->classData(session('class_id'),FALSE);
			$this->assign('myclass',$myclass); //我的有效视频
			$this->assign('myclass2',$myclass2); //我的过期视频
		}else{
			$this->assign('empty','<div class="nonebox" style="background:none"><img src="'.APP_IMG.'none-news.png" width="291" height="120"><span>还没有课程哦</span></div>');
		}
        $this->display();
    }
    
    /*取得订单号*/
    public function getOrder($userid){
        $Model=D('Home/Wangxiao');
        return $Model->getOrder($userid);
    }
    
    /*取得用户班级ID*/
    public function getClassid($orderid){
        $Model=D('Home/Wangxiao');
        return $Model->getClasses($orderid);
    }

    /* catalog视频班级章节目录*/
    public function catalog($classid){
        $Model=D("Home/Wangxiao");
        $info=$Model->OneClassData($classid);
        $jilu=$Model->LastListen($classid,session('userid'));
        //echo $info['subject_id'];
        //die();
        $chapters=$Model->GetChapters($info['subject_id']);
        $this->assign('chapters',$chapters);   //章节信息
        $this->assign('info',$info);
        $this->assign('lastid',$jilu);          //最后学习记录
        $this->display();
    }


    /* 显示用户信息*/
    public function Profile(){
        $Model = D('Home/Users');
        $Data = $Model->UserData(session('username'));
        $this->assign('info',$Data);
        $this->display();
    }
    
    /*更新用户信息*/
    public function profile_update(){
        $Model = D('Home/Users');
        $name = I('post.name');
        $phone = I('post.phone');
        $email = I('post.email');
        $result = compact('name','phone','email');
        $fields = array('name','phone','email');
        $condition = "account='".session('username')."'";
        if($Model->UpdateData($fields,$result,$condition)){
            $this->success("您的信息已更新",U('wangxiao/profile'));
        }
    }
    
    /*用户密码修改*/
    public function password_update(){
       $Model = D('Home/Users');
       $curpass = I('post.oldpass');
       $newpass = I('post.newpass');
       $data = $Model->UserData(session('username'));
       if(md5($curpass)!==$data['password']){
           $this->error('您当前密码输入错误,将不能进行修改');
       }
       if($Model->UpdateData('password',  md5($newpass),"account='".session('username')."'")){
           $this->success('您的密码已经修改!');
       }
    }
    
    /*问题列表*/
    public function Question(){
        $Model=D('Home/Askview');
		if(session('class_id')){
			$data=$Model->GetQuestion(session('userid'),'',5);
			$empty='<div class="nonebox"><img src="'.APP_IMG.'none-news.png" width="291" height="120"><span>您暂时还没有提问</span></div>';
			$this->assign('empty',$empty);
			$this->assign('questions',$data['data']);
			$this->assign('page',$data['page']); //分页
		}else{
			$empty_1='<div class="nonebox"><img src="'.APP_IMG.'none-news.png" width="291" height="120"><span>购买课程后才能提问哦</span></div>';
			$this->assign('empty_1',$empty_1);
		}
        $this->display();
    }
    
    /*提问模块*/
    public function Ask(){
        $Model=D('Home/Wangxiao');
        $Ask_Model=D('Home/Askview');
        $exam=$Model->getExaminfo(array_column(F('mysubject'.session('username')),'subject_id'));
        $question=$Ask_Model->myquestion(3);
        $this->assign('myquestion',$question);
        $this->assign('exam',$exam);
        $this->display();
    }
    
    /*问题回答详情*/
    public function Answer($qid){
        $Model=D('Home/Wangxiao');
        if(IS_POST){
            $redata['id']=uuid();
            $redata['topic_id']=$qid;
            $redata['content']=I('context');
            $redata['user_id']=session('userid');
            if($Model->addreply($redata)){
                exit("<script>alert('继续提问成功');window.location.href='".U('wangxiao/answer',array('qid'  => $qid))."'</script>");
            }
        }
        $data=$Model->answerInfo($qid);
        $this->assign('answer',$data);
        $this->display();
    }
    
    
    /** 载入考试-科目-章节Select信息*/
    public function LoadOptions(){
        $Model = D('Home/Wangxiao');
        $id=I('id');
        $types=I('types');
        switch ($types){
            case 'chapter':
                $tid='subject_id';
                $data = $Model->GetChapters($id);
                break;
            case 'lessons':
                $data = $Model->GetLessons($id); //章节id
                break;
            default :
                $tid='';
        }
        $this->ajaxReturn($data);
    }
    
    /**
     * 学员提问
     * 两种方式Post,Ajax
     */
    public function tiwen(){
        $Model=D('Home/Wangxiao');
        $data=array();
        $data['id']=uuid();
        $data['agency_id']=  empty(session('agency_id'))?C('default_agenices'):session('agency_id');
        $data['student_id']=session('userid');
        if(IS_POST){
           $data['lesson_id']=I('lessons');
           $data['content']=I('content');
           if(empty($data['lesson_id'])){
               exit($this->error('请选择问题相关课时'));
           }
           if($Model->AddQuestion($data)){
               $this->success("您的提问已提交");
           }else{
               $this->error('提交失败');
           }
        }elseif (IS_AJAX) {
           $data['lesson_id']=I('get.lesson_id');
           $data['content']=I('get.content');
           if(!empty($data['lesson_id']) && !empty($data['content'])){
               if($Model->AddQuestion($data)){
                   $this->ajaxReturn($data);
               }
           }
        }
    }
    
    /**
     * 课程播放控制器
     * @param string $vid 课程id
     */
    public function Play($vid){
        $Model=D('Home/Wangxiao');
        $M2=D('Home/Askview');
        $lesson=$Model->GetLessonInfo($vid);
        $info=$Model->OneClassData($lesson['class_id']);
        $chapters=$Model->GetChapters($info['subject_id']);
        $question=$M2->myquestion(5,$vid);
        $Notes=$Model->NoteList($vid,session('userid'),5);
        $Model->Learning($vid);    //记录学员学习情况
        $this->assign('question',$question);
        $this->assign('note',$Notes);
        $this->assign('chapter',$chapters);
        $this->assign('lesson', $lesson);
        $this->display();
    }
    
    /**
     * 记录课时是否学习完成
     * @param string $lesson_id 课时id
     * @param int $playtime 当前播放时间
     */
    public function Playdone($lesson_id,$playtime){
      $Model=D('Home/Wangxiao');
      $Lesson_Info=$Model->GetLessonInfo($lesson_id);
      if($Lesson_Info['time'] - $playtime <=20){
         $Model->update_Learn($lesson_id,session('userid')); 
      }
    }
    
    /*当前用户消息*/
    public function Msg(){
        $Model=D('Home/Wangxiao');
        $Msg=$Model->GetMsg();
        $this->assign('msg',$Msg);
        $empty='<li class="nonebox" style="background:none"><img src="'.APP_IMG.'none-news.png" width="291" height="120"><span>还没有消息哦</span></li> ';
        $this->assign('empty',$empty);
        $this->display();
    }
    
    /*阅读消息*/
    public function ReadMsg($msg_id){
        $Model=D('Home/Wangxiao');
        $Msg=$Model->GetOneMsg($msg_id);
        $this->assign('msg',$Msg);
        $this->display();
    }

    /**
     * 删除用户消息
     * @param string $msg_id 消息id
     */
    public function del_msg($msg_id){
        $del_mid=array();
        array_push($del_mid, $msg_id);
        $Model=D('Home/Wangxiao');
        if($Model->DelMsg($del_mid)){
            $this->success('消息删除成功');
        }
    }
    
    /*用户笔记*/
    public function Note(){
        $Model=D('Home/Wangxiao');
        $lesson_id=I('post.lesson_id','');
        
		if(F('mysubject'.session('username'))){
			$exam=$Model->getExaminfo(array_column(F('mysubject'.session('username')),'subject_id'));
			$Notes=$Model->NoteList($lesson_id,session('userid'));
			$this->assign('note',$Notes['data']);
			$this->assign('page',$Notes['page']);
			$this->assign('exam',$exam);
		}else{
			$this->assign('empty','<div class="nonebox" style="background:none"><img src="'.APP_IMG.'none-news.png" width="291" height="120"><span>购买课程后才能做笔记哦</span></div>');
		}
        $this->display();
    }
    
    /**
     * 添加笔记
     * @param string $lesson_id 课时id
     */
    public function AddNote($lesson_id){
        if(IS_AJAX){
            $Model=D('Home/Wangxiao');
            //$Lessonvw=D('Home/Uservw');     //调用lesson的视图
            $content=I('get.context');
            $playtime=I('get.playtime');
            $data=$Model->AddNote($lesson_id,$content,$playtime,session('userid'));
            if($data){
                $this->ajaxReturn($data);
            }
        }
    }
    
    /**
     * 修改笔记
     * 修改笔记只能修改笔记内容,最后更新时间
     * @param string $note_id 笔记id
     */
    public function EditNote($note_id){
        $Model=D('Home/Wangxiao');
        $content=I('get.content');
        if(empty($content)){
            exit('error');
        }
        if($Model->EditNote($note_id,$content,session('userid'))){
            echo 'success';
        }else{
            exit('error');
        }
    }
    
    /**
     * 删除用户笔记
     * @param string $note_id 笔记id
     */
    public function DelNote($note_id){
        $Model=D('Home/Wangxiao');
        if($Model->DeleteNote($note_id,session('userid'))){
            echo 'success';
        }else{
            echo 'error';
        }
    }
    
    public function _empty(){
        echo '您访问的页面不存在';
    }
}

