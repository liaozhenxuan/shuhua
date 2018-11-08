<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gry;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;


class GryController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('个人荣誉')
            ->description('个人荣誉列表')
            ->body($this->grid()->render());
    }

    private function grid()
    {
        return Admin::grid(Gry::class, function (Grid $grid) {
            $grid->filter(function($filter){
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                // 在这里添加字段过滤器
                $filter->like('title', '标题');

            });

            $grid->disableExport();
            $grid->id('ID');
            $grid->title('标题');
            $grid->img_url('图片')->image();
            $grid->hit('点击量');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建个人荣誉');
            $content->description('创建');
            $content->body($this->form());
        });
    }

    private function form()
    {
        return Admin::form(Gry::class,function (Form $form) {
            $form->footer(function ($footer) {
                // 去掉`查看`checkbox
                $footer->disableViewCheck();
                // 去掉`继续编辑`checkbox
                $footer->disableEditingCheck();
                // 去掉`继续创建`checkbox
                $footer->disableCreatingCheck();

            });
            $form->display('id','ID');
            $form->text('title','标题');
            $form->image('img_url','图片')->help('上传图片像素180px * 240px');
            $form->text('order','排序');
        });
    }

    public function store()
    {
        $this->form()->store();
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id){
            $content->header('编辑作品');
            $content->description('编辑');
            $content->body($this->form()->edit($id));
        });
    }


}
