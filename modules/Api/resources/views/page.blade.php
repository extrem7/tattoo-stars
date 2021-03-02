<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} - TattooStars</title>
    <style>
        body {
            padding: 16px;
            font-size: 15px;
        }

        .title {
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-size: 17px;
            line-height: 23px;
            margin-left: 15px;
        }

        hr {
            background: #EEEEEF;
            margin: 10px -16px;
        }

        ul {
            list-style: none;
        }

        ul li p::before {
            content: "\2022";
            color: #F8913E;
            font-weight: bold;
            font-size: 25px;
            line-height: 20px;
            display: inline-block;
            width: 15px;
            margin-left: -15px;
        }

        .accordion-button {
            background-color: white !important;
            outline: none;
            border: none;
            border-radius: 0;
            color: #F8913E !important;
            box-shadow: none !important;
        }

        .accordion-button.collapsed {
            border-bottom: 1px solid #EEEEEF;
        }

        .accordion-button:not(.collapsed) {
            background-color: #EEEEEF !important;
        }

        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
        }

        .accordion-body {
            background-color: #EEEEEF;
        }
    </style>
</head>
<body>
<!--<div class="header d-flex align-items-center">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#clip0)">
            <path
                d="M0.416679 9.58301H19.5833C19.8133 9.58301 20 9.76969 20 9.99969C20 10.2297 19.8133 10.4164 19.5833 10.4164H0.416639C0.18664 10.4164 -3.8147e-05 10.2297 -3.8147e-05 9.99969C-3.8147e-05 9.76969 0.18668 9.58301 0.416679 9.58301Z"
                fill="black"/>
            <path
                d="M0.122267 9.70551L6.78895 3.03887C6.95145 2.87637 7.21563 2.87637 7.37813 3.03887C7.54062 3.20137 7.54062 3.46555 7.37813 3.62805L0.711447 10.2947C0.629767 10.3756 0.523127 10.4164 0.416447 10.4164C0.309767 10.4164 0.203126 10.3756 0.122267 10.2947C-0.0402327 10.1322 -0.0402327 9.86801 0.122267 9.70551Z"
                fill="black"/>
            <path
                d="M0.122303 9.70549C0.284803 9.54299 0.548983 9.54299 0.711483 9.70549L7.37812 16.3722C7.54063 16.5347 7.54063 16.7989 7.37812 16.9614C7.29645 17.0422 7.1898 17.083 7.08312 17.083C6.97645 17.083 6.8698 17.0422 6.78894 16.9614L0.122265 10.2947C-0.0401959 10.1322 -0.0401969 9.86799 0.122303 9.70549Z"
                fill="black"/>
        </g>
        <defs>
            <clipPath id="clip0">
                <rect width="20" height="20" fill="white" transform="matrix(-1 0 0 1 20 0)"/>
            </clipPath>
        </defs>
    </svg>
    <h1 class="title mb-0">{{ $title }}</h1>
</div>
<hr>-->
<p>{!! $body  !!}</p>
@yield('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0"
        crossorigin="anonymous"></script>
</body>
</html>
