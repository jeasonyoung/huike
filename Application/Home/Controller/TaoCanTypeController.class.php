<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class TaocantypeController extends BaseController{
    /*添加套餐类型*/
    public function add_class_taocantype(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Home/TaoCanType');
            $data = array();
            $data['TCTypeName'] = I('TCTypeName');
            $data['Discount'] = I('discount');
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['SortID'] = I('SortID');
            if(empty($data['TCTypeName'])){
                $this->error('套餐类型名称不能为空!');
            }
            if($model->insert_class_taocantype($data)){
                $this->success('新增套餐类型成功!',U('Taocantype/list_class_taocantype'));
            }
			else{
                $this->error('新增套餐类型失败!',U('Taocantype/add_class_taocantype'));
            }
        }
		else{
            $this->display();
        }
    }
    
    /**
     * 删除系统套餐类型
     * @param int $tctypeid 套餐类型ID
     * @return int 影响行数
     */
    public function del_class_taocantype($tctypeid){
        $model = D('Home/TaoCanType');
        if($model->delete_class_taocantype($tctypeid)){
            $this->success('删除套餐类型成功',U('Taocantype/list_class_taocantype'));
        }
		else{
            $this->error('套餐类型删除失败,请联系技术人员');
        }
    }
    
    /**
     * 修改系统套餐类型
     * @param int $CTID 套餐类型ID
     */
    public function edit_class_taocantype($tctypeid){
        $model = D('Home/TaoCanType');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $data['TCTypeID'] = I('tctypeid');
            $data['TCTypeName'] = I('TCTypeName');
            $data['discount'] = I('discount');
            $data['last_time'] = date('Y-m-d H:i:s');
            $data['SortID'] = I('SortID');
            if(empty($data['TCTypeName'])){
                $this->error('请输入系统套餐类型！',U('Taocantype/edit_class_taocantype',array('tctypeid' => $tctypeid)));
            }
            if($model->update_class_taocantype($data)){
                $this->success('套餐类型修改成功',U('Taocantype/list_class_taocantype'));
            }
			else{
                $this->error('修改失败或未做修改',U('Taocantype/edit_class_taocantype',array('tctypeid' => $tctypeid)));
            }
        }
		else{
            $data = $model->query_class_taocantype($tctypeid);
            $this->assign('info',$data);
            $this->display('edit_class_taocantype');
        }
    }
    
    /*系统套餐类型列表*/
    public function list_class_taocantype(){
        $model = D('Home/TaoCanType');
        $data = $model->query_class_taocantype();
        $this->assign('list',$data);
        $this->display();
    }
}