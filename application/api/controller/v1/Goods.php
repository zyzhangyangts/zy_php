<?php

namespace app\api\controller\v1;

use app\api\service\MerchantService;
use think\Controller;
use think\facade\Hook;
use think\Request;
use think\response\Json;
use app\api\service\GoodsService;

use app\api\controller\BaseController;

class Goods extends BaseController
{

    /**
     * 创建商品
     * @param GoodsService $GoodsService
     *
     * @return array
     */
    public function add(GoodsService $GoodsService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Goods');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $GoodsService->add($params);
        return $result;
    }

    public function edit(GoodsService $GoodsService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Goods');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $GoodsService->edit($params);
        return $result;
    }

    /**
     * 商品信息
     * @param GoodsService $GoodsService
     * @param goods_id int
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info(GoodsService $GoodsService) {
        $GoodsId = $this->request->param('goods_id/d', 0);
        if($GoodsId <= 0) {
            return outputError('请输入商品ID');
        }

        $info = $GoodsService->info($GoodsId);
        if(empty($info)) {
            return outputError('商品信息不存在');
        }

        return outputSuccess('', $info);
    }

    public function lists(GoodsService $GoodsService) {
        $params = $this->request->param();
        $result = $GoodsService->lists($params);
        return outputSuccess('', $result);
    }

    /**
     * 更改状态
     * @param MerchantService $MerchantService
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus(GoodsService $GoodsService) {
        $GoodsId = $this->request->param('goods_id/d', 0);
        $status = $this->request->param('status/d', 0);
        $result = $GoodsService->setStatus($GoodsId, $status);
        return $result;
    }

}