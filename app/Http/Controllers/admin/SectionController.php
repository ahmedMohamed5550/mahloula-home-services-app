<?php

namespace App\Http\Controllers\admin;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function show()
    {
        $sections = Section::get();

        return view('admin.section.show', ['sections'=>$sections]);
    }

    public function create()
    {
        return view('admin.section.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'desc'=>'required',
        ]);

        $data = $request->except('image');

        $image_path = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('sections_image' , 'uploads');
            $image_path = $path;
        }

        $data['image'] = $image_path;

        $section = Section::create($data);

        return redirect()->route('section.show');
    }

    public function edit($id)
    {
        $section = Section::findOrFail($id);
        return view('admin.section.edit' , ['section'=>$section]);
    }

    public function update(Request $request , $id)
    {
        $section = Section::findOrFail($id);
        $request->validate([
            'name'=>'required',
            'desc'=>'required',
        ]);

        $data = $request->except('image');

        $old_image = $section->image;

        $image_path = NULL;

        if($request->hasFile('image'))
        {
            $file = $request->file('image');
            $path = $file->store('sections_image' , 'uploads');
            $image_path = $path;
            $data['image'] = $image_path;
        }


        $section->update($data);

        if($image_path != NULL && $old_image != NULL)
        {
            Storage::disk('uploads')->delete($old_image);
        }

        return redirect()->route('section.show');

    }

    public function delete(Request $request , $id)
    {
        $section = Section::findOrFail($id);

        $old_image = $section->image;

        $section->delete();

        if($old_image != NULL)
        {
            Storage::disk('uploads')->delete($old_image);
        }
        
        return redirect()->route('section.show');
    }
}
