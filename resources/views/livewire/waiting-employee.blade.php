<div class="row ">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
            <h4>Serch For Employee By Name Or Section Name Or Service Name</h4>

            <ul class="navbar-nav w-100">
                <li class="nav-item w-100">
                  <input type="text" wire:input='updateSearch($event.target.value)' class="form-control" placeholder="Search here..."
                  style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;" id="search-input">
                </li>
            </ul>
            <br>
          <h4 class="card-title">Employees Status</h4>
          <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                    <th> # </th>
                      <th> Employee Name </th>
                      <th> Employee Email </th>
                      <th> Employee phone </th>
                      <th> Status </th>
                      <th> checkByAdmin </th>
                      <th> Show All Data </th>
                    </tr>
                  </thead>
              <tbody>

                @foreach ( $employees as $employee  )
                <tr>

                    <td> {{$loop->iteration}} </td>
                    <td> {{$employee->user->name}} </td>
                    <td> {{$employee->user->email}} </td>
                    <td> {{ $employee->user->phone }}</td>
                    {{-- <td> <img class="" src="{{asset("storage/$employee->imageSSN")}}" alt="image" /></td> --}}
                    {{-- <img class="" src="{{ asset('storage/' . $employee->livePhoto) }}" alt="image" /> --}}
                    <td> {{ $employee->status }} </td>
                    <td> {{ $employee->checkByAdmin }} </td>
                    {{-- <td><a href="{{ url("admin/checkByAdmin/{$employee->id}/accepted") }}" class="badge badge-outline-success">Accepted</a></td> --}}
                    {{-- <td><a href="{{ url("admin/checkByAdmin/{$employee->id}/rejected") }}" class="badge badge-outline-danger">Rejected</a></td> --}}
                    <td><a href="{{ route('admin.employee.waitingOne' , $employee->id) }}" class="badge badge-outline-warning">show</a></td>

                  </tr>


                @endforeach



              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    {{$employees->links()}}
</div>
