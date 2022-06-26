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
use think\db;

class RecommendService
{
    /**
     * 创建推荐位
     * @param $params
     * @return array
     */
    public function add($params) {
        $RecommendModel = new RecommendModel();
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


}