<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Reservation;
use App\Models\Inquiry;
use App\Models\UserAgent;
use App\Models\Company;
use App\Models\Trace;
use App\Models\CreditPointsHistorique;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\UpdateCreditPointsRequest;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Notifications\UserAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Storage;

class UserController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($render_view = true, $paginate = true)
    {
        $users = User::query();
       /*  if(!empty(request('q'))) {
            if(filter_var(request('q'), FILTER_VALIDATE_EMAIL)) {
            $users->where('email', request('q'))
                ->orWhere('email_pro', request('q'));
            } else {
            $users->where('nom', 'like', '%'.request('q').'%')
                ->orWhere('prenoms', 'like', '%'.request('q').'%');
            }
        }
        */
        if(!empty(request('role'))) {
            $users->whereHas('roles', function($q) {
                return $q->where('nom', 'like', '%'.request('role').'%');
            });
        }

        if(!empty(request('role_id'))) {
            $users->whereHas('roles', function($q) {
                return $q->where('id', request('role_id'));
            });
        }

        if(!$render_view && !$paginate)
        return $users->get();

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Tous des utilisateurs", 'description' => request('role')? "Consultation des utilisateurs ".request('role') : "Consultation des utilisateurs"]);
        $trace->save();

        return $render_view ? view('backend.users.index', ['users' => $users->paginate(request('perpage', 25))]) : $users->paginate(request('perpage', 25));
    }


    public function backend()
    {
        return view('backend.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.users.create', ['edit' => false]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenoms' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
            'password' => 'required'
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenoms' => $request->prenoms,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->roles()->save((Role::where('id', $request->role)->first()));
        $user->notify((new UserAccountCreated()));

        return response()->json([
            'success' => true,
            'message' => "Administrateur crée !",
            'redirect' => route('users.index', ['role' => 'admin']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Afficher profil", 'description' => "Affichage du profil ".$user->email, 'resource' => $user->id]);
        $trace->save();

        return view('backend.users.show', [
          'user' => $user,
          'roles' => Role::all(),
          'reservations' => Reservation::where('user_id', $user->id)->paginate(20),
          'inquiries' => Inquiry::with(['property', 'user', 'agent', 'status'])
            ->where('user_id', $user->id)->orwhere('agent_id', $user->id)->withCount('comments')->paginate(20)
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Editer profil", 'description' => "Consultation de la page \"Editer ".$user->email."\"", 'resource' => $user->id]);
        $trace->save();

        return view('backend.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if (auth()->check()) {
            $validated_req = $request->validated();
            $input = $request->safe()->except(['email', 'old_password', 'new_password', 'photo_file', ]);
            if($validated_req['email'] !== $user->email) {
                if(!(User::where('email', $validated_req['email'])->exists()))
                    $input['email'] = $validated_req['email'];
            }
            if(!empty($validated_req['new_password'])) {
                $input['password'] = Hash::make($validated_req['new_password']);
            }
            if($request->hasFile('photo_file') && $request->file('photo_file')) {
                $input['photo'] = $request->file('photo_file')->store('images', 'public');
            }

            $user->update($input);
        }

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Modifier profil", 'description' => "Modification du profil ".$user->email, 'resource' => $user->id]);
        $trace->save();

        return response()->json([
          'success' => true,
          'message' => "Changements enregistrés !",
          'redirect' => $validated_req['continue'] ?? route('users.edit', $user),
        ]);
    }

    public function updateCompany(UpdateCompanyRequest $request)
    {
        if (auth()->check()) {
          $validated_req = $request->validated();
          $input = $request->safe()->except(['email', 'photo_file']);
          if (!empty($validated_req['email']) && $validated_req['email'] !== auth()->user()->email) {
            if(!(User::where('email', $validated_req['email'])->exists()))
              $input['email'] = $validated_req['email'];
          }
          if ($request->hasFile('photo_file') && $request->file('photo_file')) {

            $file = $request->file('photo_file');

            $filename = 'company-' . uniqid() . $file->getClientOriginalName();
            $input['logo'] = $request->file('photo_file')->storeAs('user/company', $filename, 'public');
          }

          $input["user_id"] = auth()->user()->id;
          Company::updateOrCreate(['user_id' => auth()->id()->id], $input);
        }

        /* $trace = new Trace();
        $trace->owner()->associate(auth()->user());
        $trace->trace = "Modifier d'une compagnie";
        $trace->description = "Modification de la compagnie ".$service->label;
        $trace->resource = $service->id;
        $trace->save(); */

        return response()->json([
          'success' => true,
          'message' => "Changements enregistrés !",
          'redirect' => $validated_req['continue'] ?? route('my.company', auth()->user()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if($user->photo)
            Storage::disk('public')->delete($user->photo);
        $user->delete();

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Supprimer un utilisateur", 'description' => "Suppression de l'utilisateur ".$user->email, 'resource' => $user->id]);
        $trace->save();

        return response()->json([
            "success" => true,
            "message" => 'Le compte utilisateur a bien été supprimé.',
            "redirect" => route('users.index'),
        ]);
    }


    public function destroyMultiple(Request $request)
    {
      $this->authorize('deleteMultiple', User::class);

      if(!empty($request->input('items')) && is_array($request->input('items'))) {
        $users = User::whereIn('id', $request->items)->get();
        $users->each(function($u) {
            if($u->photo){
                Storage::disk('public')->delete($u->photo);
            }

            $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Supprimer un utilisateur", 'description' => "Suppression de l'utilisateur ".$u->email, 'resource' => $u->id]);
            $trace->save();
        });

        $users = User::whereIn('id', $request->items)->delete();

        return response()->json([
          "success" => true,
          "message" => 'Les comptes sélectionnés ont bien supprimés.',
          "redirect" => route('users.index'),
        ]);

      } else {
        abort(401, "La liste des éléments à supprimer est mal entrée.");
      }
    }


    public function syncRoles(Request $request, User $user)
    {
        $this->authorize('assign', Role::class);

        $input = $request->validate([
            'roles' => 'sometimes|array',
            'roles.*' => 'sometimes|required|integer|exists:roles,id',
        ]);
        $arr = [];
        foreach ($input['roles'] as $id) {
            $arr[$id] = ['active' => true, 'granter_id' => auth()->id(), 'granted_at' => date("Y-m-d H:i:s")];
        }
        $user->roles()->sync($arr);

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Mis à jour des rôles", 'description' => "Mis à jour des rôles"]);
        $trace->save();

        return response()->json([
            "success" => true,
            "message" => 'Rôles mis à jour !',
            "redirect" => url()->previous(),
        ]);
    }


    public function myProfile()
    {
        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Editer mon profil", 'description' => "Consultation de la page \"Editer\""]);
        $trace->save();

        return view('backend.users.edit',
            ['user' => auth()->user()]);
    }

    public function myCompany()
    {
        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Editer ma compagnie", 'description' => "Consultation de la page \"Editer\""]);
        $trace->save();

        return view('backend.users.company_edit',
            [
            'user' => auth()->user(),
            'company' => Company::where('user_id', auth()->user()->id)->first()
            ]);
    }

    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function readNotification() {
      foreach (auth()->user()->unreadNotifications as $notification) {
        if ($notification) {
          $notification->markAsRead();
        }
      }
    }
}
