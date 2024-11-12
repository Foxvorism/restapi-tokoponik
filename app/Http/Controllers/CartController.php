<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $carts
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $cart = Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'qty' => $request->qty,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Product added to cart successfully',
            'data' => $cart
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $cart = Cart::with('user','product')->find($id);

        if (!$cart) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Cart not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $cart
            ], Response::HTTP_OK);
        }
    }

    public function update(Request $request, string $id)
    {
        $cart = Cart::find($id);

        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $cart->update([
            'qty' => $request->qty,
        ]);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Cart item updated successfully',
            'data' => $cart
        ], Response::HTTP_CREATED);
    }

    public function destroy(string $id)
    {
        $cart = Cart::find($id);

        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            if (!$cart) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Cart item not found'
                ]);
            } else {
                $cart->delete();
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Cart item deleted successfully',
                    'data' => $cart
                ], Response::HTTP_CREATED);
            }
        }
    }
}
