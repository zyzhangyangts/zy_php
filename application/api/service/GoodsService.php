<?php
/**
 * 商品 service
 * @author zy
 */

namespace app\api\service;


use app\api\model\MerchantModel;
use think\facade\Config;
use think\facade\Request;
use UnexpectedValueException;
use app\api\model\GoodsModel;
use app\api\model\GoodsAlbumModel;
use app\api\model\GoodsPriceLogModel;
use app\api\service\MerchantService;
use app\api\service\GoodsClassService;
use think\db;

class GoodsService
{
    /**
     * 创建商品
     * @param $params
     * @return array
     */
    public function add($params) {
        $GoodsModel = new GoodsModel();
        $merchantId = isset($params['merchant_id']) ? $params['merchant_id'] : 0;
        $goodsSubclassId = isset($params['goods_subclass_id']) ? $params['goods_subclass_id'] : 0;
        $goodsName = isset($params['goods_name']) ? $params['goods_name'] : '';
        $goodsWeight = isset($params['goods_weight']) ? $params['goods_weight'] : 0;
        $goodsWeightUnit = isset($params['goods_weight_unit']) ? $params['goods_weight_unit'] : 0;
        $goodsIntro = isset($params['goods_intro']) ? $params['goods_intro'] : '';
        $goodsPrice = isset($params['goods_price']) ? $params['goods_price'] : 0;

        // 商户信息
        $MerchantService = new MerchantService();
        $merchantInfo = $MerchantService->info($merchantId);
        if(empty($merchantId)) {
            return outputError('商户信息不存在');
        }

        // 获取商圈ID
        $marketId = isset($merchantInfo['market_id']) ? $merchantInfo['market_id'] : 0;

        // 获取分类
        $GoodsClassService = new GoodsClassService();
        $goodsSubclassInfo = $GoodsClassService->info($goodsSubclassId);
        $goodsClassId = $goodsSubclassInfo['parent_id'];

        $goodsAlbum = [];
        if(isset($params['goods_album'])) {
            $goodsAlbum = $params['goods_album'];
            unset($params['goods_album']);
        }

        $goodsCover = isset($goodsAlbum[0]) ? $goodsAlbum[0] : '';
        if($goodsPrice < 0) {
            return outputError('商品价格不能小于0元');
        }

        $currentTime = date('Y-m-d H:i:s');
        Db::startTrans();
        $goods = [];
        $goods['market_id'] = $marketId;
        $goods['merchant_id'] = $merchantId;
        $goods['goods_class_id'] = $goodsClassId;
        $goods['goods_subclass_id'] = $goodsSubclassId;
        $goods['goods_name'] = $goodsName;
        $goods['goods_weight'] = $goodsWeight;
        $goods['goods_weight_unit'] = $goodsWeightUnit;
        $goods['goods_intro'] = $goodsIntro;
        $goods['goods_cover'] = $goodsCover;
        $goods['goods_price'] = $goodsPrice;
        $goods['goods_num'] = 0;
        $goods['status'] = 1;
        $goods['create_time'] = $currentTime;
        $goods['update_time'] = $currentTime;
        $goodsId = $GoodsModel->insertGetId($goods);
        if(!$goodsId) {
            Db::rollback();
            return outputError('创建商品失败');
        }

        if(!empty($goodsAlbum)) {
            $GoodsAlbumModel = new GoodsAlbumModel();
            $albumList = [];
            $albumWeight = 0;
            foreach($goodsAlbum as $photoUrl) {
                $albumWeight = $albumWeight + 1;
                $album = [];
                $album['goods_id'] = $goodsId;
                $album['photo_url'] = $photoUrl;
                $album['status'] = 1;
                $album['weight'] = $albumWeight;
                $album['create_time'] = $currentTime;
                $album['update_time'] = $currentTime;
                $albumList[] = $album;
            }

            $albumRes = $GoodsAlbumModel->insertAll($albumList);
            if(!$albumRes) {
                Db::rollback();
                return outputError('创建商品相册失败');
            }
        }

        // 价格记录
        $timeline = date('Y-m-d');
        $priceLogRes = $this->savePriceLog($goodsId, $timeline, $goodsPrice);
        if(!$priceLogRes) {
            Db::rollback();
            return outputError('商品价格日志保存失败');
        }

        $saveMerClassRes = $MerchantService->saveMerchantGoodsClass($merchantId, $goodsClassId, $goodsSubclassId);
        if(!$saveMerClassRes) {
            Db::rollback();
            return outputError('保存商户的商品分类数据失败');
        }

        Db::commit();
        return outputSuccess('创建商品成功');
    }

