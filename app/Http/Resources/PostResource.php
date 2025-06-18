<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'body'  => $this->body,
            'user'  => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ],
            'comments' => $this->comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'user' => $comment->user->name
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
