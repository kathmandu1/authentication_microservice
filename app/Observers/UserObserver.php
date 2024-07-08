<?php

namespace App\Observers;

use App\DTO\ReferralCoupon\ReferralCouponData;
use App\Jobs\Registration\WelcomeJob;
use App\Mail\WelcomeEmail;
use App\Models\Coupon;
use App\Models\Setting;
use App\Models\user;
use App\Services\Coupon\ReferralCouponService;
use App\Utilities\ReferralCouponUtility;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    public function __construct(
        // protected Coupon $coupon,
        // protected ReferralCouponUtility $referralCouponUtility,
        // protected ReferralCouponService $referralCouponService
    ) {
    }

    /**
     * Handle the user "created" event.
     */
    public function created(user $user): void
    {
        $user->email === 'admin@merodiscount.com'
            ? $user->assignRole('admin')
            : $user->assignRole('user');

        // Mail::to($user->email)->send(new WelcomeEmail($user));
        WelcomeJob::dispatch($user);

    }


}
