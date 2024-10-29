<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $users
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'User not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $user
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|min:6',
            'role' => 'required|string',
            'phone_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => $request->password,
                'role' => $request->role,
                'phone_number' => $request->phone_number,
            ]);
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'User created successfully',
                'data' => $user
            ], Response::HTTP_CREATED);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|min:6',
            'role' => 'required|string',
            'phone_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $user->update($request->all());
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'User updated successfully',
                'data' => $user
            ], Response::HTTP_CREATED);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'User not found'
            ]);
        } else {
            $user->delete();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'User deleted successfully',
                'data' => $user
            ], Response::HTTP_CREATED);
        }
    }
}
