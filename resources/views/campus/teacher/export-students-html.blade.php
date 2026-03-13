<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Llistat d'Estudiants - {{ $course->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }
        .header h1 {
            color: #1f2937;
            margin: 0;
            font-size: 28px;
        }
        .header .course-info {
            margin-top: 10px;
            color: #6b7280;
        }
        .header .course-info strong {
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        tr:hover {
            background-color: #f9fafb;
        }
        .status-confirmed {
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-completed {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fed7aa;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .student-code {
            background-color: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Llistat d'Estudiants</h1>
            <div class="course-info">
                <strong>Curs:</strong> {{ $course->title }}<br>
                <strong>Codi:</strong> {{ $course->code }}<br>
                <strong>Data generació:</strong> {{ now()->format('d/m/Y H:i') }}<br>
                <strong>Total estudiants:</strong> {{ count($students) }}
            </div>
        </div>

        {{-- Botones de exportación --}}
        <div style="margin-bottom: 20px; text-align: center;">
            <button onclick="exportToExcel()" style="margin-right: 10px; padding: 8px 16px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">
                📊 Exportar a Excel
            </button>
            <button onclick="exportToPDF()" style="padding: 8px 16px; background-color: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">
                📄 Exportar a PDF
            </button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nom Complet</th>
                    <th>Codi d'Estudiant</th>
                    <th>Correu Electrònic</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student['name'] }}</td>
                        <td><span class="student-code">{{ $student['student_code'] }}</span></td>
                        <td>{{ $student['email'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Document generat el {{ now()->format('d/m/Y H:i') }} des de Universitat Popular</p>
        </div>
    </div>

    <script>
        function exportToExcel() {
            let table = document.querySelector('table');
            let rows = table.querySelectorAll('tr');
            let csv = [];
            
            // Headers
            let headers = [];
            rows[0].querySelectorAll('th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            csv.push(headers.join(','));
            
            // Data rows
            for(let i = 1; i < rows.length; i++) {
                let row = [];
                rows[i].querySelectorAll('td').forEach(td => {
                    row.push('"' + td.textContent.trim().replace(/"/g, '""') + '"');
                });
                csv.push(row.join(','));
            }
            
            // Download CSV
            let csvContent = csv.join('\n');
            let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement('a');
            let url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'estudiants_{{ $course->code }}.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        function exportToPDF() {
            window.print();
        }
        
        // Add print styles for PDF
        window.addEventListener('beforeprint', function() {
            document.querySelectorAll('button').forEach(btn => btn.style.display = 'none');
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelectorAll('button').forEach(btn => btn.style.display = '');
        });
    </script>
</body>
</html>
