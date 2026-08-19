<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            $oldImagePath = $user->image ? public_path('uploads/users/' . $user->image) : null;
            $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $user->image = $imageName;
        } else {
            $oldImagePath = null;
        }

        $user->save();

        if ($oldImagePath && is_file($oldImagePath)) {
            unlink($oldImagePath);
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Updated',
            'description' => "User updated their own profile.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('msg', 'Profile updated successfully!');
    }
}