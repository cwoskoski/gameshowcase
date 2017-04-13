<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'GGMaker Downloads')</title>

        <link href='//fonts.googleapis.com/css?family=Raleway:300,400,600,700|Oswald|Droid+Serif' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('css/fancybox/jquery.fancybox.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="{{ asset('js/jquery.fancybox.pack.js') }}"></script>
        <script src="{{ asset('js/quickpager.js') }}"></script>
        <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('js/bootbox.min.js') }}"></script>
        <script src="{{ asset('js/dropzone.js') }}"></script>
        <script src="//tinymce.cachefly.net/4.2/tinymce.min.js"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        @if(Auth::check() && Auth::user()->access == 'admin')
        <script src="{{ asset('js/admin.js') }}"></script>
        @endif
        @yield('scripts')
    </head>
    <body>

        <header class="header" role="banner">
            <div class="left-slice"></div>
            <div class="right-slice"></div>
            <nav class="navbar" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#primary-nav-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('home') }}"><img src="//ggmaker.com/wp-content/themes/ggmaker/images/ggmaker-mascot-logo.png" /></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div id="primary-nav-collapse" class="collapse navbar-collapse">
                    <div class="menu-header">
                        <ul id="menu-primary" class="menu nav navbar-nav">
                            <li id="menu-item-6" class="menu-item menu-item-type-post_type menu-item-object-page page_item page-item-2 menu-item-6"><a href="//ggmaker.com/">Home</a></li>
                            <li id="menu-item-41" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-41"><a href="//ggmaker.com/products/">Products</a></li>
                            <li id="menu-item-440" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-440"><a href="//ggmaker.com/education/">Education</a></li>
                            <li id="menu-item-451" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-451 current_page_item current-menu-item"><a href="{{ route('home') }}">Resources</a></li>
                            <li id="menu-item-30" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-30"><a href="//ggmaker.com/support/">Support</a></li>
                            <li id="menu-item-77" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-77"><a href="//community.ggmaker.com">Forums</a></li>
                        </ul>
                    </div>
                </div><!-- /.navbar-collapse -->
            </nav>

        </header><!-- #branding -->

        <main role="main" class="main main-home">
            <div class="container">
                <div class="navbar navbar-default navbar-top">
                    <div class="container-fluid">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="/"><strong>Downloads</strong></a>
                        </div>

                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav">
                                <li><a href="{{ route('submissions', ['type' => 'game']) }}">Games</a></li>
                                <li><a href="{{ route('submissions', ['type' => 'resource']) }}">Resources</a></li>
                                <li><a href="{{ route('submissions', ['type' => 'tutorial']) }}">Tutorials</a></li>
                                @if(Auth::check())
                                @endif
                            </ul>

                            <form class="navbar-form navbar-left" action="{{ route('submissions') }}" role="search">
                                <div class="form-group">
                                    <input name="search" type="text" class="form-control" placeholder="Search">
                                </div>
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </form>

                            <ul class="nav navbar-nav navbar-right">
                                @if(Auth::check())
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                            <img src="{{ Auth::user()->avatar() }}" alt="{{ Auth::user()->name }}" class="navbar-avatar" />
                                            {{ Auth::user()->name }} <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="{{ route('submissions', ['mine' => 1]) }}">My Submissions</a></li>
                                            <li>{!! Html::link('/submission/pick', 'Add New Submission') !!}</li>
                                            @if(Auth::user()->access == 'admin')
                                                <li>{!! Html::link('/admin/moderate', 'Pending Approval') !!}</li>
                                            @endif
                                            <li class="divider"></li>
                                            <li>{!!Html::link('http://community.ggmaker.com/index.php?/settings/','Settings',['class'=>''])!!}</li>
                                            <li>{!!Html::link('/auth/logout','Logout',['class'=>''])!!}</li>
                                        </ul>
                                    </li>
                                @else
                                    <li>{!!Html::link('/auth/login','Login',['class'=>''])!!}</li>
                                    <li>{!!Html::link('/auth/register','Register',['class'=>'text-inline'])!!}</li>
                                @endif
                            </ul>
                        </div><!-- /.navbar-collapse -->
                    </div><!-- /.container-fluid -->
                </div>
                @include('flash::message')
                @yield('content')
            </div>
            @yield('after-content')
        </main>

        <footer class="footer">
            <div class="copyright-area">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="green">001 Game Creator / GG Maker</span> &copy; 2001-<?php echo date('Y'); ?>
                        </div>
                        <div class="col-sm-6 text-right">
                            If you have comments or questions, please visit our <a href="//community.ggmaker.com/">forum</a> or <a href="//ggmaker.com/support/contact/">contact us</a>.<br />
                        </div>
                    </div>
                </div>
            </div>
        </footer><!-- #colophon -->
    </body>
</html>