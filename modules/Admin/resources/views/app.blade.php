<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" type="image/svg+xml" href="/admin/dist/img/favicon.svg">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>{{$page['props']['metaInfo']['title']}} - TattooStars</title>
    <!-- Styles -->
    <link href="{{ mix('css/app.css','admin/dist') }}" rel="stylesheet"/>
    <!-- Fonts -->
    <link href="{{ asset('/admin/dist/vendor/nucleo/css/nucleo.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
</head>
<body>
@inertia
@can('admin-panel.access')
    @routes('admin')
@endcan
<script src="{{ mix('js/manifest.js','admin/dist') }}" defer></script>
<script src="{{ mix('js/vendor.js','admin/dist') }}" defer></script>
<script src="{{ mix('js/app.js','admin/dist') }}" defer></script>
</body>
</html>
