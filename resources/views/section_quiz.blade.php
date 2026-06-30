<?php
use App\Helper\AdminChecker;
use App\Models\GameLevel;
?>

@extends('othes.layout')

@section('title', 'Quiz | '.$section->grade_level." - ".$section->section_name)

@section('head')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- DataTables -->
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/af-2.7.0/b-3.2.4/b-html5-3.2.4/date-1.5.6/r-3.0.5/datatables.min.css"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">

    <style>
        table.dataTable thead th,
        table.dataTable tbody td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        table.dataTable,
        table.dataTable th,
        table.dataTable td {
            border: none !important;
        }

        /* 🔹 Chart container size */
        .chart-box {
            height: 300px;
        }
    </style>
@endsection

@section('content')
@include('othes.nav')

<div class="content_wrapper mt-4">
    <div class="card border-0 shadow-sm rounded-3 p-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-light border-0"
                    onclick="window.location.href='{{ route('section.show', ['id' => $section->id]) }}'">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </button>
                <h4 class="fw-semibold text-primary mb-0">
                    <i class="fa-solid fa-list-check me-2"></i> Activity Logs
                </h4>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="logs_table" class="table table-striped align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Username</th>
                        <th>Novel</th>
                        <th>Level</th>
                        <th>Attempts</th>
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quizResults as $result)
                        <tr>
                            <td>{{ $result['username'] }}</td>
                            <td>{{ $result['novel'] }}</td>
                            <td>{{ $result['level'] }}</td>
                            <td>{{ $result['attempts'] }}</td>
                            <td>{{ $result['average'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- <!-- Charts -->
        <div class="row mt-5">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm p-3">
                    <h6 class="text-center mb-2">Attempts per Status</h6>
                    <div class="chart-box">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm p-3">
                    <h6 class="text-center mb-2">Average Duration per Status (minutes)</h6>
                    <div class="chart-box">
                        <canvas id="durationBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
</div>
@endsection

@section('others')
@include('othes.swal')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="{{ asset('assets/js/table.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- <script>
        document.addEventListener("DOMContentLoaded", () => {

            // PIE CHART
            new Chart(document.getElementById('statusPieChart'), {
                type: 'pie',
                data: {
                    labels: {!! json_encode($statusCounts->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($statusCounts->values()) !!},
                        backgroundColor: [
                            '#198754',
                            '#0d6efd',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1'
                        ]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // BAR CHART
            new Chart(document.getElementById('durationBarChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($averageDurations->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($averageDurations->values()) !!},
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
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
    </script> --}}
@endsection
