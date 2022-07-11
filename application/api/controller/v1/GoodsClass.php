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
        "goods_class_name":"菌菇类",
        "parent_id":1,
        "photo_url":"http://api.admin.zy.com/uploads/20220622/3c11e9f831a4ea73e21b6d62b99ed57f.jpeg"
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
        $validate = $this->validate($params, 'app\api\validate\GoodsClass');
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

    public function lists(GoodsClassService $GoodsClassService) {
        $params = $this->request->param();
        $result = $GoodsClassService->lists($params);
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
        $status = $this->request->param('status/d', 0);
        $result = $GoodsClassService->setStatus($goodsClassId, $status);
        return $result;
    }

}