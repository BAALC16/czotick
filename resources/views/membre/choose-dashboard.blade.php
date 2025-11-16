@extends('public.layouts')
@section('title')
  Choisissez un backoffice
@endsection
@section('content')
<section class="py-8 mb-15">
  <div class="container">
    <h1>Hello {{$user->prenoms}},</h1>
    <h5>Continuez vers un Backoffice...</h5>
    <div class="mt-8 row">
      @foreach($roles as $key => $role)
        <div class="col-md-4 mb-6">
          <div class="card border-0 shadow-xxs-2 login-register">
            <div class="card-body text-center p-8">
              <h3 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">{{$role->nom}}</h3>
              <div class="mb-5 text-muted">
                {{$role->description}}
              </div>
              <div class="pt-4 pb-8">
                <span style="font-size:64pt;" class="{{ $role->icone ?? 'far fa-user-circle' }}"></span>
              </div>
              <a href="{{ url('backoffice/'.($role->backoffice_route ?? 'membre')) }}" class="btn btn-outline-primary btn-lg rounded">Continuer <span class="far fa-arrow-right ml-3"></span></a>
            </div>
          </div>
        </div>
        
      @endforeach
    </div>
  </div>
</section>
@endsection