@extends('othes.layout')

@section('title', 'Bayani Quest Admin Panel - Questions')

@section('head')
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/af-2.7.0/b-3.2.4/b-html5-3.2.4/date-1.5.6/r-3.0.5/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/navigation.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endsection

@section('content')
    <div class="wrapper">
        <button class="backBtn" onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="fa-solid fa-left-long fa-xl"></i>
        </button>

        <button class="addBtn" id="addQuestionBtn" data-bs-toggle="modal" data-bs-target="#questionModal">
            + Question
        </button>

        <div class="content_wrapper">
            <div class="contents">
                @nav('Questions')

                <div class="content">
                    {{-- Upload Form --}}
                    <form action="{{ route('questions.import') }}" method="POST" enctype="multipart/form-data"
                        class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control" accept=".csv, .txt, .xlsx, .xls" required>
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                    </form>

                    <div>
                        <a href="{{route('file.download',['storage'=>'admin','filename'=>'sample_questions_valid.csv'])}}" class="btn btn-success">Donwload Default</a>
                    </div>

                    <table class="table mt-5 table-striped table-borderless table-hover" id="section_table">
                        <thead>
                            <tr>
                                <th hidden>ID</th>
                                <th>
                                    <select id="novelSelect" class="form-select">
                                        <option value="All" {{ $novel == 'All' ? 'selected' : '' }}>All Novels</option>
                                        @foreach ($novels as $_novel)
                                            <option value="{{ $_novel->novel_name }}"
                                                {{ $_novel->novel_name == $novel ? 'selected' : '' }}>
                                                {{ $_novel->novel_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select id="levelSelect" class="form-select">
                                        <option value="All" {{ $level == 'All' ? 'selected' : '' }}>All Levels</option>
                                        @foreach ($levels as $_level)
                                            <option value="{{ $_level->level_name }}"
                                                {{ $_level->level_name == $level ? 'selected' : '' }}>
                                                {{ $_level->level_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select id="typeSelect" class="form-select">
                                        <option value="All" {{ $type == 'All' ? 'selected' : '' }}>All Types</option>
                                        @foreach ($types as $_type)
                                            <option value="{{ $_type }}" {{ $_type == $type ? 'selected' : '' }}>
                                                {{ $_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Choice 1</th>
                                <th>Choice 2</th>
                                <th>Choice 3</th>
                                <th>Rationalization</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $question)
                                <tr>
                                    <td hidden>{{ $question->id }}</td>
                                    <td>{{ $question->gamelevel->novel->novel_name }}</td>
                                    <td>{{ $question->gamelevel->level_name }}</td>
                                    <td>{{ $question->type }}</td>
                                    <td>{{ $question->question }}</td>
                                    <td>{{ $question->answer }}</td>
                                    <td>{{ $question->ans1 ?? 'Null' }}</td>
                                    <td>{{ $question->ans2 ?? 'Null' }}</td>
                                    <td>{{ $question->ans3 ?? 'Null' }}</td>
                                    <td>{{ $question->rationalization ?? 'Null' }}</td>
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-primary editBtn" data-id="{{ $question->id }}"
                                            data-novel="{{ $question->gamelevel->novel->id }}"
                                            data-level="{{ $question->gamelevel->id }}" data-type="{{ $question->type }}"
                                            data-question="{{ $question->question }}"
                                            data-answer="{{ $question->answer }}" data-ans1="{{ $question->ans1 }}"
                                            data-ans2="{{ $question->ans2 }}" data-ans3="{{ $question->ans3 }}"
                                            data-bs-toggle="modal" data-bs-target="#questionModal">
                                            <i class="fa-solid fa-user-pen" style="color:white;"></i>
                                        </button>
                                        <form action="{{ route('question.delete') }}" method="POST" class="delete-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $question->id }}">
                                            <button class="btn btn-danger delete-btn" type="button">
                                                <i class="fa-solid fa-trash" style="color:white;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for Add/Edit --}}
    <div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" class="modal-content" id="questionForm" action="{{ route('question.add') }}">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header bg-dark text-white">
                    <h1 class="modal-title fs-5" id="modalLabel">ADD NEW QUESTION</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="question_id" name="id" />

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="novel" class="form-label">Novel</label>
                            <select name="novel" id="novel" class="form-control" onchange="GetLevels()">
                                @foreach ($novels as $nov)
                                    <option value="{{ $nov->id }}">{{ $nov->novel_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="level" class="form-label">Level</label>
                            <select name="level" id="level" class="form-control">
                                <option value="">Select Level</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control" onchange="ChangedType()">
                                <option value="Multiple">Multiple Choice</option>
                                <option value="Identification">Identification</option>
                                <option value="ToF">True or False</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="rationalization" class="form-label">Rationalization</label>
                            <textarea name="rationalization" id="rationalization" rows="2" class="form-control"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="question" class="form-label">Question</label>
                            <textarea name="question" id="question" rows="2" class="form-control"></textarea>
                        </div>
                    </div>

                    <hr>
                    <div id="modal-answers" class="mt-3"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveBtn" class="btn btn-dark">Save changes</button>
                </div>
            </form>
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
    <script src="{{ asset('assets/js/table.js') }}"></script>

    <script>
        const baseRoute = "{{ url('/Questions/List') }}";

        function updateFilters() {
            const novel = document.getElementById('novelSelect').value || 'All';
            const level = document.getElementById('levelSelect').value || 'All';
            const type = document.getElementById('typeSelect').value || 'All';
            window.location.href = `${baseRoute}/${novel}/${level}/${type}`;
        }

        ['novelSelect', 'levelSelect', 'typeSelect'].forEach(id => {
            document.getElementById(id).addEventListener('change', updateFilters);
        });

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#section_table')) {
                $('#section_table').DataTable().destroy();
            }

            $('#section_table').DataTable({
                pageLength: 5,
                lengthChange: false,
                ordering: false,
                searching: true,
                info: true,
            });
        });

        function ChangedType(answer = '', ans1 = '', ans2 = '', ans3 = '') {
            const type = document.getElementById('type').value;
            const answers = document.getElementById('modal-answers');
            let html = "";

            if (type === "Multiple") {
                html = `
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Answer</label><input type="text" name="answer" class="form-control" value="${answer}" required/></div>
            <div class="col-md-6"><label class="form-label">Choice 1</label><input type="text" name="ans1" class="form-control" value="${ans1}" required/></div>
            <div class="col-md-6"><label class="form-label">Choice 2</label><input type="text" name="ans2" class="form-control" value="${ans2}" required/></div>
            <div class="col-md-6"><label class="form-label">Choice 3</label><input type="text" name="ans3" class="form-control" value="${ans3}" required/></div>
        </div>`;
            } else if (type === "Identification") {
                html =
                    `<div class="mb-3"><label class="form-label">Answer</label><input type="text" name="answer" class="form-control" value="${answer}" required/></div>`;
            } else if (type === "ToF") {
                html = `<div class="mb-3"><label class="form-label">Answer</label><select name="answer" class="form-control">
            <option value="true" ${answer==='true'?'selected':''}>True</option>
            <option value="false" ${answer==='false'?'selected':''}>False</option></select></div>`;
            }
            answers.innerHTML = html;
        }

        function GetLevels() {
            const novelId = document.getElementById("novel").value;
            const levelSelect = document.getElementById("level");
            levelSelect.innerHTML = '<option value="">Loading...</option>';

            $.ajax({
                url: "{{ route('question.getlevel') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: novelId // ✅ Must match your controller's validation
                },
                success: function(response) {
                    levelSelect.innerHTML = '<option value="">Select Level</option>';
                    if (response.status === "success" && response.levels.length > 0) {
                        response.levels.forEach(level => {
                            levelSelect.innerHTML +=
                                `<option value="${level.id}">${level.level_name}</option>`;
                        });
                    } else {
                        levelSelect.innerHTML = '<option value="">No levels found</option>';
                    }
                },
                error: function() {
                    levelSelect.innerHTML = '<option value="">Failed to load</option>';
                }
            });
        }

        // 🔹 Edit button
        $(document).on('click', '.editBtn', function() {
            const id = $(this).data('id');
            $('#question_id').val(id);
            $('#novel').val($(this).data('novel'));
            $('#type').val($(this).data('type'));
            $('#question').val($(this).data('question'));
            $('#rationalization').val($(this).data('rationalization'));
            ChangedType($(this).data('answer'), $(this).data('ans1'), $(this).data('ans2'), $(this).data('ans3'), $(this).data('rationalization'));

            $('#modalLabel').text('UPDATE QUESTION');
            $('#saveBtn').text('Update');
            $('#questionForm').attr('action', "{{ route('question.update') }}");
            $('#methodField').html('@method('PUT')');

            // Fetch levels dynamically for this novel
            GetLevels();
            setTimeout(() => $('#level').val($(this).data('level')), 500);
        });

        // 🔹 Add button
        $('#addQuestionBtn').on('click', function() {
            $('#modalLabel').text('ADD NEW QUESTION');
            $('#saveBtn').text('Save changes');
            $('#questionForm').attr('action', "{{ route('question.add') }}");
            $('#questionForm')[0].reset();
            $('#methodField').html('');
            ChangedType();
        });

        $(document).on('click', '.delete-btn', function() {
            const form = $(this).closest('form');
            Swal.fire({
                title: "Are you sure?",
                text: "This question will be permanently deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        GetLevels();
    </script>
@endsection
