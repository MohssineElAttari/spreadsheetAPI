@extends('layouts.dashboard')

@section('content')

    <div class="row my-5">
        <h2 class="fs-4 mb-3">Create new API</h2>
        <form class="row g-3" action="{{ route('readSheet') }}" method="post">
            @csrf
            <h3>Google Spreadsheet URL:</h3>
            <div class="col-auto">
                <p>Paste google spreadsheet URL here from address bar.
                </p>
            </div>
            <div class="col">
                <label for="linkSpreadSheet" class="visually-hidden">Link :</label>
                <input type="text" class="form-control" id="linkSpreadSheet" name="link" placeholder="https://docs.google.com/spreadsheets/d/1JR2uAjnN67c4sRnfnyGXdzXjz535v6MNgB48pLvVI1I/edit#gid=0">
            </div>
            <button type="submit" class="btn btn-primary col-auto">CREATE API</button>
        </form>
    </div>
@endsection
