<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use LinCmsTp5\exception\ParameterException;
use think\response\Json;

/**
 * 统一响应包装函数
 * @param $code
 * @param $errorCode
 * @param $data
 * @param $msg
 * @return Json
 */
function writeJson($code, $data, $msg = 'ok', $errorCode = 0)
{
    $data = [
        'code' => $errorCode,
        'result' => $data,
        'message' => $msg
    ];
    return json($data, $code);
}

/**
 * 分页参数处理函数
 * @param int $count
 * @param int $page
 * @return array
 * @throws ParameterException
 */
function paginate(int $count = 10, int $page = 0)
{
    // $count = intval(Request::get('count', $count));
    // $start = intval(Request::get('page', $page));
    // $page = $start;
    $count = $count >= 15 ? 15 : $count;
    $start = $page * $count;

    if ($start < 0 || $count < 0) throw new ParameterException();

    return [$start, $count];
}

/**
 * 权限数组格式化函数
 * @param array $permissions
 * @return array
 */
function formatPermissions(array $permissions)
{
    $groupPermission = [];
    foreach ($permissions as $permission) {
        $item = [
            'permission' => $permission['name'],
            'module' => $permission['module']
        ];
        $groupPermission[$permission['module']][] = $item;
    }
    $result = [];
    foreach ($groupPermission as $key => $item) {
        array_push($result, [$key => $item]);
    }

    return $result;
}

/**
 * 返回输出信息
 * @param string $msg
 * @param array $data
 * @param int $status
 * @return array
 */
function outputMsg($msg = '', $data = array(), $status = 200) {
    $result = array();
    $result['status'] = $status;
    $result['msg'] = $msg;
    $result['data'] = $data;
    return $result;
}

function outputSuccess($msg = '', $data = array(), $status = 200) {
    return outputMsg($msg, $data, $status);
}

function outputError($msg = '', $data = array(), $status = 400) {
    return outputMsg($msg, $data, $status);
}

function isDev() {
    return getenv('ENV') === "dev";
}

function isTest() {
    return getenv('ENV') === "test";
}

function isProd() {
    return getenv('ENV') === "prod";
}

function getEnvName() {
    return getenv('ENV');
}

/**
 * 推荐类型列表
 * @return string[]
 */
function recommendTypeList() {
    $list = [
        1 => '分类',
        2 => '商品',
        3 => '商户',
    ];

    return $list;
}

/**
 * 推荐模式列表
 * @return string[]
 */
function recommendModelList() {
    $list = [
        1 => '普通推荐',
        2 => '联合推荐',
    ];

    return $list;
}

/**
 * 展示类型
 * @return string[]
 */
function showTypeList() {
    $list = [
        1 => '分类金刚位',
        2 => '简要商品',
        3 => '商品列表',
        4 => '商户列表',
        5 => '分类商品列表',
    ];

    return $list;
}

/**
 * 数组 字段映射
 * @param $array
 * @param $field
 * @return array
 */
function arrayMap($array, $field) {
    $list = [];
    foreach($array as $item) {
        $key = isset($item[$field]) ? $item[$field] : '';
        $list[$key] = $item;
    }

    return $list;
}

