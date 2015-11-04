<?php
namespace Home\Model;
use Think\Model;

class MemberModel extends Model{
    protected $trueTableName='hk_user';
    private $where=array();
    /**
     * 添加学员数据
     * @param array $data 学员数据
     */
    public function insert_member($data=array()){
        return $this->add($data);
    }
    
    /**
     * 更新学员数据
     * @param array $data 学员数据带主键
     */
    public function update_memeber($data=array()){
        return $this->save($data);
    }
    
    /**
     * 删除学员数据
     * @param array $data 学员数据带主键
     */
    public function delete_member($uid){
        return $this->where('userid='.$uid)->delete();
    }

    //学员列表[分页]
    public function show_member($condition="hk_user.username<>''"){
        if(!empty($this->where)){
            $condition=$this->parseCondition(array_filter($this->where));
        }
        $totalRows = $this->user_total($condition);
        $page = new \Think\Page($totalRows,20);
        $fields = array('userid','hk_user.username','hk_user.realname','mobile','hk_user.regtime','hk_user.lock','hk_user.jgid','jgadminname','adminname','jigou.abbr_cn' => 'agency','jga.username' => 'agencyuser');
        $data = $this->field($fields)->limit($page->firstRow.','.$page->listRows)->
                join('left join hk_jigou jigou ON jigou.jgid=hk_user.jgid')->
                join('left join hk_jigou_admin jga ON jga.username=hk_user.jgadminname')->
                where($condition)->order('regtime desc')->select();
        //echo $this->getLastSql();
        return array(
            'user'  => $data,
            'page'  => $page->show()
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

    public function _initialize() {
        $this->where=I('get.');
    }
}

