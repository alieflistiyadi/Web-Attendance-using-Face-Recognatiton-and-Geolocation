<?php
// app/Services/FonnteService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected $token;
    protected $endpoint = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    public function sendOtp($no_hp, $otp)
    {
        $message = "Kode OTP reset password Anda: {$otp}\nBerlaku 2 menit. Jangan berikan kode ini ke siapa pun.";

        return Http::withHeaders([
            'Authorization' => $this->token,
        ])->post($this->endpoint, [
                    'target' => $no_hp,
                    'message' => $message,
                ])->json();
    }
}
?>
<!-- ini fonnte service.php -->