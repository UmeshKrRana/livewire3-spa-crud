<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class PostList extends Component
{
    use WithPagination, WithoutUrlPagination;

    public function render()
    {
        $posts = Post::paginate(5);
        return view('livewire.post-list', compact('posts'));
    }
}
