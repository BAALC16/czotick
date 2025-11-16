<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Event; // CORRECTION: Import correct du modèle Event

class ResolveEventSlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $eventSlug = $request->route('event_slug');
        
        if (!$eventSlug) {
            return $next($request);
        }
        
        // Vérifier qu'on a une organisation dans le contexte
        if (!app()->bound('current.organization')) {
            abort(500, 'Organisation non résolue. Vérifiez que le middleware tenant.resolve est appliqué avant event.resolve');
        }
        
        try {
            // Chercher l'événement dans la base tenant en utilisant DB directement
            $event = DB::connection('tenant')
                ->table('events')
                ->where('event_slug', $eventSlug)
                ->where('is_published', true)
                ->first();
            
            if (!$event) {
                // Log pour debug - vérifier si l'événement existe mais n'est pas publié
                $unpublishedEvent = DB::connection('tenant')
                    ->table('events')
                    ->where('event_slug', $eventSlug)
                    ->first();
                
                Log::warning('Événement non trouvé', [
                    'event_slug' => $eventSlug,
                    'organization' => app('current.organization')->org_name ?? 'Unknown',
                    'url' => $request->url(),
                    'event_exists' => $unpublishedEvent ? true : false,
                    'is_published' => $unpublishedEvent->is_published ?? false,
                    'all_events_with_slug' => DB::connection('tenant')
                        ->table('events')
                        ->where('event_slug', 'like', '%' . $eventSlug . '%')
                        ->select('id', 'event_slug', 'event_title', 'is_published')
                        ->get()
                ]);
                
                abort(404, 'Événement non trouvé ou non publié');
            }
            
            // Convertir en modèle Event pour la compatibilité
            $eventModel = Event::on('tenant')->find($event->id);
            
            if (!$eventModel) {
                Log::error('Erreur lors de la conversion de l\'événement en modèle', [
                    'event_id' => $event->id,
                    'event_slug' => $eventSlug
                ]);
                abort(500, 'Erreur lors du chargement de l\'événement');
            }
            
            // Stocker l'événement dans le contexte
            app()->instance('current.event', $eventModel);
            
            // Log succès
            Log::info('Événement résolu avec succès', [
                'event_id' => $eventModel->id,
                'event_title' => $eventModel->event_title,
                'event_slug' => $eventModel->event_slug,
                'is_published' => $eventModel->is_published,
                'organization' => app('current.organization')->org_name
            ]);
            
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la résolution de l\'événement', [
                'event_slug' => $eventSlug,
                'organization' => app('current.organization')->org_name ?? 'Unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if (app()->environment('local')) {
                throw $e; // En développement, montrer l'erreur complète
            }
            
            abort(500, 'Erreur lors du chargement de l\'événement');
        }
    }
}