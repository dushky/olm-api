<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemoPolicy
{
    use HandlesAuthorization;

    public function show(User $user): bool
    {
        return $user->canAny(['demo.show', 'user_experiment.create']);
    }

    public function create(User $user): bool
    {
        return $user->can('demo.create');
    }

    public function update(User $user): bool
    {
        return $user->can('demo.update');
    }

    public function delete(User $user): bool
    {
        return $user->can('demo.delete');
    }

    public function restore(User $user): bool
    {
        return $user->can('demo.restore');
    }
}
