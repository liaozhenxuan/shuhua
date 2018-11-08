<?php
/**
 * Created by PhpStorm.
 * User: liao
 * Date: 18/11/2
 * Time: 上午9:49
 */

namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Category;
use App\Models\Gry;
use App\Models\News;
use App\Models\Product;
use Illuminate\Http\Request;

class IndexController extends CommonController
{
    public function index()
    {
        $author = Author::find(1);
        //作品
        $products = Product::where('id', '>', 0)->orderBy('created_at')->take(20)->get();
        //新闻资讯
        $news = News::where('type', '=', '1')->take(5)->get();
        //常识
        $changshi = News::where('type', '=', '2')->take(5)->get();

        $data = [
            'author' => $author,
            'products' => $products,
            'news' => $news,
            'changshi' => $changshi,
        ];

        return view('home.index', $data);
    }

    public function author()
    {
        //分类
        $fenlei = Category::where('parent_id', '=', '2')->get();
        //个人简介
        $author = Author::where('id', '=', '1')->first();
        $data = [
            'fenlei' => $fenlei,
            'author' => $author
        ];
        return view('home.author', $data);
    }

    //个人荣誉
    public function author_gry()
    {
        //分类
        $fenlei = Category::where('parent_id', '=', '2')->get();
        //荣誉
        $gry = Gry::where('id', '>', '0')->take(20)->get();
        $data = [
            'fenlei' => $fenlei,
            'gry' => $gry
        ];
        return view('home.author_gry', $data);
    }

    public function author_gry_show(Request $request)
    {
        //分类
        $fenlei = Category::where('parent_id', '=', '2')->get();
        $id = $request->get('id');
        $gry = Gry::where('id', '=', $id)->first();
        //dd(Gry::where('id', '>', $id)->min('id'));
        if (!empty($gry)) {
            $pre = Gry::find(Gry::where('id', '<', $id)->max('id'));
            $next = Gry::find(Gry::where('id', '>', $id)->min('id'));
            $data = [
                'fenlei' => $fenlei,
                'gry' => $gry,
                'pre' => $pre,
                'next' => $next
            ];
            return view('home.author_gry_show', $data);
        }else{
            return redirect('/');
        }
    }

    public function product(Request $request)
    {
        $where = [];
        $type = $request->get('type');
        if ($type){
               $where['type'] = $type;
        }

        $fenlei = Category::where('parent_id', '=', '4')->get();
        $product = Product::where($where)->get();
        $data = [
            'fenlei' => $fenlei,
            'product'=>$product
        ];
        return view('home.product',$data);
    }

    public function product_show(Request $request)
    {
        $fenlei = Category::where('parent_id', '=', '4')->get();
        $id = $request->get('id');
        $product = Product::where('id', '=', $id)->first();
        if (!empty($product)) {
            $pre = Product::find(Product::where('id', '<', $id)->max('id'));
            $next = Product::find(Product::where('id', '>', $id)->min('id'));
            $data = [
                'fenlei' => $fenlei,
                'product' => $product,
                'pre' => $pre,
                'next' => $next
            ];
            return view('home.product_show', $data);
        }else {
            return redirect('/');
        }
    }

    public function news(Request $request)
    {
        $type = $request->get('type');
        $where['type']='1';
        if ($type && $type=='2'){
            $where['type']='2';
        }
        $fenlei = Category::where('parent_id', '=', '5')->get();
        $news = News::where($where)->get();
        $data = [
            'fenlei' => $fenlei,
            'news' => $news
        ];
        return view('home.news', $data);
    }

    public function news_show(Request $request)
    {

        $id = $request->get('id');
        $news = News::where('id', '=', $id)->first();
        if (!empty($news)) {
            $pre = News::find(News::where('id', '<', $id)->max('id'));
            $next = News::find(News::where('id', '>', $id)->min('id'));
            $fenlei = Category::where('parent_id', '=', $news->type)->get();
            $data = [
                'fenlei' => $fenlei,
                'news' => $news,
                'pre' => $pre,
                'next' => $next
            ];
            return view('home.news_show', $data);
        }else {
            return redirect('/');
        }
    }
}