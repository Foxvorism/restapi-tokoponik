<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogPic;
use App\Models\BlogLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('user')->with('blog_pics')->with('blog_links')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $blogs
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $blog = Blog::with('user')->find($id);

        if (!$blog) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Blog not found'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $blog
            ], Response::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'links' => 'array',
            'links.*' => 'url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $blog = Blog::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::user()->id,
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $file) {
                    $fileName = "blog" . $blog->id . "pic" . ($index + 1) . "." . $file->getClientOriginalExtension();;
                    $path = $file->storeAs('photos', $fileName, 'public');
                    BlogPic::create([
                        'blog_id' => $blog->id,
                        'pic_path' => 'storage/' . $path,
                    ]);
                }
            }

            if ($request->has('links')) {
                foreach ($request->links as $link) {
                    BlogLink::create([
                        'blog_id' => $blog->id,
                        'link' => $link
                    ]);
                }
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Blog created successfully with photos',
                'data' => $blog->load('blog_pics'),
            ], Response::HTTP_CREATED);
        }
    }

    public function update(Request $request, string $id)
    {
        $blog = Blog::find($id);

        if ($blog->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $blog->update($request->all());
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Blog updated successfully',
                    'data' => $blog
                ], Response::HTTP_CREATED);
            }
        }
    }

    public function destroy(string $id)
    {
        $blog = Blog::find($id);

        if ($blog->user_id !== Auth::id()) {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'Not authorized',
            ], Response::HTTP_FORBIDDEN);
        } else {
            if (!$blog) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Blog not found'
                ]);
            } else {
                $blog->delete();
                return response()->json([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Blog deleted successfully',
                    'data' => $blog
                ], Response::HTTP_CREATED);
            }
        }
    }
}
