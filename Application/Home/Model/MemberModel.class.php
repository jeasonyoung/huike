<?php
/**
 * 学员数据模型。
 */
namespace Home\Model;
use Think\Model;
use Think\Page;

class MemberModel extends Model{
    protected $trueTableName='hk_user';
    private $where=array();
    
    /**
     * 根据学员ID加载学员数据。
     * @param  string $useId 学员ID
     * @return mixed         学员数据。
     */
    public function loadMember($useId=null){
        if(APP_DEBUG) trace("根据学员ID[$useId]加载学员数据...");
        if(isset($useId) && !empty($useId)){
            $_model = $this->where("`UserID` = '%s' ", array($useId))
                           ->find();
            return $_model;
        }
        return null;
    }

    /**
     * 判断机构学员用户的唯一性。
     * @param  string $agencyId 机构ID
     * @param  string $username 用户名
     * @return mixed            查询结果
     */
    public function uniqueMemberUser($agencyId=null,$username=null){
        if(APP_DEBUG) trace("判断机构[$agencyId]学员用户[$username]的唯一性...");
        $_model = $this->where("`JGID` = '%s'", array($agencyId))
                       ->where("`UserName` = '%s'", array($username))
                       ->Count();
        return $_model;
    }

    // /**
    //  * 更新学员数据
    //  * @param array $data 学员数据带主键
    //  */
    // public function update_memeber($data=array()){
    //     return $this->save($data);
    // }
    
    /**
     * 删除学员数据
     * @param array $data 学员数据带主键
     */
    public function delete_member($uid){
        return $this->where('userid='.$uid)->delete();
    }

    //学员列表[分页]
    public function show_member($condition="hk_user.username<>''"){
        // if(!empty($this->where)){
        //     $condition=$this->parseCondition(array_filter($this->where));
        // }
        $totalRows = $this->user_total($condition);
        $page = new Page($totalRows);
        $fields = array('userid',
                        'hk_user.username',
                        'hk_user.realname',
                        'mobile',
                        'hk_user.regtime',
                        'hk_user.lock',
                        'hk_user.jgid',
                        'jgadminname',
                        'adminname',
                        'jigou.abbr_cn' => 'agency',
                        'jga.username' => 'agencyuser');
        $data = $this->field($fields)
                     ->limit($page->firstRow.','.$page->listRows)
                     ->join('left join hk_jigou jigou ON jigou.jgid=hk_user.jgid')
                     ->join('left join hk_jigou_admin jga ON jga.username=hk_user.jgadminname')
                     ->where($condition)
                     ->order('userid desc')
                     ->select();
        //echo $this->getLastSql();
        return array(
            'user'  => $data,
            // 'page'  => $page->show()
        );
    }
    
    //初始化查询条件
    private function parseCondition($where,$preFix='hk_user.'){
        $tempArray = array();
        foreach ($where as $key => $value){
            $tempArray[$preFix.$key] = $value;
        }
        return $tempArray;
    }


    //查询学员数据
    public function query_memeber($condition,$mulit=true){
        if($mulit){
           return $this->where($condition)->select(); 
        }else{
            return $this->where($condition)->find();
        }
    }
    
    private function user_total($condition){
        return $this->where($condition)->count();
    }

    // public function _initialize() {
    //     $this->where=I('get.');
    // }

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
}

