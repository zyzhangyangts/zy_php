<?php


namespace app\api\validate;


use LinCmsTp5\validate\BaseValidate;

class Merchant extends BaseValidate
{
    protected $rule = [
        'market_id' => 'require|gt:0',
        'market_name' => 'require',
    ];

    protected $message = [
        'market_id.require' => '请输入商圈',
        'market_id.gt' => '请选择商圈',
        'merchant_name.require' => '请输入商户名称',
    ];
}