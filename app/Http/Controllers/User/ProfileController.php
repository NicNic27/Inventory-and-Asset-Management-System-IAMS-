<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|min:6',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        
        // If the user typed a new password, hash it and save it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle Avatar Image Upload
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

        // Redirects back to whatever page the user was on
        return redirect()->back()->with('profile_success', 'Profile updated successfully!');
    }
}