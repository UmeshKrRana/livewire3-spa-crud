<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;

class PostList extends Component
{
    use WithPagination, WithoutUrlPagination;

    #[Title('Livewire 3 CRUD - Posts Listing')]

    public function render()
    {
        $posts = Post::orderBy('id', 'DESC')->paginate(5);
        return view('livewire.post-list', compact('posts'));
    }

    public function deletePost(Post $post) {
        if ($post) {

            # Delete Featured Image
            if (Storage::exists($post->featured_image)) {
                Storage::delete($post->featured_image);
            }

            $deleteResponse = $post->delete();

            if ($deleteResponse) {
                session()->flash('success', 'Post deleted successfully!');
            } else {
                session()->flash('error', 'Unable to delete Post. Please try again!');
            }
        }
        else {
            session()->flash('error', 'Post not found. Please try again!');
        }

        return $this->redirect('/posts', navigate: true);
    }
}
