<?php
/**
 * 推荐位 service
 * @author zy
 */

namespace app\api\service;


use think\facade\Config;
use think\facade\Request;
use UnexpectedValueException;
use app\api\model\RecommendModel;
use app\api\model\RecommendLogModel;
use app\api\service\GoodsClassService;
use app\api\service\GoodsService;
use app\api\service\MerchantService;
use think\db;

class RecommendService
{
    /**
     * 创建推荐位
     * @param $params
     * @return array
     */
    public function add($params) {
        $checkResult = $this->checkParams($params);
        if($checkResult['status'] != 200) {
            return $checkResult;
        }

        $RecommendModel = new RecommendModel();
        $params['recommend_data'] = '';
        $params['status'] = 1;
        $params['create_time'] = date('Y-m-d H:i:s');
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $RecommendModel->insert($params);
        if(!$res) {
            return outputError('创建推荐位失败');
        }

        return outputSuccess('创建推荐位成功');
    }

    public function edit($params) {
        if(!isset($params['recommend_id']) || $params['recommend_id'] <= 0) {
            return outputError('请输入推荐位ID');
        }

        $checkResult = $this->checkParams($params);
        if($checkResult['status'] != 200) {
            return $checkResult;
        }

        $recommendId = intval($params['recommend_id']);
        unset($params['recommend_id']);
        $info = $this->info($recommendId);
        if(empty($info)) {
            return outputError('推荐位信息不存在');
        }

        $RecommendModel = new RecommendModel();
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $RecommendModel->where('recommend_id', $recommendId)->update($params);
        if(!$res) {
            return outputError('编辑失败');
        }

        return outputSuccess('编辑成功');
    }

    public function checkParams($params) {
        $recommendTypeList = recommendTypeList();
        $recommendModelList = recommendModelList();
        $showTypeList = showTypeList();
        if(!isset($params['recommend_type']) || !isset($recommendTypeList[$params['recommend_type']])) {
            return outputError('推荐类型不存在');
        }

        if(!isset($params['recommend_model']) || !isset($recommendModelList[$params['recommend_model']])) {
            return outputError('推荐模式不存在');
        }

        if(!isset($params['show_type']) || !isset($showTypeList[$params['show_type']])) {
            return outputError('展示类型不存在');
        }


        return outputSuccess('success');
    }

    /**
     * 获取推荐位信息
     * @param $recommendId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($recommendId) {
        $recommendId = intval($recommendId);
        if($recommendId <= 0) {
            return [];
        }

        $RecommendModel = new RecommendModel();
        $info = $RecommendModel->where('recommend_id', $recommendId)->find();
        if(empty($info)) {
            return [];
        }

        $info = $info->toArray();
        if(!empty($info['recommend_data'])) {
            $info['recommend_data'] = json_decode($info['recommend_data'], true);
        }

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

        $RecommendModel = RecommendModel::where('1=1');
        if($marketId > 0) {
            $RecommendModel->where('market_id', $marketId);
        }

        if($status > -1) {
            $RecommendModel->where('status', $status);
        }

        $allData = $RecommendModel->paginate($pageSize, false, ["page" => $page])->toArray();
        //var_dump($RecommendModel->getlastsql());exit;
        return $allData;
    }

    /**
     * 设置状态
     * @param $recommendId
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($recommendId, $status) {
        $recommendId = intval($recommendId);
        if($recommendId <= 0) {
            return outputError('请输入推荐位ID');
        }

        if(!in_array($status, [1, 0])) {
            return outputError('请输入正确状态');
        }

        $update = [];
        $update['status'] = $status;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = RecommendModel::where('recommend_id', $recommendId)->update($update);
        if($res) {
            return outputSuccess('更改成功');
        }else {
            return outputError('更改失败');
        }

    }

    public function addItem($params) {
        if(!isset($params['recommend_id']) || $params['recommend_id'] <= 0) {
            return outputError('请输入推荐位ID');
        }

        $PostRecommendData = isset($params['recommend_data']) ? $params['recommend_data'] : [];
        if(empty($PostRecommendData)) {
            return outputError('请输入推荐数据');
        }

        $dataIds = array_column($PostRecommendData, 'id');
        $recommendData = [];
        foreach($PostRecommendData as $item) {
            $recomDataInfo = [];
            $recomDataInfo['id'] = $item['id'];
            $recommendData[] = $recomDataInfo;
        }

        $recommendId = intval($params['recommend_id']);
        unset($params['recommend_id']);
        $info = $this->info($recommendId);
        if(empty($info)) {
            return outputError('推荐位信息不存在');
        }

        $checkResult = $this->checkRecommendDataByType($dataIds, $info['recommend_type']);
        if($checkResult['status'] != 200) {
            return $checkResult;
        }

        Db::startTrans();
        $currentTime = date('Y-m-d H:i:s');
        $recommend = [];
        $recommend['recommend_data'] = json_encode($recommendData);
        $recommend['update_time'] = $currentTime;
        $RecommendModel = new RecommendModel();
        $res = $RecommendModel->where('recommend_id', $recommendId)->update($recommend);
        if(!$res) {
            return outputError('保存推荐数据失败');
        }

        $oldRecommendData = is_array($info['recommend_data']) ? json_encode($info['recommend_data']) : $info['recommend_data'];
        $recommendLog = $info;
        $recommendLog['recommend_data'] = $oldRecommendData;
        $recommendLog['recommend_time'] = $info['create_time'];
        $recommendLog['create_time'] = $currentTime;
        $RecommendLogModel = new RecommendLogModel();
        $logRes = $RecommendLogModel->insert($recommendLog);
        if(!$logRes) {
            Db::rollback();
            return outputError('保存推荐日志数据失败');
        }

        Db::commit();
        return outputSuccess('编辑成功');

    }

    public function checkRecommendDataByType($recommendDataIds, $recommendType) {
        if(empty($recommendDataIds)) {
            return outputError('推荐数据IDS为空');
        }

        $dataName = '';
        if($recommendType == 1) {
            $dataName = '商品分类';
            $goodsClassService = new GoodsClassService();
            $dataList = $goodsClassService->getListByIds($recommendDataIds);
        }else if($recommendType == 2) {
            $dataName = '商品';
            $goodsService = new GoodsService();
            $dataList = $goodsService->getListByIds($recommendDataIds);
        }else if($recommendType == 3) {
            $dataName = '商户';
            $MerchantService = new MerchantService();
            $dataList = $MerchantService->getListByIds($recommendDataIds);
        }

        $errorArr = [];
        foreach($recommendDataIds as $id) {
            if(!isset($dataList[$id])) {
                $errorArr[] = $dataName.'数据不存在 id: '.$id.' ';
                continue;
            }

            if($dataList[$id]['status'] <= 0) {
                $errorArr[] = $dataName.'数据状态无效 id: '.$id.' ';
                continue;
            }
        }

        if(!empty($errorArr)) {
            $errorStr = implode('|', $errorArr);
            return outputError($errorStr);
        }

        return outputSuccess('success');
    }


    /**
     * 获取列表 通过 IDS
     * @param $recommendIds
     * @return array
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getListByIds($recommendIds) {
        if(empty($recommendIds)) {
            return [];
        }

        $list = RecommendModel::where('recommend_id', 'in', $recommendIds)->select();
        if($list->isEmpty()) {
            return [];
        }

        $list = $list->toArray();
        $list = arrayMap($list, 'recommend_id');
        return $list;
    }


}