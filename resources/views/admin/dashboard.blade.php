@extends('admin.main.main')

@section('content')




  <br><br>

  <div class="row">
    <div class="col-sm-4 grid-margin">
        <a href="{{ route('admin.user.show') }}" style="text-decoration: none">

            <div class="card">
                <div class="card-body">
                    <h5 class="text-success">Users</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-success">{{ $users }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of Users</h6>
            </div>

        </div>
    </div>
</div>
</a>
</div>

<div class="col-sm-4 grid-margin">
    <a href="{{ route('admin.employee.waiting') }}" style="text-decoration: none">

            <div class="card">
                <div class="card-body">
          <h5 class="text-warning">Waiting Employees</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-warning">{{$waiting_employees}}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of Waiting Employees</h6>
            </div>

        </div>
        </div>
        </div>
    </a>
</div>
<div class="col-sm-4 grid-margin">
    <a href="{{ route('admin.employee.accept') }}" style="text-decoration: none">

            <div class="card">
                <div class="card-body">
          <h5 class="text-warning">Accepted Employees</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-warning">{{$accept_employees}}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of Accepted Employees</h6>
            </div>

        </div>
        </div>
        </div>
    </a>
</div>
<div class="col-sm-4 grid-margin">
    <a href="{{ route('admin.employee.update') }}" style="text-decoration: none">

            <div class="card">
                <div class="card-body">
          <h5 class="text-warning">Updated Employees</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-warning">{{$update_employees}}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of Updated Employees</h6>
            </div>

        </div>
        </div>
        </div>
    </a>
</div>
<div class="col-sm-4 grid-margin">
    <a href="{{ route('sponser.show') }}" style="text-decoration: none">

            <div class="card">
                <div class="card-body">
          <h5 class="text-warning">Sponsers</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-warning">{{$sponsers}}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of Sponsers</h6>
            </div>

        </div>
        </div>
        </div>
    </a>
</div>
<div class="col-sm-4 grid-margin">
    <a href="{{ route('section.show') }}" style="text-decoration: none">

            <div class="card">
        <div class="card-body">
          <h5 class="text-danger">Sections</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-danger">{{ $sections }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of sections</h6>
            </div>
          </div>
        </div>
            </div>
        </a>
</div>
    <div class="col-sm-4 grid-margin">
    <a href="{{ route('service.show') }}" style="text-decoration: none">

            <div class="card">
                <div class="card-body">
                    <h5 class="text-danger">Services</h5>
                    <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-danger">{{ $services }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of services</h6>
            </div>
            </div>
                </div>
            </div>
        </a>
    </div>

    <hr>


  </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function(){
    $("#myInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#myTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
    });
  });
</script>



@endsection
