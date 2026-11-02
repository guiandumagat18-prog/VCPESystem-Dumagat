<?php

namespace App\Http\Controllers;

use App\Models\MedicalVisit;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Veterinarian;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('dashboard', [
            'stats' => [
                'pets' => Pet::count(),
                'owners' => PetOwner::count(),
                'veterinarians' => Veterinarian::count(),
                'visits' => MedicalVisit::count(),
            ],
            'recentVisits' => MedicalVisit::query()
                ->with(['pet:id,name', 'veterinarian:id,name'])
                ->latest('examination_date')
                ->latest('id')
                ->limit(5)
                ->get([
                    'id',
                    'pet_id',
                    'veterinarian_id',
                    'examination_date',
                    'diagnosis',
                    'procedure_cost',
                    'payment_status',
                ]),
        ]);
    }
}
