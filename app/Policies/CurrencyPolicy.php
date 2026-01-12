<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, Currency $currency): bool {}

    public function create(User $user): bool {}

    public function update(User $user, Currency $currency): bool {}

    public function delete(User $user, Currency $currency): bool {}

    public function restore(User $user, Currency $currency): bool {}

    public function forceDelete(User $user, Currency $currency): bool {}
}
