<?php
namespace Agency\Controller;
use Think\Controller;

class JigouClassController extends Controller{
   /*添加机构班级*/
    public function add_class(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Agency/JigouClass');
            $data = $model->create();
            $data['JGID'] = session('JGID');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            if(($data['SCID'])==='false'){
                 $this->error('系统班级未选择！');
            }
            if(empty($data['CnName'])||empty($data['sale_price'])||empty($data['price'])){
                $this->error('班级名称、销售价格、优惠价格必须填写!');
            }
            if($model->insert_class($data)){
                $this->success('新增机构班级成功!',U('JigouClass/list_class'));
            }else{
                $this->error('新增机构班级失败!');
            }
        }else{  
            $model = D('Agency/ClassSys');
            $data = $model->query_sysclass();
            $this->assign('classlist',$data);
            $this->display();
        }
    }
    
    /**
     * 删除机构班级
     * @param int $jgcid 机构班级ID
     * @return int 影响行数
     */
    public function del_class($jgcid){
        $model = D('Agency/JigouClass');
        if($model->delete_class($jgcid)){
            $this->success('删除成功',U('JigouClass/list_class'));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
    
    /**
     * 修改机构班级
     * @param int $jgcid 机构班级ID
     */
    public function edit_class($jgcid){
        $model = D('Agency/JigouClass');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
           $data = $model->create();
            if(empty($data['CnName'])||empty($data['sale_price'])||empty($data['price'])){
                $this->error('班级名称、销售价格、优惠价格必须填写!');
            }
            $data['JGCID']=$jgcid;
            if($model->update_class($data)){
                $this->success('修改成功',U('JigouClass/list_class'));
            }else{
                $this->error('修改失败或未做修改',U('JigouClass/edit_class',array('jgcid' => $jgcid)));
            }
        }else{
            $data = $model->query_class($jgcid);
            $this->assign('info',$data);
            $this->display('edit_class');
        }
    }
     
    /*机构班级列表*/
    public function list_class(){
        $model = D('Agency/JigouClass');
        $data = $model->query_class();
        $this->assign('classlist',$data);
        $this->display();
    }
    /**
     * 系统班级表关联数据
     * @param int $scid 系统班级ID
     */
    public function query_sys_class($scid){
        $model = D('Agency/ClassSys');
        $data = $model->query_sysclass($scid);
        $this->ajaxReturn($data,'JSON');
    }
}