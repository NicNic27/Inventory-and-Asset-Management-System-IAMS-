<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->except(['password', 'image', 'designation', 'employee_id']);
        $data['password'] = Hash::make($request->password); 
        $data['remember_token'] = Str::random(10);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $data['image'] = $imageName;
        }

        $user = User::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created',
            'description' => "Created new user account for: {$user->email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/admin/users')->with('msg', 'User added successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->except(['password', 'image', 'designation', 'employee_id']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password); 
        }

        if ($request->hasFile('image')) {
            $oldImagePath = $user->image ? public_path('uploads/users/' . $user->image) : null;
            $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $data['image'] = $imageName;
        }

        $user->update($data);

        if (isset($oldImagePath) && is_file($oldImagePath)) {
            unlink($oldImagePath);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Updated user account: {$user->email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/admin/users')->with('msg', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted',
            'description' => "Deleted user account: {$user->email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $user->delete();
        return redirect('/admin/users')->with('msg', 'User deleted successfully!');
    }

    public function details($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.view_details', compact('user'))->render();
    }
}