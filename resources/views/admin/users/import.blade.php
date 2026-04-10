@extends('layouts.app')

@section('title', 'Import Users from Excel')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-file-earmark-excel" style="margin-right: 8px;"></i>Import Users from Excel</h1>
        <p>Bulk upload users using an Excel file</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>
            <strong>Error:</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Upload Section -->
        <div class="card">
            <h2 style="margin-bottom: 20px;"><i class="bi bi-upload" style="margin-right: 8px;"></i>Upload Excel File</h2>

            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label for="file" style="display: block; margin-bottom: 10px; font-weight: 600;">
                        Select Excel File <span style="color: red;">*</span>
                    </label>
                    <div style="border: 2px dashed #e5e7eb; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer;" 
                        onclick="document.getElementById('file').click()">
                        <i class="bi bi-cloud-upload" style="font-size: 32px; color: #f97316; margin-bottom: 10px;"></i>
                        <p style="color: #666; margin-bottom: 10px;">
                            Click to select or drag and drop
                        </p>
                        <p style="color: #999; font-size: 12px;">
                            Supported formats: XLSX, XLS, CSV
                        </p>
                        <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" 
                            style="display: none;" onchange="updateFileName(this)">
                    </div>
                    <p id="file-name" style="margin-top: 10px; color: #666; font-size: 12px;"></p>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                    <i class="bi bi-cloud-upload" style="margin-right: 5px;"></i>Upload & Import
                </button>
            </form>
        </div>

        <!-- Template Section -->
        <div class="card">
            <h2 style="margin-bottom: 20px;"><i class="bi bi-file-text" style="margin-right: 8px;"></i>Excel/CSV Template</h2>

            <p style="color: #666; margin-bottom: 15px;">
                Your file can use one of these column formats:
            </p>

            <p style="color: #666; font-size: 13px; margin-bottom: 20px; font-weight: 600;">
                <strong>Format 1 (Recommended):</strong>
            </p>

            <table style="width: 100%; font-size: 12px; margin-bottom: 20px;">
                <tr style="background: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 8px; text-align: left; font-weight: 600;">Column Name</th>
                    <th style="padding: 8px; text-align: left; font-weight: 600;">Required</th>
                    <th style="padding: 8px; text-align: left; font-weight: 600;">Example</th>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>name</code></td>
                    <td style="padding: 8px; color: #dc2626;">Yes</td>
                    <td style="padding: 8px;">John Doe</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>email</code></td>
                    <td style="padding: 8px; color: #dc2626;">Yes</td>
                    <td style="padding: 8px;">john@example.com</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>password</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">SecurePass123</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>phone</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">081234567890</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>address</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">123 Main St, City</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>role</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">student, instructor, admin</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><code>status</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">active, inactive</td>
                </tr>
            </table>

            <p style="color: #666; font-size: 13px; margin-bottom: 20px; margin-top: 30px; font-weight: 600;">
                <strong>Format 2 (Alternative):</strong> Use firstname/lastname instead of name
            </p>

            <table style="width: 100%; font-size: 12px; margin-bottom: 20px;">
                <tr style="background: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 8px; text-align: left; font-weight: 600;">Column Name</th>
                    <th style="padding: 8px; text-align: left; font-weight: 600;">Required</th>
                    <th style="padding: 8px; text-align: left; font-weight: 600;">Example</th>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>firstname</code> OR <code>username</code></td>
                    <td style="padding: 8px; color: #dc2626;">Yes</td>
                    <td style="padding: 8px;">John</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>lastname</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">Doe</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>email</code></td>
                    <td style="padding: 8px; color: #dc2626;">Yes</td>
                    <td style="padding: 8px;">john@example.com</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>password</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">SecurePass123</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>role</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">student, instructor, admin</td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px;"><code>status</code></td>
                    <td style="padding: 8px; color: #10b981;">No</td>
                    <td style="padding: 8px;">active, inactive</td>
                </tr>
            </table>

            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 4px;">
                <p style="color: #92400e; font-size: 12px; margin: 0;">
                    <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                    <strong>Tip:</strong> If no password is provided, users will be created with a default password "password"
                </p>
            </div>
        </div>
    </div>

    <!-- Tips Section -->
    <div class="card">
        <h2 style="margin-bottom: 20px;"><i class="bi bi-lightbulb" style="margin-right: 8px;"></i>Import Tips</h2>

        <ul style="color: #666; line-height: 1.8;">
            <li><strong>Duplicate Prevention:</strong> Users with duplicate emails will be skipped automatically</li>
            <li><strong>Role Names:</strong> Use exact role names (e.g., "student", "instructor", "admin")</li>
            <li><strong>Headers Required:</strong> The first row must contain column headers</li>
            <li><strong>Default Password:</strong> Users created without a password will use "password" as default</li>
            <li><strong>Validation:</strong> Email addresses are validated during import</li>
            <li><strong>Status Default:</strong> If not specified, status defaults to "active"</li>
        </ul>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName ? `Selected: ${fileName}` : '';
        }

        // Drag and drop functionality
        const dropZone = document.querySelector('[onclick="document.getElementById(\'file\').click()"]');
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.background = '#f3f4f6';
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.style.background = '';
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.background = '';
                document.getElementById('file').files = e.dataTransfer.files;
                updateFileName(document.getElementById('file'));
            });
        }
    </script>
@endsection
