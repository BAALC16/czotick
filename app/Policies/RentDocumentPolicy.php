<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RentDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentPolicy
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
        //
    }

    public function viewAssigned(User $user)
    {
        //
    }

    public function viewMine(User $user)
    {
       //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //return $user->getIsAdminAttribute() || $user->id === $rent->agent_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RentDocument $rentDocument
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RentDocument $rentDocument)
    {
        return $user->getIsAdminAttribute() || $user->id === $rentDocument->agent_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RentDocument $rentDocument
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RentDocument $rentDocument)
    {
        return $user->getIsAdminAttribute() || $user->id === $rentDocument->agent_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RentDocument $rentDocument
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, RentDocument $rentDocument)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RentDocument $rentDocument
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, RentDocument $rentDocument)
    {
        return false;
    }

    public function validate(User $user)
    {
        return $user->getIsAdminAttribute();
    }
}
