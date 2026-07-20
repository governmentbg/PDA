<?php

namespace App\Mail;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GalleryPublishRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Gallery $gallery;

    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function build()
    {
        return $this->subject(__('gallery.publish_requested_subject', ['app_name' => config('app.name'),]))
            ->view('emails.gallery.publish_requested', [
                'gallery' => $this->gallery,
            ]);
    }
}
