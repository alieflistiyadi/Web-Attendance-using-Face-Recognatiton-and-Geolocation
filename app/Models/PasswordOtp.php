<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordOtp extends Model
{
    protected $fillable = ['nis', 'no_hp', 'otp_code', 'expires_at', 'is_verified'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
?>

<!-- ini passwordOtp.php -->