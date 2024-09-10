<!DOCTYPE html>
<html lang="en">

<head>

    <!-- meta tags -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('school/assets/img/favicon.png') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- title of current page -->

    <title>{{ config('app.name', 'Online registration') }}</title>

    <!-- Bootstrap Styles-->
    <link href=" {{ asset('school/assets/css/bootstrap.css') }} " rel="stylesheet" />

    <!-- datables style-->
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.2/b-2.0.0/b-colvis-2.0.0/b-html5-2.0.0/b-print-2.0.0/datatables.min.css" />


    <!-- FontAwesome Styles-->
    <link href="{{ asset('school/assets/font-awesome/css/all.css') }}" rel="stylesheet" />

    <!-- Google Fonts styles-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <!-- Fonts and icons Google icons-->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />

    <!-- boxicons style-->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <link href="{{ asset('school/assets/css/toastr.min.css') }}" rel="stylesheet" />

    <!-- CSS custom style-->
    <link href="{{ asset('school/assets/css/style.css') }}" rel="stylesheet" />



</head>

<body>
