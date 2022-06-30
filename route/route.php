<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

Route::get('v1/test', 'api/v1.Test/index');

Route::group('', function () {
    Route::group('cms', function () {
        // 账户相关接口分组
        Route::group('user', function () {
            // 登陆接口
            Route::post('login', 'api/cms.User/userLogin');
            // 刷新令牌
            Route::get('refresh', 'api/cms.User/refreshToken');
            // 查询自己拥有的权限
            Route::get('permissions', 'api/cms.User/getAllowedApis');
            // 注册一个用户
            Route::post('register', 'api/cms.User/register');
            // 查询自己信息
            Route::get('information', 'api/cms.User/getInformation');
            // 用户更新信息
            Route::put('', 'api/cms.User/update');
            // 修改自己密码
            Route::put('change_password', 'api/cms.User/changePassword');
        });
        // 管理类接口
        Route::group('admin', function () {
            // 查询所有可分配的权限
            Route::get('permission', 'api/cms.Admin/getAllPermissions');
            // 查询所有用户
            Route::get('users', 'api/cms.Admin/getAdminUsers');
            // 修改用户密码
            Route::put('user/:id/password', 'api/cms.Admin/changeUserPassword');
            // 删除用户
            Route::delete('user/:id', 'api/cms.Admin/deleteUser');
            // 更新用户信息
            Route::put('user/:id', 'api/cms.Admin/updateUser');
            // 查询所有权限组
            Route::get('group/all', 'api/cms.Admin/getGroupAll');
            // 新增权限组
            Route::post('group', 'api/cms.Admin/createGroup');
            // 查询指定分组及其权限
            Route::get('group/:id', 'api/cms.Admin/getGroup');
            // 更新一个权限组
            Route::put('group/:id', 'api/cms.Admin/updateGroup');
            // 删除一个分组
            Route::delete('group/:id', 'api/cms.Admin/deleteGroup');
            // 删除多个权限
            Route::post('permission/remove', 'api/cms.Admin/removePermissions');
            // 分配多个权限
            Route::post('permission/dispatch/batch', 'api/cms.Admin/dispatchPermissions');

        });
        // 日志类接口
        Route::group('log', function () {
            Route::get('', 'api/cms.Log/getLogs');
            Route::get('users', 'api/cms.Log/getUsers');
            Route::get('search', 'api/cms.Log/getUserLogs');
        });
        //上传文件类接口
        Route::post('file', 'api/cms.File/postFile');
    });
    Route::group('v1', function () {
        Route::group('book', function () {
            // 查询所有图书
            Route::get('', 'api/v1.Book/getBooks');
            // 新建图书
            Route::post('', 'api/v1.Book/create');
            // 查询指定bid的图书
            Route::get(':bid', 'api/v1.Book/getBook');
            // 搜索图书

            // 更新图书
            Route::put(':bid', 'api/v1.Book/update');
            // 删除图书
            Route::delete(':bid', 'api/v1.Book/delete');

            // 新建图书
            Route::post('add', 'api/v1.Book/add');
        });

        // 商户创建
        Route::post('merchant/add', 'api/v1.Merchant/add');
        // 商户编辑
        Route::post('merchant/edit', 'api/v1.Merchant/edit');
        // 商户创建
        Route::get('merchant/info', 'api/v1.Merchant/info');
        // 商户创建
        Route::get('merchant/list', 'api/v1.Merchant/list');
        // 商户更改状态
        Route::get('merchant/setStatus', 'api/v1.Merchant/setStatus');


        // 商品分类创建
        Route::post('goods_class/add', 'api/v1.GoodsClass/add');
        // 商品分类编辑
        Route::post('goods_class/edit', 'api/v1.GoodsClass/edit');
        // 商品分类创建
        Route::get('goods_class/info', 'api/v1.GoodsClass/info');
        // 商品分类创建
        Route::get('goods_class/list', 'api/v1.GoodsClass/list');
        // 商品分类更改状态
        Route::get('goods_class/setStatus', 'api/v1.GoodsClass/setStatus');


        // 商品创建
        Route::post('goods/add', 'api/v1.Goods/add');
        // 商品编辑
        Route::post('goods/edit', 'api/v1.Goods/edit');
        // 商品创建
        Route::get('goods/info', 'api/v1.Goods/info');
        // 商品创建
        Route::get('goods/list', 'api/v1.Goods/list');
        // 商品更改状态
        Route::get('goods/setStatus', 'api/v1.Goods/setStatus');

        // 推荐位创建
        Route::post('recommend/add', 'api/v1.Recommend/add');
        // 推荐位编辑
        Route::post('recommend/edit', 'api/v1.Recommend/edit');
        // 推荐位信息
        Route::get('recommend/info', 'api/v1.Recommend/info');
        // 推荐位创建
        Route::get('recommend/list', 'api/v1.Recommend/list');
        // 推荐位更改状态
        Route::get('recommend/setStatus', 'api/v1.Recommend/setStatus');

        // 推荐位.添加推荐明细
        Route::post('recommend/addItem', 'api/v1.Recommend/addItem');

        // 页面推荐-创建
        Route::post('index_recommend/add', 'api/v1.IndexRecommend/add');
        // 页面推荐-编辑
        Route::post('index_recommend/edit', 'api/v1.IndexRecommend/edit');
        // 页面推荐-信息
        Route::get('index_recommend/info', 'api/v1.IndexRecommend/info');
        // 页面推荐-列表
        Route::get('index_recommend/list', 'api/v1.IndexRecommend/list');
        // 页面推荐-更改状态
        Route::get('index_recommend/setStatus', 'api/v1.IndexRecommend/setStatus');

    });
})->middleware(['Authentication', 'ReflexValidate'])->allowCrossDomain();

