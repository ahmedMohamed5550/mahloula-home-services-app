@extends('admin.main.main')

@section('content')


<div class="container w-50 h-25">
    <h3 class="text-center m-2">Employee Details</h3>
    <div class="card">
        <div style="display: flex;justify-content:space-around">
            <p>ImageSSN</p>
            <p>LivePhoto</p>
        </div>
        <div class="row no-gutters" style="display: flex;">
            <div class="col-md-6 d-flex justify-content-center align-items-center p-2">
                @if ($employee->imageSSN == null)
                    <img src="{{ asset('not_found/not_found.jpeg') }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                @endif
                @if ($employee->imageSSN != null)
                    <img src="{{ asset("$employee->imageSSN") }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                @endif
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center p-2">
                @if ($employee->livePhoto == null)
                    <img src="{{ asset('not_found/not_found.jpeg') }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                 @endif
                 @if ($employee->livePhoto != null)
                    <img src="{{ asset("$employee->livePhoto") }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                @endif
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title">Name: {{ $employee->user->name }}</h5>
            <hr>
            <p class="card-text">Desc: {{ $employee->description }}</p>
            <hr>
            <p class="card-text">ID: {{ $employee->nationalId }}</p>
            <hr>
            <p class="card-text">Phone: {{ $employee->user->phone }}</p>
            <hr>
        </div>
    </div>
</div>


@endsection
