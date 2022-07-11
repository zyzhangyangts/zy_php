<?php


namespace app\api\validate;


use LinCmsTp5\validate\BaseValidate;

class IndexRecommend extends BaseValidate
{
    protected $rule = [
        'recommend_id' => 'require|gt:0',
        'weight' => 'require',
    ];

    protected $message = [
        'recommend_id.require' => '请输入推荐位',
        'recommend_id.gt' => '请输入推荐位',
        'recommend_type' => '请输入排序权重',
    ];
}