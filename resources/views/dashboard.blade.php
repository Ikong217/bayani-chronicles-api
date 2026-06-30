<?php use App\Helper\AdminChecker; ?>
@extends('othes.layout') {{-- import layout --}}

@section('title', 'Bayani Chronicles Dashboard') {{-- title  --}}


{{-- head  --}}
@section('head')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/navigation.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endsection


@section('css')

@endsection
{{-- head  --}}



{{-- body  --}}
@section('body')
    style="background-image: url('{{ asset('assets/images') }}/book.jpg');"
@endsection

@section('content')
    <!-- wrapper -->
    <div class="wrapper">
        <!-- content_wrapper -->
        <div class="content_wrapper">


            <!-- contents -->
            <div class="contents">
                @nav('')

                <div class="content">
                    <!-- navigation -->
                    <div class="navigation" onclick="window.location.href='{{ route('users.show') }}'">
                        <div class="grid" style="background-image: url('assets/images/monument.jpg');"></div>
                        <div class="grid">
                            <h4>Users</h4>
                        </div>

                        <div class="backdrop">
                            <h2>VIEW PAGE</h2>
                        </div>
                    </div>
                    <!-- navigation -->

                    @if (AdminChecker::isMeAdmin())
                        <!-- navigation -->
                        <div class="navigation" onclick="window.location.href='{{ route('teachers.show') }}'">
                            <div class="grid" style="background-image: url('assets/images/book.jpg');"></div>
                            <div class="grid">
                                <h4>Teachers</h4>
                            </div>

                            <div class="backdrop">
                                <h2>VIEW PAGE</h2>
                            </div>
                        </div>
                        <!-- navigation -->
                    @endif

                    <!-- navigation -->
                    <div class="navigation" onclick="window.location.href='{{ route('section.show') }}'">
                        <div class="grid" style="background-image: url('assets/images/noli.jpg');"></div>
                        <div class="grid">
                            <h4>Sections</h4>
                        </div>

                        <div class="backdrop">
                            <h2>VIEW PAGE</h2>
                        </div>
                    </div>
                    <!-- navigation -->

                    <!-- navigation -->
                    <div class="navigation" onclick="window.location.href='{{ route('questions.show') }}'">
                        <div class="grid" style="background-image: url('assets/images/jose_rizal.jpg');"></div>
                        <div class="grid">
                            <h4>Questions</h4>
                        </div>

                        <div class="backdrop">
                            <h2>VIEW PAGE</h2>
                        </div>
                    </div>
                    <!-- navigation -->


                    @if (AdminChecker::isMeAdmin())
                        <!-- navigation -->
                        <div class="navigation" onclick="window.location.href='{{ route('admin.audit') }}'">
                            <div class="grid" style="background-image: url('assets/images/book.jpg');"></div>
                            <div class="grid">
                                <h4>Audit Logs</h4>
                            </div>

                            <div class="backdrop">
                                <h2>VIEW PAGE</h2>
                            </div>
                        </div>
                        <!-- navigation -->
                    @endif


                </div>
            </div>
            <!-- contents -->

        </div>
        <!-- content_wrapper -->

    </div>
    <!-- wrapper -->
@endsection


@section('others')

    @include('othes.swal')

@endsection


@section('scripts')
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script></script>
@endsection
{{-- body  --}}
