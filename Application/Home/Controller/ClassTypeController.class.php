<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class ClasstypeController extends BaseController{
    /*添加系统班级类型*/
    public function add_class_type(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Home/ClassType');
            $data = array();
            $data['ClassTypeName'] = I('ClassTypeName');
            $data['SortID'] = I('SortID');
            if(empty($data['ClassTypeName'])){
                $this->error('班级类型名称不能为空!');
            }
            if($model->insert_class_type($data)){
                $this->success('新增班级类型成功!',U('ClassType/list_class_type'));
            }else{
                $this->error('新增班级类型失败!',U('ClassType/add_class_type'));
            }
        }else{
            $this->display();
        }
    }
    
    /**
     * 删除系统班级类型
     * @param int $CTid 班级类型ID
     * @return int 影响行数
     */
    public function del_class_type($ctid){
        $model = D('Home/ClassType');
        if($model->delete_class_type($ctid)){
            $this->success('删除用户成功',U('ClassType/list_class_type'));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
    
    /**
     * 修改系统班级类型
     * @param int $CTID 班级类型ID
     */
    public function edit_class_type($ctid){
        $model = D('Home/ClassType');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $data['ClassTypeName'] = I('ClassTypeName');
            $data['SortID'] = I('SortID');
            if(empty($data['ClassTypeName'])){
                $this->error('请输入系统班级类型！',U('ClassType/edit_class_type',array('ctid' => $ctid)));
            }
            if($model->update_class_type($data)){
                $this->success('用户修改成功',U('ClassType/list_class_type'));
            }else{
                $this->error('修改失败或未做修改',U('ClassType/edit_class_type',array('ctid' => $ctid)));
            }
        }else{
            $data = $model->query_class_type($ctid);
            $this->assign('info',$data);
            $this->display('edit_class_type');
        }
    }
    
    /*系统班级类型列表*/
    public function list_class_type(){
        $model = D('Home/ClassType');
        $data = $model->query_class_type();
        $this->assign('users',$data);
        $this->display();
    }
}