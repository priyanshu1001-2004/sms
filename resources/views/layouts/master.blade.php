<!doctype html>
<html lang="en" dir="ltr">

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sash – Bootstrap 5  Admin & Dashboard Template">
    <meta name="author" content="Spruko Technologies Private Limited">
    <meta name="keywords"
        content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/brand/favicon.ico" />

    <!-- TITLE -->
    <title>@yield('title', 'Dashboard')</title>

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- STYLE CSS -->
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="../assets/css/dark-style.css" rel="stylesheet" />
    <link href="../assets/css/transparent-style.css" rel="stylesheet">
    <link href="../assets/css/skin-modes.css" rel="stylesheet" />

    <!--- FONT-ICONS CSS -->
    <link href="../assets/css/icons.css" rel="stylesheet" />

    <!-- COLOR SKIN CSS -->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="../assets/colors/color1.css" />

    <!-- INTERNAL Switcher css -->
    <link href="../assets/switcher/css/switcher.css" rel="stylesheet" />
    <link href="../assets/switcher/demo.css" rel="stylesheet" />

    <!-- COLOR SKIN CSS -->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="../assets/colors/color1.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.css')

</head>




<body class="app sidebar-mini ltr light-mode">

    <div id="global-loader">
        <img src="../assets/images/loader.svg" class="loader-img" alt="Loader">
    </div>

    <div class="page">
        <div class="page-main">
            @include('layouts.header')
            @include('layouts.sidebar')

            <div class="side-app">
                <div class="">
                    @yield('content')
                </div>
            </div>
        </div>


        <div class="modal fade" id="globalConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <div id="modal-icon-container" class="mb-4">
                            <svg id="icon-warning" xmlns="http://www.w3.org/2000/svg" height="60" width="60"
                                viewBox="0 0 24 24">
                                <path fill="#f07f8f"
                                    d="M20.05713,22H3.94287A3.02288,3.02288,0,0,1,1.3252,17.46631L9.38232,3.51123a3.02272,3.02272,0,0,1,5.23536,0L22.6748,17.46631A3.02288,3.02288,0,0,1,20.05713,22Z" />
                                <circle cx="12" cy="17" r="1" fill="#e62a45" />
                                <path fill="#e62a45" d="M12,14a1,1,0,0,1-1-1V9a1,1,0,0,1,2,0v4A1,1,0,0,1,12,14Z" />
                            </svg>
                        </div>

                        <h4 id="modal-title" class="h4 mb-0 mt-3 text-dark">Are you sure?</h4>
                        <p id="modal-message" class="card-text mt-2 text-muted">This action cannot be undone!</p>

                        <div class="mt-5">
                            <button type="button" class="btn btn-light me-2 px-4"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="modal-confirm-btn" class="btn btn-danger px-4">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @include('layouts.footer')


        @include('layouts.js')

        @yield('scripts')

    </div>

</body>

</html>