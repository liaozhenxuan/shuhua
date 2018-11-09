<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\News;
use App\Models\Product;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use App\Models\Category;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;


class NewsController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('文章管理')
            ->description('文章列表')
            ->body($this->grid()->render());
    }

    private function grid()
    {
        $type_options = [
            '1'=>'新闻资讯',
            '2'=>'书画常识'
        ];
        return Admin::grid(News::class, function (Grid $grid) use ($type_options) {
            $grid->filter(function($filter) use ($type_options){

                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                // 在这里添加字段过滤器
                $filter->like('title', '标题');

                $filter->in('type','类型')->select($type_options);
            });

            $grid->disableExport();
            $grid->id('ID');
            $grid->type('类型')->display(function ($type) use ($type_options){
                return $type_options[$type];
            });
            $grid->title('标题');
            $grid->img_url('图片')->image();
            $grid->text('内容')->display(function ($obj){
                return str_limit($obj, $limit = 20, $end = '...');
            });
            $grid->hit('点击量');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建文章');
            $content->description('创建');
            $content->body($this->form());
        });
    }

    private function form()
    {
        $type_options = [
            '1'=>'新闻资讯',
            '2'=>'书画常识'
        ];

        return Admin::form(News::class,function (Form $form) use ($type_options) {
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
            $form->image('img_url','图片');
            $form->select('type', '作品类型')->options($type_options);
            $form->textarea('text','文章')->rows(20);
            $form->text('order','排序');
        });
    }

    public function store()
    {
        $this->form()->store();
    }

    public function update($id)
    {
        return $this->form($id)->update($id);
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id){
            $content->header('编辑文章');
            $content->description('编辑');
            $content->body($this->form()->edit($id));
        });
    }


}
