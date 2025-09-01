<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <meta name="description" content=" Employees Database Management System - PUBLIC HEALTH ENGINEERING DEPARTMENT : MIZORAM.Developed by Raunak Lalmuanpuia, Software Developer, MSeGS.">
    <meta name="keywords" content="Mizoram, EDMS, PHED, Employees Database, Government of Mizoram">
    <meta name="author" content="Raunak Lalmuanpuia">

    <title>EDMS</title>

    <title inertia>{{ config('app.name', 'PHED Dept: GOM') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

</head>
<body style="background-color: #fbfbfc" class="font-sans antialiased">
@inertia
</body>
</html>

