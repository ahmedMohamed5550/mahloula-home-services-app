@extends('admin.main.main')
@section('content')

<form method="POST" action="{{route('service.store')}}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="exampleInputEmail1">Service Name</label>
      <input type="text" name="name" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter name" value="{{ old('name') }}" >
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Service desc</label>
        <textarea type="text" name="desc" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter desc">{{ old('desc') }}</textarea>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Section Name</label>
        <select name="section_id" class="form-control text-white" id="">
            <option value="">Select Setion</option>
            @foreach ($sections as $section)
                <option value="{{ $section->id }}">{{ $section->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <input type="file" name="image" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter image" value="{{ old('image') }}" >

    </div>


    <button type="submit" class="btn btn-primary">Add service</button>
</form>

@endsection