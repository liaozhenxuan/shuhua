<div class="foot_wrap">
    <div class="foot_box">
        <div class="w1200">
            <div class="footnav">
                @foreach($common['lanmu'] as $key=>$lanmu)
                    @if($key>1)
                 <a href="{{$lanmu->router}}" target="_self">{{$lanmu->title}}</a>
                    @endif
                @endforeach
            </div>
            <div class="copy"><p>Copyright © {{$common['author']->app_name}} 版权所有</p></div>
        </div>
    </div>
    <div class="foot_bg"></div>
</div>