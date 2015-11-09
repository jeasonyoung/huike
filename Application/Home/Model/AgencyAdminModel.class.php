<?php
/**
 * 机构管理员用户数据模型。
 */
namespace Home\Model;
use Think\Model;

class AgencyAdminModel extends Model{
    //设置默认表名
    protected $tableName = 'JiGou_Admin';
    //验证
    protected $_validate = array(
         array('UserName','require','用户名不能为空'),
         array('UserName','','此用户名已存在',0,'unique',3),

         array('RealName','require','真实姓名不能为空'),

         array('PassWords','require','用户密码不能为空'),
         array('RePassWords','PassWords','确认密码不正确',0,'confirm'),

         array('JGID','require','机构不能为空'),
         array('GroupID','require','管理组不能为空'),
    );

    /**
     * 根据用户ID加载机构用户。
     * @param  string $uid 用户ID
     * @return mixed       机构用户数据
     */
    public function loadAgencyAdmin($uid=null){
        if(APP_DEBUG) trace("根据用户ID[$uid]加载机构用户...");
        if(isset($uid) && !empty($uid)){
            return $this->where("`UID` = '%s'", array($uid))
                        ->find();
        }
        return null;
    }

    /**
     * 加载全部机构数据。
     * @return mixed  机构数据(id,名称)
     */
    public function loadAllAgencies($agencyId=null){
        if(APP_DEBUG) trace('加载全部机构ID和名称数据...');
        $_model = M('Jigou')->field('`JGID` as id,`abbr_cn` as name');
        if(isset($agencyId) && !empty($agencyId)){
            $_model = $_model->where("`JGID` = '%s'", array($agencyId));
        }
        $_model = $_model->order('`JGID` desc')->select();
        return $_model;
    }

    /**
     * 加载机构管理组数据。
     * @param int $status  状态(0,1)
     * @return mixed 机构管理组(id,名称)
     */
    public function loadAgencyGroups($status=null){
        if(APP_DEBUG) trace('加载机构管理组ID和名称数据...');
        $_model = M('JigouGroup')->field('`id`,`title` as name');
        if(isset($status) && !empty($status)){
            $_model = $_model->where("`status` = '%s'", array($status));
        }  
        $_model = $_model->order('`id` asc')->select();
        return $_model;
    }
}
