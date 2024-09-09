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

    public $searchTerm = null;
    public $activePageNumber = 1;

    public $sortColumn = 'id';
    public $sortOrder = 'asc';

    public function sortBy($columnName) {
        if ($this->sortColumn === $columnName) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $columnName;
            $this->sortOrder = 'asc';
        }
    }

    /**
     * Function: fetchPosts
     * Description: This function will fetch the blog posts
     * @param NA
     * @return App\Models\Post
     */
    public function fetchPosts() {
        return Post::where('title', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('content', 'like', '%' . $this->searchTerm . '%')
        ->orderBy($this->sortColumn, $this->sortOrder)->paginate(5);
    }

    public function render()
    {
        $posts = $this->fetchPosts();
        return view('livewire.post-list', compact('posts'));
    }

    /**
     * Function: deletePost
     * Description: This function will delete the post
     * @param App\Models\Post $post
     * @return void
     */
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

        $posts = $this->fetchPosts();

        if ($posts->isEmpty() && $this->activePageNumber > 1) {
            # Redirect to the Active page - 1 (Previous Page)
            $this->gotoPage($this->activePageNumber - 1);
        }
        else {
            # Redirect to the Active Page
            $this->gotoPage($this->activePageNumber);
        }

        // return $this->redirect('/posts', navigate: true);
    }

    /**
     * Function: updatingPage
     * Description: Track the active page from pagination
     * @param integer $pageNumber
     * @return void
     */
    public function updatingPage($pageNumber) {
        $this->activePageNumber = $pageNumber;
    }
}
