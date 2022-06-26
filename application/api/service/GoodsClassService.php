<?php
/**
 * 商品分类 service
 * @author zy
 */

namespace app\api\service;


use app\api\model\MerchantModel;
use think\facade\Config;
use think\facade\Request;
use UnexpectedValueException;
use app\api\model\GoodsClassModel;
use think\db;

class GoodsClassService
{
    /**
     * 创建商品分类
     * @param $params
     * @return array
     */
    public function add($params) {
        $GoodsClassModel = new GoodsClassModel();
        $params['status'] = 1;
        $params['create_time'] = date('Y-m-d H:i:s');
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $GoodsClassModel->insert($params);
        if(!$res) {
            return outputError('创建商品分类失败');
        }

        return outputSuccess('创建商品分类成功');
    }

    public function edit($params) {
        if(!isset($params['goods_class_id']) || $params['goods_class_id'] <= 0) {
            return outputError('请输入商品分类ID');
        }

        $goodsClassId = intval($params['goods_class_id']);
        unset($params['goods_class_id']);
        $info = $this->info($goodsClassId);
        if(empty($info)) {
            return outputError('商品分类信息不存在');
        }

        $GoodsClassModel = new GoodsClassModel();
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $GoodsClassModel->where('goods_class_id', $goodsClassId)->update($params);
        if(!$res) {
            return outputError('编辑失败');
        }

        return outputSuccess('编辑成功');
    }

    /**
     * 获取商品分类信息
     * @param $goodsClassId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($goodsClassId) {
        $goodsClassId = intval($goodsClassId);
        if($goodsClassId <= 0) {
            return [];
        }

        $GoodsClassModel = new GoodsClassModel();
        $info = $GoodsClassModel->where('goods_class_id', $goodsClassId)->find();
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
        $parentId = isset($params['parent_id']) ? $params['parent_id'] : -1;
        $status = isset($params['status']) ? $params['status'] : -1;

        $GoodsClassModel = GoodsClassModel::where('1=1');
        if($parentId > -1) {
            $GoodsClassModel->where('parent_id', $parentId);
        }

        if($status > -1) {
            $GoodsClassModel->where('status', $status);
        }

        $allData = $GoodsClassModel->paginate($pageSize, false, ["page" => $page])->toArray();
        //var_dump($GoodsClassModel->getlastsql());exit;
        return $allData;
    }

    /**
     * 设置状态
     * @param $goodsClassId
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($goodsClassId, $status) {
        $goodsClassId = intval($goodsClassId);
        if($goodsClassId <= 0) {
            return outputError('请输入商户ID');
        }

        if(!in_array($status, [1, 0])) {
            return outputError('请输入正确状态');
        }

        $update = [];
        $update['status'] = $status;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = GoodsClassModel::where('goods_class_id', $goodsClassId)->update($update);
        if($res) {
            return outputSuccess('更改成功');
        }else {
            return outputError('更改失败');
        }

    }
}