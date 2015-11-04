<?php
/**
 * 登录日志数据模型。
 * @author yangyong <jeason1914@qq.com>
 */
namespace Home\Model;
use Think\Model;
use Think\Page;

class LoginlogModel extends Model{
    //定义表名
    protected $tableName = 'log_view';

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
     * 查询系统用户登录日志数据。
     * @param  string $username   用户名
     * @param  string $startTime  开始日期
     * @param  string $endTime    结束日志
     * @return mixed              返回数据
     */
    public function searchSysLogs($username=null,$startTime=null,$endTime=null){
        if(APP_DEBUG) trace("查询系统用户登录日志数据[[username=>$username][startTime=>$startTime,endTime=>$endTime]...");
        return $this->searchLogs(null,$username,$startTime,$endTime,'0');
    }

    /**
     * 查询机构用户登录日志数据。
     * @param  string $agencyId  机构ID
     * @param  string $username  用户名
     * @param  string $startTime 开始日期
     * @param  string $endTime   结束日期
     * @return mixed             返回数据
     */
    public function searchAgencyLogs($agencyId=null,$username=null,$startTime=null,$endTime=null){
        if(APP_DEBUG) trace("查询机构用户登录日志数据[agencyId=>$agencyId][username=>$username][startTime=>$startTime,endTime=>$endTime]...");
        return $this->searchLogs($agencyId,$username,$startTime,$endTime,'1');
    }

    /**
     * 查询学员用户登录日志数据。
     * @param  string $agencyId  机构ID
     * @param  string $username  用户名
     * @param  string $startTime 开始日期
     * @param  string $endTime   结束日期
     * @return mixed             返回数据
     */
    public function searchStudengLogs($agencyId=null,$username=null,$startTime=null,$endTime=null){
        if(APP_DEBUG) trace("查询学员用户登录日志数据[agencyId=>$agencyId][username=>$username][startTime=>$startTime,endTime=>$endTime]...");
        return $this->searchLogs($agencyId,$username,$startTime,$endTime,'2');
    }

    /**
     * 查询全部登录日志数据。
     * @param  string $agencyId  机构ID
     * @param  string $username  用户名
     * @param  string $startTime 开始日期
     * @param  string $endTime   结束日期
     * @return mixed             返回数据
     */
    public function searchAllLogs($agencyId=null,$username=null,$startTime=null,$endTime=null){
        if(APP_DEBUG) trace("查询全部用户登录日志数据[agencyId=>$agencyId][username=>$username][startTime=>$startTime,endTime=>$endTime]...");
        return $this->searchLogs($agencyId,$username,$startTime,$endTime);
    }

    /**
     * 查询登录日志数据。
     * @param  string $agencyId  机构ID
     * @param  string $username  用户名
     * @param  string $startTime 开始日期
     * @param  string $endTime   结束日期
     * @param  string $type      类型(0:系统用户,1:机构用户,2:学员用户)
     * @return void
     */
    protected function searchLogs($agencyId=null,$username=null,$startTime=null,$endTime=null,$type='0,1,2'){
        if(APP_DEBUG) trace("查询用户[type=$type]登录日志...");
        $_where = array();
        //机构ID
        if(isset($agencyId)){
            $_where['agency_id'] = $agencyId;
        }
        //用户名
        if(isset($username) && !empty($username)){
            //用户名
            $_subWhere['user_name'] = array('like','%'.$username.'%');
            //真实姓名
            $_subWhere['real_name'] = array('like','%'.$username.'%');
            $_subWhere['_logic'] = 'or';
            //复合查询
            $_where['_complex'] = $_subWhere;
        }
        //日期
        if((isset($startTime) || isset($endTime)) && (!empty($startTime) || !empty($endTime))){
            //开始日期
            if(!isset($startTime) || empty($startTime)){
                $startTime = date('Y-m-d',time());
            }
            //结束日期
            if(!isset($endTime) || empty($endTime)){
                $endTime = date('Y-m-d',time());
            }
            $_where["date_format(`login_time`,'%Y-%m-%d')"] = array('between',array($startTime,$endTime));
        }
        //类型
        if(isset($type)){
            $_where['type'] = array('in', $type);
        }
        //查询统计
        $_totalRows = $this->table('hk_log_view')
                           ->where($_where)
                           ->count();
        //查询数据
        $_page = new Page($_totalRows);
        $_data = $this->table('hk_log_view')
                      ->where($_where)
                      ->limit($_page->firstRow.','.$_page->listRows)
                      ->order('login_time desc')
                      ->select();
        //初始化数据模型
        return array(
            'data'  => $_data,
            'page'  => $_page->show()
        );
    }

    /**
     * 删除一个月前的日志数据。
     * @param int $type 类型(0:系统,1:机构,2:学员)
     * @return int 删除的数据量
     */
    public function deleteMonthLogs($type=0,$agencyId=null){
        if(APP_DEBUG) trace("删除一个月前的日志数据...");
        $_end_time = date("Y-m-d",strtotime("last month"));
        if(APP_DEBUG) trace("删除[$_end_time]以前的数据!");
        switch ($type) {
            case 0:{
                //删除系统用户日志
                $_model = $this->table('HK_admin_log')
                               ->where(array('LoginTime' => array('lt', $_end_time)))
                               ->delete();
                if(APP_DEBUG) trace("删除系统日志...$_model");
                return $_model;
            }
            case 1:{
                if(APP_DEBUG) trace("机构ID=>$agencyId");
                if(!isset($agencyId)) return false;
                //删除机构用户日志
                $_model = $this->table('HK_JiGou_Loginlog')
                               ->where(array('logintime' => array('lt', $_end_time)))
                               ->where(array('_string' => "`uid` in (select `UID` from hk_jigou_admin where `JGID` = '$agencyId')"))
                               ->delete(); 
                if(APP_DEBUG) trace("删除机构用户日志...$_model");
                return $_model;
            }
            case 2:{
                if(APP_DEBUG) trace("机构ID=>$agencyId");
                if(!isset($agencyId)) return false;
                //删除机构学员日志
                $_model = $this->table('HK_User_Log')
                               ->where(array('create_time' => array('lt', $_end_time)))
                               ->where(array('_string' => "`uid` in (select `UserID` from HK_User where `JGID` = '$agencyId')"))
                               ->delete();
                if(APP_DEBUG) trace("删除机构学员日志...$_model");
                return $_model;
            }
            default:return false;
        }
    }
}