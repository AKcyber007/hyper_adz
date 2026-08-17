@extends('admin.layouts.app')

@section('page_title')
    <i class="bi bi-person-fill text-[#1155CC]"></i> Admin Profile
@endsection

@section('content')
<div class="max-w-4xl space-y-8">
    <!-- Profile Info Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-6">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            <i class="bi bi-person-circle text-[#1155CC]"></i> Profile Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Name</label>
                <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 font-semibold text-sm">
                    {{ $user->name }}
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Email Address</label>
                <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 font-semibold text-sm">
                    {{ $user->email }}
                </div>
            </div>
        </div>
    </div>

    <!-- Password Change Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-6">
        <div class="space-y-1">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="bi bi-shield-lock-fill text-[#1155CC]"></i> Change Password
            </h3>
            <p class="text-xs text-slate-400">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        @if (session('status') === 'password-updated')
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 text-emerald-800 dark:text-emerald-400 rounded-2xl text-sm font-semibold flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> Password updated successfully.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Current Password</label>
                    <input id="update_password_current_password" name="current_password" type="password" class="w-full p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:border-[#1155CC] focus:ring-1 focus:ring-[#1155CC] transition-all" required autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <span class="block text-xs text-red-500 font-semibold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="update_password_password" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">New Password</label>
                    <input id="update_password_password" name="password" type="password" class="w-full p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:border-[#1155CC] focus:ring-1 focus:ring-[#1155CC] transition-all" required autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <span class="block text-xs text-red-500 font-semibold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Confirm Password</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:border-[#1155CC] focus:ring-1 focus:ring-[#1155CC] transition-all" required autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword')
                        <span class="block text-xs text-red-500 font-semibold mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-5 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-blue-500/10 flex items-center gap-2">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
