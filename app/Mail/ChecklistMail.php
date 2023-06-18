<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChecklistMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $file_path;
    public $template;
    public function __construct($file_path, $template)
    {
        $this->file_path = $file_path;
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Lead checklist')
            ->markdown('mails.checklistMail')->attach(public_path($this->file_path), [
                'as' => 'checklist.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
