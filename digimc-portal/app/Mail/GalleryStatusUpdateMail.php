<?php

namespace App\Mail;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GalleryStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Gallery $gallery;
    public string $reason;
    public string $actionType;

    public function __construct(Gallery $gallery, string $reason, string $actionType)
    {
        $this->gallery = $gallery;
        $this->reason = $reason;
        $this->actionType = $actionType;
    }

    public function build()
    {
        $subjectKey = ($this->actionType === 'reject')
            ? 'gallery.rejected_subject'
            : 'gallery.unpublished_subject';

        return $this->subject(__($subjectKey, ['app_name' => config('app.name')]))
            ->view('emails.gallery.status_update', [
                'gallery' => $this->gallery,
                'reason' => $this->reason,
                'actionType' => $this->actionType,
            ]);
    }
}
