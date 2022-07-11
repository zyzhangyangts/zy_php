<?php


namespace app\api\validate;


use LinCmsTp5\validate\BaseValidate;

class Merchant extends BaseValidate
{
    protected $rule = [
        'market_id' => 'require|gt:0',
        'merchant_name' => 'require',
        'merchant_photo' => 'require',
        'merchant_score' => 'require|between:0,5',
        'start_delivery_price' => 'require',
        'delivery_price' => 'require',
    ];

    protected $message = [
        'market_id.require' => '请输入商圈',
        'market_id.gt' => '请选择商圈',
        'merchant_name.require' => '请输入商户名称',
        'merchant_photo.require' => '请上传商户图片',
        'merchant_score.require' => '请输入商户评分',
        'merchant_score.between' => '商户评分 0-5 之间',
        'start_delivery_price.require' => '请输入起送价格',
        'delivery_price.require' => '请输入配送价格',
    ];
}