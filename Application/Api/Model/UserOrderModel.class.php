<?php
/**
 * 用户订单处理封装.
 * 
 */
namespace Api\Model;
use Think\Model;

class UserOrderModel extends Model{
    protected $autoCheckFields = false;

    /**
     * 创建订单并用户支付。
     * @param  string  $agencyId  机构ID
     * @param  string  $userId    用户ID
     * @param  string  $productId 产品ID
     * @param  integer $type      产品类型
     * @return string             订单号
     */
    public function createPayOrder($agencyId='',$userId='',$productId='',$type=1){
        if(APP_DEBUG)trace("创建订单[$agencyId,$userId,$type,$productId]...");
        //加载用户数据
        $user = M('User')->where(array('UserID'=>$userId))->find();
        //加载机构折扣
        $discount = M('jigou')->where(array('JGID',$agencyId))->getField('Dicount');
        //加载产品
        $product = $this->loadProduct($agencyId,$type,$productId,$discount);
        if(empty($product)) return false;
        //生成订单号
        $order_no = create_order($userId);
        //创建订单
        $db = M('Orders');
        $data['OrderID'] = $order_no;
        $data['UID'] = $userId;
        $data['UserName'] = $user['UserName'];
        $data['Mobile'] = $user['Mobile'];
        $data['JGID'] = $agencyId;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['OldPrice'] = $product['oldprice'];
        $data['CostPrice'] = $product['cost_price'] * $discount;
        $data['TotalPrice'] = $product['totalprice'];
        $data['SalesPrice'] = $product['goodprice'];
        $data['OrderType'] = 2;//1:机构付费,2:学员付费
        $data['OrderState'] = 1;//0:未付费订单,1:已付费,2:已开通,-1:已关闭,-2:已删除
		//$data['OpenTime'] = date('Y-m-d H:i:s');
        $data['PayState'] = 1;//0:未支付,1:已支付,2:退款申请,3:部分退款,4:全部退款
		$data['PayTime'] = date('Y-m-d H:i:s');
        $data['Operator'] = 'ios';
		//用户余额
		if($user['Money'] < $product['goodprice']) return 0;
		//创建订单
        if($db->add($data)){
            //创建订单明细
            if($this->createOrderDetail($agencyId,$user,$order_no,$type,$product,$discount)){
                //创建支付流水
                $accId = M('UserAccount')->add(array(
                        'JGID' => $agencyId,
                        'UID'  => $userId,
						'OrderID' => $order_no,
                        'Channel' => -2,//余额扣款
                        'MoneyFlow' => 1,//0:收入,1:支出
                        'TradeType' => 1,//0:充值,1:付款
                        'StateTF' => 1,//0:无效,1:有效
                        'Money' => $product['goodprice'],
                        'Note' => 'ios付款',
                        'AddTime' => date('Y-m-d h:i:s'),
                    ));
                if($accId){
                    //更新用户余额(支付订单)
                    M('user')->where(array(
                        'JGID'=>$agencyId,
                        'UserID'=>$userId
                    ))->setDec('Money',$product['goodprice']);
					//返回订单号
                    return $order_no;
                }
            }
        }
        return 0;
    }

