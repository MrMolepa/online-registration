<!doctype html>
<html lang="en">

<head>



    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">



    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('adminAssets/assets/vendor/bootstrap/css/bootstrap.min.css') }}">

    <!-- FontAwesome Styles-->
    <link href="{{ asset('adminAssets/assets/vendor/font-awesome/css/all.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('adminAssets/assets/vendor/linearicons/style.css') }}   ">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="{{ asset('adminAssets/assets/css/daterangepicker.css') }}">

    <!-- DATATABLE CSS -->

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('adminAssets/assets/css/main.css') }}">

    <link rel="stylesheet" href=" {{ asset('adminAssets/assets/css/toastr.min.css') }}">

    <!-- GOOGLE FONTS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">



    <!-- ICONS -->
    <!-- <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png"> -->
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('adminAssets/assets/img/favicon.png') }}">
</head>
