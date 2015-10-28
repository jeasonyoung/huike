<?php
namespace Home\Controller;
use Think\Controller;

class TeacherController extends Controller{
    /*添加主讲老师*/
    public function add_user(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        $model = D('Home/Teacher');
        if(IS_POST){
            $data = array();
            $data['TeachName'] = I('TeachName');
            $data['ZCXL'] = I('ZCXL');
            $data['TeachPic'] = I('TeachPic');
            $data['AllExam'] = I('AllExam');
            $data['Content'] = I('Content');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            if(empty($data['TeachName']) || empty($data['TeachPic']) || empty($data['AllExam']) || empty($data['Content'])){
                $this->error('教师姓名、相片、主讲考试、教师简介必须填写!');
            }
            if($model->insert_user($data)){
                $this->success('新增主讲老师成功!',U('Teacher/list_user'));
            }else{
                $this->error('新增主讲老师失败!',U('Teacher/add_user'));
            }
        }else{
			$data = $model->query_examclass();
            $this->assign('examlist',$data);
            $this->display();
        }
    }
    
    /**
     * 删除主讲老师
     * @param int $TeacherId 主讲老师ID
     * @return int 影响行数
     */
    public function del_user($uid){
        $model = D('Home/Teacher');
        if($model->delete_user($uid)){
            $this->success('删除主讲老师成功',U('Teacher/list_user'));
        }else{
            $this->error('删除主讲老师失败,请联系技术人员');
        }
    }
    
    /**
     * 修改主讲老师
     * @param int $tid 教师ID
     */
    public function edit_user($uid){
        $model = D('Home/Teacher');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $data['TID'] = $uid;
            $data['TeachName'] = I('TeachName');
            $data['ZCXL'] = I('ZCXL');
            $data['TeachPic'] = I('TeachPic');
            $data['AllExam'] = I('AllExam');
            $data['Content'] = I('Content');
			$data['last_time'] = date('Y-m-d H:i:s',time());
            if(empty($data['TeachName']) || empty($data['TeachPic']) || empty($data['AllExam']) || empty($data['Content'])){
                $this->error('教师姓名、相片、主讲考试、教师简介必须填写!');
            }
            if($model->update_user($data)){
                $this->success('用户修改成功',U('Teacher/list_user'));
            }else{
                $this->error('修改失败或未做修改',U('Teacher/edit_user',array('uid' => $uid)));
            }
        }else{
            $data = $model->query_examclass();
            $this->assign('examlist',$data);
			$data = $model->query_user($uid);
			$this->assign('info',$data);
            $this->display('edit_user');
        }
    }
    
    /*主讲老师列表*/
    public function list_user(){
        $model = D('Home/Teacher');
        $data = $model->query_user();
        $this->assign('users',$data);
        $this->display();
    }
}