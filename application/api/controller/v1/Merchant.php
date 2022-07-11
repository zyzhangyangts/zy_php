<?php

namespace app\api\controller\v1;

use think\Controller;
use think\facade\Hook;
use think\Request;
use think\response\Json;
use app\api\service\MerchantService;

use app\api\controller\BaseController;

class Merchant extends BaseController
{

    /**
     * 创建商户
     * @param MerchantService $MerchantService
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
    public function add(MerchantService $MerchantService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Merchant');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $MerchantService->add($params);
        return $result;
    }

    public function edit(MerchantService $MerchantService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Merchant');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $MerchantService->edit($params);
        return $result;
    }

    /**
     * 商户信息
     * @param MerchantService $MerchantService
     * @param merchant_id int
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info(MerchantService $MerchantService) {
        $merchantId = $this->request->param('merchant_id/d', 0);
        if($merchantId <= 0) {
            return outputError('请输入商户ID');
        }

        $info = $MerchantService->info($merchantId);
        if(empty($info)) {
            return outputError('商户信息不存在');
        }

        return outputSuccess('', $info);
    }

    /**
     * 列表
     * @param MerchantService $MerchantService
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists(MerchantService $MerchantService) {
        $params = $this->request->param();
        $result = $MerchantService->lists($params);
        return outputSuccess('', $result);
    }

    /**
     * 更改状态
     * @param MerchantService $MerchantService
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus(MerchantService $MerchantService) {
        $merchantId = $this->request->param('merchant_id/d', 0);
        $status = $this->request->param('status/d', 0);
        $result = $MerchantService->setStatus($merchantId, $status);
        return $result;
    }

}