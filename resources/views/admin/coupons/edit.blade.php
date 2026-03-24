<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.coupons.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Coupon: ') }} {{ $coupon->code }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700">Coupon Code</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" required class="mt-1 font-mono uppercase focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Discount Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Discount Type</label>
                                <select id="type" name="type" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                                </select>
                                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Value -->
                            <div>
                                <label for="value" class="block text-sm font-medium text-gray-700">Discount Value</label>
                                <input type="number" step="0.01" min="0" name="value" id="value" value="{{ old('value', $coupon->value) }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Max Uses -->
                            <div>
                                <label for="max_uses" class="block text-sm font-medium text-gray-700">Maximum Uses Limit (Used: {{ $coupon->used_count }})</label>
                                <input type="number" min="1" name="max_uses" id="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Leave blank for unlimited">
                                @error('max_uses') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Target/Scope -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Target Scope</label>
                                <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10 border p-4 rounded-md bg-gray-50/50">
                                    <div class="flex items-center">
                                        <input id="target_global" name="is_global" type="radio" value="1" {{ old('is_global', $coupon->is_global) == '1' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" onclick="document.getElementById('service_selector').classList.add('hidden')">
                                        <label for="target_global" class="ml-3 block text-sm font-medium text-gray-700">
                                            Global (Any Service)
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="target_specific" name="is_global" type="radio" value="0" {{ old('is_global', $coupon->is_global) == '0' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" onclick="document.getElementById('service_selector').classList.remove('hidden')">
                                        <label for="target_specific" class="ml-3 block text-sm font-medium text-gray-700">
                                            Specific Service
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Specific Service Selector Details (Hidden if Global) -->
                            <div id="service_selector" class="md:col-span-2 {{ old('is_global', $coupon->is_global) == '1' ? 'hidden' : '' }}">
                                <label for="service_id" class="block text-sm font-medium text-gray-700">Select Linked Service</label>
                                <select id="service_id" name="service_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Choose target service --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id', $coupon->service_id) == $service->id ? 'selected' : '' }}>{{ $service->name }} (${{ $service->price }})</option>
                                    @endforeach
                                </select>
                                @error('service_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Visibility & Access -->
                            <div class="md:col-span-2 border-t border-gray-100 pt-6 mt-2">
                                <label class="block text-sm font-bold text-indigo-900 mb-3 uppercase tracking-wider">Visibility & Access Control</label>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="is_private" name="is_private" type="checkbox" value="1" {{ old('is_private', $coupon->is_private) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" onchange="document.getElementById('user_selector_wrapper').classList.toggle('hidden', !this.checked)">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_private" class="font-medium text-gray-700">Make Private (Hidden from public lists)</label>
                                        <p class="text-gray-500">Private coupons will not be shown in the client dashboard or pricing pages. They must be shared manually.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Assigned User Details (Hidden if not Private) -->
                            <div id="user_selector_wrapper" class="md:col-span-2 {{ old('is_private', $coupon->is_private) ? '' : 'hidden' }}">
                                <label for="assigned_user_id" class="block text-sm font-medium text-gray-700">Assign to Specific User (Optional)</label>
                                <p class="text-xs text-gray-500 mb-2 font-medium">Leave unassigned for a "secret" coupon anyone can use if they have the code.</p>
                                <select id="assigned_user_id" name="assigned_user_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- No specific user (Secret for all) --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_user_id', $coupon->assigned_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('assigned_user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Expiry Date -->
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('expires_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="1" {{ old('status', $coupon->status) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $coupon->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
                            <a href="{{ route('admin.coupons.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Coupon
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
