<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Models\BlogConfiguration;
use App\Models\User;
use App\Models\Role;
use App\Models\Trace;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Notifications\UserAccountCreated;
use Laravel\Socialite\Facades\Socialite;

class RegisteredUserController extends Controller
{

    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     *
     */
    public function create()
    {
        return view('auth.register');
        //return response("Not applicable");
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $bonus= BlogConfiguration::find(1);
        $request->validate([
            'nom' => ['required', 'string', 'between:2,64'],
            'prenoms' => ['required', 'string', 'between:2,64'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'points_disponibles'=> $bonus->bonusRegister,//
            'nom' => \ucwords($request->nom),
            'prenoms' => \ucwords($request->prenoms),
            'email' => $request->email,
            'points_disponibles'=> $bonus->bonusRegister,//
            'password' => Hash::make($request->password),
        ]);
        $user->roles()->save((Role::where('nom', 'Membre')->first()));
        $user->notify((new UserAccountCreated()));

        event(new Registered($user));

        Auth::login($user);

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Création d'un compte", 'description' => "Création du compte ".$user->email, 'resource' => $user->id]);
        $trace->save();

        return response()->json([
          'success' => true,
          'redirect' => RouteServiceProvider::HOME,
        ]);

        // return redirect(RouteServiceProvider::HOME);
    }

    public function facebookStore() {
        $bonus= BlogConfiguration::find(1);
        $driver = 'facebook';
        $provUser = Socialite::driver($driver)->fields([
            'name', 'first_name', 'last_name', 'email'
        ])->user();

        if (!empty($provUser) && ($provUser->user)) {
            $user = User::where('email', $provUser->email)->first();
            //user exists
            if ($user) {
                $user->update([
                    'provider' => $driver,
                    'provider_id' => $provUser->id,
                    'provider_token' => $provUser->token,
                    'provider_refresh_token' => $provUser->refreshToken,
                ]);
            }
            else {
                //dd($provUser);
                $user = User::create(
                    [
                        'points_disponibles'=> $bonus->bonusRegister,//
                        'email' => $provUser->email,
                        'nom' => $provUser->user["last_name"],
                        'prenoms' => $provUser->user["first_name"],
                        'points_disponibles'=> $bonus->bonusRegister,//
                        'provider' => $driver,
                        'provider_id' => $provUser->id,
                        'provider_token' => $provUser->token,
                        'provider_refresh_token' => $provUser->refreshToken,
                    ]
                );

                $user->roles()->save((Role::where('nom', 'Membre')->first()));
                $user->notify((new UserAccountCreated()));

                event(new Registered($user));

            }

            Auth::login($user);

            $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Création d'un compte via facebook", 'description' => "Création du compte ".$user->email, 'resource' => $user->id]);
            $trace->save();

            return redirect()->intended(RouteServiceProvider::HOME . '#');
        }

        //return redirect()->route('login')->withErrors(['provider' => 'Unknown error']);

    }

    public function googleStore() {
        $bonus= BlogConfiguration::find(1);
        $driver = 'google';
        $provUser = Socialite::driver($driver)->user();

        if (!empty($provUser) && ($provUser->user)) {
            $user = User::where('email', $provUser->email)->first();
            //user exists
            if ($user) {
                $user->update([
                    'provider' => $driver,
                    'provider_id' => $provUser->id,
                    'provider_token' => $provUser->token,
                    'provider_refresh_token' => $provUser->refreshToken,
                ]);
            }
            else {
                //dd($gUser);
                $user = User::create(
                    [
                        'points_disponibles'=> $bonus->bonusRegister,//
                        'email' => $provUser->email,
                        'nom' => $provUser->user["family_name"],
                        'prenoms' => $provUser->user["given_name"],
                        'points_disponibles'=> $bonus->bonusRegister,//
                        'provider' => $driver,
                        'provider_id' => $provUser->id,
                        'provider_token' => $provUser->token,
                        'provider_refresh_token' => $provUser->refreshToken,
                    ]
                );

                $user->roles()->save((Role::where('nom', 'Membre')->first()));
                $user->notify((new UserAccountCreated()));

                event(new Registered($user));
            }

            Auth::login($user);

            $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Création d'un compte via google", 'description' => "Création du compte ".$user->email, 'resource' => $user->id]);
            $trace->save();

            return redirect()->intended(RouteServiceProvider::HOME);
        }
        //return redirect()->route('login')->withErrors(['provider' => 'Unknown error']);
    }
}
