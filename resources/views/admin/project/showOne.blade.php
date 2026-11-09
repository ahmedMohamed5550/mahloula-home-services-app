@extends('admin.main.main')

@section('content')

<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="d-flex justify-content-between align-items-center">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $project->title }}</h5>
                <div style="display: flex; justify-content:space-between">
                    <p class="card-text text-muted">
                        <strong style="font-size: 20px">{{ $project->user->name }}</strong> |
                        {{ $project->created_at->format('M d, Y') }} 
                        <br>
                        {{ $project->city }}
                    </p>
                    <p>
                        <div class="d-flex justify-content-between mt-3">
                            <form action="{{ route('admin.project.delete' , $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash fa-lg"></i>
                                </button>
                            </form>
                        </div>
                    </p>
                </div>

                <!-- عرض الصورة -->
                <div style="display: flex;justify-content:space-around;">
                    <p> Image </p>
                    <p> Video </p>
                </div>
            <div style="display: flex; justify-content:space-around">

                @if($project->img)
                <div class="" style="width: 40%">
                    <img src="{{ asset($project->image) }}" alt="Project Image" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: cover;">
                </div>
                @endif

                @if ($project->img == null)
                <div class="" style="width: 40%">
                    <img src="{{ asset('not_found/not_found.jpeg') }}" alt="Project Image" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: cover;">
                </div>
                @endif

                <!-- عرض الفيديو -->
                @if($project->video)
                <div class="" style="width: 40%">
                    <video controls style="width: 100%; max-height: 400px;">
                        <source src="{{ asset($project->video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                @endif

                @if ($project->video == null)
                <div class="" style="width: 40%">
                    <img src="{{ asset('not_found/not_found.jpeg') }}" alt="Project Image" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: cover;">
                </div>
                @endif
            </div>
            
                <br>

                <p class="card-text">{{ $project->desc }}</p>
                <hr>
                <p>
                    <span class="text-success font-weight-bold comment-toggle" style="cursor: pointer;">
                        <i class="fas fa-comments fa-lg"></i> {{ $project->comments->count() }}
                    </span> |
                    <span class="text-primary font-weight-bold">
                        <i class="fas fa-thumbs-up fa-lg"></i> {{ $project->likes->count() }}
                    </span>
                </p>
                <!-- قسم التعليقات -->
                <div class="comments-section" style="display: none; margin-top: 20px;">
                    <h5 style="color: blue;">Comments:</h5>
                    @forelse ($project->comments as $comment)
                    <div style="display: flex; justify-content:space-between">
                        <p class="card-text text-muted">
                            <strong style="font-size: 20px">{{ $comment->user->name }}</strong> |
                            {{ $comment->created_at->format('M d, Y') }} 
                            <br>
                        </p>
                        <p>
                            <div class="d-flex justify-content-between mt-3">
                                <form action="{{ route('admin.project.comment.delete' , $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash fa-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </p>
                    </div>
                    <p class="card-text">{{ $comment->content }}</p>
                    <hr>
                    @empty
                        <p class="text-muted">No comments available.</p>
                        <hr>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const commentToggle = document.querySelector('.comment-toggle');
        const commentsSection = document.querySelector('.comments-section');

        if (commentToggle && commentsSection) {
            commentToggle.addEventListener('click', function () {
                const isHidden = commentsSection.style.display === 'none';
                commentsSection.style.display = isHidden ? 'block' : 'none';
            });
        }
    });
</script>

@endsection
