<div class="row ">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
            <h4>Serch For sponser By Totle Or Type</h4>


            <ul class="navbar-nav w-100">
                <li class="nav-item w-100">
                    <input type="text" wire:input='updateSearch($event.target.value)' class="form-control" placeholder="Search here..."
                    style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;" id="search-input">
                </li>
            </ul>
            <br>
          <h4 class="card-title">All Sponsers</h4>
          <div class="table-responsive">

<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">image</th>
            <th scope="col">title</th>
            <th scope="col">desc</th>
            <th scope="col">type</th>
            <th scope="col">expired_at</th>
            <th scope="col">Actions </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sponsers as $sponser)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td><img src="{{ asset("$sponser->image") }}" width="500" height="300" alt=""></td>
                <td>{{ $sponser->title }}</td>
                <td> {{ $sponser->desc }}</td>
                <td> {{ $sponser->type }}</td>
                <td> {{ $sponser->expired_at }}</td>

                <td>
                    <div style="display: flex; justify-content:space-around;">
                        <a class="btn btn-success" href="{{ route('sponser.showOne' , $sponser->id)}}">show</a>
                        <a class="btn badge btn-outline-warning" href="{{route('sponser.edit' , $sponser->id)}}">Edit</a>
                        <form action="{{ route('sponser.delete' , $sponser->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">delete</button>
                        </form>
                    </div>
                    {{-- <a class="btn btn-success" href="{{url("admin/services/edit/$service->id")}}" >edit</a> --}}

                </td>
            </tr>
        @endforeach

    </tbody>
</table>
</div>
</div>
</div>
</div>
{{ $sponsers->links() }}

</div>