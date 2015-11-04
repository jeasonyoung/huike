<?php
/**
 * 学员用户登录日志控制器。
 * @author yangyong <jeason1914@qq.com>
 */
namespace Home\Controller;
use Home\Controller\BaseController;

class StudentloginlogController extends BaseController{

    /**
     * 默认入口。
     * @return void
     */
    public function index(){
        if(APP_DEBUG) trace('执行index...');
        //初始化数据模型
        $_model = D('Loginlog');
        //设置机构
        $this->assign('agencies',$_model->loadAgencies());
        //显示
        $this->display('select_agency');
    }

    /**
     * 查询数据。
     * @return void
     */
    public function search(){
        if(APP_DEBUG) trace('执行search...');
        //获取机构ID
        $_agencyId = I('agencyId','');
        //获取用户名
        $_username = I('user_name','');
        //初始化数据模型
        $_model = D('Loginlog');
        //查询数据
        $_result = $_model->searchStudengLogs($_agencyId,$_username);
        //设置查询用户名
        $this->assign('username',$_username);
        //设置机构ID
        $this->assign('agency_id',$_agencyId);
        //设置数据
        $this->assign($_result);
        //显示
        $this->display('list');
    }

    /**
     * 删除日志
     * @return void
     */
    public function del(){
        if(APP_DEBUG) trace('执行del...');
        if(I('session.groupid',0) != 1){
            $this->error('无权限删除!');
        }
        //初始化数据模型
        $_model = D('Loginlog');
        //删除一个月前日志
        if($_model->deleteMonthLogs(2)){
            $this->success("删除学员用户登录日志成功(共删除:$_model条数据)!",U('Home/Studentloginlog/index'));
        }else{
            $this->error('删除学员用户登录日志失败,请联系技术人员!');
        }
    }

}