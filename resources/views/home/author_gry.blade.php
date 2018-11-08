@extends('home.layouts.app')

@section('title', '个人荣誉')

@section('content')
    <link href="{{URL::asset('/')}}home/neiye.css" rel="stylesheet" type="text/css">
    <div class="main">
        <!--左侧start-->
        <div class="sideleft">
            <div class="lanmu_title">
                <h1>个人荣誉</h1><p class="breadNav">您当前所在位置 &gt; <a href="{{URL::asset('/')}}">首页</a> &gt;<span class="ztcolor">个人荣誉</span></p>
            </div>
            <div class="danye">

                <ul class="picList hide">
                    @foreach($gry as $val)
                    <li>
                        <em><a href="{{URL::asset('/')}}author/gry_show?id={{$val->id}}" target="_blank">
                                <img src="{{URL::asset('/')}}backend/{{$val->img_url}}"></a>
                        </em
                        ><h1>
                            <a href="{{URL::asset('/')}}author/gry_show?id={{$val->id}}" target="_blank" title="{{$val->title}}">{{$val->title}}</a>
                        </h1>
                    </li>
                    @endforeach
                </ul>

              {{--  <div class="page">
                    <i> 22 条记录 1/2 页  <a href="#">下一页</a>     <span class="current">1</span><a href="#">2</a>   </i>
                </div>--}}




            </div>
        </div>
        <!--左侧end-->
    <!--右侧start-->
        <div class="sideright">
            <div class="side_menu">
                <dl>
                    <dt>
                    <h1>书画家</h1></dt>
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

