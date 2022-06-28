<?php

namespace app\api\controller\v1;

use think\Controller;
use think\facade\Hook;
use think\Request;
use think\response\Json;
use app\api\service\RecommendService;

use app\api\controller\BaseController;

class Recommend extends BaseController
{

    /**
     * 创建推荐位
     * @param RecommendService $RecommendService
     * {
        "market_id": "1",
        "Recommend_name": "良友菜店",
        "Recommend_photo": "http://aasdf.aads.com/Recommend/photo/asdfsf.jpg",
        "Recommend_score": 3.5,
        "start_delivery_price": 20,
        "delivery_price": "1.5"
        }
     * @return array
     */
    public function add(RecommendService $RecommendService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Recommend');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $RecommendService->add($params);
        return $result;
    }

    public function edit(RecommendService $RecommendService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Recommend');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $RecommendService->edit($params);
        return $result;
    }

    /**
     * 推荐位信息
     * @param RecommendService $RecommendService
     * @param recommend_id int
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info(RecommendService $RecommendService) {
        $recommendId = $this->request->param('recommend_id/d', 0);
        if($recommendId <= 0) {
            return outputError('请输入推荐位ID');
        }

        $info = $RecommendService->info($recommendId);
        if(empty($info)) {
            return outputError('推荐位信息不存在');
        }

        return outputSuccess('', $info);
    }

    /**
     * 列表
     * @param RecommendService $RecommendService
     * @return array
     * @throws \think\exception\DbException
     */
    public function list(RecommendService $RecommendService) {
        $params = $this->request->param();
        $result = $RecommendService->list($params);
        return outputSuccess('', $result);
    }

    /**
     * 更改状态
     * @param RecommendService $RecommendService
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus(RecommendService $RecommendService) {
        $recommendId = $this->request->param('recommend_id/d', 0);
        $status = $this->request->param('status/d', 0);
        $result = $RecommendService->setStatus($recommendId, $status);
        return $result;
    }

    public function addItem(RecommendService $RecommendService) {
        $params = $this->request->param();
        $result = $RecommendService->addItem($params);
        return $result;
    }

}