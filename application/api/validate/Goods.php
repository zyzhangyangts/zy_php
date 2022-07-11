<?php


namespace app\api\validate;


use LinCmsTp5\validate\BaseValidate;

class Goods extends BaseValidate
{
    protected $rule = [
        'merchant_id' => 'require|gt:0',
        'goods_subclass_id' => 'require',
        'goods_name' => 'require',
        'goods_intro' => 'require',
        'goods_album' => 'require',
        'goods_price' => 'require',
    ];

    protected $message = [
        'merchant_id.require' => '请输入商户',
        'merchant_id.gt' => '请输入商户',
        'goods_subclass_id.require' => '请输入商品分类',
        'goods_name.require' => '请输入商品名称',
        'goods_intro.require' => '请输入商品简介',
        'goods_album.require' => '请输入商品相册',
        'goods_price.require' => '请输入商品价格',
    ];
}