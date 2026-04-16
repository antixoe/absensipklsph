@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; color: #333; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-gear" style="font-size: 32px; color: #f97316;"></i>Settings
            </h1>
            <p style="color: #666; margin: 5px 0 0 0;">Manage your account and preferences</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($message = Session::get('success'))
        <div style="background-color: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle" style="font-size: 18px;"></i>
            <span>{{ $message }}</span>
        </div>
    @endif

    @if($message = Session::get('error'))
        <div style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-exclamation-circle" style="font-size: 18px;"></i>
            <span>{{ $message }}</span>
        </div>
    @endif

    <!-- Settings Container -->
    <div style="display: grid; grid-template-columns: 230px 1fr; gap: 25px;">

        <!-- Sidebar Navigation -->
        <div style="display: flex; flex-direction: column; gap: 8px; position: sticky; top: 20px; height: fit-content;">
            <button class="nav-btn active" onclick="showSection('account')">
                <i class="bi bi-person-circle" style="margin-right: 8px;"></i>Account
            </button>
            <button class="nav-btn" onclick="showSection('password')">
                <i class="bi bi-lock" style="margin-right: 8px;"></i>Password
            </button>
            <button class="nav-btn" onclick="showSection('privacy')">
                <i class="bi bi-shield-lock" style="margin-right: 8px;"></i>Privacy
            </button>
            <button class="nav-btn" onclick="showSection('notifications')">
                <i class="bi bi-bell" style="margin-right: 8px;"></i>Notifications
            </button>
            <button class="nav-btn" onclick="showSection('activity')">
                <i class="bi bi-clock-history" style="margin-right: 8px;"></i>Activity
            </button>
        </div>

        <!-- Content Area -->
        <div>

            <!-- Account Settings Section -->
            <div id="account-section" class="settings-section" style="display: block;">
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 30px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #333; margin: 0 0 25px 0;">Account Information</h2>

                    <form method="POST" action="{{ route('settings.updateProfile') }}">
                        @csrf

                        <!-- Name -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.2s;"
                                onchange="this.style.borderColor='#ddd'" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#ddd'">
                            @error('name')<span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>@enderror
                        </div>

                        <!-- Email -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#ddd'">
                            @error('email')<span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>@enderror
                        </div>

                        <!-- Phone -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#ddd'">
                            @error('phone')<span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>@enderror
                        </div>

                        <!-- Address -->
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Address</label>
                            <textarea name="address" rows="3" placeholder="Your street address"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit; transition: border-color 0.2s; resize: vertical;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#ddd'">{{ old('address', $user->address) }}</textarea>
                            @error('address')<span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>@enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" style="background-color: #f97316; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background-color 0.2s;">
                            <i class="bi bi-check-lg" style="margin-right: 8px;"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Settings Section -->
            <div id="password-section" class="settings-section" style="display: none;">
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 30px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #333; margin: 0 0 25px 0;">Change Password</h2>

                    <form method="POST" action="{{ route('settings.updatePassword') }}">
                        @csrf

                        <!-- Current Password -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Current Password</label>
                            <input type="password" name="current_password" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#ddd'">
                            @error('current_password')<span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>@enderror
                        </div>

                        <!-- New Password -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">New Password</label>
                            <input type="password" name="password" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#ddd'">
                            <small style="color: #666; display: block; margin-top: 5px;">Minimum 8 characters</small>
                            @error('password')<span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>@enderror
                        </div>

                        <!-- Confirm Password -->
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px;">Confirm Password</label>
                            <input type="password" name="password_confirmation" 
                                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#ddd'">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" style="background-color: #f97316; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background-color 0.2s;">
                            <i class="bi bi-shield-check" style="margin-right: 8px;"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Privacy Settings Section -->
            <div id="privacy-section" class="settings-section" style="display: none;">
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 30px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #333; margin: 0 0 25px 0;">Privacy Settings</h2>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <!-- Profile Visibility -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 6px; border-left: 4px solid #f97316;">
                            <div>
                                <h3 style="font-weight: 600; color: #333; margin: 0 0 5px 0;">Profile Visibility</h3>
                                <p style="color: #666; font-size: 13px; margin: 0;">Control who can see your profile</p>
                            </div>
                            <select style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; cursor: pointer;">
                                <option>Everyone</option>
                                <option>School Only</option>
                                <option>Private</option>
                            </select>
                        </div>

                        <!-- Show Email -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 6px;">
                            <div>
                                <h3 style="font-weight: 600; color: #333; margin: 0 0 5px 0;">Show Email Address</h3>
                                <p style="color: #666; font-size: 13px; margin: 0;">Allow others to see your email</p>
                            </div>
                            <input type="checkbox" style="width: 20px; height: 20px; cursor: pointer;">
                        </div>

                        <!-- Show Phone -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 6px;">
                            <div>
                                <h3 style="font-weight: 600; color: #333; margin: 0 0 5px 0;">Show Phone Number</h3>
                                <p style="color: #666; font-size: 13px; margin: 0;">Allow others to see your contact</p>
                            </div>
                            <input type="checkbox" style="width: 20px; height: 20px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Section -->
            <div id="notifications-section" class="settings-section" style="display: none;">
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 30px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #333; margin: 0 0 25px 0;">Notification Preferences</h2>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <!-- Email Notifications -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 6px; border-left: 4px solid #10b981;">
                            <div>
                                <h3 style="font-weight: 600; color: #333; margin: 0 0 5px 0;">Email Notifications</h3>
                                <p style="color: #666; font-size: 13px; margin: 0;">Receive updates via email</p>
                            </div>
                            <input type="checkbox" checked style="width: 20px; height: 20px; cursor: pointer;">
                        </div>

                        <!-- Absence Updates -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 6px;">
                            <div>
                                <h3 style="font-weight: 600; color: #333; margin: 0 0 5px 0;">Absence Status Updates</h3>
                                <p style="color: #666; font-size: 13px; margin: 0;">Notify when absence is approved/rejected</p>
                            </div>
                            <input type="checkbox" checked style="width: 20px; height: 20px; cursor: pointer;">
                        </div>

                        <!-- System Updates -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 6px;">
                            <div>
                                <h3 style="font-weight: 600; color: #333; margin: 0 0 5px 0;">System Updates</h3>
                                <p style="color: #666; font-size: 13px; margin: 0;">Important system announcements</p>
                            </div>
                            <input type="checkbox" checked style="width: 20px; height: 20px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log Section -->
            <div id="activity-section" class="settings-section" style="display: none;">
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 30px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #333; margin: 0 0 25px 0; display: flex; justify-content: space-between; align-items: center;">
                        <span>Your Recent Activity</span>
                        <span style="font-size: 14px; color: #666; font-weight: 400;">Last 10 activities</span>
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @forelse($userLogs as $log)
                            <div style="padding: 15px; background: #f9fafb; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid 
                                @if(str_contains($log->action, 'approved'))
                                    #10b981
                                @elseif(str_contains($log->action, 'rejected'))
                                    #ef4444
                                @elseif(str_contains($log->action, 'created'))
                                    #0284c7
                                @else
                                    #f97316
                                @endif
                            ;">
                                <div style="flex: 1;">
                                    <h4 style="font-weight: 600; color: #333; margin: 0 0 3px 0;">{{ str_replace('_', ' ', ucfirst($log->action)) }}</h4>
                                    <p style="color: #666; font-size: 12px; margin: 0;">{{ $log->description ?? 'No description' }}</p>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-size: 12px; color: #666; display: block;">{{ $log->created_at->diffForHumans() }}</span>
                                    <span style="font-size: 11px; color: #999;">{{ $log->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 40px 20px; color: #999;">
                                <i class="bi bi-inbox" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                                <p>No activity recorded yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .nav-btn {
        background: white;
        border: 1px solid #e5e7eb;
        padding: 12px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        color: #666;
        transition: all 0.2s;
        text-align: left;
        font-size: 14px;
    }

    .nav-btn:hover {
        background-color: #f9fafb;
        border-color: #f97316;
        color: #333;
    }

    .nav-btn.active {
        background-color: #f97316;
        color: white;
        border-color: #f97316;
    }

    .settings-section {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        div[style*="display: grid; grid-template-columns: 230px 1fr"] {
            grid-template-columns: 1fr !important;
        }

        div[style*="position: sticky"] {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px !important;
            position: relative !important;
            top: auto !important;
        }
    }
</style>

<script>
    function showSection(sectionName) {
        // Hide all sections
        document.querySelectorAll('.settings-section').forEach(section => {
            section.style.display = 'none';
        });

        // Remove active class from all buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected section
        document.getElementById(sectionName + '-section').style.display = 'block';

        // Add active class to clicked button
        event.target.closest('.nav-btn').classList.add('active');
    }
</script>
@endsection
