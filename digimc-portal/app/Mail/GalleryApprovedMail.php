<?php

namespace App\Mail;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GalleryApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Gallery $gallery;

    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function build()
    {
        return $this->subject(__('gallery.approved_subject', ['app_name' => config('app.name')]))
            ->view('emails.gallery.approved', [
                'gallery' => $this->gallery,
            ]);
    }
}
