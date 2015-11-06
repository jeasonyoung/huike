<?php
/**
 * 机构管理数据模型。
 */
namespace Home\Model;
use Think\Model;

class AgencyModel extends Model{
    //设置表名
    protected $trueTableName = 'HK_JiGou';
    //验证
    protected $_validate = array(
        array('Company','require','公司名称不能为空'),
        array('Company','','此公司已存在',0,'unique',3),

        array('abbr_cn','require','对外名称不能为空',1),
        array('abbr_cn','','对外名称已经存在!',0,'unique',1),

        array('abbr_en','require','英文简称不能为空',1),
        array('abbr_en','','英文简称已经存在!',0,'unique',1),

        array('login_icon','require','登陆logo不能为空',1),
        array('logo_icon','require','登陆后logo不能为空',1),

        array('video_icon','require','视频logo不能为空',1),
        array('video_icon','require','视频logo不能为空',1),

        array('domain','require','机构合作域名不能为空',1),
        array('domain','','机构域名已经存在!',0,'unique',1),

        array('Province','require','请选择所在省',1),
        array('City','require','请选择所在市',1),
        array('County','require','请选择所在区县',1),
        array('Address','require','详细地址不能为空',1),
        array('Contact','require','联系人不能为空',1),

        array('HZTel','tel_validate','合作电话不正确',0,'function'),
        array('StuTel','tel_validate','学员联系电话不正确',0,'function'),

        array('Introduce','require','机构简介不能为空',1),

        array('Md5Key','require','机构秘钥不能为空',1),
        array('APPKey','require','手机端秘钥不能为空',1),
    );

    /**
     * 加载机构数据。
     * @param  string $agencyId 机构ID
     * @return mixed            返回数据
     */
    public function loadAgency($agencyId=null){
        if(APP_DEBUG) trace("加载机构[$agencyId]数据...");
        if(!isset($agencyId)) return null;
        return $this->where("`JGID` = '%s'", $agencyId)
                    ->find();
    }

    /**
     * 添加合作机构
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function insert_agency($data){
        return $this->add($data);
    }
    
    public function query_agency($field,$condition,$mulit){
        if($mulit){return $this->where($condition)->field($field)->select();}
        return $this->where($condition)->field($field)->find(); 
    }
    
    /**
     * 更新机构
     * @param array $data 机构数据
     */
    public function update_agency($data=array()){
        return $this->field('Money',true)->save($data);
    }
    
    //删除机构
    public function delete_agency($jgid){
        return $this->where('jgid='.$jgid)->delete();
    }

    /**
     * 机构列表数据[后期需加入分页]
     */
    public function get_agencyList($field,$condition="domain <> ''",$mulit=true){
        $where[]['_string']=$condition;
        if($mulit){
            return $this->where($where)
                        ->field($field)
                        ->join('Left JOIN hk_admin a ON a.adminid=hk_jigou.kefuid')
                        ->join('Left JOIN hk_admin b ON b.adminid=hk_jigou.marketid')
                        ->order('jgid desc')
                        ->select();
        }else{
            return $this->where($where)
                        ->field($field)
                        ->find();  
        }
    }

    // /**
    //  * 添加机构用户
    //  * @param array $user 机构用户数据
    //  */
    // public function insert_user($user=array()){
    //     $db = M('jigou_admin');
    //     return $db->add($user);
    // }
    
    /**
     * 查询机构管理员信息
     * @param string $condition 查询条件
     * @param $mulit 返回结果集
     */
    public function query_user($condition="username<>''",$mulit=true){
        $db = M('jigou_admin');
        $where[]['_string']=$condition;
        if($mulit){
            return $db->field('hk_jigou_admin.*,jg.company,jg.abbr_cn,jgg.title')
                      ->where($where)
                      ->join('left join hk_jigou jg ON jg.jgid=hk_jigou_admin.jgid')
                      ->join('left join HK_JiGou_group jgg ON jgg.id=hk_jigou_admin.groupid')
                      ->order('hk_jigou_admin.uid desc')
                      ->select();
        }else{
            return $db->where($where)->field('hk_jigou_admin.*,jg.company,jg.abbr_cn')->
                    join('left join hk_jigou jg ON jg.jgid=hk_jigou_admin.jgid')->
                    find(); 
        }
    }
    
    // /**
    //  * 更新机构管理员信息
    //  * @param array $data 机构管理员信息 需包含主键
    //  */
    // public function update_user($data=array()){
    //     $db = M('jigou_admin');
    //     return $db->save($data);
    // }
    
    // /**
    //  * 删除机构管理员
    //  * @param int $uid 管理员ID
    //  */
    // public function delete_user($uid){
    //     $db = M('jigou_admin');
    //     //同事将此管理员在机构表中默认JG_UID清除
    //     $this->where('jg_uid='.$uid)->setField('JG_UID',null);
    //     return $db->where('uid='.$uid)->delete();
    // }

    /**
     * 根据管理组ID加载管理员。
     * @param  array $groups 管理组ID数组
     * @return mixed         返回数据
     */
    public function loadAdminUsers($groups=null){
        if(APP_DEBUG) trace('根据管理组ID['.serialize($groups).']加载管理员用户...');
        if(!isset($groups)) return null;
        $_model = M('Admin');
        if(is_array($groups)){
            $_model = $_model->where(array('GroupID' => array('in', $groups)));
        }else{
            $_model = $_model->where("`GroupID` = '%s'", $groups);
        }
        $_model = $_model->field('AdminID,UserName,RealName')
                         ->select();
        return $_model;
    }

    /**
     * 加载全部的考试数据。
     * @return mixed 考试数据。
     */
    public function loadAllExams(){
        if(APP_DEBUG) trace('加载全部的考试数据...');
        $_model = M('Examclass');
        return $_model->field('`ExamID` as id, `CnName` as name')
                      ->order('SortID')
                      ->select();
    }
}