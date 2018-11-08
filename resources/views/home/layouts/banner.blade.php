<div class="banner">

    <div class="flexslider">
        <ul class="slides">
            @foreach($common['banner'] as $banner)
            <li class="" style="width: 100%; float: left; margin-right: -100%; position: relative; opacity: 0; display: block; z-index: 1;">
                <a href="javascript:void(0);"><img src="{{URL::asset('/')}}backend/{{$banner->img_url}}" draggable="false"></a>
            </li><!--后台注明上传尺寸：1180px*366px-->
            @endforeach

        </ul>

    </div>
    <script type="text/javascript" src="{{URL::asset('/')}}home/jquery.flexslider-min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.flexslider').flexslider({
                directionNav: true,
                pauseOnAction: false
            });
        });
    </script>

</div>