    /**
     * 创建订单明细
     * @param  string $agencyId 机构ID
     * @param  array  $user     用户数据
     * @param  string $orderId  订单ID
     * @param  integer $type    类型
     * @param  array $product   产品数据
     * @param  float $discount  机构折扣
     * @return string           明细ID
     */
    private function createOrderDetail($agencyId,$user,$orderId,$type,$product,$discount){
        if(APP_DEBUG)trace("创建订单明细...");
        $db = M('OrderDetail');
        $detail['OrderID'] = $orderId;
        $detail['UID'] = $user['UserID'];
        $detail['UserName'] = $user['UserName'];
        $detail['ProName'] = $product['name'];
        $detail['ProType'] = $type;//1:套餐,2:单科
        $detail['JG_ProID'] = $product['id'];
        $detail['JGID'] = $agencyId;
        $detail['create_time'] = date('Y-m-d H:i:s');
        $detail['OldPrice'] = $product['oldprice'];
        $detail['PicPath'] = $product['picpath'];
        $detail['ExamID'] = $product['examid'];
        if(isset($product['all_classIds'])){
            $detail['AllJGCID'] = $product['all_classIds'];
        }
        if($type == 1){
            //获取套餐类型ID
            $tccid = M('JigouTaocanList')->where(array(
                    'TaocanId' => $product['id'],
                    'JGID' => $agencyId,
                ))->getField('TCCID');
            //套餐类型折扣
            $detail['Discount'] = M('JigouTaocanClass')->where(array('TCCID'=>$tccid))
                ->join('left join HK_Class_TaoCanType t2 ON t2.tctypeid=hk_jigou_taocan_class.tctypeid')
                ->getField('t2.discount');
            $detail['JG_Discount'] = $discount;
            $detail['TotalPrice'] = $product['totalprice'];
            $detail['Cost_Price'] = $product['cost_price'] * $discount;
        }else{
            $detail['Discount'] = $discount;
            $detail['JG_Discount'] = $discount;
            $detail['TotalPrice'] = $product['oldprice'] * $discount;
            $detail['Cost_Price'] = $product['oldprice'] * $discount;
        }
        $detail['Sale_Price'] = $product['price'];
        $detail['Real_Price'] = $product['goodprice'];
        $detail['OrderState'] = 0;//0:未开通,1:已开通,
        $detail['OrderType'] = 2;//1:机构付费,2:学员付费
        return $db->add($detail);
    }
	//查询有效期
	public function loadValidity($type,$productId){
		if($type==1){
			return M('JigouTaocanList')->where(array('TaoCanID'=>$productId))
									   ->getField('validity');
		}else{
			return M('JigouClass')->where(array('JGCID'=>$productId))
									->join('left join hk_class_sys t2 ON t2.scid = hk_jigou_class.scid')
									->getField('validity');
		}
	}
    /**
     * 加载产品数据。
     * @param  string $agencyId  机构ID
     * @param  string $type      产品类型(1:套餐,2:单科)
     * @param  string $productId 产品ID
     * @param  integer $discount 折扣
     * @return array             产品数据
     */
    private function loadProduct($agencyId,$type,$productId,$discount=1.0){
        if(APP_DEBUG)trace("加载机构[$agencyId]产品[$type,$productId]数据...");
        if($type == 1){//套餐
            return $this->loadTaocan($agencyId,$productId);
        }else{//单科
            return $this->loadClass($agencyId,$productId,$discount);
        }
    }

    /**
     * 加载套餐数据。
     * @param  string $agencyId  机构ID
     * @param  string $productId 产品ID
     * @return array             产品数据
     */
    private function loadTaocan($agencyId,$productId){
        if(APP_DEBUG)trace("加载套餐[$agencyId,$productId]数据...");
        $data = M('JigouTaocanList')->field(array(
                'TaoCanID' => 'id',
                'TaoCanName' => 'name',
                'Sale_Price' => 'price',
                'Real_Price' => 'goodprice',
                'totalprice','oldprice','cost_price',
                'picpath','examid',
            ))->where(array('JGID'=>$agencyId,'TaoCanID'=>$productId))
              ->find();
        if(!empty($data)){
            $classIds = $this->loadTaocanClasses($data['id']);
            $data['all_classIds'] = implode($classIds, ',');
        }
        return $data;
    }

    /**
     * 加载套餐下班级数据
     * @param  string $taocanId 套餐ID
     * @return array           班级数组
     */
    private function loadTaocanClasses($taocanId){
        if(APP_DEBUG)trace("加载套餐[$taocanId]下班级数据集合...");
        $data = M('JigouTaocanInfo')->where(array(
                'TaoCanID' => $taocanId
            ))->getField('JGCID',true);
        return $data;
    }

    /**
     * 加载单科数据。
     * @param  string $agencyId  机构ID
     * @param  string $productId 产品ID
     * @param  float  $discount  折扣
     * @return array             产品数据
     */
    private function loadClass($agencyId,$productId,$discount=1.0){
        if(APP_DEBUG)trace("加载单科[$agencyId,$productId,$discount]数据...");
        $data = M('JigouClass')->field(array(
                'JGCID' => 'id',
                'CnName' => 'name',
                'hk_jigou_class.sale_price' => 'price',
                'hk_jigou_class.price' => 'goodprice',
                'sys_class.Price' => 'oldprice',
                'sys_class.sale_price' => 'cost_price',
                'hk_jigou_class.picpath' => 'picpath',
				'hk_jigou_class.examid' => 'examid',
            ))->where(array('JGID'=>$agencyId,'JGCID'=>$productId))
              ->join('left join hk_class_sys sys_class ON sys_class.scid=hk_jigou_class.scid')
              ->find();
        if(!empty($data)){
            $data['totalprice'] = $data['oldprice'] * $discount;
        }
        return $data;
    }
}