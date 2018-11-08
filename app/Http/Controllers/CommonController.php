<?php
/**
 * Created by PhpStorm.
 * User: liao
 * Date: 18/11/2
 * Time: 上午9:49
 */

namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Banner;
use App\Models\Category;
use App\Models\News;

class CommonController extends Controller
{
   public function __construct()
   {
       //栏目
       $lanmu = Category::where('parent_id','=','0')->get();
       //banner
       $banner = Banner::all();
       //news
       $news = News::where('type','=','1')->take(10)->get();
       $author = Author::where('id','=','1')->first();
       view()->share('common',array(
           'lanmu'=>$lanmu,
           'banner'=>$banner,
           'common_news'=>$news,
           'author'=>$author
       ));
   }
}