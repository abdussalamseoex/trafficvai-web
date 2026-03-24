<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with('service')->latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $services = Service::all();
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.coupons.create', compact('services', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_global' => 'required|boolean',
            'is_private' => 'required|boolean',
            'assigned_user_id' => 'nullable|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        // If it's global, ensure service_id is null
        if ($data['is_global']) {
            $data['service_id'] = null;
        }

        // If not private, ensure assigned_user_id is null
        if (!$data['is_private']) {
            $data['assigned_user_id'] = null;
        }

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        $services = Service::all();
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.coupons.edit', compact('coupon', 'services', 'users'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_global' => 'required|boolean',
            'is_private' => 'required|boolean',
            'assigned_user_id' => 'nullable|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        if ($data['is_global']) {
            $data['service_id'] = null;
        }

        if (!$data['is_private']) {
            $data['assigned_user_id'] = null;
        }

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
