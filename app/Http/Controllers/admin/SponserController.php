<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Sponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SponserController extends Controller
{
    public function show()
    {
        $sponsers = Sponser::get();

        return view('admin.sponser.show' , ['sponsers'=>$sponsers]);
    }

    public function showOne($id)
    {
        $sponser = Sponser::findOrFail($id);

        return view('admin.sponser.showOne' , ['sponser'=>$sponser]);
    }

    public function create()
    {
        return view('admin.sponser.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'desc' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:available,expired',
            'expired_at' => 'required|date',
        ]);

        $data = $request->except('image');

        $file = $request->file('image');
        $path = $file->store('sponser_images' , 'uploads');
        $data['image'] = $path;


        Sponser::create($data);

        return redirect()->route('sponser.show');
    }

    public function edit($id)
    {
        $sponser = Sponser::findOrFail($id);

        return view('admin.sponser.edit' , ['sponser'=>$sponser]);
    }

    public function update(Request $request , $id)
    {
        $sponser = Sponser::findOrFail($id);

        $old_image = $sponser->image;

        $request->validate([
            'title' => 'required|string',
            'desc' => 'required|string',
            'type' => 'required|in:available,expired',
            'expired_at' => 'required|date',
        ]);

        $data = $request->except('image');

        if($request->hasFile('image'))
        {
            $file = $request->file('image');
            $path = $file->store('sponser_images' , 'uploads');
            $data['image'] = $path;
            Storage::disk('uploads')->delete($old_image);
        }

        $sponser->update($data);

        return redirect()->route('sponser.show');

    }

    public function delete(Request $request , $id)
    {
        $sponser = Sponser::findOrFail($id);

        $old_image = $sponser->image;

        $sponser->delete();

        Storage::disk('uploads')->delete($old_image);

        return redirect()->route('sponser.show');
    }
}
