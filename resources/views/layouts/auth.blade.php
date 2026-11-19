<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tix and You')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] text-[#0A0A0A] antialiased m-0" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
    @yield('content')
</body>
</html>