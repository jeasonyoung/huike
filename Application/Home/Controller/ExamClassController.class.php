<?php
namespace Home\Controller;
use Think\Controller;

class ExamclassController extends Controller{
    /*添加考试*/
    public function add_user(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Home/ExamClass');
            $data = array();
            $data['EnName'] = I('EnName');
            $data['CnName'] = I('CnName');
            if(!empty(I('ExamTime'))){
				$data['ExamTime'] = I('ExamTime');
			}
            $data['EndDate'] = I('EndDate');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            $data['SortID'] = I('SortID');
            if(empty($data['EnName']) || empty($data['CnName']) || empty($data['EndDate'])){
                $this->error('考试英文名、中文名称、招生截至时间必须填写!');
            }
            if($model->insert_user($data)){
                $this->success('新增考试成功!',U('ExamClass/list_user'));
            }else{
                $this->error('新增考试失败!',U('ExamClass/add_user'));
            }
        }else{
            $this->display();
        }
    }
    
    /**
     * 删除考试
     * @param int $ExamId 考试ID
     * @return int 影响行数
     */
    public function del_user($examid){
        $model = D('Home/ExamClass');
        if($model->delete_user($examid)){
            $this->success('删除考试成功',U('ExamClass/list_user'));
        }else{
            $this->error('删除考试失败,请联系技术人员');
        }
    }
    
    /**
     * 修改考试
     * @param int $tid 教师ID
     */
    public function edit_user($examid){
        $model = D('Home/ExamClass');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $data['ExamID'] = $examid;
            $data['EnName'] = I('EnName');
            $data['CnName'] = I('CnName');
			if(!empty(I('ExamTime'))){
				$data['ExamTime'] = I('ExamTime');
			}
            $data['EndDate'] = I('EndDate');
            $data['SortID'] = I('SortID');
			$data['last_time'] = date('Y-m-d H:i:s',time());
            if(empty($data['EnName']) || empty($data['CnName']) || empty($data['EndDate'])){
                $this->error('考试英文名、中文名称、招生截至时间必须填写!');
            }
            if($model->update_user($data)){
                $this->success('用户修改成功',U('ExamClass/list_user'));
            }else{
                $this->error('修改失败或未做修改',U('ExamClass/edit_user',array('examid' => $examid)));
            }
        }else{
            $data = $model->query_user($examid);
            $this->assign('info',$data);
            $this->display('edit_user');
        }
    }
    
    /*考试列表*/
    public function list_user(){
        $model = D('Home/ExamClass');
        $data = $model->query_user();
        $this->assign('users',$data);
        $this->display();
    }
    
    //对外查询接口
    public function data_query(){
        $model = D('Home/ExamClass');
        return $model->query_user();
    }
}