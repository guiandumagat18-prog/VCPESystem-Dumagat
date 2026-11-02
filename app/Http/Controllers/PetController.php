<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Services\DomainService;
use Inertia\Inertia;
use Inertia\Response;

class PetController extends Controller
{
    public function __construct(private DomainService $service) {}

    public function index(): Response
    {
        $pets = Pet::with('owner')->paginate(15);
        return Inertia::render('pets/index', compact('pets'));
    }

    public function create(): Response
    {
        return Inertia::render('pets/create');
    }

    public function store(StorePetRequest $request)
    {
        $this->service->createPet($request->validated());
        return redirect()->route('pets.index')->with('success', 'Pet created successfully.');
    }

    public function update(UpdatePetRequest $request, Pet $pet)
    {
        $this->service->updatePet($pet, $request->validated());
        return redirect()->route('pets.index')->with('success', 'Pet updated successfully.');
    }

    public function edit(Pet $pet): Response
    {
        return Inertia::render('pets/edit', [
            'pet' => $pet,
            'owners' => PetOwner::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}