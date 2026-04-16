@extends('layouts.app')

@section('content')
    <!-- Hero Welcome Section -->
    <div style="background: linear-gradient(135deg, #f97316 0%, #ff6b35 100%); color: white; padding: 60px 20px; border-radius: 12px; margin-bottom: 40px; text-align: center;">
        <h1 style="font-size: 48px; font-weight: 700; margin-bottom: 10px;">
            <i class="bi bi-hand-thumbs-up" style="margin-right: 12px;"></i>Welcome, {{ $user->name }}!
        </h1>
        <p style="font-size: 18px; opacity: 0.95; margin-bottom: 20px;">Student Internship Attendance & Logbook Management System</p>
        <p style="font-size: 16px; opacity: 0.9;">Manage your attendance, track logbook entries, and monitor your internship progress</p>
    </div>

    <!-- Quick Stats Dashboard -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #f97316; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">Today's Status</div>
            <div style="font-size: 36px; font-weight: 700; color: #f97316; margin-bottom: 5px;"><i class="bi bi-geo-alt-fill"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">Mark Attendance</p>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #10b981; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">Logbook</div>
            <div style="font-size: 36px; font-weight: 700; color: #10b981; margin-bottom: 5px;"><i class="bi bi-journal-text"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">Record Activities</p>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #0284c7; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">Documents</div>
            <div style="font-size: 36px; font-weight: 700; color: #0284c7; margin-bottom: 5px;"><i class="bi bi-file-earmark"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">Upload Files</p>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #a855f7; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">Activities</div>
            <div style="font-size: 36px; font-weight: 700; color: #a855f7; margin-bottom: 5px;"><i class="bi bi-gear-fill"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">Manage Tasks</p>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="card" style="margin-bottom: 40px;">
        <div class="card-title" style="margin-bottom: 25px;">
            <i class="bi bi-graph-up" style="margin-right: 8px; color: #f97316;"></i>Your Statistics
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
            <div style="text-align: center; padding: 20px; background: #fff5f0; border-radius: 8px; border-left: 4px solid #f97316;">
                <div style="font-size: 32px; font-weight: 700; color: #f97316; margin-bottom: 8px;">{{ $stats['absenceCount'] ?? 0 }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Absences Recorded</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #10b981;">
                <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">{{ $stats['logbookCount'] ?? 0 }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Logbook Entries</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0284c7;">
                <div style="font-size: 32px; font-weight: 700; color: #0284c7; margin-bottom: 8px;">{{ $stats['documentCount'] ?? 0 }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Documents Uploaded</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #faf5ff; border-radius: 8px; border-left: 4px solid #a855f7;">
                <div style="font-size: 32px; font-weight: 700; color: #a855f7; margin-bottom: 8px;">{{ $stats['activityCount'] ?? 0 }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Activities Completed</div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px;">
        <!-- Feature 1 -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-bullseye" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Easy Attendance Tracking</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Scan QR codes or upload photos to record your attendance. Real-time location tracking and automatic timestamp recording.</p>
        </div>

        <!-- Feature 2 -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-bar-chart-fill" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Activity Logbook</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Document all your daily activities and achievements. Submit detailed entries for instructor review and approval.</p>
        </div>

        <!-- Feature 3 -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-cloud-fill" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Document Management</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Upload and organize your internship documents, certificates, and project files in one secure location.</p>
        </div>

        <!-- Feature 4 -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-phone" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Mobile Friendly</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Access your attendance and logbook from any device. Responsive design works perfectly on smartphones and tablets.</p>
        </div>

        <!-- Feature 5 -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-bell-fill" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Real-time Notifications</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Get instant updates on absence approvals, logbook reviews, and important system notifications.</p>
        </div>

        <!-- Feature 6 -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-shield-check" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Secure & Reliable</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Your data is protected with modern security standards. Encrypted storage and secure authentication system.</p>
        </div>
    </div>

    <!-- Getting Started Section -->
    <div class="card" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.05) 0%, rgba(255, 107, 53, 0.05) 100%); border-left: 5px solid #f97316;">
        <h2 style="font-size: 24px; font-weight: 600; color: #333; margin-bottom: 20px;">
            <i class="bi bi-rocket-takeoff" style="margin-right: 10px; color: #f97316;"></i>Getting Started
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">1</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Record Your Absences</h3>
                <p style="color: #666; font-size: 14px;">Go to the Absence section and use the QR code scanner or camera to record your presence. It takes just a few seconds!</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">2</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Create Logbook Entries</h3>
                <p style="color: #666; font-size: 14px;">Write detailed descriptions of your daily activities, achievements, and learning experiences. Keep your logbook updated regularly.</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">3</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Upload Documents</h3>
                <p style="color: #666; font-size: 14px;">Keep all your important documents organized. Upload certificates, project files, and other relevant materials for easy access.</p>
            </div>
        </div>
    </div>

    <style>
        .card {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
    </style>
@endsection
