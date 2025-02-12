<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title>503 - Val på Datasektionen</title>

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

    window.methone_conf = {
        system_name: "skywhale",
        color_scheme: "dark_blue",

        login_text: "Logga ut",
        login_href: "/logout",
                links: [
        {
            str: "Hem",
            href: "/"
        }
                ,{
            str: "RSS",
            href: "/rss"
        }
        ]
    };
    </script>
    <script defer src="//methone.datasektionen.se/bar.js"></script>
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
                        <h2>Nere för underhåll</h2>
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
            <p>Kommer snart tillbaka! Kanske. Säg till annars.</p>
            <div class="clear"></div>
        </div>
        <footer>
            <a href="http://github.com/datasektionen/skywhale" class="footer">&#10084;</a>
        </footer>

    </div>

</body>
</html>
