<div class="row ">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
            <h4>Serch For User By Name</h4>

            <ul class="navbar-nav w-100">
                <li class="nav-item w-100">
                    <input type="text" wire:input='updateSearch($event.target.value)' class="form-control" placeholder="Search here..."
                    style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;" id="search-input">
                </li>
            </ul>
            <br>
          <h4 class="card-title">Users</h4>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                <th  scope="col"> # </th>
                  <th  scope="col"> User Name </th>
                  <th  scope="col"> User Email </th>
                  <th  scope="col"> User phone </th>
                  <th  scope="col">Action</th>
                </tr>
              </thead>
              <tbody>

                @foreach ( $users as $user  )
                <tr>

                    <td> {{$loop->iteration}} </td>
                    <td> {{$user->name}} </td>
                    <td> {{$user->email}} </td>
                    <td> {{ $user->phone }}</td>

                    <td>
                        <div style="display: flex; justify-content:space-around;">
                            <a href="{{ route('admin.user.showOne' , $user->id) }}" class="btn btn-success">Show</a>
                            <form action="{{ route('admin.user.delete' , $user->id) }}" method="post">
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
  </div>
