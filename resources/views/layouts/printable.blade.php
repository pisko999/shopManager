<html>
<head>
    <style>
        body {
            /*background: rgb(204,204,204);*/
        }
        .page {
            /*background: white;*/
            /*display: block;*/
            /*margin: 0 auto;*/
            /*margin-top: 1.6cm;*/
            /*margin-bottom: 1.6cm;*/
            /*box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);*/
        }

        .page[size="A4"] {
            width: 20cm;
            height: 28.7cm;
            page-break-after: always;
            /*padding: 0.5cm;*/

        }
        .expansion-card {
            /*width: 9.6cm !important;*/
            /*height: 4.7cm !important;*/
            width: 6.2cm !important;
            height: 9.5cm !important;
            border: 1px solid black;
            margin-bottom: .1cm;
            text-align: center;
            border-radius: .3cm;
        }
        .card-hr {
            width: 75%;
            margin-left: auto;
            margin-right: auto;
            opacity: 1;
        }
        .expansion-icon {
            width: 33px;
            height: 33px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>

@yield('content')

</body>
</html>
