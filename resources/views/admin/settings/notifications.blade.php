<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Email Notification Toggles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <p class="mb-0 text-gray-500">Enable or disable specific email notifications globally.</p>
            </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4 border-0 rounded-4">
        <div class="card-header py-3 bg-white border-bottom-0">
            <h6 class="m-0 font-weight-bold" style="color:#E8470A;">Toggle Email Delivery Events</h6>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('admin.notifications.toggles.update') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Event / Trigger</th>
                                <th>Internal Variable</th>
                                <th class="text-end">Status (On/Off)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $slug => $label)
                                @php
                                    $key = "email_toggle_{$slug}";
                                    $isActive = \App\Models\Setting::get($key, '1') == '1';
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $label }}</td>
                                    <td><code class="text-muted">{{ $slug }}</code></td>
                                    <td class="text-end">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch" name="{{ $key }}" id="{{ $key }}" {{ $isActive ? 'checked' : '' }} style="transform: scale(1.3); cursor: pointer;">
                                            <label class="form-check-label d-none" for="{{ $key }}">{{ $label }}</label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2" style="background:#E8470A; border:none; border-radius:8px;">
                        <i class="fas fa-save me-2"></i> Save Notification Toggles
                    </button>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>
</x-app-layout>
