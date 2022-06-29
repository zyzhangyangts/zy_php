<?php
/**
 * 商户 service
 * @author zy
 */

namespace app\api\service;


use app\api\model\GoodsModel;
use think\facade\Config;
use think\facade\Request;
use UnexpectedValueException;
use app\api\model\MerchantModel;
use app\api\model\MerchantGoodsClassModel;
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
        if(!isset($params['merchant_id']) || $params['merchant_id'] <= 0) {
            return outputError('请输入商户ID');
        }

        $merchantId = intval($params['merchant_id']);
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
        $merchantId = intval($merchantId);
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
        $pageSize = isset($params['page_size']) ? $params['page_size'] : 20;
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

    /**
     * 设置状态
     * @param $merchantId
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($merchantId, $status) {
        $merchantId = intval($merchantId);
        if($merchantId <= 0) {
            return outputError('请输入商户ID');
        }

        if(!in_array($status, [1, 0])) {
            return outputError('请输入正确状态');
        }

        $update = [];
        $update['status'] = $status;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = MerchantModel::where('merchant_id', $merchantId)->update($update);
        if($res) {
            return outputSuccess('更改成功');
        }else {
            return outputError('更改失败');
        }

    }

    /**
     * 保存商户的 商品分类ID
     * @param $merchantId
     * @param $goodsClassId
     * @param $goodsSubclassId
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function saveMerchantGoodsClass($merchantId, $goodsClassId, $goodsSubclassId, $oldGoodsSubclassId = 0) {
        if($merchantId <= 0 || $goodsClassId <= 0 || $goodsSubclassId <= 0) {
            return false;
        }

        $MerchantGoodsClassModel = new MerchantGoodsClassModel();
        $currentTime = date('Y-m-d H:i:s');
        $where = [];
        $where['merchant_id']       = $merchantId;
        $where['goods_subclass_id'] = $goodsSubclassId;
        $checkMerGoodsClass = MerchantGoodsClassModel::where($where)->find();

        $merGoodsClass = [];
        $merGoodsClass['merchant_id']       = $merchantId;
        $merGoodsClass['goods_class_id']    = $goodsClassId;
        $merGoodsClass['goods_subclass_id'] = $goodsSubclassId;
        $merGoodsClass['status']            = 1;
        $merGoodsClass['update_time']       = $currentTime;
        if(empty($checkMerGoodsClass)) {
            $merGoodsClass['create_time']       = $currentTime;
            $res = $MerchantGoodsClassModel->insert($merGoodsClass);
        }else {
            $res = $MerchantGoodsClassModel->where('id', $checkMerGoodsClass['id'])->update($merGoodsClass);
        }

        if(!$res) {
            return false;
        }

        // 如果商品更换 分类，商户下 旧分类则关闭关联关系
        if($oldGoodsSubclassId > 0 && $goodsSubclassId != $oldGoodsSubclassId) {
            $closeMapWhere = [];
            $closeMapWhere['merchant_id']       = $merchantId;
            $closeMapWhere['goods_subclass_id'] = $oldGoodsSubclassId;
            $closeUp = [];
            $closeUp['status'] = 0;
            $closeUp['update_time'] = $currentTime;
            $closeRes = $MerchantGoodsClassModel->where($closeMapWhere)->update($closeUp);
        }

        return true;
    }


    public function delMerchantGoodsClass($merchantId, $goodsSubclassId) {
        if($merchantId <= 0 || $goodsSubclassId <= 0) {
            return false;
        }

        $currentTime = date('Y-m-d H:i:s');
        $merGoodsClass = [];
        $merGoodsClass['status']            = 0;
        $merGoodsClass['update_time']       = $currentTime;
        $where = [];
        $where['merchant_id']       = $merchantId;
        $where['goods_subclass_id'] = $goodsSubclassId;
        $MerchantGoodsClassModel = new MerchantGoodsClassModel();
        $res = $MerchantGoodsClassModel->where($where)->update($merGoodsClass);
        if($res === false) {
            return false;
        }

        return true;
    }

    /**
     * 获取列表 通过 IDS
     * @param $merchantIds
     * @return array
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getListByIds($merchantIds) {
        if(empty($merchantIds)) {
            return [];
        }

        $list = MerchantModel::where('merchant_id', 'in', $merchantIds)->select();
        if($list->isEmpty()) {
            return [];
        }

        $list = $list->toArray();
        $list = arrayMap($list, 'merchant_id');
        return $list;
    }

}