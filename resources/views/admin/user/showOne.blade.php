@extends('admin.main.main')

@section('content')


<div class="container w-50 h-25">
    <h3 class="text-center m-2">User Details</h3>
    <div class="card">
        <p style="padding: 20px">User Image</p>
        <div class="row no-gutters" style="align-items:center">
            <div class="col-md-12 d-flex justify-content-center align-items-center p-2">
                @if ($user->image == null)
                    <img src="{{ asset('not_found/not_found.jpeg') }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                 @endif
                 @if ($user->image != null)
                    <img src="{{ asset("$user->image") }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                @endif
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title">Name: {{ $user->name }}</h5>
            <hr>
            <p class="card-text">Email: {{ $user->email }}</p>
            <hr>
            <p class="card-text">Phone: {{ $user->phone }}</p>
            <hr>
        </div>
    </div>
</div>


@endsection
