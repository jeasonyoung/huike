<?php
namespace Home\Controller;
use Think\Controller;

class SubjectsController extends Controller{
    /*添加考试科目*/
    public function add_user($examid){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        $model = D('Home/Subjects');
        if(IS_POST){
            $data = array();
            $data['ExamID'] = $examid;
            $data['SubName'] = I('SubName');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            $data['SortID'] = I('SortID');
            if(empty($data['SubName']) || empty($data['ExamID'])){
                $this->error('科目名称、所属考试必须填写!');
            }
            if($model->insert_user($data)){
                $this->success('新增考试科目成功!',U('Subjects/list_user').'&examid='.$examid);
            }else{
                $this->error('新增考试科目失败!',U('Subjects/add_user',array('examid' => $examid)));
            }
        }else{
            $ExamName = array();
            $ExamName = $model->get_examname($examid);
            $this->assign('ExamName',$ExamName);
            $this->display();
        }
    }
    
    /**
     * 删除考试
     * @param int $subid 考试科目ID
     * @return int 影响行数
     */
    public function del_user($subid){
        $model = D('Home/Subjects');
        if($model->delete_user($subid)){
            $this->success('删除考试科目成功',U('Subjects/list_user',array('examid' => $examid)));
        }else{
            $this->error('删除考试科目失败,请联系技术人员');
        }
    }
    
    /**
     * 修改考试
     * @param int $subid 考试科目ID
     */
    public function edit_user($subid,$examid){
        $model = D('Home/Subjects');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $data['SubID'] = $subid;
            $data['SubName'] = I('SubName');
            $data['SortID'] = I('SortID');
			$data['last_time'] = date('Y-m-d H:i:s',time());
            if(empty($data['SubName'])){
                $this->error('科目名称、所属考试必须填写!');
            }
            if($model->update_user($data)){
                $this->success('考试科目修改成功',U('Subjects/list_user',array('examid' => $examid)));
            }else{
                $this->error('修改失败或未做修改',U('Subjects/edit_user',array('subid' => $subid,'examid' => $examid)));
            }
        }else{
            $ExamName = array();
            $ExamName = $model->get_examname($examid);
            $this->assign('ExamName',$ExamName);
            $data = $model->query_usersub($subid);
            $this->assign('info',$data);
            $this->display('edit_user');
        }
    }
    
    /*考试列表*/
	public function list_exam(){
        $model = D('Home/Subjects');
        $data = $model->query_exam();
        $this->assign('exams',$data);
        $this->display();
    }
    public function list_user($examid){
        $model = D('Home/Subjects');
        $data = $model->query_user($examid);
        if(isset($examid) && !empty($examid)){
                $this->assign('sublist',$data);
        }
        else{
            $this->error('考试ID不存在',U('Subjects/list_exam'));
        }
        $this->display();
    }
}