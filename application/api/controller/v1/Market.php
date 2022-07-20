<?php

namespace app\api\controller\v1;

use think\Controller;
use think\facade\Hook;
use think\Request;
use think\response\Json;
use app\api\service\MarketService;

use app\api\controller\BaseController;

class Market extends BaseController
{

    /**
     * 创建商圈
     * @param MarketService $MarketService
     * {
        "market_id": "1",
        "market_name": "良友菜店",
        }
     * @return array
     */
    public function add(MarketService $MarketService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Market');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $MarketService->add($params);
        return $result;
    }

    public function edit(MarketService $MarketService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Market');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $MarketService->edit($params);
        return $result;
    }

    /**
     * 商圈信息
     * @param MarketService $MarketService
     * @param Market_id int
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info(MarketService $MarketService) {
        $MarketId = $this->request->param('Market_id/d', 0);
        if($MarketId <= 0) {
            return outputError('请输入商圈ID');
        }

        $info = $MarketService->info($MarketId);
        if(empty($info)) {
            return outputError('商圈信息不存在');
        }

        return outputSuccess('', $info);
    }

    /**
     * 列表
     * @param MarketService $MarketService
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists(MarketService $MarketService) {
        $params = $this->request->param();
        $result = $MarketService->lists($params);
        return outputSuccess('', $result);
    }

    /**
     * 更改状态
     * @param MarketService $MarketService
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus(MarketService $MarketService) {
        $MarketId = $this->request->param('Market_id/d', 0);
        $status = $this->request->param('status/d', 0);
        $result = $MarketService->setStatus($MarketId, $status);
        return $result;
    }

}