<form action="{{ route('complete-profile') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="phone">phone</label>
        <input type="text" name="phone" required>
    </div>
    <div>
        <label for="address">address</label>
        <input type="address" name="address" required>
    </div>
    <button type="submit">Update Profile</button>
</form>
@if (session('success'))
    <div>{{ session('success') }}</div>
@endif