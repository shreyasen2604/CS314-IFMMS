<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('vehicle')->orderBy('role')->orderBy('name')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['Admin','Driver','Technician'];
        $vehicles = Vehicle::where('status', 'active')
                          ->whereNull('driver_id')
                          ->orderBy('vehicle_number')
                          ->get();
        return view('admin.users.create', compact('roles', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'role'     => ['required', Rule::in(['Admin','Driver','Technician'])],
            'password' => ['required','min:8','confirmed'],
            'vehicle_id' => ['nullable','string','exists:vehicles,vehicle_number'],
            'profile_picture' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:2048'], // 2MB max
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            $filename = time() . '_' . $profilePicture->getClientOriginalName();
            $path = $profilePicture->storeAs('profile-pictures', $filename, 'public');
            $validated['profile_picture'] = $path;
        }

        // Create the user
        $user = User::create($validated);

        // If this is a driver and a vehicle is assigned, update the vehicle's driver_id
        if ($user->role === 'Driver' && $validated['vehicle_id']) {
            Vehicle::where('vehicle_number', $validated['vehicle_id'])
                   ->update(['driver_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('ok', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = ['Admin','Driver','Technician'];
        
        // Get available vehicles (not assigned to other drivers) plus the current user's vehicle
        $vehicles = Vehicle::where('status', 'active')
                          ->where(function($query) use ($user) {
                              $query->whereNull('driver_id')
                                    ->orWhere('driver_id', $user->id);
                          })
                          ->orderBy('vehicle_number')
                          ->get();
                          
        return view('admin.users.edit', compact('user','roles', 'vehicles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'role'  => ['required', Rule::in(['Admin','Driver','Technician'])],
            'password' => ['nullable','min:8','confirmed'],
            'vehicle_id' => ['nullable','string','exists:vehicles,vehicle_number'],
            'profile_picture' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:2048'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']); // keep old password
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            $profilePicture = $request->file('profile_picture');
            $filename = time() . '_' . $profilePicture->getClientOriginalName();
            $path = $profilePicture->storeAs('profile-pictures', $filename, 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle vehicle assignment changes
        $oldVehicleId = $user->vehicle_id;
        $newVehicleId = $validated['vehicle_id'] ?? null;

        // If vehicle assignment changed
        if ($oldVehicleId !== $newVehicleId) {
            // Remove driver from old vehicle
            if ($oldVehicleId) {
                Vehicle::where('vehicle_number', $oldVehicleId)
                       ->update(['driver_id' => null]);
            }
            
            // Assign driver to new vehicle
            if ($newVehicleId && $user->role === 'Driver') {
                Vehicle::where('vehicle_number', $newVehicleId)
                       ->update(['driver_id' => $user->id]);
            }
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('ok', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('err', 'You cannot delete your own admin account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('ok', 'User deleted.');
    }
}
