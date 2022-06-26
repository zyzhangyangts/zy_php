<?php
/**
 * Created by PhpStorm.
 * User: 沁塵
 * Date: 2019/4/20
 * Time: 19:58
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class GoodsPriceLogModel extends BaseModel
{
    protected $table = 'xs_goods_price_log';
    protected $pk    = 'goods_price_log_id';

}