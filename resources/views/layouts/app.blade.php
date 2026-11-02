<!DOCTYPE html>
<html>
<head><title>Vet Clinic</title></head>
<body style="font-family:sans-serif;padding:20px">
    <nav>
        <a href="{{ route('dashboard') }}">Dashboard</a> |
        <a href="{{ route('pets.index') }}">Pets</a> |
        <a href="{{ route('visits.index') }}">Visits</a>
    </nav>
    <hr>
    @yield('content')
</body>
</html>