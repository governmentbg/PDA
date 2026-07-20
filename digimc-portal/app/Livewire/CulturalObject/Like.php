<?php

namespace App\Livewire\CulturalObject;

use Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Like extends Component
{

    public int $cultural_object_id;
    public bool $liked = false;

    public function mount(int $cultural_object_id, bool $initial_state = null)
    {
        $this->cultural_object_id = $cultural_object_id;

        if (!is_null($initial_state)) {
            $this->liked = $initial_state;
        } else {
            $this->checkLikeStatus();
        }
    }

    public function checkLikeStatus()
    {
        $this->liked = Auth::user()
            ? Auth::user()->likes()->where('cultural_object_id', $this->cultural_object_id)->exists()
            : false;
    }


    #[On('refreshLikes')]
    public function refreshLikes()
    {
        $this->checkLikeStatus();
    }

    public function toggleLike()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($this->liked) {
            $user->likes()->where('cultural_object_id', $this->cultural_object_id)->delete();
            $this->liked = false;
        } else {
            $user->likes()->create(['cultural_object_id' => $this->cultural_object_id]);
            $this->liked = true;
        }
    }

    public function render()
    {
        return view('livewire.cultural_object.like');
    }
}
