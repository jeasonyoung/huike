<?php
namespace Agency\Controller;
use Agency\Controller\AdminController;

class OrdersController extends AdminController{ 
    /*添加订单*/
    public function add_order(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Agency/orders');
            $data = $model->create();
            $data['JGID'] = session('JGID');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            if($model->insert_order($data)){
                $this->success('新增订单成功!',U('orders/list_order'));
            }else{
                $this->error('新增订单失败!');
            }
        }else{  
            $this->display();
        }
    }
    
    
    
    /**
     * 删除订单
     * @param int $orders 订单号数组
     * @return int 影响行数
     */
    public function del_order($orders){
        $model = D('Agency/Orders');
        if($model->delete_order($orders)){
            $this->success('删除成功',U('class/list_order'));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
    
    /**
     * 修改订单
     * @param int $orderid 订单号
     */
    public function edit_order($orderid){
        $model = D('Agency/Orders');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = $model->create();
            if($model->update_orders($data)){
                $this->success('修改成功',U('orders/list_order'));
            }else{
                $this->error('修改失败或未做修改',U('orders/edit_order',array('orderid' => $orderid)));
            }
        }else{
            $conditions = array();
            $conditions['orderid'] = $orderid; 
            $data = $model->query_orders($conditions);
            $this->assign('info',$data);
            $this->display();
        }
    }
     
    /*机构订单列表*/
    public function list_order(){ 
        $model = D('Agency/Orders');
        $conditions = array();
        $conditions['hk_orders.jgid'] = session('JGID'); 
        $data = $model->query_orders($conditions);
        $this->assign('orders',$data);
        $this->display();
    }
}