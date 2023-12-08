<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'profile_photo_url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoPathAttribute($value) {
        if($value) {
            return asset('https://chatsupport.co.in/public/users/'.$value);
        }
        return asset('https://chatsupport.co.in/public/default-user.jpg');
    }

    public function activeMembership()
    {
        $today_date = date('Y-m-d');
        return $this->hasOne(Transaction::class)->where('start_date', '<=', $today_date)
                                                ->where('end_Date', '>=', $today_date)
                                                ->where('plan', '!=', 1)
                                                ->orderBy('created_at', 'desc')
                                                ->first();
    }

    public function Membership()
    {
        return $this->hasOne(Transaction::class,'user_id','id');
    }

}
