<?php

namespace App\Http\Controllers\Api\V1;

use App\Transformers\CommentTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Api\CommentsRequest;
use App\Post;

class PostCommentsController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->transformer = new CommentTransformer;
        $this->resourceKey = 'comments';
    }

    /**
    * Return the post's comments.
    *
    * @param  Request $request
    * @param  int $id
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request, $id)
    {
        $post = Post::find($id);

        if (! $post) {
            return $this->respondNotFound();
        }

        $comments = $post->comments()->latest()->paginate($request->input('limit', 20));

        return $this->paginatedCollection($comments);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  CommentsRequest $request
    * @param  int $id
    * @return \Illuminate\Http\Response
    */
    public function store(CommentsRequest $request, $id)
    {
        $post = Post::find($id);

        if (! $post) {
            return $this->respondNotFound();
        }

        $comment = Auth::user()->comments()->create([
            'post_id' => $post->id,
            'content' => $request->input('content')
        ]);

        return $this->setStatusCode(201)->item($comment);
    }
}
