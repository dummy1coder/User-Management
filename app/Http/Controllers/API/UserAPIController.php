<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserAPIController extends BaseController
{
    public function index()
    {
        $users = User::all();
        return $this->sendResponse($users, 'Users retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->sendResponse($user, 'User created successfully.');
    }

    public function show($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user->update($request->only('name', 'email'));

        return $this->sendResponse($user, 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        $user->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }

    public function changePassword(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->sendResponse([], 'Password updated successfully.');
    }
}
