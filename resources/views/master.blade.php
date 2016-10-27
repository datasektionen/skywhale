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
    <link href="//aurora.datasektionen.se" rel="stylesheet" type="text/css">
    <link href="/css/app.css" rel="stylesheet" type="text/css">
    <link href="/css/jquery-ui.min.css" rel="stylesheet" type="text/css">

    <meta name="theme-color" content="#06c" />
    @yield('head-extra')
    <script type="text/javascript">

    window.tbaas_conf = {
        system_name: "test",
        target_id: "methone-container-replace",
        primary_color: "#06b",
        secondary_color: "white",
        bar_color: "#05a",
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
</head>
<body>
    <div id="methone-container-replace"></div>
    <div id="application" class="dark-blue">
        <header>
            <div class="header-inner">
                <div class="row">
                    <div class="header-left col-md-2">
                        {{--<a href="/">&laquo; Tillbaka</a>--}}
                    </div>
                    <div class="col-md-8">
                        <h2>@yield('title')</h2>
                    </div>
                    <div class="header-right col-md-2">
                        {{--<span class="visible-lg-inline">Se p&aring;</span>--}}
                        @yield('status')
                        @yield('action-button')
                        {{--<a href="https://github.com/datasektionen/skywhale" class="primary-action">GitHub</a>--}}
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </header>
        <div id="content">

            @include('includes.messages')
            @yield('content')
            <div class="clear"></div>
        </div>
        <footer>
            <a href="http://github.com/datasektionen/skywhale" class="footer">&#10084;</a>
        </footer>

    </div>
        {{--
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
    --}}
    
</body>
</html>
