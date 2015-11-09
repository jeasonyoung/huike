<?php
/**
 * 权限管理控制器。
 * 
 */
namespace Home\Controller;
use Home\Controller\BaseController;

class AuthController extends BaseController{

    /**
     * 添加权限。
     * @return void
     */
    public function add_rule(){
        $model = D('Auth');
        $module = $this->show_module();
        $this->assign('module',$module);
        if(IS_POST){
            $rules = array();
            $rules['title'] = I('title');
            $rules['name'] = strtolower(I('name'));
            $rules['condition'] = I('condition');
            $rules['status'] = I('status');
            $rules['mid'] = I('mid');
            $rules['sortid'] = I('sortid');
            $rules['show'] = I('show');
            if(empty($rules['title']) || empty($rules['name']) || empty($rules['mid'])){
                $this->error('规则简述，规则标识，所属模块为必填选项!');
            }
            //检测此条规则是否已经存在
            if($model->query_rule("name='".$rules['name']."'",FALSE)){$this->error('此条规则已存在');}
            //开始新增规则
            if($model->insert_rule($rules)){
                $this->success('新增一条规则成功',U('Home/Auth/add_rule'));
            }else{
                $this->error('新增规则失败',U('Home/Auth/add_rule'));
            }
        }else{
           $this->display(); 
        }
    }

    /**
     * 权限列表。
     * @return void
     */
    public function list_rule(){
        $model = D('Auth');
        $module = $this->show_module();
        $condition = '';
        if(IS_POST){
            $_moduleId = I('module','');
            if(isset($_moduleId) && !empty($_moduleId)){
                $condition = array('mid' => $_moduleId);
            }
        }
        $rules = $model->query_rule($condition);
        $this->assign('module',$module);
        $this->assign('rules',$rules);
        $this->display();
    }
    
    /**
     * 删除权限
     * @param int $rid 权限ID
     */
    public function del_rule($rid){
        $model = D('Auth');
        if($model->del_rule($rid)){
            $this->success('成功删除一条权限',U('Home/Auth/rule_list'));
        }else{
            $this->error('删除权限失败,请联系技术人员');
        }
    }
    
    /**
     * 修改权限
     * @param int $rid 权限ID
     */
    public function edit_rule($rid){
        $model = D('Auth');
        $module = $this->show_module();
        $this->assign('module',$module);
        if(IS_POST){
            $rules = array();
            $rules['id'] = I('rid');
            $rules['title'] = I('title');
            $rules['name'] = strtolower(I('name'));
            $rules['condition'] = I('condition');
            $rules['status'] = I('status');
            $rules['mid'] = I('mid');
            $rules['sortid'] = I('sortid');
            $rules['show'] = I('show');
            if(empty($rules['title']) || empty($rules['name']) || empty($rules['mid'])){
                $this->error('规则简述，规则标识，所属模块为必填选项!');
            }
            $result = $model->update_rule($rules);
            if(is_array($result)){
                $this->error($result['error']);
            }
            if($result){
                $this->success('规则修改成功!',U('Home/Auth/list_rule'));
            }else{
                $this->error('规则修改失败或未做修改',U('Home/Auth/edit_rule',array('rid' => $rid)));
            }
        }else{
            $singlerule = $model->query_rule('hk_admin_rule.id='.$rid,FALSE);
            $this->assign('info',$singlerule);
            $this->display();
        }
    }

    /**
     * 添加用户组。
     * @return void
     */
    public function add_group(){
        $model = D('Auth');
        if(!IS_POST){
            $this->assign('rules',$model->ModuleAndRule());
            $this->display();
        }else{
            $data = array();
            $data['title'] = I('title');
            $data['describe'] = I('describe');
            $data['status'] = I('status');
            $data['rules'] = implode(',', I('rules'));
            if(empty($data['title'])||empty($data['describe'])||empty($data['rules'])){
                $this->error('角色名称，角色简述，权限都必须指定');
            }
            if($model->insert_group($data)){
                $this->success('用户组添加成功');
            }else{
                $this->error('用户组添加失败');
            }
        }
    }

    /**
     * 用户组列表。
     * @return void
     */
    public function list_group(){
        $model = D('Auth');
        $data = $model->query_group();
        $this->assign('group',$data);
        $this->display();
    }
    
    /**
     * 修改用户组。
     * @param int $gid 用户组ID
     */
    public function edit_group($gid){
        $model = D('Auth');
        if(!IS_POST){
            $data=$model->query_group($gid);
            $this->assign('info',$data);
            $this->assign('rules',$model->ModuleAndRule());
            $this->display();
        }else{
            $data = array();
            $data['id'] = I('gid');
            $data['title'] = I('title');
            $data['describe'] = I('describe');
            $data['status'] = I('status');
            $data['rules'] = implode(',', I('rules'));
            if(empty($data['title'])||empty($data['describe'])||empty($data['rules'])){
                $this->error('角色名称，角色简述，权限都必须指定');
            }
            if($model->update_group($data)){
                $this->success('用户组更新成功',U('Home/Auth/list_group'));
            }else{
                $this->error('用户组更新失败',U('Home/Auth/list_group',array('gid' => $gid)));
            }
        }
    }
    
    /**
     * 删除用户组。
     * @param int $gid 用户组id
     */
    public function del_group($gid){
        $model = D('Auth');
        if($model->del_group($gid)){
            $this->success('成功删除一个用户组',U('Home/Auth/list_group'));
        }else{
            $this->error('删除用户组失败');
        }
    }
    
    /**
     * 系统权限模块列表。
     * @return void
     */
    public function list_module(){
        $model = D('Auth');
        $data = $model->query_module();
        $this->assign('module',$data);
        $this->display();
    }

    /**
     * 添加系统权限模块。
     * @return void
     */
    public function add_module(){
        $model = D('Auth');
        if(IS_POST){
            $data = array();
            $data['title'] = I('title');
            $data['module'] = I('module');
            $data['sortid'] = I('post.sortid',0);
            $data['show'] = I('show');
            $data['pid'] = I('pid');
            if(empty($data['title'])/* || empty($data['pid'])*/){
                $this->error('模块名称,所属模块是必填选项');
            }
            if($model->insert_module($data)){
                $this->success('新增权限模块成功!',U('Home/Auth/list_module'));
            }else{
                $this->error('新增权限模块失败');
            }
        }else{
            $module = $model->query_module();
            $this->assign('module',$module);
            $this->display(); 
        }
    }
    
    /**
     * 修改系统权限模块
     * @param int $mid 模块ID
     */
    public function edit_module($mid){
        $model = D('Auth');
        if(IS_POST){
            $data = array();
            $data['id'] = I('mid');
            $data['title'] = I('title');
            $data['module'] = I('module');
            $data['sortid'] = I('post.sortid',0);
            $data['show'] = I('show');
            $data['pid'] = I('pid');
            if(empty($data['title']) /*|| empty($data['pid'])*/){
                $this->error('模块名称是必填选项');
            }
            if($model->update_module($data)){
                $this->success('修改权限模块成功!',U('Home/Auth/list_module'));
            }else{
                $this->error('修改权限模块失败',U('Home/Auth/edit_module',array('mid' => $mid)));
            }
        }else{
            $module = $model->query_module(null);
            $data = $model->query_module($mid);
            $this->assign('module',$module);
            $this->assign('info',$data);
            $this->display();
        }
    }
    
    //权限所属模块
    private function show_module(){
        $model = D('Auth');
        $module = $model->query_module(null);
        return $module;
    }
}