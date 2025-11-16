<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
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
        return $user->hasPermission('properties.list');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Property $property)
    {
        return $user->getIsAdminAttribute() || $user->id === $property->user_id; //hasPermission('properties.create');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('properties.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Property $property)
    {
        return $user->getIsAdminAttribute() || $user->id === $property->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Property $property)
    {
        return $user->getIsAdminAttribute() || $user->id === $property->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Property $property)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property $property
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Property $property)
    {
        return false;
    }

    public function validate(User $user)
    {
        return $user->getIsAdminAttribute();
    }
}
