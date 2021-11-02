@extends('layouts.dashboard')

@section('content')

<div class="row my-5">
    <h3 class="fs-4 mb-3">Your Spreadsheets</h3>
    <div class="col">
        <table class="table bg-white rounded shadow-sm  table-hover">
            <thead>
                <tr>
                    <th scope="col" width="50">#</th>
                    <th scope="col">API NAME</th>
                    <th scope="col">CREATED</th>
                    <th scope="col">OPTION</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach ($spreads as $spread)
                <tr>

                    {{-- <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" id="myForm"> --}}

                    <th scope="row">{{ $spread->id}}</th>
                    <td>{{ $spread->registration_number }}</td>
                    <td>{{ $spread->created_at }}</td>
                    <td>{{ $spread->spreadsheetID }}</td>
                    <td>
                        {{-- <button class="btn btn-success editbtn" value="{{ $spread->id }}">Edit</button> --}}
                        {{-- @csrf --}}
                        {{-- <a class="btn btn-danger" href="categorie/delete/{{ $spread->id }}"
                            onclick="return confirm('are you sure you want to delete this Catrgory')">Delete</a> --}}
                        {{-- <form> --}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection