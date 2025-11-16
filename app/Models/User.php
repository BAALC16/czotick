<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Laravel\Sanctum\HasApiTokens;
use Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $perms = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'nom',
        'prenoms',
        'photo',
        'titre',
        'introduction',
        'ville',
        'code_pays',
        'email_pro',
        'mobile',
        'telephone',
        'site_web',
        'twitter',
        'facebook',
        'instagram',
        'linkedin',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'points_disponibles',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
      return $this->prenoms.' '.$this->nom;
    }

    public function getPhotoUrlAttribute()
    {
      if($this->photo && !FacadesStorage::disk('public')->missing($this->photo)) {
        return FacadesStorage::url($this->photo);
      } else {
        return asset('images/my-profile.png');
      }
    }

    public function roles()
    {
      return $this->belongsToMany(Role::class, 'user_roles')->withPivot('active', 'granter_id', 'granted_at');
    }

    public function roles_actifs()
    {
      return $this->belongsToMany(Role::class, 'user_roles')
        ->withPivot('active', 'granter_id', 'granted_at')
        ->wherePivot('active', true);
    }

    public function hasRole($role)
    {
      return $this->roles_actifs->contains('nom', $role);
    }

    public function hasAnyRole($list = [])
    {
      if(empty($list)) return false;
      foreach ($list as $role) {
        if($this->hasRole($role)) return true;
      }
      return false;
    }

    public function getPermissionsAttribute()
    {
      if(null !== $this->perms) {
        return $this->perms;
      } else {
        $permissions = $this->roles_actifs->pluck('permissions');
        $collection = collect([]);
        foreach ($permissions as $p) {
          foreach ($p as $item) {
            $collection->push($item);
          }
        }
        $this->perms = $collection;
      }
      return $this->perms;
    }

    public function hasPermission($code)
    {
      $permissions = $this->permissions;
      return $permissions->contains('code', $code);
    }

    public function hasAnyPermission($list = [])
    {
      $permissions = $this->permissions;
      if(empty($permissions))
        return false;
      foreach ($list as $code) {
        if($this->hasPermission($code))
          return true;
      }
      return false;
    }

    public function hasAllPermission($list = [])
    {
      $permissions = $this->permissions;
      if(empty($permissions))
        return false;
      foreach ($list as $code) {
        if(!($this->hasPermission($code)))
          return false;
      }
      return true;
    }

    public function getIsAdminAttribute()
    {
      return $this->hasAnyRole(['Fondateur', 'Administrateur']);
    }

    public function getIsAdmin()
    {
      return $this->hasAnyRole(['Redacteur', 'Administrateur']);
    }

    public static function getAdmins() {
      return User::whereHas('roles_actifs', function($q) {
        return $q->whereIn('nom', ['Fondateur', 'Administrateur']);
      })->get();
    }

    public function properties() {
      return $this->hasMany(Property::class);
    }
    public function articles() {
      return $this->hasMany(Article::class);
    }

    public function companies() {
      return $this->hasMany(Company::class);
    }

    //////
    public function historiques(){
      return $this->hasMany(Historique::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'code_pays', 'code');
    }
}
