<?php use App\Helper\AdminChecker;
use App\Models\GameLevel; ?>
@extends('othes.layout') {{-- import layout --}}

@section('title', 'Users') {{-- title  --}}


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
        <button class="backBtn"
            onclick="window.location.href='{{ route('users.scores.show', ['id' => Crypt::encrypt($user->id)]) }}'">
            <i class="fa-solid fa-left-long fa-xl"></i>
        </button>
        <!-- content_wrapper -->
        <div class="content_wrapper">
            <!-- contents -->
            <div class="contents">
                @nav($user->username . ' | ' . $level->novel->novel_name . ' | ' . $level->level_name)

                <div class="content"><!-- table -->
                    <table class="table mt-5 table-striped table-borderless table-hover" id="section_table" border="1">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Started</th>
                                <th>Finished</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr class="align-middle">
                                    <td>{{ $log->status }}</td>
                                    <td>{{ $log->score }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->started_at)->format('h:i:s a M d, Y l') }}</td>
                                    <td>{{ $log->finished_at ? \Carbon\Carbon::parse($log->finished_at)->format('h:i:s a M d, Y l') : 'Did Not Finished' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- table -->
                </div>
            </div>
            <!-- contents -->

            <!-- Chart Section -->
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-center">Attempts per Status</h5>
                        <canvas id="statusPieChart" height="200"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-center">Average Duration per Status (minutes)</h5>
                        <canvas id="durationBarChart" height="200"></canvas>
                    </div>
                </div>
            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // === PIE CHART ===
            const pieCtx = document.getElementById('statusPieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($statusCounts->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($statusCounts->values()) !!},
                        backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545', '#6f42c1']
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // === BAR CHART ===
            const barCtx = document.getElementById('durationBarChart').getContext('2d');
            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($averageDurations->keys()) !!},
                    datasets: [{
                        label: 'Average Duration (minutes)',
                        data: {!! json_encode($averageDurations->values()) !!},
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Minutes'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>

@endsection
{{-- body  --}}
