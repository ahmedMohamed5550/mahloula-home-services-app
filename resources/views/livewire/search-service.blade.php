
<div class="row ">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
            <h4>Serch For Service By Name</h4>

            <ul class="navbar-nav w-100">
                <li class="nav-item w-100">
                    <input type="text" wire:input='updateSearch($event.target.value)' class="form-control" placeholder="Search here..."
                    style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;" id="search-input">

                </li>
            </ul>
            <br>
          <h4 class="card-title">All Services</h4>
          <div class="table-responsive">

<table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Image</th>
        <th scope="col">desc</th>
        <th scope="col">Name</th>
        <th scope="col">section</th>
        <th scope="col">Aciton</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($services as $index=>$service )
      <tr>
          <th scope="row">{{$index+1}}</th>
          <td>
            @if ($service->image == null) 
                <img src="{{asset('not_found/not_found.jpeg')}}" width="500" height="300" alt="">
            @endif
            @if ($service->image != null)
                <img src="{{asset("$service->image")}}" width="500" height="300" alt="">
            @endif
        </td>
        <td>{{$service->name}}</td>
        <td> {{ substr($service->desc, 0,30) }}</td>
        <td> {{ $service->section->name }}</td>

        <td>
            <div style="display: flex; justify-content:space-around">
                <a class="btn btn-success" href="{{ route('service.edit' , $service->id) }}">edit</a>
                <form action="{{ route('service.delete' , $service->id) }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger" >Delete</button>
                </form>
            </div>

        </td>
    </tr>
    @endforeach

    </tbody>
</table>


</div>
</div>
</div>
</div>
{{ $services->links() }}
</div>
