<?php
/**
 * 机构充值数据模型。
 */
namespace Home\Model;
use Think\Model;
use Think\Page;

class RechargeModel extends Model{
    //定义表名
    protected $tableName = 'JiGou_Accout';
    //验证
    protected $_validate = array(
        array('JGID','require','所属机构不能为空'),

        array('Money','require','交易金额不能为空'),
        array('Money','/^(\d*\.)?\d+$/','交易金额必须为数字',0,'regex'),
        array('Money','check_recharge_money','交易金额必须大于0',1,'function'),

        array('Channel','require','资金渠道不能为空'),
        array('MoneyFlow','require','资金流向不能为空'),
        array('TradeType','require','交易类型不能为空'),
        array('StateTF','require','状态不能为空'),
    );
    
    /**
     * 加载机构数据。
     * @return array 机构数据。
     */
    public function loadAgencies(){
        if(APP_DEBUG) trace('加载机构数据...');
        $_model = $this->field('`JGID` id,`abbr_cn` name')
                       ->table('HK_JiGou')
                       ->order('`abbr_cn` asc')
                       ->select();
        return $_model;
    }

    /**
     * 加载机构流水数据。
     * @param  string $agencyId  机构ID
     * @param  string $typeId    交易类型ID
     * @param  string $flowId    资金流向ID
     * @param  string $channelId 资金渠道ID
     * @param  string $statusId  状态ID
     * @return void
     */
    public function loadRecharges($agencyId,$typeId,$flowId,$channelId,$statusId){
        if(APP_DEBUG) trace("加载机构流水数据[agency=>$agencyId][type=>$typeId][flow=>$flowId][channel=>$channelId][status=>$statusId]...");
        $_where = array();
        //机构ID
        if(isset($agencyId) && !empty($agencyId)){
            $_where['agency_id'] = $agencyId;
        }
        //交易类型ID
        if(isset($typeId) && !empty($typeId)){
            $_where['type_id'] = $typeId;
        }
        //资金流向ID
        if(isset($flowId) && !empty($flowId)){
            $_where['flow_id'] = $flowId;
        }
        //资金渠道ID
        if(isset($channelId) && !empty($channelId)){
            $_where['channel_id'] = $channelId;
        }
        //状态ID
        if(isset($statusId) && !empty($statusId)){
            $_where['status_id'] = $statusId;
        }
        //查询统计
        $_totalRows = $this->table('hk_agency_fund_water_view')
                           ->where($_where)
                           ->count();
        //查询数据
        $_page = new Page($_totalRows);
        $_data = $this->table('hk_agency_fund_water_view')
                      ->where($_where)
                      ->limit($_page->firstRow.','.$_page->listRows)
                      ->order('last_time desc')
                      ->select();
        //初始化数据模型
        return array(
            'data'  => $_data,
            'page'  => $_page->show()
        );
    }

    /**
     * 新增机构充值。
     * @param  array $data 数据。
     * @return mixed       新增充值结果。
     */
    public function insertRecharge($data){
        if(APP_DEBUG) trace('新增机构充值...');
        //初始化数据模型
        $_model = M('Jigou');
        if(isset($data['JGID']) && !empty($data['JGID'])){
            $_total = $_model->field('ifnull(Money,0) as balance')
                             ->where("`JGID` = '%s'", array($data['JGID']))
                             ->find();
            //echo dump($_total);
            if(APP_DEBUG) trace('获取机构['.$data['JGID'].']余额['.$_total['balance'].']...');
            if($_total){
               $data['AccMoney'] = floatval($_total['balance']) + floatval($data['Money']);
               if(APP_DEBUG) trace('充值后余额:'.$data['AccMoney']);
            }
        }
        $_result = $this->add($data);
        if($_result){
            //更新机构余额
            $_model->where("`JGID` = '%s'", array($data['JGID']))
                   ->save(array('Money' => $data['AccMoney']));
        }
        return $_result;
    }
}