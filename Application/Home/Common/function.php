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


/**
 * 创建随机字符串。
 * @return string 随机字符串
 */
function create_rand_str(){
    if(APP_DEBUG) trace("创建随机字符串...");
    return Org\Util\String::randString();
}

/**
 * 加密。
 * @param  string $text 明文。
 * @return string       密文。
 */
function create_des_encrypt($text=''){
    if(APP_DEBUG) trace("加密...");
    if(empty($text)) return null;
    $_key = md5(C('md5_key'));
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
                                        $_key, 
                                        $text, 
                                        MCRYPT_MODE_ECB, 
                                        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, 
                                                                            MCRYPT_MODE_ECB), 
                                                         MCRYPT_RAND)));
}

/**
 * 解密。
 * @param  string $base64 密文。
 * @return string         明文。
 */
function create_des_decrypt($base64=''){
    if(APP_DEBUG) trace("解密...");
    if(empty($base64)) return null;
    $_key = md5(C('md5_key'));
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 
                          $_key,
                          base64_decode($base64),
                          MCRYPT_MODE_ECB,
                          mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,
                                                              MCRYPT_MODE_ECB), 
                                           MCRYPT_RAND));
}