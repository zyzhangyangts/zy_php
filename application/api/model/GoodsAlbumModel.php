<?php
/**
 * Created by PhpStorm.
 * User: 沁塵
 * Date: 2019/4/20
 * Time: 19:58
 */

namespace app\api\model;


use think\model\concern\SoftDelete;

class GoodsAlbumModel extends BaseModel
{
    protected $table = 'xs_goods_album';
    protected $pk    = 'goods_album_id';

}