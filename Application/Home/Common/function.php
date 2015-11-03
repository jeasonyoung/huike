<?php
/**
 * 模块公共函数
 */

/**
 * 验证交易金额
 * @param  string $money 交易金额
 * @return boolean       验证结果
 */
function check_recharge_money($money){
    if(APP_DEBUG) trace("验证交易金额=>[$money]...");
    if(isset($money) && !empty($money)){
        return floatval($money) > 0;
    }
    return false;
}