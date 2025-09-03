<!DOCTYPE html>
<html lang="en"  dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin table</title>

    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        table {
            width: 95%;

            border-collapse: collapse;
            margin: 50px auto;
        }

        /* Zebra striping */
        tr:nth-of-type(odd) {
            background: #eee;
        }

        th {
            background: #3498db;
            color: white;
            font-weight: bold;
        }

        td,
        th {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 18px;
        }


    </style>

</head>

<body>

<div style="width: 95%; margin: 0 auto;">
    <div style="width: 10%; float:left; margin-right: 20px;">
        <img src="{{ public_path('assets/images/logo.png') }}" width="100%"  alt="">
    </div>
    <div style="width: 50%; float: left;">
        <h1>لوحه التحكم</h1>
    </div>
</div>

<table style="position: relative; top: 50px;">
    <thead>
    <tr>
        <th>Name</th>
        <th> currency</th>
        <th>Email</th>
        <th>Date Of creating</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($centers as $center)
        <tr>
            <td data-column="First Name">{{ $center->name }}</td>
            <td data-column="Last Name">{{ $center->currency }}</td>
            <td data-column="Email" style="color: dodgerblue;">
                {{ $center->email }}
            </td>
            <td data-column="Date">
                {{ date('F j, Y', strtotime($center->created_at)) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>

</html>
