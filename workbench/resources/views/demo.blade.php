<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <title>Demo - Testing</title>
    
    <!-- Styles -->
    <link
        href="{{ asset('build/demo.css') }}"
        rel="stylesheet"
    >
    @stack('styles')

    <!-- Head Scripts -->
    <script
        defer
        src="{{ asset('build/demo.js') }}"
    ></script>
    @stack('head_scripts')
</head>

<body class="antialiased">
    @include(':package_slug::partials.hello-world')

    <!-- Body Scripts -->
    @stack('body_scripts')
</body>

</html>
