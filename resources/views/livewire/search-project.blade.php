


<div class="row ">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
            <h4>Serch For Project By Name Or Section Name Or Service Name</h4>


            <ul class="navbar-nav w-100">
                <li class="nav-item w-100">
                    <input type="text" wire:input='updateSearch($event.target.value)' class="form-control" placeholder="Search here..."
                    style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;" id="search-input">
                </li>
            </ul>
            <br>
          <h4 class="card-title">All Projects</h4>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Project Number</th>
                  <th> User Name </th>
                  <th> Section </th>
                  <th> Service </th>
                  <th> created_at </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>

                @foreach ( $projects as $index=>$project  )
                <tr>
                    <td>{{ $index+1 }}</td>
                    <td>
                      <span class="ps-2">{{ $project->user->name }}</span>
                    </td>
                    <td> {{$project->section->name}} </td>
                    <td> {{ $project->service->name }}</td>
                    <td> {{ $project->created_at->format('M d, Y') }} </td>
                    <td><a href="{{route('admin.project.showOne' , $project->id)}}" class="badge badge-outline-warning">show</a></td>

                  </tr>


                @endforeach



              </tbody>
            </table>
        </div>
    </div>
</div>
</div>
{{ $projects->links() }}

</div>
