<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(public PostService $postService){}

    public function index() {
        return $this->postService->getAllPost();
    }

    public function store(){
        $data = ['name' => 'Post Title', 'description' => 'Post Description'];
        $post = $this->postService->create($data);
        dd($post->toArray());
    }
}
