<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;


class AuthorController extends Controller
{
    public function index(){
        return redirect('/admin/manage/author/1/edit');
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('个人简介')
                ->description('编辑')
                ->body($this->form()->edit($id));
        });
    }

    private function form()
    {
        return Admin::form(Author::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉`列表`按钮
                $tools->disableList();
                // 去掉`删除`按钮
                $tools->disableDelete();
                // 去掉`查看`按钮
                $tools->disableView();
            });
            $form->footer(function ($footer) {
                // 去掉`查看`checkbox
                $footer->disableViewCheck();
                // 去掉`继续编辑`checkbox
                $footer->disableEditingCheck();
                // 去掉`继续创建`checkbox
                $footer->disableCreatingCheck();

            });
            $form->text('app_name','网站名设置');
            $form->image('logo','网站logo设置')->help('图片像素为:434px * 97px');
            $form->image('img_url','个人照片')->help('图片像素为:494px * 334px');
            $form->textarea('text', '个人简介')->rows(20);
        });
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }

}
