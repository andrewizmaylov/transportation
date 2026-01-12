<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TransportationAddress;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransportationAddressPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, TransportationAddress $transportationAddress): bool {}

    public function create(User $user): bool {}

    public function update(User $user, TransportationAddress $transportationAddress): bool {}

    public function delete(User $user, TransportationAddress $transportationAddress): bool {}

    public function restore(User $user, TransportationAddress $transportationAddress): bool {}

    public function forceDelete(User $user, TransportationAddress $transportationAddress): bool {}
}
