<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Courses</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #007bff;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .signature-section {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
        }
        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-spacing: 20px;
        }
        .signature-table td {
            text-align: center;
        }
        .signature-line {
            margin-top: 10px;
            width: 80%;
            border-top: 1px solid #333;
            margin: 0 auto;
        }
        .logo h1{
            margin: 0;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo Section -->
        <div class="logo">
            <h1>Educore School management system</h1>
        </div>

        <div class="header">
            <h2>Registered Courses</h2>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Department:</strong> {{ $department }}</p>
            {{-- <p><strong>Level:</strong> {{ $level }}</p> --}}
            <p><strong>Semester:</strong> {{ $semester }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Credit Unit</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $registration)
                    <tr>
                        <td>{{ $registration->course->code }}</td>
                        <td>{{ $registration->course->title }}</td>
                        <td>{{ $registration->course->credit_unit }}</td>
                        <td>{{ ucfirst($registration->course->semester) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <p>Student</p>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <p>Course Adviser</p>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <p>HOD</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
