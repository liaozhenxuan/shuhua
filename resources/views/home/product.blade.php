@extends('home.layouts.app')

@section('title', '作品集合')

@section('content')
    <link href="{{URL::asset('/')}}home/neiye.css" rel="stylesheet" type="text/css">
    <div class="main">
        <!--左侧start-->
        <div class="sideleft">
            <div class="lanmu_title">
                <h1>作品集</h1>
                <p class="breadNav">您当前所在位置 &gt; <a href="{{URL::asset('/')}}">首页</a>
                    &gt; <span
                            class="ztcolor"><a href="{{URL::asset('/')}}product">作品集</a></span></p>
            </div>
            <div class="zuopin">

                <ul class="picList hide">
                    @foreach($product as $pro)
                    <li>
                        <em><a href="{{URL::asset('/')}}/product/show?id={{$pro->id}}" target="_blank">
                                <img src="{{URL::asset('/')}}backend/{{$pro->img_url}}"></a></em>
                        <h1>
                            <a href="{{URL::asset('/')}}/product/show?id={{$pro->id}}" target="_blank" title="{{$pro->title}}">{{$pro->title}}</a>
                        </h1>
                    </li>
                    <!--后台注明图片上传尺寸：180px*240px-->
                    @endforeach

                </ul>

                {{--<div class="page">
                    <i> 152 条记录 1/8 页 <a
                                href="#">下一页</a>
                        <span class="current">1</span><a
                                href="#">2</a><a
                                href="#">3</a><a
                                href="#">4</a><a
                                href="#">5</a> <a
                                href="#">下5页</a> <a
                                href="#">最后一页</a></i>
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

