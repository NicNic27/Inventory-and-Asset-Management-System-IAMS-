<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $keys = [
            'seq_ris_no', 
            'seq_par_no', 
            'seq_sphv_no', 
            'seq_splv_no'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Updated core system sequence settings.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('msg', 'Sequence settings updated successfully!');
    }
}