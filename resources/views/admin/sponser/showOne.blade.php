@extends('admin.main.main')

@section('content')


<table class="table">
    <thead>
        <tr>
            <th scope="col">image</th>
            <th scope="col">title</th>
            <th scope="col">desc</th>
            <th scope="col">type</th>
            <th scope="col">expired_at</th>

        </tr>
    </thead>
    <tbody>
        <tr>
          <td><img src="{{ asset("$sponser->image") }}" width="500" height="300" alt=""></td>
          <td>{{ $sponser->title }}</td>
          <td> {{ $sponser->desc }}</td>
          <td> {{ $sponser->type }}</td>
          <td> {{ $sponser->expired_at }}</td>

           
        </tr>

    </tbody>
</table>


@endsection
