<?php
/**
 * 商圈 service
 * @author zy
 */

namespace app\api\service;


use app\api\model\GoodsModel;
use think\facade\Config;
use think\facade\Request;
use UnexpectedValueException;
use app\api\model\MarketModel;
use app\api\model\marketGoodsClassModel;
use think\db;

class MarketService
{
    /**
     * 创建商圈
     * @param $params
     * @return array
     */
    public function add($params) {
        $MarketModel = new MarketModel();
        $params['status'] = 1;
        $params['create_time'] = date('Y-m-d H:i:s');
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $MarketModel->insert($params);
        if(!$res) {
            return outputError('创建商圈失败');
        }

        return outputSuccess('创建商圈成功');
    }

    public function edit($params) {
        if(!isset($params['market_id']) || $params['market_id'] <= 0) {
            return outputError('请输入商圈ID');
        }

        $marketId = intval($params['market_id']);
        unset($params['market_id']);
        $info = $this->info($marketId);
        if(empty($info)) {
            return outputError('商圈信息不存在');
        }

        $MarketModel = new MarketModel();
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $MarketModel->where('market_id', $marketId)->update($params);
        if(!$res) {
            return outputError('编辑失败');
        }

        return outputSuccess('编辑成功');
    }

    /**
     * 获取商圈信息
     * @param $marketId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($marketId) {
        $marketId = intval($marketId);
        if($marketId <= 0) {
            return [];
        }

        $MarketModel = new MarketModel();
        $info = $MarketModel->where('market_id', $marketId)->find();
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
    public function lists($params) {
        $page = isset($params['page']) ? $params['page'] : 1;
        $pageSize = isset($params['page_size']) ? $params['page_size'] : 20;
        $marketId = isset($params['market_id']) ? $params['market_id'] : 0;
        $status = isset($params['status']) ? $params['status'] : -1;

        $MarketModel = MarketModel::where('1=1');
        if($marketId > 0) {
            $MarketModel->where('market_id', $marketId);
        }

        if($status > -1) {
            $MarketModel->where('status', $status);
        }

        $allData = $MarketModel->paginate($pageSize, false, ["page" => $page])->toArray();
        if(isset($allData['data']) && !empty($allData['data'])) {
            $marketIds = array_column($allData['data'], 'market_id');

            foreach($allData['data'] as $item) {

            }
        }
        //var_dump($MarketModel->getlastsql());exit;
        return $allData;
    }

    /**
     * 设置状态
     * @param $marketId
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($marketId, $status) {
        $marketId = intval($marketId);
        if($marketId <= 0) {
            return outputError('请输入商圈ID');
        }

        if(!in_array($status, [1, 0])) {
            return outputError('请输入正确状态');
        }

        $update = [];
        $update['status'] = $status;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = MarketModel::where('market_id', $marketId)->update($update);
        if($res) {
            return outputSuccess('更改成功');
        }else {
            return outputError('更改失败');
        }

    }


    /**
     * 获取列表 通过 IDS
     * @param $marketIds
     * @return array
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getListByIds($marketIds) {
        if(empty($marketIds)) {
            return [];
        }

        $list = MarketModel::where('market_id', 'in', $marketIds)->select();
        if($list->isEmpty()) {
            return [];
        }

        $list = $list->toArray();
        $list = arrayMap($list, 'market_id');
        return $list;
    }

}