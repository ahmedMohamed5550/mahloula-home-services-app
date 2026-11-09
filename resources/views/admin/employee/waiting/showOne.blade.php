@extends('admin.main.main')

@section('content')
<div class="container w-50 h-25">
    <h3 class="text-center m-2">Employee Details</h3>
    <div class="card">
        <div style="display: flex;justify-content:space-around">
            <p>ImageSSN</p>
            <p>LivePhoto</p>
        </div>
        <div class="row no-gutters" style="display: flex;">
            <div class="col-md-6 d-flex justify-content-center align-items-center p-2">
                @if ($employee->imageSSN == null)
                    <img src="{{ asset('not_found/not_found.jpeg') }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                @endif
                @if ($employee->imageSSN != null)
                    <img src="{{ asset("$employee->imageSSN") }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                @endif
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center p-2">
                @if ($employee->livePhoto == null)
                    <img src="{{ asset('not_found/not_found.jpeg') }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                 @endif
                 @if ($employee->livePhoto != null)
                    <img src="{{ asset("$employee->livePhoto") }}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
                @endif
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title">Name: {{ $employee->user->name }}</h5>
            <hr>
            <p class="card-text">Desc: {{ $employee->description }}</p>
            <hr>
            <p class="card-text">ID: {{ $employee->nationalId }}</p>
            <hr>
            <p class="card-text">Phone: {{ $employee->user->phone }}</p>
            <hr>
            <a href="{{ route('admin.acceptEmployee' , $employee->id) }}" class="badge badge-outline-success">Accept</a>

            <a href="{{ route('admin.rejectEmployee' , $employee->id) }}" class="badge badge-outline-danger">Reject</a>
            <span id="update-section">
                <a href="#" class="badge badge-outline-primary" id="update-btn">Update</a>
                <div id="update-reason" style="display: none; margin-top: 10px;">
                    <textarea id="reason-input" class="form-control mb-2" style="color: white; background-color: #333;" rows="4" placeholder="Enter reason"></textarea>
                    <a href="#" id="submit-update" class="btn btn-primary btn-sm">Send</a>
                </div>
            </span>

        </div>
    </div>
</div>

<script>
    document.getElementById('update-btn').addEventListener('click', function(e) {
        e.preventDefault(); // منع الانتقال للرابط
        document.getElementById('update-reason').style.display = 'block'; // إظهار textarea
    });

    document.getElementById('submit-update').addEventListener('click', function(e) {
        e.preventDefault();
        const reason = document.getElementById('reason-input').value;

        if(reason.trim() === '') {
            alert('Please provide a reason.');
            return;
        }

        // إرسال السبب مع الرابط
        const updateUrl = "{{ route('admin.updateEmployee', $employee->id) }}?reason=" + encodeURIComponent(reason);
        window.location.href = updateUrl;
    });
</script>
@endsection
