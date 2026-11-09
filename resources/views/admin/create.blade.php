@extends('admin.main.main')

@section('content')

<form method="POST" action="{{route('admin.store')}}" >
    @csrf
    <div class="form-group">
      <label for="exampleInputEmail1">Admin Name</label>
      <input type="text" name="name" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter name" value="{{ old('name') }}" >
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Admin Email</label>
        <input type="email" name="email" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Email" value="{{ old('email') }}" >
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Admin Phone</label>
        <input type="text" name="phone" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Phone" value="{{ old('phone') }}" >
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Admin Password</label>
        <input type="password" name="password" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Password" value="{{ old('password') }}" >
    </div>
   
    <input type="hidden" name="userType" value="admin" id="">

    <button type="submit" class="btn btn-primary">Add Admin</button>
</form>

@endsection
