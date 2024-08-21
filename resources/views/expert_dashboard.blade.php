<div class="container">
    <h1>Welcome, {{ $expert->name }}</h1>

    @if($expert->photo)
        <img src="{{ asset('storage/' . $expert->photo) }}" alt="{{ $expert->name }}" class="img-thumbnail">
    @endif

    <p><strong>Specialization:</strong> {{ $expert->specialization }}</p>
    <p><strong>Experiences:</strong> {{ $expert->experiences }}</p>
    <p><strong>Phone:</strong> {{ $expert->phone }}</p>
    <p><strong>Address:</strong> {{ $expert->address }}</p>
    <p><strong>Availability:</strong> {{ $expert->availability }}</p>
    <p><strong>Consultation Categories:</strong> {{ $expert->consultation_categories }}</p>

    <a href="{{ route('login') }}" class="btn btn-primary">Edit Profile</a>
</div>
