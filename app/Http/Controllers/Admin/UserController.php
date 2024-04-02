<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // getusers
    public function index(){
        $users = User::latest()->get()->map(function ($user){
            return [
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'created_at'=>$user->created_at->format('Y-m-d'),   // format('Y-m-d') 对应数据库中的内容不能为空，否则报错
                'role'=>$user->role,
            ];
        });

        return $users;
    }
    public function store(){

        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password'=>'required|min:8',
        ]);

        $user = User::create([
            'name' => request('name'),
            'email'=> request('email'),
            'password'=>bcrypt(request('password')),
        ]);

        return $user;
    }
    public function update(User $user){
        request()->validate([
            'name' =>'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password'=>'sometimes|min:8',
        ]);
        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
        ]);

        return $user;
    }
    public function destory(User $user){
        $user->delete();

        return response()->noContent();
    }

    public function changeRole(User $user){
        
        $user->update([
            'role' => request('role'),
        ]);

        return response()->json(['success' => true]);
    }

    public function search(){
        $searchQuery = request('query');

        $users = User::where('name','like',"%{$searchQuery}%")->get();

        return response()->json($users);
    }
}
