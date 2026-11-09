<?php

namespace App\Http\Controllers\admin;

use App\Models\Section;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function show()
    {
        $services = Service::get();

        return view('admin.service.show', ['services'=>$services]);
    }

    public function create()
    {
        $sections = Section::get();
        return view('admin.service.create' , ['sections'=>$sections]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'desc'=>'required',
            'section_id'=>'required|exists:sections,id',
        ]);

        $data = $request->except('image');

        $image_path = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('services_image' , 'uploads');
            $image_path = $path;
        }

        $data['image'] = $image_path;

        $service = Service::create($data);

        return redirect()->route('service.show');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $sections = Section::get();
        return view('admin.service.edit' , ['service'=>$service , 'sections'=>$sections]);
    }

    public function update(Request $request , $id)
    {
        $service = Service::findOrFail($id);
        $request->validate([
            'name'=>'required',
            'desc'=>'required',
            'section_id'=>'required|exists:sections,id'
        ]);

        $data = $request->except('image');

        $old_image = $service->image;

        $image_path = NULL;

        if($request->hasFile('image'))
        {
            $file = $request->file('image');
            $path = $file->store('sections_image' , 'uploads');
            $image_path = $path;
            $data['image'] = $image_path;
        }


        $service->update($data);

        if($image_path != NULL && $old_image != NULL)
        {
            Storage::disk('uploads')->delete($old_image);
        }

        return redirect()->route('service.show');

    }

    public function delete(Request $request , $id)
    {
        $service = Service::findOrFail($id);

        $old_image = $service->image;

        $service->delete();

        if($old_image != NULL)
        {
            Storage::disk('uploads')->delete($old_image);
        }
        
        return redirect()->route('service.show');
    }
}
