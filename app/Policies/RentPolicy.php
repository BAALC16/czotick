<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rent;
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
        return $this->viewMine($user) || $user->hasPermission('rent.list');
    }

    public function viewAssigned(User $user)
    {
        return Rent::where('agent_id', $user->id)->count() > 0;
    }

    public function viewMine(User $user)
    {
       return Rent::where('user_id', $user->id)->count() > 0 ||
              Rent::where('agent_id', $user->id)->count() > 0;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rent $rent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Rent $rent)
    {
        return $rent->user_id === $user->id ||
            $rent->agent_id === $user->id ||
            $user->getIsAdminAttribute();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->getIsAdminAttribute();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rent $rent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Rent $rent)
    {
        return $user->getIsAdminAttribute() || $user->id === $rent->agent_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rent $rent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Rent $rent)
    {
        return $user->getIsAdminAttribute() || $user->id === $rent->agent_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rent $rent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Rent $rent)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rent $rent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Rent $rent)
    {
        return false;
    }

    public function validate(User $user)
    {
        return $user->getIsAdminAttribute();
    }
}
