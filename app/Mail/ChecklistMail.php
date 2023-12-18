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

    public $files;
    public $template;
    public $subject;
    public function __construct($files, $template,$subject)
    {
        $this->files = $files;
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
        $file_path = array();
        
        $this
            ->subject($this->subject)
            ->markdown('mails.checklistMail');
            foreach($this->files as $file){
                $this->attach(public_path($file->file_path));
            }
            return $this;
    }
}
