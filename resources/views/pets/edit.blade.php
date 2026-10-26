@extends('layouts.app')
@section('content')
<h2>Edit Pet #{{ $pet->id }}</h2>
@if($errors->any())<ul style="color:red">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
<form method="POST" action="{{ route('pets.update', $pet) }}">
    @csrf @method('PUT')
    <p>Owner:
        <select name="owner_id">
            @foreach($owners as $o)
                <option value="{{ $o->id }}" @selected($o->id == $pet->owner_id)>{{ $o->name }}</option>
            @endforeach
        </select>
    </p>
    <p>Name: <input name="name" value="{{ $pet->name }}"></p>
    <p>Species: <input name="species" value="{{ $pet->species }}"></p>
    <p>Breed: <input name="breed" value="{{ $pet->breed }}"></p>
    <button type="submit">Update</button>
</form>
@endsection