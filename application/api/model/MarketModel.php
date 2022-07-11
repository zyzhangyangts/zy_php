<?php
/**
 * Created by PhpStorm.
 * User: 沁塵
 * Date: 2019/4/20
 * Time: 19:58
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class MarketModel extends BaseModel
{
    protected $table = 'xs_market';
    protected $pk    = 'market_id';

    protected $auto = ['create_time', 'update_time'];
}