<?php
/**
 * Created by PhpStorm.
 * User: 沁塵
 * Date: 2019/4/20
 * Time: 19:58
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class RecommendLogModel extends BaseModel
{
    protected $table = 'xs_recommend_log';
    protected $pk    = 'recommend_log_id';

}