<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
    </div>
    <div>
        <label for="role">Role:</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="expert">Expert</option>
        </select>
    </div>
    <div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
</body>
</html>
