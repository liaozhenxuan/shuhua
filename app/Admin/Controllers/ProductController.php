<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use App\Models\Category;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;


class ProductController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('作品管理')
            ->description('作品列表')
            ->body($this->grid()->render());
    }

    private function grid()
    {
        $zuoping = Category::where('sign','=','product')->first();
        $types = Category::where('parent_id','=',$zuoping->id)->get();
        $type_options = [];
        foreach ($types as $type){
            $type_options[$type->id] = $type->title;
        }
        return Admin::grid(Product::class, function (Grid $grid) use ($type_options){
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
            $grid->size('规格');
            $grid->hit('点击量');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建作品');
            $content->description('创建');
            $content->body($this->form());
        });
    }

    private function form()
    {
        $zuoping = Category::where('sign','=','zuoping')->first();
        $types = Category::where('parent_id','=',$zuoping->id)->get();
        $type_options = [];
        foreach ($types as $type){
            $type_options[$type->id] = $type->title;
        }

        return Admin::form(Product::class,function (Form $form) use ($type_options) {
            $form->footer(function ($footer) {
                // 去掉`查看`checkbox
                $footer->disableViewCheck();
                // 去掉`继续编辑`checkbox
                $footer->disableEditingCheck();
                // 去掉`继续创建`checkbox
                $footer->disableCreatingCheck();

            });
            $form->display('id','ID');
            $form->text('title','标题')->required();
            $form->image('img_url','缩略图')->help('图片像素180px * 240px')->required();
            $form->image('img','作品图片')->required();
            $form->select('type', '作品类型')->options($type_options)->required();
            $form->text('size','规格');
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
            $content->header('编辑作品');
            $content->description('编辑');
            $content->body($this->form()->edit($id));
        });
    }


}
