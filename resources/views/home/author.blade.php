@extends('home.layouts.app')

@section('title', '个人简介')

@section('content')
    <link href="{{URL::asset('/')}}home/neiye.css" rel="stylesheet" type="text/css">
    <div class="main">
        <!--左侧start-->
        <div class="sideleft">
            <div class="lanmu_title">
                <h1>书画家简介</h1>
                <p class="breadNav">您当前所在位置 &gt; <a href="{{URL::asset('/')}}">首页</a>
                    &gt; <span
                            class="ztcolor">个人简介</span></p>
            </div>
            <div class="danye">
                <p style="text-indent:2em;">
                    <br>
                </p>
                <p style="text-indent:2em;">
                    {{$author->text}}
                </p>
                <p style="text-indent:2em;">
                    <br>
                </p>
                <p style="text-indent:2em;">
                    <br>
                </p>
                <p style="text-indent:2em;">
                    <br>
                </p>
                <p style="text-indent:2em;">
                    <br>
                </p>
                <p style="text-indent:2em;">
                    <br>
                </p></div>
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

