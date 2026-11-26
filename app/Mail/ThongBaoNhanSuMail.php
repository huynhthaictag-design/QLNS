<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThongBaoNhanSuMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tieuDe;
    public $nguoiNhan;
    public $noiDungChinh;
    public $chiTiet;
    public $actionUrl;
    public $actionText;

    public function __construct($tieuDe, $nguoiNhan, $noiDungChinh, array $chiTiet = [], $actionUrl = null, $actionText = null)
    {
        $this->tieuDe = $tieuDe;
        $this->nguoiNhan = $nguoiNhan;
        $this->noiDungChinh = $noiDungChinh;
        $this->chiTiet = $chiTiet;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function build()
    {
        return $this->subject($this->tieuDe)
            ->view('emails.thongbao');
    }
}
