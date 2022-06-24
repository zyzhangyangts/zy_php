<?php

namespace app\api\controller\v1;

use app\api\service\MerchantService;
use think\Controller;
use think\facade\Hook;
use think\Request;
use think\response\Json;
use app\api\service\GoodsClassService;

use app\api\controller\BaseController;

class GoodsClass extends BaseController
{

    /**
     * 创建商品分类
     * @param GoodsClassService $GoodsClassService
     * {
        "market_id": "1",
        "merchant_name": "良友菜店",
        "merchant_photo": "http://aasdf.aads.com/merchant/photo/asdfsf.jpg",
        "merchant_score": 3.5,
        "start_delivery_price": 20,
        "delivery_price": "1.5"
        }
     * @return array
     */
    public function add(GoodsClassService $GoodsClassService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\GoodsClass');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $GoodsClassService->add($params);
        return $result;
    }

    public function edit(GoodsClassService $GoodsClassService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Merchant');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $GoodsClassService->edit($params);
        return $result;
    }

    /**
     * 商品分类信息
     * @param GoodsClassService $GoodsClassService
     * @param goods_class_id int
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info(GoodsClassService $GoodsClassService) {
        $goodsClassId = $this->request->param('goods_class_id/d', 0);
        if($goodsClassId <= 0) {
            return outputError('请输入商品分类ID');
        }

        $info = $GoodsClassService->info($goodsClassId);
        if(empty($info)) {
            return outputError('商品分类信息不存在');
        }

        return outputSuccess('', $info);
    }

    public function list(GoodsClassService $GoodsClassService) {
        $params = $this->request->param();
        $result = $GoodsClassService->list($params);
        return outputSuccess('', $result);
    }

    /**
     * 更改状态
     * @param MerchantService $MerchantService
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus(GoodsClassService $GoodsClassService) {
        $goodsClassId = $this->request->param('goods_class_id/d', 0);
        $status = $this->request->param('goods_class_id/d', 0);
        $result = $GoodsClassService->setStatus($goodsClassId, $status);
        return $result;
    }

}