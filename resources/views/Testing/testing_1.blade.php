@extends('othes.layout') {{-- import layout --}}

@section('title', 'Request Info') {{-- page title --}}

@section('css')
    #request {
    max-width: 400px;
    margin: 10vh auto;
    padding: 2rem;
    background-color: #ffffff;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    border-radius: 8px;
    text-align: center;
    font-family: 'Segoe UI', sans-serif;
    }
    #request select, #request button {
    width: 100%;
    padding: 0.75rem;
    margin-top: 1rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    transition: all 0.3s ease;
    }
    #request select:focus, #request button:hover {
    border-color: #3b82f6;
    outline: none;
    }
    #request button {
    background-color: #3b82f6;
    color: white;
    cursor: pointer;
    }
    #request button:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
    }
@endsection

@section('content')
    <form method="Post" action="{{route('request.all')}}" id="request">
        <h2>Quick Request</h2>
        <select name="links" id="links">
            <option value="userAgent" selected>User Agent</option>
            <option value="ip">IP Address</option>
            <option value="token">Token</option>
            <option value="remember">remember</option>
        </select>
        @csrf
        {{-- CSRF token manually for JS --}}
        <input type="hidden" id="token" value="{{ csrf_token() }}">

        <button type="button" onclick="makeRequest()">OK</button>
        <button type="submit">Request All</button>
    </form>
@endsection

@section('others')
    @include('othes.swal')
@endsection

@section('scripts')
    <script>
        const endpointMap = '{{ route('test.request.get') }}';

        async function makeRequest() {
            const select = document.getElementById('links');
            const value = {links: select.value};
            const token = document.getElementById('token').value;
            const link = endpointMap;
            console.log(value);

            try {
                const response = await fetch(link, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify(value)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: result.title || 'Success',
                        text: result.message || ''
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: result.message || 'Something went wrong.'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fetch Error',
                    text: error.message
                });
            }
        }
    </script>
@endsection
