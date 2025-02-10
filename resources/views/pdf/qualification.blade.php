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
                <td>{{$qualification["first_name"]}}</td>
            </tr>
            <tr>
                <th>second name:</th>
                <td>{{$qualification["second_name"]}}</td>
            </tr>
            <tr>
                <th>names:</th>
                <td>{{$qualification["names"]}}</td>
            </tr>
            <tr>
                <th>code student:</th>
                <td>{{$qualification["code"]}}</td>
            </tr>
        </table>
        <div>
            <h2 class="title">Qualifications</h2>
            @foreach ($qualification["enrollements"] as $enrollement)
                <table>
                    <tr>
                        <th>academic year:</th>
                        <td>{{$enrollement["academic_year"]}}</td>
                    </tr>
                    <tr>
                        <th>level:</th>
                        <td>{{$enrollement["level"]}}</td>
                    </tr>
                    <tr>
                        <th>grade:</th>
                        <td>{{$enrollement["grade"]}}</td>
                    </tr>
                </table>
                <table class="table table_courses">
                    <tr>
                        <th>N°</th>
                        <th>Course</td>
                        <th>Note1</td>
                        <th>Note2</td>
                        <th>Note3</td>
                        <th>AVG</td>
                    </tr>
                    @foreach ($enrollement["courses"] as $course)
                    @php $avg = 0; @endphp
                        <tr>
                            <td style="text-align: center">{{$loop->index +1}}</th>
                            <td>{{$course["course"]}}</td>
                            @foreach ($course["notes"] as $note)
                                <td>{{$note->number_note}}</td>
                                @php $avg += $note->avg; @endphp
                            @endforeach
                            <td>{{$avg}}</td>
                        </tr>
                    @endforeach
                </table>
            @endforeach
        </div>
    </main>
</body>
</html>