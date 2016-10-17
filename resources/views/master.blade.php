<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - Val på Datasektionen</title>

    <!-- Fonts -->
    <script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet" type="text/css">
    <link href="/css/jquery-ui.min.css" rel="stylesheet" type="text/css">

    <meta name="theme-color" content="#06c" />
    @yield('head-extra')
</head>
<body>
    <div id="top-bar-parent"></div>
    <div id="top-bar-push"></div>
    <div class="header">    
        <div class="center">
            @yield('action-button')
            <h1><span>@yield('title', 'Rubrik')</span></h1>
        </div>
    </div>
    <div class="wrapper">
        @include('includes.messages')
        @yield('content')
    </div>

    <script type="text/javascript">

    window.tbaas_conf = {
        system_name: "test",
        target_id: "top-bar-parent",
        primary_color: "#07d",
        secondary_color: "white",
        bar_color: "#06c",
        @if (\Auth::guest())
            login_text: "Logga in",
            login_href: "/login",
        @else 
            login_text: "Logga ut",
            login_href: "/logout",
        @endif
        topbar_items: [
        {
            str: "Hem",
            href: "/"
        }
        ,{
            str: "Nominera",
            href: "/nominate"
        }
        @if (\Auth::user())
        ,{
            str: "Mina nomineringar",
            href: "/nomination/answer"
        }
        @endif
        @if (\Auth::user() && \Auth::user()->isAdmin())
        ,{
            str: "Administrera",
            href: "/admin"
        }
        @endif
        ]
    };
    

    </script>
    <script async src="//methone.datasektionen.se"></script>
</body>
</html>
