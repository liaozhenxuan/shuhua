<div class="headerWrap">

    <div class="header">
        <div class="logo"><a href="{{URL::asset('/')}}"><img src="{{URL::asset('/')}}backend/{{$common['author']->logo}}" alt="{{$common['author']->app_name}}"></a></div>
        <div class="nav">
            <ul>
                @foreach($common['lanmu'] as $lanmu)
                <li>
                    <a href="{{$lanmu->router}}"
                       @if(strpos(url()->full(),$lanmu->sign) !==false)
                                  class="active"
                       @endif
                    >
                        <div>
                            <em>{{$lanmu->first_str}}</em>
                            <span>{{$lanmu->title}}</span>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

    </div>

</div>