<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Cart;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['transaction_detail', 'bank'])
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $transactions,
        ], Response::HTTP_OK);
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        // Retrieve all cart items for the user with status "pending"
        $cartItems = Cart::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with('product') // Ensure we load the product relationship to access prices
            ->get();
    
        // Calculate the grand total from cart items
        $grandTotal = $cartItems->sum(function ($cartItem) {
            return $cartItem->qty * $cartItem->product->price;
        });
    
        // Create the transaction
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'bank_id' => $request->bank_id,
            'grand_total' => $grandTotal,
            'status' => 'pending',
            'proof' => null,
        ]);
    
        // Move items from cart to transaction details and update cart status
        foreach ($cartItems as $cartItem) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $cartItem->product_id,
                'qty' => $cartItem->qty
            ]);
    
            // Update cart item status to "checkout"
            $cartItem->update(['status' => 'checkout']);
        }
    
        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], Response::HTTP_CREATED);
    }

    public function addProof(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$transaction) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Transaction not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        } else {
            // Save the proof image
            if ($request->hasFile('proof')) {
                $file = $request->file('proof');
                $fileName = "proof" . $transaction->id . "pic." . $file->getClientOriginalExtension();
                $path = $file->storeAs('proof', $fileName, 'public');
                
                $transaction->update([
                    'proof' => $path,
                    'status' => 'waiting for verification',
                ]);
            }
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Proof uploaded successfully',
                'data' => $transaction,
            ], Response::HTTP_OK);
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Transaction not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $transaction->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Transaction status updated successfully',
            'data' => $transaction,
        ], Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        // Find the transaction by ID
        $transaction = Transaction::find($id);

        // Check if the transaction exists
        if (!$transaction) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Transaction not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Delete associated transaction details
        $transaction->transaction_detail()->delete();

        // Delete the transaction itself
        $transaction->delete();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Transaction deleted successfully'
        ], Response::HTTP_OK);
    }
}
