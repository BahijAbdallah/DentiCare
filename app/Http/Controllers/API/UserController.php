<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function index() {
        if (Auth::user()->role !== 'Admin') {
            return response()->json([
                'message'=> 'Unauthorized'
            ], 403);
        }

        $users = User::all();
        return response()->json($users, 200);
    }

    public function show($id) {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'message'=> 'User not found'
            ], 404);
        }

        return response()->json($user, 200);
    }

    public function update(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message'=> 'User not found'
            ], 404);
        }

        // Allow only account holder or admin to make updates for the concerned account
        if (Auth::user()->id !== $user->id && Auth::user()->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }        

        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8|confirmed',
            'phone_number' => 'sometimes|string|max:20',
            'dob' => 'sometimes|date',
            'address' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:Patient,Admin,Doctor'
        ];

        $validatedData = $request->validate($rules);

        // Update user details
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        }

        $user->update($validatedData);

        return response()->json([
            'message'=> 'User updated successfully',
            'user'=> $user
        ], 200);
    }

    public function destroy($id) {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'message'=> 'User not found'
            ], 404);
        }

        // allow only account holder or admin to delete the concerned account
        if (Auth::user()->id !== $user->id && Auth::user()->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }        

        $user->delete();
        return response()->json([
            'message'=> 'User deleted successfully'
        ], 200);
    }
}
