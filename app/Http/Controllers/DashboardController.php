<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistrationsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        // Récupérer les informations de l'organisation
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('id', $organizationId)
            ->first();
        
        // Connexion à la base de données de l'organisation
        $orgDb = $organization->database_name;
        config(['database.connections.org.database' => $orgDb]);
        
        // Statistiques globales
        $stats = $this->getOrganizationStats($orgDb);
        
        // Récupération des inscriptions avec filtres
        $registrations = $this->getRegistrations($orgDb, $request);
        
        return view('organization.dashboard', compact('stats', 'registrations', 'organization'));
    }
    
    private function getOrganizationStats($database)
    {
        // Nombre d'événements
        $eventsCount = DB::connection('org')
            ->table('events')
            ->where('is_published', true)
            ->count();
        
        // Nombre total d'inscrits
        $participantsCount = DB::connection('org')
            ->table('registrations')
            ->where('status', 'confirmed')
            ->count();
        
        // Montant total collecté
        $totalRevenue = DB::connection('org')
            ->table('registrations')
            ->where('payment_status', 'paid')
            ->sum('amount_paid');
        
        // Montant en attente
        $pendingRevenue = DB::connection('org')
            ->table('registrations')
            ->where('payment_status', 'pending')
            ->sum('ticket_price');
        
        // Événements récents
        $recentEvents = DB::connection('org')
            ->table('events')
            ->where('is_published', true)
            ->orderBy('event_date', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'events_count' => $eventsCount,
            'participants_count' => $participantsCount,
            'total_revenue' => $totalRevenue,
            'pending_revenue' => $pendingRevenue,
            'recent_events' => $recentEvents
        ];
    }
    
    private function getRegistrations($database, Request $request)
    {
        $query = DB::connection('org')
            ->table('registrations as r')
            ->join('events as e', 'r.event_id', '=', 'e.id')
            ->join('ticket_types as tt', 'r.ticket_type_id', '=', 'tt.id')
            ->select([
                'r.id',
                'r.registration_number',
                'r.fullname',
                'r.email',
                'r.phone',
                'r.organization',
                'r.position',
                'r.status',
                'r.payment_status',
                'r.ticket_price',
                'r.amount_paid',
                'r.registration_date',
                'r.confirmation_date',
                'e.event_title',
                'e.event_date',
                'tt.ticket_name'
            ]);
        
        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('r.fullname', 'like', "%{$search}%")
                  ->orWhere('r.email', 'like', "%{$search}%")
                  ->orWhere('r.phone', 'like', "%{$search}%")
                  ->orWhere('r.organization', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('event_id')) {
            $query->where('r.event_id', $request->event_id);
        }
        
        if ($request->filled('status')) {
            $query->where('r.status', $request->status);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('r.payment_status', $request->payment_status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('r.registration_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('r.registration_date', '<=', $request->date_to . ' 23:59:59');
        }
        
        return $query->orderBy('r.registration_date', 'desc')->paginate(15);
    }
    
    public function getTicket($registrationId)
    {
        $user = Auth::user();
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('id', $user->organization_id)
            ->first();
        
        config(['database.connections.org.database' => $organization->database_name]);
        
        $registration = DB::connection('org')
            ->table('registrations as r')
            ->join('events as e', 'r.event_id', '=', 'e.id')
            ->join('ticket_types as tt', 'r.ticket_type_id', '=', 'tt.id')
            ->where('r.id', $registrationId)
            ->select([
                'r.*',
                'e.event_title',
                'e.event_date',
                'e.event_start_time',
                'e.event_location',
                'e.primary_color',
                'e.secondary_color',
                'tt.ticket_name'
            ])
            ->first();
        
        if (!$registration) {
            return response()->json(['error' => 'Inscription non trouvée'], 404);
        }
        
        return response()->json(['registration' => $registration]);
    }
    
    public function resendTicket($registrationId)
    {
        $user = Auth::user();
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('id', $user->organization_id)
            ->first();
        
        config(['database.connections.org.database' => $organization->database_name]);
        
        $registration = DB::connection('org')
            ->table('registrations as r')
            ->join('events as e', 'r.event_id', '=', 'e.id')
            ->where('r.id', $registrationId)
            ->where('r.status', 'confirmed')
            ->select(['r.*', 'e.event_title', 'e.event_date'])
            ->first();
        
        if (!$registration) {
            return response()->json(['error' => 'Inscription non trouvée ou non confirmée'], 404);
        }
        
        // Ici vous pouvez ajouter la logique d'envoi d'email
        // Mail::to($registration->email)->send(new TicketMail($registration));
        
        return response()->json(['message' => 'Ticket renvoyé avec succès']);
    }
    
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('id', $user->organization_id)
            ->first();
        
        return Excel::download(
            new RegistrationsExport($organization->database_name, $request->all()), 
            'inscriptions_' . date('Y-m-d') . '.xlsx'
        );
    }
    
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('id', $user->organization_id)
            ->first();
        
        config(['database.connections.org.database' => $organization->database_name]);
        
        $registrations = $this->getRegistrations($organization->database_name, $request);
        
        $pdf = Pdf::loadView('organization.exports.registrations-pdf', [
            'registrations' => $registrations->items(),
            'organization' => $organization,
            'filters' => $request->all()
        ]);
        
        return $pdf->download('inscriptions_' . date('Y-m-d') . '.pdf');
    }
}