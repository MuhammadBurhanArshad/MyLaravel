<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(private Post $post)
    {
        //
    }

    public function create($data): Post {
        $post = $this->post->create($data);
        return $post;
    }

    public function getAllPost() {
        return $this->post->all();
    }
}
