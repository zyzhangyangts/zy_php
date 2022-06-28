<?php


namespace app\api\validate;


use LinCmsTp5\validate\BaseValidate;

class Recommend extends BaseValidate
{
    protected $rule = [
        'recommend_name' => 'require',
        'recommend_type' => 'require',
        'recommend_model' => 'require',
        'show_type' => 'require',
        'is_show_name' => 'require',
    ];

    protected $message = [
        'recommend_name' => '请输入推荐位名称',
        'recommend_type' => '请选择推荐类型',
        'recommend_model.require' => '请选择推荐模式',
        'show_type.require' => '请选择展示类型',
        'is_show_name.require' => '请选择是否显示推荐位名称',
    ];
}