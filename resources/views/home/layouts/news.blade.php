<div class="tj_news">
    <dl>
        <dt>最新动态</dt>
        @foreach($common['common_news'] as $new)
        <dd>
            <a href="http://www.wangzhenqian.com/index.php/News/show/aid/247" target="_blank">{{$new->title}}</a>
        </dd>
        @endforeach
    </dl>
</div>