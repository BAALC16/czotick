<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InquiryPolicy
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
        return $this->viewMine($user) || $user->hasPermission('inquiry.list');
    }


    public function viewAssigned(User $user)
    {
      return Inquiry::where('agent_id', $user->id)->count() > 0;
    }

    public function viewMine(User $user)
    {
      return Inquiry::where('user_id', $user->id)->count() > 0 ||
        Inquiry::where('agent_id', $user->id)->count() > 0;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Inquiry $inquiry)
    {
        return $inquiry->user_id === $user->id ||
        $inquiry->agent_id === $user->id ||
        $user->getIsAdminAttribute();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create($user = null)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Inquiry $inquiry)
    {
        return $user->getIsAdminAttribute() || $user->id === $inquiry->agent_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Inquiry $inquiry)
    {
        return $user->hasPermission('inquiry.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Inquiry $inquiry)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Inquiry $inquiry)
    {
        return false;
    }

    public function assign(User $user)
    {
      return $user->hasPermission('inquiry.assign');
    }
}
