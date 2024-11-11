<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $products
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Product not found'
            ]);
        } else {
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
            'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'type' => $request->type
            ]);
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Product created successfully',
                'data' => $product
            ], Response::HTTP_CREATED);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $file) {
                    $fileName = "product" . $product->id . "pic" . ($index + 1) . "." . $file->getClientOriginalExtension();;
                    $path = $file->storeAs('photos', $fileName, 'public');
                    ProductPic::create([
                        'product_id' => $product->id,
                        'path' => 'storage/'. $path
                    ]);
                }
            }
        }
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $product->update($request->all());
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Product updated successfully',
                'data' => $product
            ], Response::HTTP_CREATED);
        }
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
