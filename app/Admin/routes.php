<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //add
    $router->resource('category', 'CategoryController', ['except' => ['create']]);

    $router->group(['prefix'=>'manage'],function ($route){
        //banner
        $route->resource('banner','BannerController');
        //个人简介
        //$route->match(['get','post'],'/author','AuthorController@show');

        $route->resource('author', 'AuthorController', ['except' => ['create']]);

        $route->resource('gry', 'GryController');

        $route->resource('news','NewsController');

        $route->resource('footer','FooterController');
    });

    $router->resource('product', 'ProductController');



});
