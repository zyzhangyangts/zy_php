<?php
/**
 * Created by PhpStorm.
 * User: 沁塵
 * Date: 2019/4/20
 * Time: 19:58
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class RecommendModel extends BaseModel
{
    protected $table = 'xs_recommend';
    protected $pk    = 'recommend_id';

}