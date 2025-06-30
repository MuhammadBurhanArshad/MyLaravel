<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepositoryInterface {
    public function getAllPost();

    public function create(array $data) :Post;

}
