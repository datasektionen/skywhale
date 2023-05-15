<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title>@yield('title') - Val på Datasektionen</title>

    <!-- Fonts -->
    <script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>

    <!-- Styles -->
    <link href="//aurora.datasektionen.se" rel="stylesheet" type="text/css">
    <link href="/css/app.css" rel="stylesheet" type="text/css">
    <link href="/css/jquery-ui.min.css" rel="stylesheet" type="text/css">

    <link rel="apple-touch-icon" sizes="57x57" href="/images/logos/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/images/logos/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/logos/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/images/logos/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/logos/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/images/logos/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/logos/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/logos/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/logos/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/images/logos/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/logos/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/images/logos/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logos/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#06c">
    <meta name="msapplication-TileImage" content="/images/logos/ms-icon-144x144.png">
    <meta name="theme-color" content="#06c">
    @yield('head-extra')
    <script type="text/javascript">

    window.methone_conf = {
        system_name: "skywhale",
        color_scheme: "lime",
        @if (\Auth::guest())
        login_text: "Logga in",
        login_href: "/login",
        @else
        login_text: "Logga ut",
        login_href: "/logout",
        @endif
        links: [
        {
            str: "Hem",
            href: "/"
        }
        @if (\App\Models\Election::nominateable()->count() > 0)
        ,{
            str: "Nominera",
            href: "/nominate"
        }
        @endif
        @if (\Auth::user())
            @if (\App\Models\Election::open()->count() > 0)
        ,{
            str: "Mina nomineringar",
            href: "/nomination/answer"
        }
            @endif
        ,{
            str: "Inställningar",
            href: "/user/settings"
        }
        @endif
        @if (\Auth::user() && \Auth::user()->id == session('admin'))
        ,{
            str: "Administrera",
            href: "/admin"
        }
        @endif
        ,{
            str: "RSS",
            href: "/rss"
        }
        ]
    };
    </script>
    <script async src="//methone.datasektionen.se/bar.js"></script>
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

    </div>

</body>
</html>
