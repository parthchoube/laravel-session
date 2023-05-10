<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\EditRegisterRequest;
use App\Notifications\SignupNotification;
use Auth;
use DataTables;

class AdminController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function profile(Request $request) {
        if ($request->ajax()) {
           $users = User::where('id','!=', 1)->with('roleName')->orderBy('created_at','desc')->get();
            return Datatables::of($users)->addIndexColumn()
            ->addColumn('profile_pic', function ($artist) {
                $url= asset($artist->profile_pic);
            return '<img src="'.$url.'" border="0" width="40" height="40" class="img-rounded" align="center" title="Image" alt="Image" />';})
            ->addColumn('action', function($row){
                    $btn = '<div class="btn-group btn-group-sm"><a href= "'.route('admin.users.view',$row->id).'" data-toggle="tooltip" data-id="'.$row->id.'" title="Preview" data-original-title="View" class="btn btn-info btn-sm view"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                    $btn = $btn.' <a href="'.route('admin.users.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" title="Edit" data-original-title="Edit" class="btn btn-primary btn-sm edit"><i class="fa fa-edit"></i></a>';
                    return $btn;
                     })->rawColumns(['action','profile_pic'])
                ->make(true);
        } 
       
        return view('admin.users.userlist');
    }

    public function Myprofile() {
        $roles = Role::where('id',' != ', 1)->get();
        $user  = Auth::user();
        $id    = $user->id;
        return view('admin.users.editprofile ',compact('user','id','roles'));
    }

    public function destroy($id) {
        $userDelete = User::find($id);
        if ($userDelete) {
            $userDelete->forceDelete();
            return redirect()->back()->with('message','User Deleted Successfully');
        }
        return redirect()->back();
    }

    public function addUser($id = null) {
        $roles = Role::where('id','!=', 1)->get();
        if ($id) {
            $user = User::find($id);
            return view('admin.users.adduser',compact('user','roles'));
        }
        return view('admin.users.adduser',compact('roles'));
    }

    public function store(RegisterRequest $request) {
        $data             = $request->only('name','email','profile_pic');
        $data['password'] = bcrypt($request->password);
        $data['role_id']  = $request->role_id;
        $user             = User::create($data);
        if ($user) {
            $mailData = [
                'name'     => $user->name,
                'email'    => $user->email,
                'password' => $request->password,
                'url'      => url('/'),
            ];
            $user->notify(new SignupNotification($mailData));
            return redirect()->route('admin.users')->with('message','User Created Successfully');
        } else {
            return redirect()->back()->with('error','Something went wrong')->withInput();
        }
    }

    public function view($id)
    {
        $users = User::find($id);
        return view('admin.users.userview')->with(compact('users'));
    }

    public function edit($id)
    {
        $users = User::find($id);
        return view('admin.users.adduser')->with(compact('users'));
    }

    public function update(Request $request,$id) {
        $email = User::where('id',$id)->value('email');
        $user = User::find($id);
        $fields = ['name', 'profile_pic','role_id'];
        foreach ($fields as $field) {
            if ($request->exists($field)) {
                switch ($field) {
                    default:
                    $user->$field = $request->$field;
                    break;
                }
            }
        }
        $user->save();
        if ($user) {
            $mailData = ['name' => $user['name'],'email' => $email,'url' => url('/'),];
            return redirect()->route('admin.users')->with('message','User Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went wrong');
        }
    }
}
