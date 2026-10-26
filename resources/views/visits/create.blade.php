@extends('layouts.app')
@section('content')
<h2>Record Visit</h2>
@if($errors->any())<ul style="color:red">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
<form method="POST" action="{{ route('visits.store') }}">
    @csrf
    <p>Pet:
        <select name="pet_id">
            @foreach($pets as $p)
                <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->owner->name ?? 'no owner' }}</option>
            @endforeach
        </select>
    </p>
    <p>Veterinarian:
        <select name="veterinarian_id">
            @foreach($vets as $v)
                <option value="{{ $v->id }}">{{ $v->name }}</option>
            @endforeach
        </select>
    </p>
    <p>Examination Date: <input type="date" name="examination_date" value="{{ old('examination_date', date('Y-m-d')) }}"></p>
    <p>Diagnosis: <textarea name="diagnosis">{{ old('diagnosis') }}</textarea></p>
    <p>Procedure Cost: <input name="procedure_cost" value="{{ old('procedure_cost', 0) }}"></p>
    <p>Payment Status:
        <select name="payment_status">
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </p>
    <button type="submit">Save Visit</button>
</form>
@endsection