<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RatingController extends Controller
{
    public function index()
    {
        $ratings = Rating::with('user', 'product')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $ratings
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $rating = Rating::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Rating created successfully',
                'data' => $rating
            ], Response::HTTP_CREATED);
        }
    }

    public function show(string $id)
    {
        $rating = Rating::with('user', 'product')->find($id);

        if (!$rating) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Rating not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $rating
            ], Response::HTTP_OK);
        }
    }

    public function update(Request $request, string $id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Rating not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($rating->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $rating->update($request->all());
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Rating updated successfully',
                    'data' => $rating
                ], Response::HTTP_CREATED);
            }
        }
    }

    public function destroy(string $id)
    {
        $rating = Rating::find($id);

        if ($rating->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            if (!$rating) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Rating not found'
                ]);
            } else {
                $rating->delete();
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Rating deleted successfully',
                    'data' => $rating
                ], Response::HTTP_CREATED);
            }
        }
    }

    public function getAverage($productId)
    {
        $averageRating = number_format(Rating::where('product_id', $productId)->avg('rating'), 2);

        if (is_null($averageRating)) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Product not found or no ratings available'
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'average_rating' => $averageRating
            ], Response::HTTP_OK);
        }
    }
}
