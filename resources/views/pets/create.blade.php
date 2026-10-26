@extends('layouts.app')
@section('content')
<h2>Create Pet</h2>
@if($errors->any())<ul style="color:red">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
<form method="POST" action="{{ route('pets.store') }}">
    @csrf
    <p>Owner:
        <select name="owner_id">
            @foreach($owners as $o)
                <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->phone }})</option>
            @endforeach
        </select>
    </p>
    <p>Name: <input name="name" value="{{ old('name') }}"></p>
    <p>Species: <input name="species" value="{{ old('species') }}"></p>
    <p>Breed: <input name="breed" value="{{ old('breed') }}"></p>
    <button type="submit">Save</button>
</form>
@endsection