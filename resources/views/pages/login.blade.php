<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
    lang="en"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="/public/sneat/"
    data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>{{ __('menu.auth.login') }} | {{ config('app.name') }}</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/sneat/img/favicon/favicon.ico"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="/sneat/vendor/fonts/boxicons.css"/>

    <!-- Core CSS -->
    <link rel="stylesheet" class="template-customizer-core-css" href="/sneat/vendor/css/core.css"/>
    <link rel="stylesheet" class="template-customizer-theme-css"
          href="/sneat/vendor/css/theme-default.css"/>
    <link rel="stylesheet" href="/sneat/css/demo.css"/>

    <!-- Page -->
    <link rel="stylesheet" href="/sneat/vendor/css/pages/page-auth.css"/>
</head>

<body>
<!-- Content -->

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Register -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="{{ route('home') }}" class="app-brand-link gap-2">
                            <img src="/logo-black.png" alt="{{ config('app.name') }}" srcset="" width="75px">
                            <span class="app-brand-text demo text-black fw-bolder ms-2">{{ config('app.name') }}</span>
                        </a>
                    </div>

                    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-center mt-4">
                            <a href="{{ route('auth.google') }}" class="btn btn-info w-100">
                                <img src="/google-logo.png" class="me-2" alt="" width="30">
                                <span>Continue with Google</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>

<!-- / Content -->
</body>
</html>
