@extends('home.layouts.app')

@section('title', '首页')

@section('content')

    <div class="content">
        <!--书画家简介start-->
        <div class="w1200 hide">
            <div class="about_left">
                <a href="{{URL::asset('/')}}author" title="书法家简介"><img src="{{URL::asset('/')}}backend/{{$author->img_url}}" alt="书法家简介"></a></div>
            <div class="about_right">
                <div class="news_title"><h1>书画家简介</h1><em class="more"><a href="{{URL::asset('/')}}author">更多</a></em></div>
                <div class="about_text">
                    <p>  {{str_limit($author->text, $limit = 686, $end = '...')}}</p>
                </div>
            </div>
        </div>
        <!--书画家简介end-->

        <!--作品集start-->
        <div class="zuopin">
            <div class="zptitle"><h1 title="作品集"><img src="{{URL::asset('/')}}home/zptitle.png" alt="作品集"></h1><span></span><em class="more"><a href="http://www.wangzhenqian.com/zuopin.php">更多</a></em></div>

            <div class="zpList">
                <a onmouseup="ISL_StopUp_1()" class="LeftBotton" onmousedown="ISL_GoUp_1()" onmouseout="ISL_StopUp_1()" href="javascript:void(0);" target="_self"></a>
                <div class="pcont" id="ISL_Cont_1">
                    <div class="ScrCont">
                        <div id="List1_1">
                            @foreach($products as $product)
                            <div class="pl">
                                <a href="{{URL::asset('/')}}product/show?id={{$product->id}}" target="_blank">
                                    <span>
                                        <img src="{{URL::asset('/')}}backend/{{$product->img_url}}">
                                    </span>
                                    <h1>{{$product->title}}</h1>
                                </a>
                            </div><!--后台注明上传尺寸：180px*240px-->
                            @endforeach
                        </div>
                        <div id="List2_1">
                            @foreach($products as $product)
                                <div class="pl">
                                    <a href="{{URL::asset('/')}}product/show?id={{$product->id}}" target="_blank">
                                    <span>
                                        <img src="{{URL::asset('/')}}backend/{{$product->img_url}}">
                                    </span><h1>{{$product->title}}</h1>
                                    </a>
                                </div><!--后台注明上传尺寸：180px*240px-->
                            @endforeach
                        </div>
                    </div>
                </div>
                <a onmouseup="ISL_StopDown_1()" class="RightBotton" onmousedown="ISL_GoDown_1()" onmouseout="ISL_StopDown_1()" href="javascript:void(0);" target="_self"></a>
            </div>


            <script type="text/javascript" src="{{URL::asset('/')}}home/casesList.js"></script>
            <script type="text/javascript">picrun_ini()</script>

        </div>
        <!--作品集end-->

        <!--新闻资讯start-->
        <div class="pic_news">
            <div id="slide_x" class="slide_x">
                <div class="box">
                    <ul class="list">
                        <li style="">
                            <a href="#" target="_blank">
                                <img src="{{URL::asset('/')}}home/149084389448018.jpg"></a>
                            <p>画</p>
                        </li><!--后台注明上传尺寸：367px*265px-->
                        <li><a href="#" target="_blank"><img
                                        src="{{URL::asset('/')}}home/149085974470075.jpg"></a>
                            <p>书法</p></li><!--后台注明上传尺寸：367px*265px-->
                        <li><a href="#" target="_blank"><img
                                        src="{{URL::asset('/')}}home/149084430433985.jpg"></a>
                            <p>水墨</p></li><!--后台注明上传尺寸：367px*265px-->  </ul>
                </div>
                <ul class="btn">
                    <li class="b_1">1</li>
                    <li class="b_2 selected">2</li>
                    <li class="b_3">3</li>
                    <!-- <li class="b_4">4</li>
                     <li class="b_4">5</li>-->
                </ul>
                <div class="plus"></div>
                <div class="minus"></div>
            </div>
            <script src="{{URL::asset('/')}}home/jquery.cxslide.min.js"></script>
            <script>$("#slide_x").cxSlide({plus:true,minus:true});</script>
        </div>
        <div class="text_news">
            <div class="news_title"><h1>新闻资讯</h1><em class="more"><a href="{{URL::asset('/')}}news">更多</a></em></div>
            <ul class="newslist fright">
                @foreach($news as $new)
                <li>
                    <a href="{{URL::asset('/')}}news/show?id={{$new->id}}" target="_blank" class="fleft">{{$new->title}}</a>
                    <span class="fright">{{date('Y-m-d',strtotime($new->created_at))}}</span>
                </li>
                @endforeach
            </ul>
        </div>
        <!--新闻资讯end-->
        <!--书画常识start-->
        <div class="text_news fr">
            <div class="news_title"><h1>书画常识</h1><em class="more"><a
                            href="{{URL::asset('/')}}common_sene?type=2">更多</a></em></div>
            <ul class="newslist">
                @foreach($changshi as $value)
                <li>
                    <a href="{{URL::asset('/')}}news/show?type=2&id={{$value->id}}" target="_blank" class="fleft">{{$value->title}}</a>
                    <span class="fright">{{date('Y-m-d',strtotime($new->created_at))}}</span>
                </li>
                @endforeach

            </ul>
        </div>
        <!--书画常识end-->

    </div>
@endsection

