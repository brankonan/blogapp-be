<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\ShortPostResource;
use App\Models\Post;
use Illuminate\Http\Request;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $page =  $request->input('page') ? $request->input("page") : 1;
        $perPage =  $request->input('perPage') ? $request->input('perPage') : 10;
        if($perPage && $perPage >20){
            return response("invalid value", 400);
        }
        $skip = $page * $perPage - $perPage;
        $posts = Post::take($perPage)->skip($skip)->get();
        $metadata = [
            'metadata' =>[
                'total' =>Post::count(),
                'count' =>$posts->count(),
                'perPage' => $perPage
            ]
        ];
        return ShortPostResource::collection($posts)->additional($metadata);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $request->validated();
        $post = Post::create(
            $request->only('title', 'body')
        );
        // $post->save();
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new PostResource(Post::find($id));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response('Post Not Found', 404);
        }
        $post->update($request->only('title', 'body'));
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response('Post Not Found', 404);
        }
        $post->delete();
        return  response('Post Deleted succ', 203);
    }
}
