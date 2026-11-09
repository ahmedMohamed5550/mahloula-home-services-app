<?php

namespace App\Http\Controllers\admin;

use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    public function show()
    {
        $projects = Project::latest()->get();
        return view('admin.project.show' , ['projects'=>$projects]);
    }

    public function showOne($id)
    {
        $project = Project::findOrFail($id);

        return view('admin.project.showOne' , ['project'=>$project]);
    }

    public function delete(Request $request , $id)
    {
        Project::destroy($id);

        return redirect()->route('admin.project.show');
    }

    public function deleteComment(Request $request , $id)
    {
        Comment::destroy($id);
        return redirect()->back();
    }
}
