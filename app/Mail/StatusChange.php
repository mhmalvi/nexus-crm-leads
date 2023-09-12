<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusChange extends Mailable
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
    //  public $response;
    public function __construct($status, $college, $course, $name)
    {
        $this->status = $status;
        $this->college = $college;
        $this->course = $course;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->status == 2) {
            return $this
                ->subject($this->college . "-" . $this->course)
                ->markdown('mails.skilled');
        } else if ($this->status == 4) {
            return $this->with(['name' => $this->name])
                ->subject($this->college . "-" . $this->course)
                ->markdown('mails.paid');
        } else if ($this->status == 5) {
            return $this->with(['name' => $this->name])
                ->subject($this->college . "-" . $this->course)
                ->markdown('mails.verified');
        } else if ($this->status == 6) {
            return $this->with(['name' => $this->name, 'course' => $this->course])
                ->subject($this->college . "-" . $this->course)
                ->markdown('mails.completed');
        }
    }
}
