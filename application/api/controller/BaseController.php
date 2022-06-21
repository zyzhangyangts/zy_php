<?php

namespace app\api\controller;

use app\common\model\User;
use think\Controller;

/**
 * 所有控制器的基类，必须继承
 */
abstract class BaseController extends Controller
{
    //用户信息
    protected $user;

    // 验证失败是否抛出异常
    //protected $failException = true;

}
