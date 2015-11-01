<?php
namespace Home\Model;
use Think\Model;

class OrderModel extends Model{
    protected $autoCheckFields =false;
    private $condition;
    private static $db_exam;
    private static $db_subject;
    private static $db_class;
    private static $db_taocan;
    private static $db_taocan_type;
    private static $db_taocan_class;
    private static $db_orders;
    private static $db_order_detail;


    //查询产品不需要的条件
    protected $specialInput = array('ProType','TempExamID','CTYPE');

    /**
     * 返回考试类别数据
     * @param string $examid 考试类别ID
     */
    public function examData($examid){
        return self::$db_exam->where("examid in ($examid)")->select();
    }
    
    /**
     * 返回科目数据
     * @param string $examid 考试类别ID
     */
    public function subjectData($examid){
        return self::$db_subject->where('examid='.$examid)->select();
    }
    
    //获取系统套餐类型
    public function taocanTypeData(){
        return self::$db_taocan_type->field('tctypeid','tctypename')->order('sortid asc')->select();
    }
    
    public function taocanClassData($tctypeid,$examid){
        return self::$db_taocan_class->field('tccid','tctypeid','tccname')->where('tctypeid='.$tctypeid.' and examid='.$examid)->select();
    }
    
    /**
     * 获取产品数据列表
     */
    public function getProductData(){
        $condition = $this->condition;
        if($condition['ProType']==2){
            $type = '单科';
            $where = $this->parseCondition($condition,'hk_jigou_class.');
            $proField = array('jgcid' => 'pid','kemu.subname',"concat(ks.cnname,'--',kemu.subname,'--',hk_jigou_class.cnname)" => 'proname','price','sale_price','useyear');
            $proList = self::$db_class->where($where)->field($proField)->
                    join('left join hk_subjects kemu ON kemu.subid=hk_jigou_class.subid')->
                    join('left join hk_examclass ks on ks.examid=hk_jigou_class.examid')->
                    select();
        }else{
            $type = '套餐';
            $where = $this->parseCondition($condition,'hk_jigou_taocan_list.');
            unset($where['hk_jigou_taocan_list.TcTypeID']); //去掉多余条件
            $proField = array('taocanid' => 'pid',"concat(hk_jigou_taocan_list.useyear,'-',ks.cnname,'-',taocanname,'-',fenlei.tccname)" => 'proname','real_price' => 'price','hk_jigou_taocan_list.sale_price','hk_jigou_taocan_list.useyear');
            $proList = self::$db_taocan->where($where)->field($proField)->
                    join('left join hk_examclass ks on ks.examid=hk_jigou_taocan_list.examid')->
                    join('left join hk_jigou_taocan_class fenlei on fenlei.tccid=hk_jigou_taocan_list.tccid')->
                    select();
        }
        foreach ($proList as &$value){
            if(is_array($value)){$value['type'] = $type;}
        }
        return $proList;
    }
    
    /**
     * 获取一个产品数据
     */
    public function getOneProduct($pid,$ptype){
        if($ptype==2){
            $data = self::$db_class->where('jgcid='.$pid)->
                    field(array('jgcid' => 'pid','kemu.subname',"concat(kemu.subname,'--',cnname)" => 'proname','price','useyear','sale_price','jgid'))->
                    join('left join hk_subjects kemu ON kemu.subid=hk_jigou_class.subid')->
                    find();
            $data['type'] = $ptype;
        }else{
            $data = self::$db_taocan->where('taocanid='.$pid)->
                    field(array('taocanid' => 'pid',"concat(hk_jigou_taocan_list.useyear,'-',ks.cnname,'-',taocanname,'-',fenlei.tccname)" => 'proname','real_price' => 'price','hk_jigou_taocan_list.sale_price','hk_jigou_taocan_list.useyear','hk_jigou_taocan_list.jgid','oldprice','discount','totalprice','cost_price'))->
                    join('left join hk_examclass ks on ks.examid=hk_jigou_taocan_list.examid')->
                    join('left join hk_jigou_taocan_class fenlei on fenlei.tccid=hk_jigou_taocan_list.tccid')
                    ->find();
            $data['type'] = $ptype;
        }
        return $data;
    }

    /**
     * 插入订单数据
     * @param array $order 订单信息
     */
    public function insertOrder($order=array()){
        $this->insertOrderDetail($order['OrderID'],$order['UID'],$order['UserName']);
        return self::$db_orders->add($order);
    }
    
    /*插入订单详情*/
    private function insertOrderDetail($orderId,$uid,$userName){
        //购物车中商品
        $products = session('myCart');
        show_bug($products);
        foreach ($products as $key => $values){
            if(is_array($values)){
                $insert['OrderID'] = $orderId;
                $insert['UID'] = $uid;
                $insert['UserName'] = $userName;
                $insert['ProName'] = $values['proname'];
                $insert['ProType'] = $values['type'];
                $insert['JG_ProID'] = $values['pid'];
                $insert['JGID'] = $values['jgid'];
                $insert['create_time'] = date('Y-m-d H:i:s');
                $insert['OldPrice'] = 0;
                if($values['type']==1){
                    $tccid = self::$db_taocan->where('taocanid='.$values['pid'].' and jgid='.$values['jgid'])->getField('TCCID');
                    $discount = self::$db_taocan_class->where('tccid='.$tccid)->
                            join('left join HK_Class_TaoCanType t2 ON t2.tctypeid=hk_jigou_taocan_class.tctypeid')->getField('t2.discount');
                    $insert['Discount'] = $discount; //系统套餐折扣
                    $insert['JG_Discount'] = self::$db_taocan->where('taocanid='.$values['pid'])->getField('Discount'); //机构套餐折扣
                }else{
                    $insert['Discount'] = 1;
                    $insert['JG_Discount'] = 1;
                }
                $insert['TotalPrice'] = $values['price'];
                //$insert['Cost_Price'] = '';
                $insert['Sale_Price'] = $values['sale_price'];
                //$insert['Real_Price'] = $values['sale_price'];
                $insert['OrderState'] = 0;
                $insert['OrderType'] = 2;
                self::$db_order_detail->add($insert);
            }
        }
    }

    //格式化查询条件
    private function parseCondition($condition=array(),$preFix=''){
        $tempArr = array();
        foreach ($condition as $k => $v){
            if(!in_array($k,$this->specialInput)){
               $tempArr[$preFix.$k] = $v; 
            }
        }
        return $tempArr;
    }

    /*初始化表*/
    public function _initialize() {
        $para = I('get.');
        if($para['CTYPE']==2){
            unset($para['TcTypeID']);
            unset($para['TCCID']);
        }else{
            unset($para['SubID']);
        }
        $this->condition = $para;
        
        self::$db_exam = M('examclass');
        self::$db_subject = M('subjects');
        self::$db_class = M('jigou_class');
        self::$db_taocan_type = M('class_taocantype');
        self::$db_taocan_class = M('jigou_taocan_class');
        self::$db_taocan = M('jigou_taocan_list');
        self::$db_orders = M('orders');
        self::$db_order_detail = M('order_detail');
    }
}
