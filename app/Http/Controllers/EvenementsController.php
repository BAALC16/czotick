<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EvenementsController extends Controller
{
    /**
     * Afficher la liste des événements
     */
    public function index()
    {
        // Logique pour afficher tous les événements
        return view('evenements.index');
    }
    
    /**
     * Afficher le formulaire de création d'événement
     */
    public function create()
    {
        return view('evenements.creer');
    }
    
    /**
     * Enregistrer un nouvel événement
     */
    public function store(Request $request)
    {
        // Logique pour créer un événement
        return redirect()->route('evenements.index')
            ->with('success', 'Événement créé avec succès !');
    }
    
    /**
     * Afficher les détails d'un événement
     */
    public function show($id)
    {
        return view('evenements.details', compact('id'));
    }
    
    /**
     * Afficher le formulaire d'édition d'un événement
     */
    public function edit($id)
    {
        return view('evenements.modifier', compact('id'));
    }
    
    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, $id)
    {
        // Logique pour mettre à jour un événement
        return redirect()->route('evenements.show', $id)
            ->with('success', 'Événement mis à jour avec succès !');
    }
    
    /**
     * Supprimer un événement
     */
    public function destroy($id)
    {
        // Logique pour supprimer un événement
        return redirect()->route('evenements.index')
            ->with('success', 'Événement supprimé avec succès !');
    }
}

