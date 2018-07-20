<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    //
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit',compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
//        DB::connection()->enableQueryLog();
        $user->update($request->all());
//        print_r(DB::getQueryLog());
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
