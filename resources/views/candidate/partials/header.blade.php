<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('candidates/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{ asset('candidates/vendors/base/vendor.bundle.base.css')}}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('candidates/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('candidates/css/vereficationForm.css')}}">
    <!-- endinject -->
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('adminAssets/assets/img/favicon.png') }}">
    <link href="{{ asset('school/assets/css/toastr.min.css') }}" rel="stylesheet" />
</head>
