<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Response extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $status;
    public $college;
    public $course;
    public $name;
    public $response;
    public function __construct($status, $college, $course, $name, $response)
    {
        $this->status = $status;
        $this->college = $college;
        $this->course = $course;
        $this->name = $name;
        $this->response = $response;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->with(['response' => $this->response, 'name' => $this->name, 'course' => $this->course])
            ->subject($this->college . "-" . $this->course)
            ->markdown('mails.called');
    }
}
