<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>{{config('app.name')}}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="description" content="EBS" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
        <link rel="shortcut icon" href="{{asset('/media/logos/favicon.ico')}}" />
        <link href="{{asset('/css/pages/login/login-4.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/css/themes/layout/header/base/light.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/css/themes/layout/header/menu/light.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/css/themes/layout/brand/dark.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/css/themes/layout/aside/dark.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/css/app.css')}}" />
	</head>
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<div class="page-loader page-loader-logo">
            {{-- <img alt="{{ config('app.name') }}" src="{{ asset('media/logos/logo-letter-1.png') }}"/> --}}
            <h2>{{ config('app.name') }}</h2>
            <div class="spinner spinner-primary"></div>
        </div>
        <div id="app">
            <index></index>
        </div>
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
		<script src="{{asset('/plugins/global/plugins.bundle.js')}}"></script>
        <script src="{{asset('/plugins/custom/prismjs/prismjs.bundle.js')}}"></script>
        <script src="{{asset('/js/scripts.bundle.js')}}"></script>
        <script src="{{asset('js/app.js')}}" defer></script>
	</body>
</html>
