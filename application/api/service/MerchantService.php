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
use think\db;

class MerchantService
{
    /**
     * 创建商户
     * @param $params
     * @return array
     */
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

    public function edit($params) {
        if(!isset($params['merchant_id'])) {
            return outputError('请输入商户ID');
        }

        $merchantId = $params['merchant_id'];
        unset($params['merchant_id']);
        $info = $this->info($merchantId);
        if(empty($info)) {
            return outputError('商户信息不存在');
        }

        $MerchantModel = new MerchantModel();
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $MerchantModel->where('merchant_id', $merchantId)->update($params);
        if(!$res) {
            return outputError('编辑失败');
        }

        return outputSuccess('编辑成功');
    }

    /**
     * 获取商户信息
     * @param $merchantId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($merchantId) {
        if($merchantId <= 0) {
            return [];
        }

        $MerchantModel = new MerchantModel();
        $info = $MerchantModel->where('merchant_id', $merchantId)->find();
        if(empty($info)) {
            return [];
        }

        $info = $info->toArray();
        return $info;
    }

    /**
     * 列表
     * @param $params
     * @return array
     * @throws \think\exception\DbException
     */
    public function list($params) {
        $page = isset($params['page']) ? $params['page'] : 1;
        $pageSize = isset($params['page']) ? $params['page'] : 20;
        $marketId = isset($params['market_id']) ? $params['market_id'] : 0;
        $status = isset($params['status']) ? $params['status'] : -1;

        $MerchantModel = MerchantModel::where('1=1');
        if($marketId > 0) {
            $MerchantModel->where('market_id', $marketId);
        }

        if($status > -1) {
            $MerchantModel->where('status', $status);
        }

        $allData = $MerchantModel->paginate($pageSize, false, ["page" => $page])->toArray();
        //var_dump($MerchantModel->getlastsql());exit;
        return $allData;
    }

}