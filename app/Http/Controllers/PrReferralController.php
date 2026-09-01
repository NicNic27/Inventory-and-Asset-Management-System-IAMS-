<?php

namespace App\Http\Controllers;

use App\Models\PrReferral;
use Illuminate\Http\Request;

class PrReferralController extends Controller
{
    /** Pending (referred/po_issued) referrals for a supply, for the direct-issuance RIS picker */
    public function pending(Request $request)
    {
        $supplyId = $request->query('supply_id');

        $referrals = PrReferral::whereIn('status', ['referred', 'po_issued'])
            ->when($supplyId, fn ($q) => $q->where('supply_id', $supplyId))
            ->with('risRequest')
            ->orderBy('referred_at')
            ->get()
            ->map(function (PrReferral $referral) {
                return [
                    'id' => $referral->id,
                    'ris_no' => $referral->risRequest->ris_no ?? null,
                    'office' => $referral->risRequest->office ?? null,
                    'quantity_needed' => (int) $referral->quantity_needed,
                    'remaining' => max(0, (int) $referral->quantity_needed - $referral->getFulfilledQuantity()),
                ];
            })
            ->filter(fn ($r) => $r['remaining'] > 0)
            ->values();

        return response()->json($referrals);
    }
}
