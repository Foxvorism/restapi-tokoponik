<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('product_pics')->get();

        $products->each(function ($product) {
            $product->product_pics->each(function ($pic) {
                $pic->path = url($pic->path);
            });
        });

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $products
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $product = Product::with('product_pics')->find($id);

        if (!$product) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Product not found'
            ]);
        } else {
            $product->product_pics->each(function ($pic) {
                $pic->path = url($pic->path);
            });

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $product
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'type' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = "product" . $product->id . "." . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos', $fileName, 'public');

            ProductPic::create([
                'product_id' => $product->id,
                'path' => 'storage/' . $path
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Product created successfully',
            'data' => $product->load('product_pics')
        ], Response::HTTP_CREATED);
    }


    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'type' => 'required',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $product->update($request->only(['name', 'description', 'price', 'type']));

        if ($request->hasFile('photo')) {
            $existingPhoto = $product->product_pics()->first();
            if ($existingPhoto) {
                if (file_exists(public_path($existingPhoto->path))) {
                    unlink(public_path($existingPhoto->path));
                }
                $existingPhoto->delete();
            }

            $file = $request->file('photo');
            $fileName = "product" . $product->id . "." . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos', $fileName, 'public');
            ProductPic::create([
                'product_id' => $product->id,
                'path' => 'storage/' . $path
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Product updated successfully',
            'data' => $product->load('product_pics')
        ], Response::HTTP_CREATED);
    }


    public function getByName(string $name)
    {
        $products = Product::where('name', 'LIKE', "%{$name}%")->with('product_pics')->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'No products found with the given name'
            ]);
        } else {
            $products->each(function ($product) {
                $product->product_pics->each(function ($pic) {
                    $pic->path = url($pic->path);
                });
            });

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $products
            ], Response::HTTP_OK);
        }
    }

    public function getByCategory(string $category)
    {
        $products = Product::where('type', $category)->with('product_pics')->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'No products found in the given category'
            ]);
        } else {
            $products->each(function ($product) {
                $product->product_pics->each(function ($pic) {
                    $pic->path = url($pic->path);
                });
            });

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $products
            ], Response::HTTP_OK);
        }
    }

    public function getWithLimit(int $limit)
    {
        $products = Product::with('product_pics')
            ->leftJoin('ratings', 'products.id', '=', 'ratings.product_id')
            ->select('products.*', DB::raw('ROUND(AVG(ratings.rating), 2) as average_rating'))
            ->groupBy('products.id')
            ->orderBy('average_rating', 'asc')
            ->limit($limit)
            ->get();

        $products->each(function ($product) {
            $product->product_pics->each(function ($pic) {
                $pic->path = url($pic->path);
            });
        });

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $products
        ], Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Product not found'
            ]);
        } else {
            $product->delete();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Product deleted successfully',
                'data' => $product
            ], Response::HTTP_CREATED);
        }
    }
}
