<?php


namespace app\api\validate;


use LinCmsTp5\validate\BaseValidate;

class Recommend extends BaseValidate
{
    protected $rule = [
        'recommend_id' => 'require',
        'weight' => 'require',
    ];

    protected $message = [
        'recommend_id' => '请输入推荐位',
        'recommend_type' => '请输入排序权重',
    ];
}