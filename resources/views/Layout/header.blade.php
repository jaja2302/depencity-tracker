<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dtracker Dashboard | SSMS</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('img/logo-SSS.png')}}" type="image/x-icon">
    <!-- Custom styles -->
    <link rel="stylesheet" href="{{asset('elegant/css/style.min.css')}}">

    <script src="https://kit.fontawesome.com/3f9c068564.js" crossorigin="anonymous"></script>
</head>


<body>
    <div class="layer"></div>
    <!-- ! Body -->
    <a class="skip-link sr-only" href="#skip-target">Skip to content</a>
    <div class="page-flex">
        <!-- ! Sidebar -->
        <aside class="sidebar">


            <div class="sidebar-start">
                <div class="sidebar-head">
                    <a href="{{ route('dashboard') }}" class="logo-wrapper" title="Home">
                        <span class="sr-only">Home</span>
                        <!-- <span class="icon logo" aria-hidden="true"></span> -->
                        <img src="{{ asset('img/logo-SSS.png') }}" style="height: 100%; width: 100%">
                        <div class="logo-text">
                            <span class="logo-title">Welcome</span>
                            <span class="logo-subtitle">Dashboard</span>
                        </div>

                    </a>
                    <button class="sidebar-toggle transparent-btn" title="Menu" type="button">
                        <span class="sr-only">Toggle menu</span>
                        <span class="icon menu-toggle" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="sidebar-body">
                    <ul class="sidebar-body-menu">
                        <li>
                            <a class="active" href="{{ route('dashboard') }}"><span class="icon home" aria-hidden="true"></span>Dashboard</a>
                        </li>
                        <li>

                            <ul class="cat-sub-menu">
                                <li>
                                    <a href="posts.html">All Posts</a>
                                </li>
                                <li>
                                    <a href="new-post.html">Add new post</a>
                                </li>
                            </ul>
                        </li>

                    </ul>


                    <span class="system-menu__title">system</span>
                    <ul class="sidebar-body-menu">
                        <li>
                            <a class="danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i data-feather="log-out" aria-hidden="true"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                                @csrf
                            </form>
                        </li>

                    </ul>



                </div>
            </div>

        </aside>


        <div class="main-wrapper">
            <!-- ! Main nav -->
            <nav class="main-nav--bg">
                <div class="container main-nav">
                    <div class="main-nav-start">
                        <div class="search-wrapper">
                            <i data-feather="search" aria-hidden="true"></i>

                            <input type="text" placeholder="Selamat Datang {{ session('user_name') }} " required>
                        </div>
                    </div>
                    <div class="main-nav-end">
                        <button class="sidebar-toggle transparent-btn" title="Menu" type="button">
                            <span class="sr-only">Toggle menu</span>
                            <span class="icon menu-toggle--gray" aria-hidden="true"></span>
                        </button>
                        <button class="theme-switcher gray-circle-btn" type="button" title="Switch theme">
                            <span class="sr-only">Switch theme</span>
                            <i class="sun-icon" data-feather="sun" aria-hidden="true"></i>
                            <i class="moon-icon" data-feather="moon" aria-hidden="true"></i>
                        </button>


                    </div>
                </div>
            </nav>


            <main class="main users chart-page" id="skip-target">