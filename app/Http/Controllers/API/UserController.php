<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        $users = User::select('id', 'name', 'email')->get();
        return response()->json(['success' => true, 'data' => $users]);
    }

    // Create a new user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => true, 'data' => $user]);
    }

    // Update user name/email
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => $validator->errors()], 422);
        }

        $user->update($request->only('name', 'email'));

        return response()->json(['success' => true, 'data' => $user]);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    // Change user password
    public function changePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated']);
    }
}
