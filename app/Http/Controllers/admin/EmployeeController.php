<?php

namespace App\Http\Controllers\admin;

use App\Models\Contact;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{


    //Show Employees

    public function waitingEmployee()
    {
        $employees = Employee::where('checkByAdmin' , 'waiting')->get();
        return view('admin.employee.waiting.showAll' , ['employees' => $employees]);
    }

    public function OneWaitingEmployee($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.employee.waiting.showOne' , ['employee'=>$employee]);
    }


    public function acceptEmployee()
    {
        $employees = Employee::where('checkByAdmin' , 'accepted')->get();

        return view('admin.employee.accept.showAll' , ['employees'=>$employees]);
    }

    public function OneAcceptEmployee($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.employee.accept.showOne' , ['employee'=>$employee]);
    }

    public function updateEmployee()
    {
        $employees = Employee::where('checkByAdmin' , 'updated')->get();

        return view('admin.employee.update.showAll' , ['employees'=>$employees]);
    }

    public function OneUpdateEmployee($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.employee.update.showOne' , ['employee'=>$employee]);
    }


    // Accept Or Update Or Reject Employees


    public function accept($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->update([
            'checkByAdmin'=>'accepted',
        ]);
        return redirect()->route('admin.employee.accept');
    }


    public function update(Request $request , $id)
    {

        $request->validate([
            'reason'=>'required',
        ]);


        $employee = Employee::findOrFail($id);

        $user_id = $employee->user->id;

        Contact::create([
            'content'=>$request->reason,
            'user_id'=>$user_id,
        ]);

        $employee->update([
            'checkByAdmin'=>'updated',
        ]);

        return redirect()->route('admin.employee.update');
    }

    public function reject($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->delete();

        return redirect()->route('admin.employee.waiting');
    }
}
