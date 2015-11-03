<?php
/**
 * 机构充值控制器。
 *
 * @author yangyong <jeason1914@qq.com>
 */
namespace Home\Controller;
use Think\Controller;

class RechargeController extends Controller{

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
        //初始化数据模型
        $_model = D('Recharge');
        //设置机构
        $this->assign('agencies',$_model->loadAgencies());

        //查询数据
        $_result = $_model->loadRecharges(I('agency_id',''),
                                          I('type_id',''),
                                          I('flow_id',''),
                                          I('channel_id',''),
                                          I('status_id',''));
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