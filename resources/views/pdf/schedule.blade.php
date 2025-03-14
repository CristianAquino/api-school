<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('css/student_pdf.css') }}" >
    {{--  <link rel="stylesheet" href="{{ asset('css/student_pdf.css') }}" >  --}}
    <title>Document</title>
</head>
<body>
    <p class="invalid">copia sin valor oficial</p>
    <header class="header">
        <h6>code enrollement: {{$schedule["id"]}}</h6>
    </header>
    <main class="main">
            <table class="table_head">
                <td>
                    <img src="{{ public_path('storage/images/logo.png') }}" alt="Logo"></td>
                <td>
                    <div>
                        <h1>Lorem, ipsum.</h1>
                        <h5>Lorem ipsum dolor sit amet.</h5>
                    </div>
                </td>
            </table>
        <h2 class="title">student data</h2>
        <table class="table">
            <tr>
                <th>first name:</th>
                <td>{{$schedule["first_name"]}}</td>
            </tr>
            <tr>
                <th>second name:</th>
                <td>{{$schedule["second_name"]}}</td>
            </tr>
            <tr>
                <th>names:</th>
                <td>{{$schedule["names"]}}</td>
            </tr>
            <tr>
                <th>level:</th>
                <td>{{$schedule["level"]}}</td>
            </tr>
            <tr>
                <th>grade:</th>
                <td>{{$schedule["grade"]}}</td>
            </tr>
            <tr>
                <th>academic year:</th>
                <td>{{$schedule["academic_year"]}}</td>
            </tr>
            <tr>
                <th>code student:</th>
                <td>{{$schedule["code"]}}</td>
            </tr>
        </table>
        <div>
            <h2 class="title">schedules courses</h2>
            <table class="table table_courses">
                <tr>
                    <th>N°</th>
                    <th>Course</td>
                    <th>Start Time</td>
                    <th>End Time</td>
                    <th>Day</td>
                    <th>Section</td>
                    <th>Teacher</td>

                </tr>
                @foreach ($schedule["courses"] as $item)
                    <tr>
                        <td style="text-align: center">{{$loop->index +1}}</th>
                        <td>{{$item["course"]}}</td>
                        <td style="text-align: center">{{$item["schedule"]["start_time"]}}</td>
                        <td style="text-align: center">{{$item["schedule"]["end_time"]}}</td>
                        <td style="text-align: center">{{$item["schedule"]["day"]}}</td>
                        <td style="text-align: center">U</td>
                        <td>{{$item["teacher"]["first_name"]}} {{$item["teacher"]["second_name"]}} {{$item["teacher"]["names"]}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </main>
    <footer class="footer">
        <h6>code enrollement: {{$schedule["id"]}}</h6>
    </footer>
</body>
</html>