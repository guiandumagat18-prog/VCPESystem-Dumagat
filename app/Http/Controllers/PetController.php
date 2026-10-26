<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Services\DomainService;

class PetController extends Controller
{
    public function __construct(private DomainService $service) {}

    public function index()
    {
        $pets = Pet::with('owner')->paginate(15);
        return view('pets.index', compact('pets'));
    }

    public function create()
    {
        return view('pets.create', [
            'owners' => PetOwner::orderBy('name')->get(),
        ]);
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

    public function edit(Pet $pet)
    {
        return view('pets.edit', [
            'pet'    => $pet,
            'owners' => PetOwner::orderBy('name')->get(),
        ]);
    }
}