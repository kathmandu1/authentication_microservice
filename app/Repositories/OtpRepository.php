<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\VerificationCode;

class OtpRepository extends AbstractRepository
{
    public static string $modelClass = VerificationCode::class;

    public function getOtpList()
    {
        return $this->query();
    }

    public function optOfUser($user_id)
    {
        return $this->query()->where('user_id', $user_id)->unsedOtp();
    }

    public function usedOtp()
    {
        return $this->query()->where('status', true);
    }

    public function unsedOtp()
    {
        return $this->query()->where('status', false);
    }
}
