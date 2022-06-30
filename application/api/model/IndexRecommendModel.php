<?php
/**
 * Created by PhpStorm.
 * User: 沁塵
 * Date: 2019/4/20
 * Time: 19:58
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class IndexRecommendModel extends BaseModel
{
    protected $table = 'xs_index_recommend';
    protected $pk    = 'index_recommend_id';

}