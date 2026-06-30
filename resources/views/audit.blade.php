<?php use App\Helper\AdminChecker;
use App\Models\GameLevel; ?>
@extends('othes.layout') {{-- import layout --}}

@section('title', 'Audit Logs') {{-- title  --}}


{{-- head  --}}
@section('head')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css" rel="stylesheet" />

    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/af-2.7.0/b-3.2.4/b-html5-3.2.4/date-1.5.6/r-3.0.5/datatables.min.css"
        rel="stylesheet" integrity="sha384-PEtbirWf6d/J8jRJhnuVK3eE/ezCiRaLlGTWvvZmfMb2DKaJyknWJ3OhNDxjAlrk"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/navigation.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endsection


@section('css')

@endsection
{{-- head  --}}



{{-- body  --}}
@section('content')

    <!-- wrapper -->
    <div class="wrapper">
        <button class="backBtn" onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="fa-solid fa-left-long fa-xl"></i>
        </button>
        <!-- content_wrapper -->
        <div class="content_wrapper">
            <!-- contents -->
            <div class="contents">
                @nav('Audit Logs')

                <div class="content"><!-- table -->
                    <table class="table mt-5 table-striped table-borderless table-hover" id="section_table" border="1">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Medium</th>
                                <th>Activity</th>
                                <th>Person</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr class="align-middle">
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->status }}</td>
                                    <td>{{ '[' . $log->ip . '] ' . $log->device }}</td>
                                    <td>{{ $log->action . '=>' . $log->response }}</td>
                                    <td>{{ ($log->type ?? '') . ' -- ' . ($log->username ?? '') }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <!-- table -->
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="{{ asset('assets/js/table.js') }}"></script>

@endsection
{{-- body  --}}
