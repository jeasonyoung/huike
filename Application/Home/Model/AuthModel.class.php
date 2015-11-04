<?php
namespace Home\Model;
use Think\Model;

class AuthModel extends Model{
    //权限模型涉及表有多个,关闭表检测
    protected $autoCheckFields =false;
    /**
     * 添加权限数据
     * @param array $data 权限数据
     */
    public function insert_rule($data=array()){
        $db = M('admin_rule');
        return $db->add($data);
    }
    
    /**
     * 删除权限
     * @param int $rid 权限id
     */
    public function del_rule($rid){
        $db = M('admin_rule');
        return $db->where('id='.$rid)->delete();
    }
    
    /**
     * 修改权限
     * @param array $data 待修改权限数据，需包含主键
     */
    public function update_rule($data){
        $db = M('admin_rule');
        $count = $db->where("name='".$data['name']."'")->count();
        if($count > 1){
            return array(
                'error'  => '规则标识已存在,不能修改!'
            );
        }
        return $db->save($data);
    }
    
    /**
     * 查询权限
     * @param string $condition 查询条件
     * @param int $multi 返回结果集 true 二维数组  false 一位数组
     */
    public function query_rule($condition='',$multi=TRUE){
        $db = M('admin_rule');
        if($multi){
            if(!empty($condition)){return $db->where($condition)->field('t2.title as modname,hk_admin_rule.*')->join('left join hk_admin_module t2 ON t2.id=hk_admin_rule.mid')->select();}
            return $db->field('t2.title as modname,hk_admin_rule.*')->join('left join hk_admin_module t2 ON t2.id=hk_admin_rule.mid')->select();
        }else{
            if(!empty($condition)){return $db->where($condition)->field('t2.title as modname,hk_admin_rule.*')->join('left join hk_admin_module t2 ON t2.id=hk_admin_rule.mid')->find();}
            return $db->field('t2.title as modname,hk_admin_rule.*')->join('left join hk_admin_module t2 ON t2.id=hk_admin_rule.mid')->find();
        }
    }
    
    /**
     * 添加用户组数据
     * @param array $data 用户组信息
     */
    public function insert_group($data=array()){
        $db = M('admin_group');
        return $db->add($data);
    }
    
    /**
     * 查询用户组数据
     * @param int $gid 用户组ID
     */
    public function query_group($gid=null){
        $db = M('admin_group');
        if(!is_numeric($gid)){
          return $db->select();  
        }
        return $db->where('id='.$gid)->find();
    }
    
    /**
     * 更新用户组数据
     * @param array $data 用户组数据 需包含主键
     */
    public function update_group($data=array()){
        $db = M('admin_group');
        return $db->save($data);
    }
    
    /**
     * 删除用户组
     * @param array $data 用户组数据 需包含主键
     */
    public function del_group($gid){
        $db = M('admin_group');
        return $db->where('id='.$gid)->delete();
    }


    /*返回权限模块数据*/
    public function query_module($mid){
        $db = M('admin_module');
        $fields = array('id','title','module','pid','sortid','path',"concat(path,'-',id)" => 'bpath','show');
        if(isset($mid) && is_numeric($mid)){
            return $db->field($fields)->where('id='.$mid)->order('bpath')->find();
        }
        return $db->field($fields)->order('bpath')->select();
    }
   
    /**
     * 新增权限模块数据
     * @param array $data 模块数据
     */
    public function insert_module($data=array()){
        $db = M('admin_module');
        $id = $db->add($data);
        if($this->setPath($id)){
            return true;
        }
    }
    
    /**
     * 更新权限模块
     * @param array $data 模块数据 需包含主键
     */
    public function update_module($data=array()){
        $db = M('admin_module');
        return $db->save($data);
    }
    
    /**
     * 删除权限模块
     * @param array $data 模块数据 需包含主键
     */
    public function del_module($mid){
        
    }

    /**
     * 填充权限模块数据path字段
     * @param int $mid 权限模块id
     */
    private function setPath($mid){
        $db = M('admin_module');
        $array = array();
        $path ='';
        if(isset($mid) && !empty($mid)){
            $result = $this->isTopClass($mid, $array);
            foreach(array_reverse($result) as $value){
                $path.= $value.'-';
            }
            return $db->where('id='.$mid)->setField('path', rtrim($path,'-'));
        }
    }

    /*规则所属模型和规则列表*/
    public function ModuleAndRule(){
        $module = $this->query_module();
        foreach ($module as &$val){
            $val['rules'] = $this->SubRule($val['id']);
        }
        return $module;
    }
    
    private function SubRule($mid){
        $db = M('admin_rule');
        return $db->field('id,title')->where('mid='.$mid)->select();
    }
    
    //判断是否为顶级模块
    private function isTopClass($id,&$array){  
        $db = M('admin_module');
        $data = $db->field('id,pid')->where('id='.$id)->find();
        if($data['pid']!=0){
            array_push($array, $data['pid']);
            $this->IsTopClass($data['pid'], $array); //递归操作数组加入
        }else{
            array_push($array,'0');
        }
        return $array;
    }
}