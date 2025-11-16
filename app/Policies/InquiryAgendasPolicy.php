<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\InquiryAgenda;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InquiryAgendasPolicy
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
        return $this->viewMine($user) || $user->hasPermission('agendas.index');
    }


    public function viewAssigned(User $user) 
    {
      return InquiryAgenda::where('agent_id', $user->id)->count() > 0;
    }

    public function viewMine(User $user)
    {
        return InquiryAgenda::where('user_id', $user->id)->where('status', 0)->count() > 0 ||
        InquiryAgenda::where('agent_id', $user->id)->where('status', 0)->count() > 0 ;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, InquiryAgenda $inquiryAgenda)
    {
        return $inquiryAgenda->user_id === $user->id ||
        $inquiryAgenda->agent_id === $user->id ||
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
    public function update(User $user, InquiryAgenda $inquiryAgenda)
    {
        return $user->getIsAdminAttribute() || $user->id === $inquiryAgenda->agent_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, InquiryAgenda $inquiryAgenda)
    {
      return $user->getIsAdminAttribute() || $user->id === $inquiryAgenda->agent_id;
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

}
