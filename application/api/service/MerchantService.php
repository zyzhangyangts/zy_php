<?php
/**
 * 商户 service
 * @author zy
 */

namespace app\api\service;


use think\facade\Config;
use think\facade\Request;
use UnexpectedValueException;
use app\api\model\MerchantModel;


class MerchantService
{
    public function add($params) {
        $MerchantModel = new MerchantModel();
        $params['status'] = 1;
        $params['create_time'] = date('Y-m-d H:i:s');
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $MerchantModel->insert($params);
        if(!$res) {
            return outputError('创建商户失败');
        }

        return outputSuccess('创建商户成功');
    }

}