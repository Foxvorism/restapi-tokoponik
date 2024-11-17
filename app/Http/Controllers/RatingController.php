<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
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
                'user_id' => Auth::id(),
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

    public function productRating(string $product_id)
    {
        $rating = Rating::with('user', 'product')->where('product_id', $product_id)->get();

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

    public function productRatingLimit(string $product_id, string $limit)
    {
        $rating = Rating::with('user', 'product')->where('product_id', $product_id)->limit($limit)->get();

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

    public function getAverage($productId)
    {
        $product = Rating::where('product_id', $productId);
        $averageRating = number_format($product?->avg('rating'), 2);

        if (!$product) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Product not found or no ratings available',
                'average_rating' => "0.00"
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'average_rating' => $averageRating
            ], Response::HTTP_OK);
        }
    }

    public function getReview($productId)
    {
        $reviewCount = Rating::where('product_id', $productId)->count();

        if ($reviewCount === 0) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Product not found or no reviews available',
                'review_count' => 0
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'review_count' => $reviewCount
            ], Response::HTTP_OK);
        }
    }
}
