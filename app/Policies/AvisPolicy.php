<?php

namespace App\Policies;

use App\Models\Avis;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvisPolicy
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
      if ($user->hasAnyRole(['Fondateur', 'Administrateur'])) {
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
        return $user->hasPermission('avis.list');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Avis  $avis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Avis $avis)
    {
        return $user->hasPermission('avis.list');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Avis  $avis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Avis $avis)
    {
        return $user->hasPermission('avis.validate');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Avis  $avis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Avis $avis)
    {
        return $user->hasPermission('avis.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Avis  $avis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Avis $avis)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Avis  $avis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Avis $avis)
    {
        //
    }

    public function validate(User $user, Avis $avis)
    {
      return $user->hasPermission('avis.validate');
    }
}
