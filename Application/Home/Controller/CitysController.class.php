<?php
namespace Home\Controller;
use Think\Controller;
//地区控制器,主要执行查询操作
class CitysController extends Controller{
    public function optSelect($level=1,$fields=array('cityid','shortname'),$pid=0){
        $db = M('citys');
        return $db->field($fields)->where('`level`='.$level.' and parentid='.$pid)->select();
    }
    
    public function loadOptions($level,$pid){
        $data = $this->optSelect($level, array('cityid','shortname'), $pid);
        $this->ajaxReturn($data, 'JSON');
    }
}