<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class OrderController extends BaseController{
    private static $model;
    private $agency;
    //生成订单
    public function generateOrder(){
        $this->assign('agency',  $this->agency);
        //系统套餐类型
        $this->assign('taocantype',self::$model->taocanTypeData());
        $examid = I('ExamID');
        $tempid = I('TempExamID');
        $tctid = I('TcTypeID');
        if(!empty($examid)){
            /*if(!empty($tctid)){
                echo $tctid;
                show_bug($this->loadTcClass($tctid,$examid,FALSE));
                $this->assign('tc_class',$this->loadTcClass($tctid,$examid,FALSE));
            }*/
			
            $this->assign('examlist',$this->loadExam($tempid,FALSE));
            $this->assign('sublist',$this->loadSubject($examid,FALSE));
            //产品列表
            $this->assign('prolist',self::$model->getProductData());
        }
        $this->display(); 
    }
    
    /**
     * 将产品加入订单
     * @param int $pid 产品ID
     * @param int $ptype 产品类型 1 套餐 2 单科
     */
    public function addToCart($pid,$ptype){
        if(!session('?myCart')){
            $temp = array();
        }else{
            $temp = session('myCart');
        }
        $data = self::$model->getOneProduct($pid,$ptype);
        if($data){
            if(!$this->proSearch(session('myCart'),$pid,$ptype)){
               array_push($temp, $data); 
               session('myCart',$temp);
            }else{
                exit('extist');
            }
            //return $this->ajaxReturn(session('myCart'));
            exit('success');
        }
    }
    
    /**
     * 从购物车中删除产品
     * @param int $pid 产品ID
     * @param int $ptype 产品类型 1 套餐 2 单科
     */
    public function removeFromCart($pid,$ptype){
        $p_num = count(session('myCart'));
        $data = self::$model->getOneProduct($pid,$ptype);
        $myCart = session('myCart');
        $key = array_search($data,$myCart);
        unset($myCart[$key]); //删除产品
        $d_num = count($myCart);
        if($d_num < $p_num){
            session('myCart',$myCart);
            return $this->ajaxReturn(array('status' => 'success','type' => $ptype,'msg' => '成功删除一个产品')); 
        }else{
            return $this->ajaxReturn(array('status' => 'error','tye' => $ptype,'msg' => '删除产品失败'));
        }
    }
    
    //清空所选产品
    public function emptyCart(){
        session('myCart',null);
        exit('所选产品已清空');
    }
    
    //查看所选产品
    public function viewCart($count=FALSE){
        if(!$count){
           $temp=array_column(session('myCart'),'type'); 
           $result = array_count_values($temp);
           $proType = array(
               'dk' => empty($result[2])?0:$result[2],
               'tc' => empty($result[1])?0:$result[1]
           );
           return $this->ajaxReturn($proType);
        }
        return session('myCart');
    }
    
    //产品价格(优惠价)
    private function productTotal(){
        $price = array_column(session('myCart'),'price');
        $sale_price = array_column(session('myCart'), 'sale_price');
        return array(
            'price'      => array_sum($price),
            'sale_price' => array_sum($sale_price)
        );
    }

    //产生订单
    public function makeOrder(){
        $submit = I('submit');
        if(IS_POST){
            $regist = array();
            $userName = array_filter(I('UserName'));
            $passWord = array_filter(I('PassWords'));
            //订单总价和优惠价
            $price = I('price');
            $sale_price = I('sale_price');
            
            if(count($userName) != count($passWord)){
                $this->error('请确认用户名、密码的对称性');
            }
            $jgid = array_unique(array_column(session('myCart'),'jgid'));
            foreach ($userName as $key => $value){
                $userInfo['UserName'] = $value;
                $userInfo['Psw'] = $passWord[$key];
                $userInfo['PassWords'] = md5($passWord[$key]);
                $userInfo['JGID'] = $jgid[0];
                $userInfo['RegTime'] = date('Y-m-d H:i:s',time());
                $regist[]=$userInfo;
            }
            $result = $this->create_user($regist,$price,$sale_price);
            session('myCart',null);
            if($result){
                $this->success('产生订单'.$result['success'].'个',U('order/generateOrder'));
            }
        }else{
            //结算价格
            $this->assign('total',$this->productTotal());
            $this->assign('prolist',session('myCart'));
            $this->display();
        }
    }
    
    /**
     * 生成订单时批量注册用户
     * @param array $regist 用户信息
     * @param int $price 订单总价
     * @param int $sale_price 订单总价
     */
    private function create_user($regist=array(),$price,$sale_price){
        $member = A('Member');
        $success = 0;
        $fail = 0;
        $order = array();
        foreach ($regist as $value){
            $uid = $member->simple_regist($value);
            if($uid){
                //生成用户订单
                $order['OrderID'] = $this->create_sn($uid);
                $order['UID'] = $uid;
                $order['UserName'] = $value['UserName'];
                $order['JGID'] = $value['JGID'];
                $order['create_time'] = date('Y-m-d H:i:s');

                $order['OldPrice'] = $price;   //原始价格 需改 此处价格将更新
                $order['CostPrice'] = $price;  //成本价格 需改 此处价格将更新
                
                $order['TotalPrice'] = $price;        //此处价格将更新
                $order['SalesPrice'] = $sale_price;   //此处价格将更新
                
                $order['OrderType'] = 2;
                $order['OrderState'] = 0;
                $order['PayState'] = 0;
                $order['AdminName'] = session('username');
                //此处做订单表插入操作
                if(!self::$model->insertOrder($order)){
                    $member->simple_del_user($uid); //订单创建失败 删除此用户
                }else{
                    $success+=1;
                }
            }else{
                $fail+=1;
            }
        }
        return array(
            'total'     => $success+$fail,
            'success'   => $success,
            'fail'      => $fail
        );
    }
    
    //订单列表
    public function list_orders(){
        $condition = array();
        $submit = I('Submit');
        if($submit==1){
            $filter['hk_orders.UserName'] = I('UserName');
            $filter['OrderID'] = I('OrderID');
            if(!empty(I('RealName'))){
                $db_user = M('user');
                $filter['UID'] = $db_user->where("realname='".I('RealName')."'")->getField('UserID'); 
            }
            $filter['hk_orders.Mobile'] = I('Mobile');
            $filter['hk_orders.JGID'] = I('JGID');
            $filter['OrderState'] = I('OrderState');
            $condition = array_filter($filter);     //去除空值
            if(I('OrderState')==0){                 //array_filter去除空值将去除0也就是false 此操作将为0情况补充
                $condition['OrderState'] = 0;
            }
        }
        $data = self::$model->query_orders($condition);
        $this->assign('orders',$data);
        $this->assign('agencyList',$this->agency);
        $this->display();
    }
    
    //查看订单详情
    public function view_order($orderid){
        $order_data = self::$model->order_detail($orderid);
        $proList = self::$model->order_prolist($orderid);
        $this->assign('order',$order_data);
        $this->assign('prolist',$proList);
        $cost = array_sum(array_column($proList,'cost_price'));
        $sale = array_sum(array_column($proList,'real_price'));
        $this->assign('cost',$cost);
        $this->assign('sale',$sale);
        $this->display();
    }

    /**
     * 生成订单号
     * @param mix $fix 防止重复值
     */
    private function create_sn($fix=''){
        mt_srand((double)microtime() * 1000000 );
        return 'C'.date("YmdHis").str_pad(mt_rand(1,99999),5,"0",STR_PAD_LEFT).$fix;
    }

    /**
     * 产品查找
     */
    private function proSearch($porList,$pid,$ptype){
        //if(empty($porList)){return true;}
        foreach ($porList as $value){
            if(is_array($value)){
                foreach ($value as $k => $v){
                    if($k=='pid' && $v==$pid){$dk=true;}
                    if($k=='type' && $v==$ptype){$tc=true;}
                }
            }
        }
        return $dk && $tc;
    }


    public function loadExam($examid,$ajax=true){
        if($ajax){
            return $this->ajaxReturn(self::$model->examData($examid));
        }else{
            return self::$model->examData($examid);
        }
    }
    
    public function loadSubject($examid,$ajax=true){
        if($ajax){
            return $this->ajaxReturn(self::$model->subjectData($examid)); 
        }else{
            return self::$model->subjectData($examid);
        }
        
    }
    
    public function loadTcClass($tcid,$examid,$ajax=true){
        if($ajax){
            return $this->ajaxReturn(self::$model->taocanClassData($tcid,$examid));
        }else{
            self::$model->taocanClassData($tcid,$examid);
        }
    }
    
    public function _initialize(){
        self::$model=D('Home/Order');
        $agency = A('Agency');
        $this->agency = $agency->getAgencyList('statetf=1',array('`abbr_cn`','`jgid`','`allexams`'));
    }
}

