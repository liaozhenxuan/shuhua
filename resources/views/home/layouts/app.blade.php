<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <title>{{$common['author']->app_name}}-@yield('title')</title>
    <link href="{{URL::asset('/')}}home/common.css" rel="stylesheet" type="text/css">
    <link href="{{URL::asset('/')}}home/index.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{URL::asset('/')}}home/jquery-1.8.0.min.js"></script>
</head>
<body>
<div class="container">

    @include('home.layouts.header')

    @include('home.layouts.banner')

    @yield('content')

    @include('home.layouts.footer')

</div>

</body>
</html>