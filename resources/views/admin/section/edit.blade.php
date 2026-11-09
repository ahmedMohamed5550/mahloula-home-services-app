@extends('admin.main.main')
@section('content')

<form method="POST" action="{{route('section.update' , $section->id)}}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="exampleInputEmail1">Section Name</label>
      <input type="text" name="name" value="{{ $section->name }}" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter name">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Section desc</label>
        <textarea type="text" name="desc" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter desc">{{$section->desc}}</textarea>
      </div>
    <div class="form-group">
        <input type="file" name="image" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter image" value="{{ old('image') }}" >

      </div>


    <button type="submit" class="btn btn-primary">Update Section</button>
</form>

@endsection