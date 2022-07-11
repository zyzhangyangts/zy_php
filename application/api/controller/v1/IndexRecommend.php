<?php

namespace app\api\controller\v1;

use think\Controller;
use think\facade\Hook;
use think\Request;
use think\response\Json;
use app\api\service\IndexRecommendService;

use app\api\controller\BaseController;

class IndexRecommend extends BaseController
{

    /**
     * 创建推荐位
     * @param IndexRecommendService $IndexRecommendService
     * {
        "market_id": "1",
        "IndexRecommend_name": "良友菜店",
        "IndexRecommend_photo": "http://aasdf.aads.com/IndexRecommend/photo/asdfsf.jpg",
        "IndexRecommend_score": 3.5,
        "start_delivery_price": 20,
        "delivery_price": "1.5"
        }
     * @return array
     */
    public function add(IndexRecommendService $IndexRecommendService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\IndexRecommend');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $IndexRecommendService->add($params);
        return $result;
    }

    public function edit(IndexRecommendService $IndexRecommendService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\IndexRecommend');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $IndexRecommendService->edit($params);
        return $result;
    }

    /**
     * 推荐位信息
     * @param IndexRecommendService $IndexRecommendService
     * @param index_recommend_id int
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info(IndexRecommendService $IndexRecommendService) {
        $IndexRecommendId = $this->request->param('index_recommend_id/d', 0);
        if($IndexRecommendId <= 0) {
            return outputError('请输入推荐位ID');
        }

        $info = $IndexRecommendService->info($IndexRecommendId);
        if(empty($info)) {
            return outputError('推荐位信息不存在');
        }

        return outputSuccess('', $info);
    }

    /**
     * 列表
     * @param IndexRecommendService $IndexRecommendService
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists(IndexRecommendService $IndexRecommendService) {
        $params = $this->request->param();
        $result = $IndexRecommendService->lists($params);
        return outputSuccess('', $result);
    }

    /**
     * 更改状态
     * @param IndexRecommendService $IndexRecommendService
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus(IndexRecommendService $IndexRecommendService) {
        $IndexRecommendId = $this->request->param('index_recommend_id/d', 0);
        $status = $this->request->param('status/d', 0);
        $result = $IndexRecommendService->setStatus($IndexRecommendId, $status);
        return $result;
    }


}