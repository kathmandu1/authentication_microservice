<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Notifications\ResetPasswordNotification;
use App\Services\Wishlist\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use EagerLoadPivotTrait;
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    // use SoftDeletes;

    public const PAGE_SIZE = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'dob',
        'address',
        'shipping_address',
        'city',
        'type',
        'password',
        'provider',
        'provider_id',
        'provider_token',
        'email_verified_at',
        'userable_id',
        'user_source',
        'email_verified_at',
        'phone_verify_at',
        'device_token',
    ];

    protected $dates = ['deleted_at'];

    protected $appends = [ 'verify_at'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function media(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable');
    }

    public function getImage(): string
    {
        return $this->media
            ? asset('storage/' . $this->media->path . '/' . $this->media->name) :
            asset('img/blank-image.svg');
    }

    //can have many service offers
    public function serviceOffers()
    {
        return $this->hasMany(ServiceOffer::class);
    }

    //vendor user should have at least one category
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function category()
    {
        return $this->categories()->first();
    }

    //can order many services
    public function orderServices()
    {
        return $this->hasMany(OrderService::class, 'order_by');
    }

    //can purchase many PurchaseServices through OrderServices
    public function purchaseServices()
    {
        return $this->hasManyThrough(PurchaseService::class, OrderService::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupon');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(CustomerShipping::class);
    }

    public function defaultShippingAddresses()
    {
        return $this->hasMany(CustomerShipping::class)->where('active', true);
    }

    public function getVerifyAtAttribute()
    {
        return $this->email_verified_at;
    }

}
