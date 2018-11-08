@extends('home.layouts.app')

@section('title', '个人荣誉详细')

@section('content')
    <link href="{{URL::asset('/')}}home/neiye.css" rel="stylesheet" type="text/css">
    <div class="main">
        <!--左侧start-->
        <div class="sideleft">
            <div class="lanmu_title">
                <h1>个人荣誉</h1><p class="breadNav">您当前所在位置 &gt; <a href="{{URL::asset('/')}}">首页</a> &gt;<span class="ztcolor">个人荣誉</span></p>&gt; <span class="ztcolor">荣誉详细</span></p>
            </div>
            <div class="news_show">
                <div class="n_s_title"><p>{{$gry->title}}</p></div>
                <div class="n_s_xinxi"><p>发布时间：{{ date('Y-m-d',strtotime($gry->created_at))}} 点击次数：<span class="ztcolor">{{$gry->hit}}</span></p></div>
                <div class="n_s_text">
                    <p></p><p>
                        <br>
                    </p>
                    <p style="text-align:center;">
                        <img src="{{URL::asset('/')}}backend/{{$gry->img_url}}" alt="">
                    </p>
                    <p style="text-align:center;">
                        <br>
                    </p>
                    <p style="text-align:center;">
                        <br>
                    </p><p></p>
                </div>

                <div class="switch">
                    @if($pre)
                    <p><span>上一条：</span><a href="{{URL::asset('/')}}author/gry_show?id={{$pre->id}}">{{$pre->title}}</a></p>
                    @endif
                    @if($next)
                    <p><span>下一条：</span><a href="{{URL::asset('/')}}author/gry_show?id={{$next->id}}">{{$next->title}}</a></p>
                    @endif
                </div>

               {{-- <div class="fanhui"><span><input type="button" onclick="javascript:window.opener=null;window.open(&#39;&#39;,&#39;_self&#39;);window.close();" value="关闭"></span></div>--}}
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

