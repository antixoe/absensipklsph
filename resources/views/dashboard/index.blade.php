@extends('layouts.app')

@section('content')
    <!-- Hero Welcome Section -->
    <div style="background: linear-gradient(135deg, #f97316 0%, #ff6b35 100%); color: white; padding: 60px 20px; border-radius: 12px; margin-bottom: 40px; text-align: center;">
        <h1 style="font-size: 48px; font-weight: 700; margin-bottom: 10px;">
            <i class="bi bi-hand-thumbs-up" style="margin-right: 12px;"></i>Welcome, {{ $user->name }}!
        </h1>
        <p style="font-size: 18px; opacity: 0.95; margin-bottom: 20px;">School Attendance & Management System</p>
        <p style="font-size: 16px; opacity: 0.9;">Track attendance, view records, and manage school activities</p>
    </div>

    <!-- Quick Stats Dashboard -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
        @if(auth()->user()->hasRole(\App\Models\Role::MURID))
        <!-- Student View -->
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #f97316; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">My Attendance</div>
            <div style="font-size: 36px; font-weight: 700; color: #f97316; margin-bottom: 5px;"><i class="bi bi-calendar-check-fill"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">View Records</p>
        </div>
        @elseif(auth()->user()->hasRole(\App\Models\Role::GURU) || auth()->user()->hasRole(\App\Models\Role::WALI_KELAS))
        <!-- Teacher/Homeroom Teacher View -->
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #f97316; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">QR Scanner</div>
            <div style="font-size: 36px; font-weight: 700; color: #f97316; margin-bottom: 5px;"><i class="bi bi-qr-code"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">Scan Students</p>
        </div>
        @endif
        
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #10b981; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">Attendance Report</div>
            <div style="font-size: 36px; font-weight: 700; color: #10b981; margin-bottom: 5px;"><i class="bi bi-bar-chart-fill"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">View Report</p>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #f97316; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">Announcements</div>
            <div style="font-size: 36px; font-weight: 700; color: #f97316; margin-bottom: 5px;"><i class="bi bi-megaphone-fill"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">Latest News</p>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 5px solid #f97316; text-align: center; transition: transform 0.3s;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600; text-transform: uppercase;">Class Schedule</div>
            <div style="font-size: 36px; font-weight: 700; color: #f97316; margin-bottom: 5px;"><i class="bi bi-calendar-event-fill"></i></div>
            <p style="color: #333; font-size: 18px; font-weight: 600;">View Schedule</p>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="card" style="margin-bottom: 40px;">
        <div class="card-title" style="margin-bottom: 25px;">
            <i class="bi bi-graph-up" style="margin-right: 8px; color: #f97316;"></i>
            @if(auth()->user()->hasRole(\App\Models\Role::MURID))
                My Statistics
            @else
                Attendance Statistics
            @endif
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
            <div style="text-align: center; padding: 20px; background: #fff5f0; border-radius: 8px; border-left: 4px solid #f97316;">
                <div style="font-size: 32px; font-weight: 700; color: #f97316; margin-bottom: 8px;">{{ $stats['absenceCount'] ?? 0 }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Total Attendance</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #10b981;">
                <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">{{ $stats['attendancePercent'] ?? '0' }}%</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Attendance Rate</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <div style="font-size: 32px; font-weight: 700; color: #f59e0b; margin-bottom: 8px;">{{ $stats['absentCount'] ?? 0 }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Absences</div>
            </div>
            <div style="text-align: center; padding: 20px; background: #fce7f3; border-radius: 8px; border-left: 4px solid #ec4899;">
                <div style="font-size: 32px; font-weight: 700; color: #ec4899; margin-bottom: 8px;">{{ $stats['pendingCount'] ?? 0 }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600;">Pending Approvals</div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px;">
        @if(auth()->user()->hasRole(\App\Models\Role::MURID))
        <!-- Student Features -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-eye-fill" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">View Your Attendance</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Check your complete attendance record with check-in times and status. Easily track your daily presence.</p>
        </div>

        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-qr-code" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">QR Code on ID Card</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Your unique QR code is printed on your student ID card. Teachers scan it to record your attendance automatically.</p>
        </div>

        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-bar-chart-fill" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Attendance Reports</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">View detailed attendance reports showing your attendance percentage, trends, and monthly summaries.</p>
        </div>

        @elseif(auth()->user()->hasRole(\App\Models\Role::GURU) || auth()->user()->hasRole(\App\Models\Role::WALI_KELAS))
        <!-- Teacher Features -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-qr-code" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">QR Code Scanner</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Scan student QR codes to instantly record attendance. Fast and accurate with automatic timestamp recording.</p>
        </div>

        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-bar-chart-fill" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Class Attendance</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">View detailed attendance records for your class. See attendance percentage, absent students, and trends over time.</p>
        </div>

        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-graph-up" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Generate Reports</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Create comprehensive attendance reports and export them in Excel or PDF format for official documentation.</p>
        </div>

        @elseif(auth()->user()->hasAnyRole([\App\Models\Role::KESISWAAN, \App\Models\Role::KURIKULUM]))
        <!-- Admin Features -->
        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-qr-code-scan" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Manage QR Codes</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Create, manage, and distribute QR codes. Print QR codes for student ID cards and manage code distribution.</p>
        </div>

        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-people-fill" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">Student Management</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">Manage student records, assign QR codes, and track student information. Update class assignments and profiles.</p>
        </div>

        <div class="card">
            <div style="font-size: 28px; margin-bottom: 15px;"><i class="bi bi-graph-up" style="color: #f97316;"></i></div>
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 12px;">System Reports</h3>
            <p style="color: #666; line-height: 1.6; font-size: 14px;">View comprehensive system reports including attendance statistics, class summaries, and school-wide analytics.</p>
        </div>
        @endif
    </div>

    <!-- Getting Started Section -->
    <div class="card" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.05) 0%, rgba(255, 107, 53, 0.05) 100%); border-left: 5px solid #f97316;">
        <h2 style="font-size: 24px; font-weight: 600; color: #333; margin-bottom: 20px;">
            <i class="bi bi-rocket-takeoff" style="margin-right: 10px; color: #f97316;"></i>Getting Started
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            @if(auth()->user()->hasRole(\App\Models\Role::MURID))
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">1</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Check Your QR Code</h3>
                <p style="color: #666; font-size: 14px;">Find your unique QR code on the back of your student ID card. This is your attendance identifier.</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">2</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Come to School</h3>
                <p style="color: #666; font-size: 14px;">Bring your ID card with you. Teachers will scan your QR code to record your attendance automatically.</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">3</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">View Your Records</h3>
                <p style="color: #666; font-size: 14px;">Go to "Attendance" section to view your complete attendance history and status.</p>
            </div>
            @elseif(auth()->user()->hasRole(\App\Models\Role::GURU) || auth()->user()->hasRole(\App\Models\Role::WALI_KELAS))
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">1</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Access QR Scanner</h3>
                <p style="color: #666; font-size: 14px;">Go to the QR Code Scanner section to start scanning student ID cards with QR codes.</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">2</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Scan Students</h3>
                <p style="color: #666; font-size: 14px;">Use the scanner to scan each student's QR code. The system automatically records check-in time and status.</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">3</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">View Class Report</h3>
                <p style="color: #666; font-size: 14px;">Check attendance reports for your class to see overall statistics and identify absent students.</p>
            </div>
            @elseif(auth()->user()->hasAnyRole([\App\Models\Role::KESISWAAN, \App\Models\Role::KURIKULUM]))
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">1</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Create QR Codes</h3>
                <p style="color: #666; font-size: 14px;">Generate unique QR codes for students and assign them to their records. Print for ID cards.</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">2</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Manage Students</h3>
                <p style="color: #666; font-size: 14px;">Update student information and track QR code assignments. Ensure all students have codes assigned.</p>
            </div>
            <div>
                <div style="background: #f97316; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 12px;">3</div>
                <h3 style="font-weight: 600; color: #333; margin-bottom: 8px;">Review Reports</h3>
                <p style="color: #666; font-size: 14px;">Generate system reports to monitor school-wide attendance and identify trends.</p>
            </div>
            @endif
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
