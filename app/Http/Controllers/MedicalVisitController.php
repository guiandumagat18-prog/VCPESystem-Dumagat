<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalVisitRequest;
use App\Http\Requests\UpdateMedicalVisitRequest;
use App\Models\MedicalVisit;
use App\Models\Pet;
use App\Models\Veterinarian;
use App\Services\DomainService;
use Inertia\Inertia;
use Inertia\Response;

class MedicalVisitController extends Controller
{
    public function __construct(private DomainService $service) {}

    public function index(): Response
    {
        $visits = MedicalVisit::with(['pet.owner', 'veterinarian', 'treatments'])
            ->latest('examination_date')
            ->latest('id')
            ->paginate(15)
            ->through(fn (MedicalVisit $visit) => [
                'id' => $visit->id,
                'examination_date' => $visit->examination_date->format('Y-m-d'),
                'diagnosis' => $visit->diagnosis,
                'procedure_cost' => $visit->procedure_cost,
                'payment_status' => $visit->payment_status,
                'pet' => [
                    'name' => $visit->pet->name,
                    'owner' => $visit->pet->owner?->name,
                ],
                'veterinarian' => $visit->veterinarian->name,
            ]);

        return Inertia::render('visits/index', compact('visits'));
    }

    public function create(): Response
    {
        return Inertia::render('visits/create', [
            'pets' => Pet::with('owner')->orderBy('name')->get(['id', 'owner_id', 'name']),
            'veterinarians' => Veterinarian::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(MedicalVisit $visit): Response
    {
        return Inertia::render('visits/edit', [
            'visit' => [
                ...$visit->toArray(),
                'examination_date' => $visit->examination_date->format('Y-m-d'),
                'treatments' => $visit->treatments()->get(['id', 'treatment_given', 'cost']),
            ],
            'pets' => Pet::with('owner')->orderBy('name')->get(['id', 'owner_id', 'name']),
            'veterinarians' => Veterinarian::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreMedicalVisitRequest $request)
    {
        $this->service->createVisit($request->validated());
        return redirect()->route('visits.index')->with('success', 'Visit recorded successfully.');
    }

    // 👇 THIS IS WHERE IT GOES 👇
    public function update(UpdateMedicalVisitRequest $request, MedicalVisit $visit)
    {
        $this->service->updateVisit($visit, $request->validated());
        return redirect()->route('visits.index')->with('success', 'Visit updated successfully.');
    }
}