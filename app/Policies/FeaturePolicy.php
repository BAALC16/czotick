<?php

namespace App\Policies;

use App\Models\Features;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeaturePolicy
{
    use HandlesAuthorization;

    /**
    * Perform pre-authorization checks.
    *
    * @param  \App\Models\User  $user
    * @param  string  $ability
    * @return void|bool
    */
    public function before(User $user, $ability)
    {
      if ($user->hasRole('Fondateur')) {
        return true;
      }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('feature.create');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Feature $feature)
    {
        return $user->hasPermission('feature.create');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('feature.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Feature $feature)
    {
        return $user->hasPermission('feature.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Feature $feature)
    {
        return $user->hasPermission('feature.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Feature $feature)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Feature $feature)
    {
        return false;
    }
}
