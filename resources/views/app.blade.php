<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
</head>
<body class="antialiased text-gray-900 bg-gray-50">
    @inertia
</body>
</html>
