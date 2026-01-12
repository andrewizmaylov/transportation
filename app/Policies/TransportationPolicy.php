<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Transportation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransportationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, Transportation $transportation): bool {}

    public function create(User $user): bool {}

    public function update(User $user, Transportation $transportation): bool {}

    public function delete(User $user, Transportation $transportation): bool {}

    public function restore(User $user, Transportation $transportation): bool {}

    public function forceDelete(User $user, Transportation $transportation): bool {}
}
