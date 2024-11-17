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

        $blogs->each(function ($blog) {
            $blog->blog_pics->each(function ($pic) {
                $pic->pic_path = url($pic->pic_path);
            });
        });

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $blogs
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $blog = Blog::with('user')->with('blog_pics')->find($id);

        if (!$blog) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Blog not found'
            ]);
        } else {
            $blog->blog_pics->each(function ($pic) {
                $pic->pic_path = url($pic->pic_path);
            });

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
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = "blog" . $blog->id . "pic" . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs('photos', $fileName, 'public');

                BlogPic::create([
                    'blog_id' => $blog->id,
                    'pic_path' => 'storage/' . $path,
                ]);
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
                'message' => 'Blog created successfully with photo',
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
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $blog->update([
                'title' => $request->title,
                'description' => $request->description
            ]);

            if ($request->hasFile('photo')) {
                $existingPhoto = $blog->blog_pics->first();
                if ($existingPhoto) {
                    $oldPhotoPath = public_path('storage/' . $existingPhoto->pic_path);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }

                    $existingPhoto->delete();
                }

                $file = $request->file('photo');
                $fileName = "blog" . $blog->id . "pic" . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs('photos', $fileName, 'public');

                BlogPic::create([
                    'blog_id' => $blog->id,
                    'pic_path' => 'storage/' . $path,
                ]);
            }

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Blog updated successfully',
                'data' => $blog->load('blog_pics'),
            ], Response::HTTP_OK);
        }
    }

    public function getByTitle(string $title)
    {
        $blogs = Blog::with('user')->with('blog_pics')->with('blog_links')->where('title', 'like', '%' . $title . '%')->get();

        if ($blogs->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'No blogs found with the given title'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $blogs->each(function ($blog) {
                $blog->blog_pics->each(function ($pic) {
                    $pic->pic_path = url($pic->pic_path);
                });
            });

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $blogs
            ], Response::HTTP_OK);
        }
    }

    public function getWithLimit(int $limit)
    {
        $blogs = Blog::with('user')->with('blog_pics')->with('blog_links')->orderBy('created_at', 'desc')->limit($limit)->get();

        $blogs->each(function ($blog) {
            $blog->blog_pics->each(function ($pic) {
                $pic->pic_path = url($pic->pic_path);
            });
        });

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $blogs
        ], Response::HTTP_OK);
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
