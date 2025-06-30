<?php

namespace App\Services;

use App\Repositories\PostRepositoryInterface;

class PostService
{
    /**
     * Create a new class instance.
     */
    public function __construct(public PostRepositoryInterface $postRepository)
    {
        //
    }

    public function getAllPost(){
        return $this->postRepository->getAllPost();
    }

    public function create(array $data){
        return $this->postRepository->create($data);
    }
}
