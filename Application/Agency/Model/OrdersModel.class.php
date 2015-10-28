<?php
namespace Agency\Model;
use Think\Model;

class OrdersModel extends Model{
    /**
     * 添加订单数据
     * @param array $data 所添加的订单信息
     */
    public function insert_order($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询订单表数据
     * @param int $conditions 查询条件数组
     * 因为是关联查询2张表有一样的字段，所以数组里的条件key的名字要带上完整表名 $conditions['hk_orders.jgid']
     */
    public function query_orders($conditions=array()){
            return $this->where($conditions)->field('t2.username,hk_orders.*')
                    ->join('left join hk_user t2 ON t2.userid=hk_orders.uid')->select();
    }
    
    /**
     * 修改订单
     * @param array $data 所修改的订单信息，必须包含主键
     */
    public function update_order($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除订单
     * @param int $orders 要删除的订单id数组
     */
    public function delete_order($orderid){
        return $this->delete($orderid);
    }
}