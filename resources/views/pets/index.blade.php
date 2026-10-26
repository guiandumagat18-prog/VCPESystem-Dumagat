@extends('layouts.app')
@section('content')
<h2>Pets</h2>
@if(session('success'))<p style="color:green">{{ session('success') }}</p>@endif
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Name</th><th>Species</th><th>Breed</th><th>Owner</th></tr>
    @foreach($pets as $pet)
    <tr>
        <td>{{ $pet->id }}</td>
        <td>{{ $pet->name }}</td>
        <td>{{ $pet->species }}</td>
        <td>{{ $pet->breed }}</td>
        <td>{{ $pet->owner->name ?? '—' }} ({{ $pet->owner->phone ?? '—' }})</td>
    </tr>
    @endforeach
</table>
{{ $pets->links() }}
@endsection