    public function edit($params) {
        if(!isset($params['goods_id']) || $params['goods_id'] <= 0) {
            return outputError('请输入商品ID');
        }

        $goodsId = intval($params['goods_id']);
        unset($params['goods_id']);
        $info = $this->info($goodsId);
        if(empty($info)) {
            return outputError('商品信息不存在');
        }

        $oldGoodsSubclassId = isset($info['goods_subclass_id']) ? $info['goods_subclass_id'] : 0;
        $GoodsModel = new GoodsModel();
        $merchantId = isset($params['merchant_id']) ? $params['merchant_id'] : 0;
        $goodsSubclassId = isset($params['goods_subclass_id']) ? $params['goods_subclass_id'] : 0;
        $goodsName = isset($params['goods_name']) ? $params['goods_name'] : '';
        $goodsWeight = isset($params['goods_weight']) ? $params['goods_weight'] : 0;
        $goodsWeightUnit = isset($params['goods_weight_unit']) ? $params['goods_weight_unit'] : 0;
        $goodsIntro = isset($params['goods_intro']) ? $params['goods_intro'] : '';
        $goodsPrice = isset($params['goods_price']) ? $params['goods_price'] : 0;

        // 商户信息
        $MerchantService = new MerchantService();
        $merchantInfo = $MerchantService->info($merchantId);
        if(empty($merchantId)) {
            return outputError('商户信息不存在');
        }

        // 获取商圈ID
        $marketId = isset($merchantInfo['market_id']) ? $merchantInfo['market_id'] : 0;

        // 获取分类
        $GoodsClassService = new GoodsClassService();
        $goodsSubclassInfo = $GoodsClassService->info($goodsSubclassId);
        $goodsClassId = $goodsSubclassInfo['parent_id'];

        $goodsAlbum = [];
        if(isset($params['goods_album'])) {
            $goodsAlbum = $params['goods_album'];
            unset($params['goods_album']);
        }

        $goodsCover = isset($goodsAlbum[0]) ? $goodsAlbum[0] : '';
        if($goodsPrice < 0) {
            return outputError('商品价格不能小于0元');
        }

        $currentTime = date('Y-m-d H:i:s');
        Db::startTrans();
        $goods = [];
        $goods['market_id'] = $marketId;
        $goods['merchant_id'] = $merchantId;
        $goods['goods_class_id'] = $goodsClassId;
        $goods['goods_subclass_id'] = $goodsSubclassId;
        $goods['goods_name'] = $goodsName;
        $goods['goods_weight'] = $goodsWeight;
        $goods['goods_weight_unit'] = $goodsWeightUnit;
        $goods['goods_intro'] = $goodsIntro;
        $goods['goods_cover'] = $goodsCover;
        $goods['goods_price'] = $goodsPrice;
        $goods['goods_num'] = 0;
        $goods['update_time'] = $currentTime;
        $upGoodsRes = $GoodsModel->where('goods_id', $goodsId)->update($goods);
        if(!$upGoodsRes) {
            Db::rollback();
            return outputError('更新商品失败');
        }

        if(!empty($goodsAlbum)) {
            $albumRes = $this->setGoodsAlbum($goodsId, $goodsAlbum);
            if(!$albumRes) {
                Db::rollback();
                return outputError('商品相册保存失败');
            }
        }

        // 价格记录
        $timeline = date('Y-m-d');
        $priceLogRes = $this->savePriceLog($goodsId, $timeline, $goodsPrice);
        if(!$priceLogRes) {
            Db::rollback();
            return outputError('商品价格日志保存失败');
        }

        $saveMerClassRes = $MerchantService->saveMerchantGoodsClass($merchantId, $goodsClassId, $goodsSubclassId, $oldGoodsSubclassId);
        if(!$saveMerClassRes) {
            Db::rollback();
            return outputError('保存商户的商品分类数据失败');
        }

        Db::commit();

        return outputSuccess('编辑成功');
    }

