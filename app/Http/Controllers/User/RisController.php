<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Supply; 
use App\Models\SystemSetting; 
use Illuminate\Support\Facades\Auth;

class RisController extends Controller
{
    /**
     * Show the printable RIS form.
     *
     * User-side submissions were removed: division users now prepare the RIS
     * on paper (typed + printed here) and submit the signed physical form to
     * the office, where staff encode it into the system.
     */
    public function create()
    {
        $seqSetting = SystemSetting::firstOrCreate(
            ['key' => 'seq_ris_no'], 
            ['value' => '1']
        );
        $risNumber = 'RIS-' . date('Y-m') . '-' . str_pad($seqSetting->value, 4, '0', STR_PAD_LEFT);
        $supplies = Supply::orderBy('article', 'asc')->get();
        $user = Auth::user();
        return view('user.ris.create', compact('risNumber', 'supplies', 'user'));
    }
}
