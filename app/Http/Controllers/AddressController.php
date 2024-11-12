<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    public function index()
    {
        $addresss = Address::with('user')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $addresss
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $address = Address::with('user')->find($id);

        if (!$address) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Address not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $address
            ], Response::HTTP_OK);
        }
    }

    public function users(string $user_id)
    {
        $address = Address::with('user')->where('user_id', $user_id)->get();

        if (!$address) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Address not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $address
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'address' => 'required',
            'note' => 'required',
            'province' => 'required',
            'district' => 'required',
            'subdistrict' => 'required',
            'post_code' => 'required',
            'receiver_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $address = Address::create([
                'user_id' => $request->user_id,
                'address' => $request->address,
                'note' => $request->note,
                'province' => $request->province,
                'district' => $request->district,
                'subdistrict' => $request->subdistrict,
                'post_code' => $request->post_code,
                'receiver_name' => $request->receiver_name,
            ]);
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Address created successfully',
                'data' => $address
            ], Response::HTTP_CREATED);
        }
    }

    public function update(Request $request, string $id)
    {
        $address = Address::find($id);

        if ($address->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'address' => 'required',
                'note' => 'required',
                'province' => 'required',
                'district' => 'required',
                'subdistrict' => 'required',
                'post_code' => 'required',
                'receiver_name' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $address->update($request->all());
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Address updated successfully',
                    'data' => $address
                ], Response::HTTP_CREATED);
            }
        }
    }

    public function destroy(string $id)
    {
        $address = Address::find($id);

        if ($address->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            if (!$address) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Address not found'
                ]);
            } else {
                $address->delete();
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Address deleted successfully',
                    'data' => $address
                ], Response::HTTP_CREATED);
            }
        }
    }
}
