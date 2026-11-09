<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Section;
use App\Models\Service;
use App\Models\Sponser;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            if (Auth::user()->userType == 'admin') {

                return redirect()->route('admin.dashboard')->with('success', 'تم تسجيل الدخول بنجاح');

            } else {

                Auth::logout();
                return redirect()->back()->with(['error' => 'You do not have admin access permissions']);
            }
        }
        return redirect()->back()->with(['error' => 'Invalid Email Or Password']);
    }


    public function home()
    {

        $users = User::where('userType' , 'user')->count();
        $waiting_employees = Employee::where('checkByAdmin' , 'waiting')->count();
        $accept_employees = Employee::where('checkByAdmin' , 'accepted')->count();
        $update_employees = Employee::where('checkByAdmin' , 'updated')->count();
        $sections = Section::count();
        $services = Service::count();
        $sponsers = Sponser::count();
        return view('admin.dashboard' , ['accept_employees'=>$accept_employees , 'update_employees'=>$update_employees ,  'users'=>$users , 'waiting_employees'=>$waiting_employees , 'sections'=>$sections , 'services'=>$services , 'sponsers'=>$sponsers]);
    }


    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'password'=>'required',
        ]);

        User::create($request->all());

        return redirect()->route('admin.dashboard');
    }

    public function showUser()
    {
        return view('admin.user.show');
    }

    public function showOne($id)
    {
        $user = User::findOrFail($id);

        return view('admin.user.showOne' , ['user'=>$user]);
    }

    public function deletUser(Request $request , $id)
    {
        User::destroy($id);
        return redirect()->back();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
