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
    public $subject;
    public function __construct($file_path, $template, $subject)
    {
        $this->file_path = $file_path;
        $this->template = $template;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)
            ->markdown('mails.checklistMail')->attach(public_path($this->file_path), [
                'as' => 'checklist.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