    /**
     * 获取商品信息
     * @param $goodsId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($goodsId) {
        $goodsId = intval($goodsId);
        if($goodsId <= 0) {
            return [];
        }

        $GoodsModel = new GoodsModel();
        $info = $GoodsModel->where('goods_id', $goodsId)->find();
        if(empty($info)) {
            return [];
        }

        $info = $info->toArray();
        $info['goods_album'] = $this->getGoodsAlbum($goodsId);
        return $info;
    }

    /**
     * 获取商品相册
     * @param $goodsId
     * @return array|array[]|\array[][]|\array[][][]
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getGoodsAlbum($goodsId) {
        if($goodsId <= 0) {
            return [];
        }

        $albumWhere = [];
        $albumWhere['goods_id'] = $goodsId;
        $albumWhere['status']   = 1;
        $GoodsAlbumService = new GoodsAlbumModel();
        $goodsAlbum = $GoodsAlbumService->where($albumWhere)->order('weight', 'asc')->select();
        if($goodsAlbum->isEmpty()) {
            return [];
        }

        $goodsAlbum = $goodsAlbum->toArray();
        return $goodsAlbum;
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
        $marketId = isset($params['market_id']) ? $params['market_id'] : -1;
        $merchantId = isset($params['merchant_id']) ? $params['merchant_id'] : -1;
        $goodsClassId = isset($params['goods_class_id']) ? $params['goods_class_id'] : -1;
        $goodsSubclassId = isset($params['goods_subclass_id']) ? $params['goods_subclass_id'] : -1;
        $goodsName = isset($params['goods_name']) ? trim($params['goods_name']) : '';
        $status = isset($params['status']) ? $params['status'] : -1;

        $GoodsModel = GoodsModel::where('1=1');
        if($marketId > -1) {
            $GoodsModel->where('market_id', $marketId);
        }

        if($merchantId > -1) {
            $GoodsModel->where('merchant_id', $merchantId);
        }

        if($goodsClassId > -1) {
            $GoodsModel->where('goods_class_id', $merchantId);
        }

        if($goodsSubclassId > -1) {
            $GoodsModel->where('goods_subclass_id', $goodsSubclassId);
        }

        if(!empty($goodsName)) {
            $GoodsModel->where('goods_name', 'like', '%'.$goodsName.'%');
        }

        if($status > -1) {
            $GoodsModel->where('status', $status);
        }

        $allData = $GoodsModel->paginate($pageSize, false, ["page" => $page])->toArray();
        //var_dump($GoodsModel->getlastsql());exit;
        return $allData;
    }

    /**
     * 设置状态
     * @param $goodsId
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($goodsId, $status) {
        $goodsId = intval($goodsId);
        if($goodsId <= 0) {
            return outputError('请输入商户ID');
        }

        if(!in_array($status, [1, 0])) {
            return outputError('请输入正确状态');
        }

        $update = [];
        $update['status'] = $status;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = GoodsModel::where('goods_id', $goodsId)->update($update);
        if($res) {
            return outputSuccess('更改成功');
        }else {
            return outputError('更改失败');
        }

    }

    /**
     * 保存价格记录
     * @param $goodsId
     * @param $timeline
     * @param $price
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function savePriceLog($goodsId, $timeline, $price) {
        $GoodsPriceLogModel = new GoodsPriceLogModel();
        $currentTime = date('Y-m-d H:i:s');
        $where = [];
        $where['goods_id'] = $goodsId;
        $where['timeline'] = $timeline;
        $checkPriceLog = GoodsPriceLogModel::where($where)->find();
        if(empty($checkPriceLog)) {
            $priceLog = [];
            $priceLog['goods_id'] = $goodsId;
            $priceLog['timeline'] = $timeline;
            $priceLog['goods_price'] = $price;
            $priceLog['create_time'] = $currentTime;
            $priceLog['update_time'] = $currentTime;
            $res = $GoodsPriceLogModel->insert($priceLog);
        }else {
            $res = true;
            if($checkPriceLog['goods_price'] != $price) {
                $priceLog = [];
                $priceLog['goods_price'] = $price;
                $priceLog['update_time'] = $currentTime;
                $res = $GoodsPriceLogModel->where('goods_price_log_id', $checkPriceLog['goods_price_log_id'])->update($priceLog);
            }
        }

        if(!$res) {
            return false;
        }

        return true;
    }

    /**
     * 设置商品相册
     * @param $goodsId
     * @param $goodsAlbumList
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function setGoodsAlbum($goodsId, $goodsAlbumList) {
        if($goodsId <= 0) {
            return true;
        }

        $currentTime = date('Y-m-d H:i:s');
        $setStatusUp = [];
        $setStatusUp['status'] = 0;
        $setStatusUp['weight'] = 10000;
        $setStatusUp['update_time'] = $currentTime;
        $GoodsAlbumModel = new GoodsAlbumModel();
        $setStatusRes = $GoodsAlbumModel->where('goods_id', $goodsId)->update($setStatusUp);
        if(!$setStatusRes) {
            return false;
        }

        if(empty($goodsAlbumList)) {
            return true;
        }

        $albumWeight = 0;
        foreach($goodsAlbumList as $photoUrl) {
            $albumWeight = $albumWeight + 1;
            $checkWhere = [];
            $checkWhere['goods_id'] = $goodsId;
            $checkWhere['photo_url'] = $photoUrl;
            $checkPhoto = $GoodsAlbumModel->where($checkWhere)->find();

            $album = [];
            $album['goods_id'] = $goodsId;
            $album['photo_url'] = $photoUrl;
            $album['status'] = 1;
            $album['weight'] = $albumWeight;
            $album['update_time'] = $currentTime;
            if(empty($checkPhoto)) {
                $album['create_time'] = $currentTime;
                $res = $GoodsAlbumModel->insert($album);
            }else {
                $res = $GoodsAlbumModel->where('goods_album_id', $checkPhoto['goods_album_id'])->update($album);
            }

            if(!$res) {
                return false;
            }

        }

        return true;
    }

    /**
     * 获取列表 通过 IDS
     * @param $goodsIds
     * @return array
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getListByIds($goodsIds) {
        if(empty($goodsIds)) {
            return [];
        }

        $list = GoodsModel::where('goods_id', 'in', $goodsIds)->select();
        if($list->isEmpty()) {
            return [];
        }

        $list = $list->toArray();
        $list = arrayMap($list, 'goods_id');
        return $list;
    }

}