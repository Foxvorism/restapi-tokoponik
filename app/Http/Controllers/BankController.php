<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $banks
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $bank = Bank::find($id);

        if (!$bank) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Bank not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $bank
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_name' => 'required',
            'bank_name' => 'required',
            'number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $bank = Bank::create([
                'owner_name' => $request->owner_name,
                'bank_name' => $request->bank_name,
                'number' => $request->number,
            ]);
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Bank created successfully',
                'data' => $bank
            ], Response::HTTP_CREATED);
        }
    }

    public function update(Request $request, string $id)
    {
        $bank = Bank::find($id);

        $validator = Validator::make($request->all(), [
            'owner_name' => 'required',
            'bank_name' => 'required',
            'number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $bank->update($request->all());
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Bank updated successfully',
                'data' => $bank
            ], Response::HTTP_CREATED);
        }
    }

    public function destroy(string $id)
    {
        $bank = Bank::find($id);

        if (!$bank) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Bank not found'
            ]);
        } else {
            $bank->delete();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Bank deleted successfully',
                'data' => $bank
            ], Response::HTTP_CREATED);
        }
    }
}
