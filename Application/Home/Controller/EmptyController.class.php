<?php
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function _empty(){
        $this->show('您所访问的页面不存在');
    }
    
}
