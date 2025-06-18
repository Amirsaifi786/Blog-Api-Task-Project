<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    // public function index()
    // {
    //     return PostResource::collection(Post::with(['user', 'comments'])->paginate(10));
    // }
    public function index(Request $request)
{
    $posts = Post::with('user','comments')->latest()->paginate(10); // 10 posts per page
    return PostResource::collection($posts);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
        ]);

        $post = $request->user()->posts()->create($data);

        return new PostResource($post);
    }
//    public function store(Request $request) //MULTIPLE POST CREATE
// {
//     $validated = $request->validate([
//         '*.title' => 'required|string|max:255',
//         '*.body' => 'required|string',
//     ]);

//     $user = $request->user();
//     $createdPosts = [];

//     foreach ($validated as $postData) {
//         $createdPosts[] = $user->posts()->create($postData);
//     }

//     return response()->json([
//         'message' => count($createdPosts) . ' posts created successfully.',
//         'data' => PostResource::collection($createdPosts)
//     ], 201);
// }


    public function show(Post $post)
    {
        return new PostResource($post->load('user', 'comments.user'));
    }

    public function update(Request $request, Post $post)
    {
        // $this->authorize('update', $post);

        $data = $request->validate([
            'title' => 'string|max:255',
            'body' => 'string',
        ]);

        $post->update($data);

        return new PostResource($post);
    }

    // public function destroy(Post $post)
    // {
    //     $this->authorize('delete', $post);
    //     $post->delete();
    //     return response()->json(null, 204);
    // }
    public function destroy(Post $post)
{
    // Make sure the logged-in user owns the post
    if (auth()->id() !== $post->user_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $post->delete();

    return response()->json(['message' => 'Post deleted successfully'], 204);
}
public function addComment(Request $request, Post $post)
{
    
    $request->validate([
        'body' => 'required|string',
    ]);

    $comment = $post->comments()->create([
        'body' => $request->body,
        'user_id' => auth()->id(),
        '_id' => auth()->id(),
    ]);

    return response()->json(['comment' => $request->all()], 201);
}


}
