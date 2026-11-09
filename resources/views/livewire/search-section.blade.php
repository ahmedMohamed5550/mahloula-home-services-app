
<div class="row ">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
            <h4>Serch For Section By Name</h4>

            <ul class="navbar-nav w-100">
                <li class="nav-item w-100">
                    <input type="text" wire:input='updateSearch($event.target.value)' class="form-control" placeholder="Search here..."
                    style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;" id="search-input">
                </li>
            </ul>
            <br>
          <h4 class="card-title">All Sections</h4>
          <div class="table-responsive">

<table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Image</th>
        <th scope="col">desc</th>
        <th scope="col">Name</th>
        <th scope="col">Aciton</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($sections as $index=>$section )
      <tr>
          <th scope="row">{{$index+1}}</th>
            <td>
                @if ($section->image == null) 
                    <img src="{{asset('not_found/not_found.jpeg')}}" width="500" height="300" alt="">
                @endif
                @if ($section->image != null)
                    <img src="{{asset("$section->image")}}" width="500" height="300" alt="">
                @endif
            </td>
        <td>{{$section->name}}</td>
        <td> {{ substr($section->desc, 0,30) }}</td>

        <td>
            <div style="display: flex; justify-content:space-around">
                <a class="btn btn-success" href="{{route('section.edit' , $section->id)}}" >edit</a>
                <form action="{{ route('section.delete' , $section->id) }}" method="post">
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
{{ $sections->links() }}
</div>
