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

    public function add(MerchantService $MerchantService) {
        $params = $this->request->param();
        $validate = $this->validate($params, 'app\api\validate\Merchant');
        if($validate !== true) {
            return outputError($validate);
        }

        $result = $MerchantService->add($params);
        return $result;
    }

}