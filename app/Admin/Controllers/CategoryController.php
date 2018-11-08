<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Form;
use App\Models\Category;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;


class CategoryController extends Controller
{
    use HasResourceActions;
    public function index(Content $content)
    {

        return $content
            ->header('分类管理')
            ->description('网站栏目、分类管理')
            ->row(function (Row $row) {
                $row->column(5, $this->treeView()->render());
                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('category'));
                    $form->select('parent_id', '父级分类')->options(Category::selectOptions(null,'顶级分类'));
                    $form->text('title', '标题')->rules('required');
                    $form->text('first_str', '标题首字母');
                    $form->text('router', '路由');
                    $form->text('sign', '标记');
                    $form->hidden('_token')->default(csrf_token());
                    $column->append((new Box('新增', $form))->style('success'));
                });
            });
    }

    private function treeView()
    {
        return Category::tree(function (Tree $tree) {
            $tree->disableCreate();
            $tree->branch(function ($branch) {
                //dd($branch);
                $payload = "<strong>{$branch['title']}</strong>";
                if (!isset($branch['children'])) {

                    //$payload .= "&nbsp;&nbsp;&nbsp;<a href=\"##\" class=\"dd-nodrag\">123</a>";
                }

                return $payload;
            });
        });
    }

    public function show($id)
    {
        return redirect()->route('menu.edit', ['id' => $id]);
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.menu'))
            ->description(trans('admin.edit'))
            ->row($this->form()->edit($id));
    }

    public function form()
    {
        $form = new Form(new Category());
        $form->display('id', 'ID');
        $form->select('parent_id', '父级分类')->options(Category::selectOptions());
        $form->text('title', '标题')->rules('required');
        $form->text('router', '路由');
        $form->text('first_str', '标题首字母');
        $form->text('sign', '标记');
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));
        return $form;
    }

    public function store()
    {
        return $this->form()->store();
    }
}
