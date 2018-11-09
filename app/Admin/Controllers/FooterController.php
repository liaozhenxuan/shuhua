<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Footer;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;


class FooterController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('底部图设置')
            ->description('底部图列表')
            ->body($this->grid()->render());
    }

    private function grid()
    {
        return Admin::grid(Footer::class, function (Grid $grid) {
            $grid->disableExport();
            $grid->id('ID');
            $grid->title('标题');
            $grid->img_url('图片')->image();
            $grid->order('排序');
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建底部图');
            $content->description('创建');
            $content->body($this->form());
        });
    }

    private function form()
    {
        return Admin::form(Footer::class,function (Form $form) {
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
            $form->image('img_url','图片')->help('图片像素为:368px * 267px');
            $form->text('order','排序');
        });
    }

    public function destroy($id)
    {
        if (Footer::destroy($id)) {
            $data = [
                'status' => true,
                'message' => '删除成功'
            ];
        } else {
            $data = [
                'status' => false,
                'message' => '删除失败',
            ];
        }
        return response()->json($data);
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id){
            $content->header('编辑底部图');
            $content->description('编辑');
            $content->body($this->form()->edit($id));
        });
    }

    public function store()
    {
        $this->form()->store();
    }



}
