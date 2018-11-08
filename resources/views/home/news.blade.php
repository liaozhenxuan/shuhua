@extends('home.layouts.app')

@section('title', '新闻资讯')

@section('content')
    <link href="{{URL::asset('/')}}home/neiye.css" rel="stylesheet" type="text/css">
    <div class="main">
        <!--左侧start-->
        <div class="sideleft">
            <div class="lanmu_title">
                <h1>新闻资讯</h1>
                <p class="breadNav">您当前所在位置 &gt; <a href="{{URL::asset('/')}}">首页</a>
                    &gt; <span
                            class="ztcolor">新闻资讯</span></p>
            </div>
            <div class="news">
                <ul>
                    @foreach($news as $new)
                    <li>
                        <div class="pic"><a href="{{URL::asset('/')}}news/show?id={{$new->id}}" target="_blank">
                                <img src="{{URL::asset('/')}}/backend/{{$new->img_url}}"></a>
                        </div>
                        <div class="text">
                            <h3>
                                <span class="fright">{{ date('Y-m-d',strtotime($new->created_at))}}</span>
                                <a href="{{URL::asset('/')}}news/show?id={{$new->id}}" target="_blank">{{$new->title}}</a>
                            </h3>
                            <p>
                                {{str_limit($new->text, $limit = 230, $end = '...')}}
                                <a class="ztcolor" href="{{URL::asset('/')}}news/show?id={{$new->id}}" target="_blank">详细&gt;&gt;</a></p></div>
                    </li>
                    @endforeach

                </ul>
            </div>
            {{--<div class="page">
                <i> 10 条记录 1/1 页          </i>
            </div>--}}


        </div>
        <!--左侧end-->
        <!--右侧start-->
        <div class="sideright">
            <div class="side_menu">
                <dl>
                    <dt>
                    <h1>快速导航</h1></dt>
                    @foreach($fenlei as $value)
                        <dd><a href="{{$value->router}}" class="on">{{$value->title}}</a></dd>
                    @endforeach
                </dl>
            </div>
            @include('home.layouts.news')
        </div>
        <!--右侧end-->

        <div class="clear"></div><!--清除浮动-->
    </div>
@endsection

