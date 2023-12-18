<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public  $name;
    public  $logo;
    public  $course;
    public  $college;
    public function __construct($name, $logo, $course, $college)
    {
        $this->name = $name;
        $this->logo = $logo;
        $this->course = $course;
        $this->college = $college;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)->with(['name' => $this->name, 'logo_file' => $this->logo, 'course' => $this->course,'college'=> $this->college])
            ->markdown('mails.newLead');
    }
}
