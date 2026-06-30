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
            onclick="window.location.href='{{ route('section.user', ['id' => $section->section->id]) }}'">
            <i class="fa-solid fa-left-long fa-xl"></i>
        </button>
        <!-- content_wrapper -->
        <div class="content_wrapper">
            <!-- contents -->
            <div class="contents">
                @nav($section->section->section_name . ' | ' . $user->username)

                <div class="content"><!-- table -->
                    <table class="table mt-5 table-striped table-borderless table-hover" id="section_table" border="1">
                        <thead>
                            <tr>
                                <th>Novel</th>
                                <th>Level</th>
                                <th>First Score</th>
                                <th>Attempts</th>
                                <th>Average</th>
                                <th>Activity Logs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scores as $score)
                                <tr class="align-middle">
                                    <td>{{ $score->gameLevel->novel->novel_name }}</td>
                                    <td>{{ $score->gameLevel->level_name }}</td>
                                    <td>{{ $score->first_score }}</td>
                                    <td>{{ $score->attempts }}</td>
                                    <td>{{ $score->average }}</td>
                                    <td><a href="{{ route('user.logs.show', ['id' => Crypt::encrypt($score->user_id . '_' . $score->game_level_id)]) }}"
                                            class="btn btn-primary">View</a></td>
                                    <td hidden>{{ $score->gameLevel->id }}</td>
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

    <!-- Chart Popup -->
    <div id="chartPopup" class="position-absolute bg-white border rounded shadow p-3"
        style="display:none; width:400px; z-index:2000;">
        <canvas id="hoverChart"></canvas>
    </div>


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
        let hoverChart = null;
        const popup = document.getElementById('chartPopup');
        const ctx = document.getElementById('hoverChart').getContext('2d');

        // helper to show chart popup
        function showChartPopup(e, type, data) {
            if (hoverChart) hoverChart.destroy();

            if (type === 'bar') {
                hoverChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(data.statusCounts),
                        datasets: [{
                            label: 'Attempts per Status',
                            data: Object.values(data.statusCounts),
                            backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545']
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            } else if (type === 'line') {
                hoverChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.attemptLabels,
                        datasets: [{
                            label: 'Scores per Attempt',
                            data: data.scoreHistory,
                            borderColor: '#0d6efd',
                            fill: false,
                            tension: 0.3
                        }]
                    }
                });
            } else if (type === 'bubble') {
                hoverChart = new Chart(ctx, {
                    type: 'bubble',
                    data: {
                        datasets: [{
                            label: 'Completed Attempts',
                            data: data.bubbleData,
                            backgroundColor: '#198754'
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Completed'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Duration (in minutes)'
                                }
                            }
                        }
                    }
                });
            }

            const rect = e.target.getBoundingClientRect();
            popup.style.left = `${rect.right + window.scrollX + 10}px`;
            popup.style.top = `${rect.top + window.scrollY - 10}px`;
            popup.style.display = 'block';

        }

        // helper to hide popup
        function hideChartPopup() {
            popup.style.display = 'none';
            if (hoverChart) hoverChart.destroy();
        }

        // Attach hover event to table cells
        $(document).ready(function() {
            $('#section_table tbody tr').each(function() {
                const userId = '{{ $user->id }}'; // or from data-attr if needed
                const levelId = $(this).find('td:nth-child(7)').text();

                $(this).find('td:nth-child(2)').on('mouseenter', function(e) { // Level cell
                    fetch(`/api/score/details/${userId}/${levelId}`)
                        .then(res => res.json())
                        .then(data => showChartPopup(e, 'bar', data));
                }).on('mouseleave', hideChartPopup);

                $(this).find('td:nth-child(3)').on('mouseenter', function(e) { // First Score
                    fetch(`/api/score/details/${userId}/${levelId}`)
                        .then(res => res.json())
                        .then(data => showChartPopup(e, 'line', data));
                }).on('mouseleave', hideChartPopup);

                $(this).find('td:nth-child(4)').on('mouseenter', function(e) { // Attempts
                    fetch(`/api/score/details/${userId}/${levelId}`)
                        .then(res => res.json())
                        .then(data => showChartPopup(e, 'bubble', data));
                }).on('mouseleave', hideChartPopup);
            });
        });
    </script>


@endsection
{{-- body  --}}
