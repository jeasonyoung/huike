<?php
/**
 * 机构充值控制器。
 *
 * @author yangyong <jeason1914@qq.com>
 */
namespace Home\Controller;
use Home\Controller\BaseController;

class RechargeController extends BaseController{

    /**
     * 默认入口。
     * @return void
     */
    public function index(){
        if(APP_DEBUG) trace('执行index...');
        $this->search();
    }

    /**
     * 列表数据。
     * @return void
     */
    public function listAction(){
        if(APP_DEBUG) trace('执行listAction...');
        $this->search();
    }

    /**
     * 查询数据。
     * @return void
     */
    public function search(){
        if(APP_DEBUG) trace('执行search...');
        //获取机构ID
        $_agencyId = I('agency_id','');
        //获取类型ID
        $_typeId = I('type_id','');
        //获取资金流向ID
        $_flowId = I('flow_id','');
        //获取渠道ID
        $_channelId = I('channel_id','');
        //获取状态ID
        $_statusId = I('status_id','');

        //初始化数据模型
        $_model = D('Recharge');
        //设置机构
        $this->assign('agencies',$_model->loadAgencies());
        $this->assign('agencyId',$_agencyId);
        $this->assign('typeId',$_typeId);
        $this->assign('flowId',$_flowId);
        $this->assign('channelId',$_channelId);
        $this->assign('statusId',$_statusId);
        //查询数据
        $_result = $_model->loadRecharges($_agencyId,$_typeId,$_flowId,$_channelId,$_statusId);
        //设置数据
        $this->assign($_result);
        //显示
        $this->display('list');
    }

    /**
     * 选择机构。
     * @return void.
     */
    public function select_agency(){
        if(APP_DEBUG) trace('选择机构select_agency...');
        //初始化数据模型
        $_model = D('Recharge');
        //设置机构
        $this->assign('agencies',$_model->loadAgencies());
        //显示
        $this->display();
    }

    /**
     * 添加充值。
     * @return void.
     */
    public function add(){
        if(APP_DEBUG) trace('添加充值add...');
        //初始化数据模型
        $_model = D('Recharge');
        if(IS_POST){
            if(APP_DEBUG) trace('提交新增数据...');
            //创建数据
            $_data = $_model->create();
            if(!$_data){
                if(APP_DEBUG) trace('验证数据错误!');
                $this->error($_model->getError());
            }else{
                $_data['SysUserName'] = I('session.username','');
                $_data['create_time'] = $_data['last_time'] = date('Y-m-d H:i:s',time());
                //
                if($_model->insertRecharge($_data)){
                    $this->success('机构充值成功!',U('Recharge/search'));
                }else{
                    $this->error('机构充值失败!');
                }
            }
        }else{
            //设置机构
            $this->assign('agencies',$_model->loadAgencies());
            //设置机构ID
            $this->assign('agency_id', I('agencyId',''));
            //显示
            $this->display();
        }
    }

}