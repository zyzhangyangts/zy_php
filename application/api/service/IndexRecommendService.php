<?php
/**
 * 推荐位 service
 * @author zy
 */

namespace app\api\service;


use think\facade\Config;
use think\facade\Request;
use UnexpectedValueException;
use app\api\model\IndexRecommendModel;
use app\api\service\GoodsClassService;
use app\api\service\GoodsService;
use app\api\service\MerchantService;
use think\db;

class IndexRecommendService
{
    /**
     * 创建推荐位
     * @param $params
     * @return array
     */
    public function add($params) {
        $IndexRecommendModel = new IndexRecommendModel();
        $params['status'] = 1;
        $params['create_time'] = date('Y-m-d H:i:s');
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $IndexRecommendModel->insert($params);
        if(!$res) {
            return outputError('创建推荐位失败');
        }

        return outputSuccess('创建推荐位成功');
    }

    public function edit($params) {
        if(!isset($params['index_recommend_id']) || $params['index_recommend_id'] <= 0) {
            return outputError('请输入推荐位ID');
        }

        $IndexRecommendId = intval($params['index_recommend_id']);
        unset($params['index_recommend_id']);
        $info = $this->info($IndexRecommendId);
        if(empty($info)) {
            return outputError('推荐位信息不存在');
        }

        $IndexRecommendModel = new IndexRecommendModel();
        $params['update_time'] = date('Y-m-d H:i:s');
        $res = $IndexRecommendModel->where('index_recommend_id', $IndexRecommendId)->update($params);
        if(!$res) {
            return outputError('编辑失败');
        }

        return outputSuccess('编辑成功');
    }


    /**
     * 获取推荐位信息
     * @param $IndexRecommendId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($IndexRecommendId) {
        $IndexRecommendId = intval($IndexRecommendId);
        if($IndexRecommendId <= 0) {
            return [];
        }

        $IndexRecommendModel = new IndexRecommendModel();
        $info = $IndexRecommendModel->where('index_recommend_id', $IndexRecommendId)->find();
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
        $status = isset($params['status']) ? $params['status'] : -1;

        $IndexRecommendModel = IndexRecommendModel::where('1=1');
        if($status > -1) {
            $IndexRecommendModel->where('status', $status);
        }

        $allData = $IndexRecommendModel->paginate($pageSize, false, ["page" => $page])->order('weight', 'desc')->toArray();
        //var_dump($IndexRecommendModel->getlastsql());exit;
        return $allData;
    }

    /**
     * 设置状态
     * @param $IndexRecommendId
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($IndexRecommendId, $status) {
        $IndexRecommendId = intval($IndexRecommendId);
        if($IndexRecommendId <= 0) {
            return outputError('请输入推荐位ID');
        }

        if(!in_array($status, [1, 0])) {
            return outputError('请输入正确状态');
        }

        $update = [];
        $update['status'] = $status;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = IndexRecommendModel::where('index_recommend_id', $IndexRecommendId)->update($update);
        if($res) {
            return outputSuccess('更改成功');
        }else {
            return outputError('更改失败');
        }

    }

}