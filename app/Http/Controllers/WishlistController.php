<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class WishlistController extends Controller
{
    public function user()
    {
        $wishlist = Wishlist::with(['user', 'product', 'product.product_pics'])
            ->where('user_id', Auth::id())
            ->get();

        $wishlist->each(function ($item) {
            $item->product->product_pics->each(function ($pic) {
                $pic->path = url($pic->path); // Prepend the full URL to the path
            });
        });

        if (!$wishlist) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Wishlist not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $wishlist
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $wishlist = Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ]);

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Wishlist created',
                'data' => $wishlist
            ], Response::HTTP_CREATED);
        }
    }

    public function destroy(string $id)
    {
        $wishlist = Wishlist::find($id);

        if ($wishlist->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            if (!$wishlist) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Wishlist not found'
                ]);
            } else {
                $wishlist->delete();
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Wishlist deleted successfully',
                    'data' => $wishlist
                ], Response::HTTP_CREATED);
            }
        }
    }
}
