<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold mb-2">Login Admin</h1>
        <p class="text-sm text-gray-600 mb-6">Masuk untuk mengakses aplikasi SIAP-KAK.</p>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                    required
                    autofocus
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                    required
                >
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember" class="text-sm text-gray-700">Ingat saya</label>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white rounded-md px-4 py-2">
                Masuk
            </button>
        </form>
    </div>
</body>
</html>