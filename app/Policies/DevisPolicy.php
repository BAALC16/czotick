<?php

namespace App\Policies;

use App\Models\Devis;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevisPolicy
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
        return $user->hasPermission('devis.list'); // || $this->viewCreated($user) || $this->viewMine($user);
    }

    public function viewCreated(User $user)
    {
      return Devis::where('user_id', $user->id)->count() > 0;
    }

    public function viewMine(User $user)
    {
      return Devis::whereHas('reservation', function($q) use($user) {
        return $q->where('user_id', $user->id);
      })->count() > 0;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Devis  $devis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Devis $devis)
    {
      return $devis->reservation->user_id === $user->id || $devis->user_id === $user->id || $user->hasPermission('devis.list');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
      $reservation = Reservation::findOrFail(request()->route('reservation'));
      return $reservation->prestataire_id === $user->id && $user->hasPermission('devis.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Devis  $devis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Devis $devis)
    {
        return $devis->reservation->prestataire_id === $user->id || $user->hasPermission('devis.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Devis  $devis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Devis $devis)
    {
        return $devis->reservation->prestataire_id === $user->id || $user->hasPermission('devis.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Devis  $devis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Devis $devis)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Devis  $devis
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Devis $devis)
    {
        return false;
    }

    public function validate(User $user)
    {
      return $user->hasPermission('devis.validate');
    }

    public function reject(User $user)
    {
      return $user->hasPermission('devis.reject');
    }
}
