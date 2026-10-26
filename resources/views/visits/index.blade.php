@extends('layouts.app')
@section('content')
<h2>Medical Visits</h2>
@if(session('success'))<p style="color:green">{{ session('success') }}</p>@endif
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Date</th><th>Pet</th><th>Owner</th><th>Vet</th><th>Diagnosis</th><th>Cost</th><th>Status</th></tr>
    @foreach($visits as $v)
    <tr>
        <td>{{ $v->id }}</td>
        <td>{{ $v->examination_date->format('Y-m-d') }}</td>
        <td>{{ $v->pet->name ?? '—' }}</td>
        <td>{{ $v->pet->owner->name ?? '—' }}</td>
        <td>{{ $v->veterinarian->name ?? '—' }}</td>
        <td>{{ $v->diagnosis }}</td>
        <td>{{ number_format($v->procedure_cost, 2) }}</td>
        <td>{{ $v->payment_status }}</td>
    </tr>
    @endforeach
</table>
{{ $visits->links() }}
@endsection