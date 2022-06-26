<?php


namespace app\api\validate;


use LinCmsTp5\validate\BaseValidate;

class GoodsClass extends BaseValidate
{
    protected $rule = [
        'goods_class_name' => 'require',
    ];

    protected $message = [
        'goods_class_name.require' => '请输入商品分类名称',
    ];
}