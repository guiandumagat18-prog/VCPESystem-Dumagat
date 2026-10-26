<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalVisitRequest;
use App\Http\Requests\UpdateMedicalVisitRequest;
use App\Models\MedicalVisit;
use App\Models\Pet;
use App\Models\Veterinarian;
use App\Services\DomainService;

class MedicalVisitController extends Controller
{
    public function __construct(private DomainService $service) {}

    public function index()
    {
        $visits = MedicalVisit::with(['pet.owner', 'veterinarian', 'treatments'])
                              ->latest('examination_date')
                              ->paginate(15);
        return view('visits.index', compact('visits'));
    }

    public function create()
    {
        return view('visits.create', [
            'pets' => Pet::with('owner')->orderBy('name')->get(),
            'vets' => Veterinarian::orderBy('name')->get(),
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