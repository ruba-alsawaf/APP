<form method="POST" action="{{ route('register') }}">
    @csrf
    <div>
        <label for="name">Name:</label>
        <input type="name" name="name" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" name="password_confirmation" required>
    </div>
    <div>
        <label for="role">Role:</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="expert">Expert</option>
        </select>
    </div>
    <div>
        <button type="submit">Register</button>
    </div>
</form>
