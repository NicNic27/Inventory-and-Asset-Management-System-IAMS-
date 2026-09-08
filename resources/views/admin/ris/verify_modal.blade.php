<style>
    .admin-action-wrap { position: relative; }
    .admin-action-input { position: absolute; opacity: 0; width: 1px; height: 1px; margin: 0; pointer-events: none; }
    .admin-action-card {
        display: flex; align-items: center; gap: 12px;
        height: 100%;
        padding: 14px 16px;
        background: #ffffff;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        user-select: none;
        transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }
    .admin-action-card:hover { border-color: #c9d0da; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 25, 84, 0.08); }
    .admin-action-input:focus-visible + .admin-action-card { outline: 3px solid rgba(16, 25, 84, 0.35); outline-offset: 2px; }
    .admin-action-input:checked + .admin-action-card {
        border-color: var(--aac);
        background: var(--aac-bg);
        box-shadow: 0 0 0 3px var(--aac-ring);
    }
    .aac-icon { font-size: 1.35rem; flex-shrink: 0; }
    .aac-body { flex: 1; min-width: 0; }
    .aac-title { display: block; font-weight: 700; font-size: .95rem; color: #212529; line-height: 1.2; }
    .aac-desc { display: block; font-size: .78rem; color: #6c757d; margin-top: 2px; }
    .aac-check { margin-left: auto; font-size: 1.1rem; color: var(--aac); opacity: 0; transform: scale(.5); transition: all .15s ease; }
    .admin-action-input:checked + .admin-action-card .aac-title { color: var(--aac); }
    .admin-action-input:checked + .admin-action-card .aac-check { opacity: 1; transform: scale(1); }
</style>

<div class="modal-header border-0 py-3 flex-shrink-0" style="background-color: #101954; color: white; border-radius: 10px 10px 0 0;">
    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-file-signature me-2"></i> Final Admin Review</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form action="{{ url('/admin/ris/'.$req->id.'/process') }}" method="POST" class="d-flex flex-column w-100 h-100 m-0" style="flex: 1; min-height: 0;">
    @csrf
    
    <div class="modal-body p-3 p-md-4 bg-light paper-scroll" style="overflow-y: auto;">
        
        <div class="bg-white p-3 p-md-4 shadow-sm border mb-4" style="overflow-x: auto;">
            <div style="min-width: 800px; font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: black; padding: 10px;">
                
                <div style="text-align: center; margin-bottom: 15px;">
                    <img src="{{ asset('assets/images/DepEdseal.png') }}" style="width: 60px; margin: 0 auto 2px auto; display: block;">
                    <div style="font-size: 14px; font-family: 'Old English Text MT', 'Engravers Old English', serif; font-weight: normal; text-transform: none;">Republic of the Philippines</div>
                    <h4 style="font-size: 24px; font-family: 'Old English Text MT', 'Engravers Old English', serif; font-weight: normal; margin-top: 5px; margin-bottom: 2px; text-transform: none;">Department of Education</h4>
                    <div style="font-size: 11px; font-weight: bold; font-family: Arial, sans-serif; margin-top: 2px;">REGION V - BICOL</div>
                    <h3 style="font-size: 14pt; font-weight: bold; margin-top: 10px;">REQUISITION AND ISSUE SLIP</h3>
                </div>

                <table style="width: 100%; border: none; margin-bottom: 5px;">
                    <tr>
                        <td style="width: 15%; white-space: nowrap; padding: 2px;">Entity Name:</td>
                        <td style="width: 45%; border-bottom: 1px solid black; padding: 2px;">{{ $req->entity_name }}</td>
                        <td style="width: 15%; text-align: right; padding-right: 10px; white-space: nowrap; padding: 2px;">Fund Cluster:</td>
                        <td style="width: 25%; border-bottom: 1px solid black; padding: 2px;">{{ $req->fund_cluster }}</td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; padding: 2px;">Division:</td>
                        <td style="border-bottom: 1px solid black; padding: 2px;">{{ $req->division }}</td>
                        <td style="text-align: right; padding-right: 10px; white-space: nowrap; padding: 2px;">Responsibility Center Code:</td>
                        <td style="border-bottom: 1px solid black; padding: 2px;">{{ $req->rcc }}</td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; padding: 2px;">Office:</td>
                        <td style="border-bottom: 1px solid black; padding: 2px;">{{ $req->office }}</td>
                        <td style="text-align: right; padding-right: 10px; white-space: nowrap; padding: 2px;">RIS No:</td>
                        <td style="border-bottom: 1px solid black; font-weight: bold; padding: 2px; color: #dc3545;">{{ $req->ris_no }}</td>
                    </tr>
                </table>

                <table style="width: 100%; border-collapse: collapse; border: 1px solid black; table-layout: fixed;">
                    <colgroup>
                        <col style="width: 10%;"> 
                        <col style="width: 8%;">  
                        <col style="width: 38%;"> 
                        <col style="width: 8%;">  
                        <col style="width: 5%;">  
                        <col style="width: 5%;">  
                        <col style="width: 8%;">  
                        <col style="width: 18%;"> 
                    </colgroup>
                    <thead>
                        <tr>
                            <th colspan="4" style="border: 1px solid black; padding: 3px; text-align: center;">REQUISITION</th>
                            <th colspan="2" style="border: 1px solid black; padding: 3px; text-align: center;">Stock Available?</th>
                            <th colspan="2" style="border: 1px solid black; padding: 3px; text-align: center;">Issue</th>
                        </tr>
                        <tr>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">Stock No.</th>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">Unit</th>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">Description</th>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">Quantity</th>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">Yes</th>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">No</th>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">Quantity</th>
                            <th style="border: 1px solid black; padding: 3px; text-align: center;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $rowsAdded = 0; 
                            $itemsCount = $req->items ? $req->items->count() : 0;
                        @endphp
                        
                        @if($itemsCount > 0)
                            @foreach($req->items as $item)
                                @php
                                    $isYes = strtolower($item->stock_avail) == 'yes' ? '✔' : '&nbsp;';
                                    $isNo = strtolower($item->stock_avail) == 'no' ? '✘' : '&nbsp;';
                                @endphp
                                <tr>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center;">{{ $item->stock_no ?: '' }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center;">{{ $item->unit ?: '' }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: left;">{{ $item->description ?: '' }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center;">{{ $item->req_quantity ?: '' }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center;">{!! $isYes !!}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center;">{!! $isNo !!}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: center; color: #198754; font-weight: bold; background-color: #e8f5e9;">{{ $item->issue_quantity ?: '' }}</td>
                                    <td style="border: 1px solid black; padding: 4px; text-align: left;">{{ $item->remarks ?: '' }}</td>
                                </tr>
                                @php $rowsAdded++; @endphp
                            @endforeach
                        @endif

                        @for($j = $rowsAdded; $j < 10; $j++)
                            @php
                                $isLast = ($j === 9);
                                $borderStyle = $isLast 
                                    ? "border-left: 1px solid black; border-right: 1px solid black; border-top: none; border-bottom: 1px solid black;" 
                                    : "border-left: 1px solid black; border-right: 1px solid black; border-top: none; border-bottom: none;";
                            @endphp
                            <tr>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                                <td style="{!! $borderStyle !!} padding: 6px;">&nbsp;</td>
                            </tr>
                        @endfor
                        <tr>
                            <td colspan="8" style="border: 1px solid black; padding: 5px; text-align: left;">
                                <b>Purpose:</b> {{ $req->purpose }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table style="width: 100%; border-collapse: collapse; border: 1px solid black; border-top: none; table-layout: fixed;">
                    <tbody>
                        <tr>
                            <td style="width: 12%; border: 1px solid black; padding: 3px; border-top: none;"></td>
                            <td style="width: 22%; border: 1px solid black; padding: 3px; font-weight: bold; text-align: center; border-top: none;">Requested by:</td>
                            <td style="width: 22%; border: 1px solid black; padding: 3px; font-weight: bold; text-align: center; border-top: none;">Approved by:</td>
                            <td style="width: 22%; border: 1px solid black; padding: 3px; font-weight: bold; text-align: center; border-top: none;">Issued by:</td>
                            <td style="width: 22%; border: 1px solid black; padding: 3px; font-weight: bold; text-align: center; border-top: none;">Received by:</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 3px; text-align: left;">Signature</td>
                            <td style="border: 1px solid black; padding: 3px;"></td>
                            <td style="border: 1px solid black; padding: 3px;"></td>
                            <td style="border: 1px solid black; padding: 3px;"></td>
                            <td style="border: 1px solid black; padding: 3px;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 3px; text-align: left;">Printed Name</td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;"><b>{{ $req->sig_requested_by }}</b></td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;"><b>{{ $req->sig_approved_by }}</b></td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;"><b>{{ $req->sig_issued_by }}</b></td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;"><b>{{ $req->sig_received_by }}</b></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 3px; text-align: left;">Designation</td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;">{!! str_replace(' (', '<br>(', $req->desig_requested) !!}</td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;">{!! str_replace(' (', '<br>(', $req->desig_approved) !!}</td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;">{!! str_replace(' (', '<br>(', $req->desig_issued) !!}</td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;">{!! str_replace(' (', '<br>(', $req->desig_received) !!}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 3px; text-align: left;">Date</td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;">{{ $req->date_requested }}</td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;"></td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;"></td>
                            <td style="border: 1px solid black; padding: 3px; text-align: center;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if(in_array($req->status, ['Rejected', 'Declined', 'Cancelled']))
            <div class="alert alert-danger mb-0 border-0 shadow-sm">
                <i class="fas fa-times-circle me-2"></i> This RIS has been permanently rejected or cancelled. No further actions can be taken.
            </div>
        @elseif($req->status == 'Approved')
            <div class="alert alert-success mb-3 border-0 shadow-sm">
                <i class="fas fa-check-circle me-2"></i> This RIS is Approved. Master inventory stocks have been deducted.
            </div>
            <div class="text-center border-top pt-3 border-2 border-primary mt-2">
                <button type="submit" name="new_status" value="Pending Staff Review" class="btn btn-warning fw-bold px-4 shadow-sm" onclick="return confirm('Are you sure you want to reopen this RIS? This will automatically RESTORE the deducted stocks back to the inventory.')">
                    <i class="fas fa-undo me-2"></i> Revoke Approval & Return to Staff
                </button>
            </div>
        @else
            <div class="border-top pt-3 border-2 border-primary mt-2">
                <label class="form-label fw-bold text-primary"><i class="fas fa-gavel me-1"></i> Final Admin Action</label>
                <small class="text-muted d-block mb-2">Select one — this is the final decision for this RIS.</small>
                <div class="row g-2">
                    <div class="col-md-4 admin-action-wrap">
                        <input type="radio" class="admin-action-input" name="new_status" id="actionApprove" value="Approved" required>
                        <label for="actionApprove" class="admin-action-card" style="--aac:#198754; --aac-bg:#e8f5e9; --aac-ring:rgba(25,135,84,.18);">
                            <span class="aac-icon" style="color:#198754;"><i class="fas fa-circle-check"></i></span>
                            <span class="aac-body">
                                <span class="aac-title">Approve Request</span>
                                <span class="aac-desc">Release stocks from inventory</span>
                            </span>
                            <i class="fas fa-circle-check aac-check"></i>
                        </label>
                    </div>
                    <div class="col-md-4 admin-action-wrap">
                        <input type="radio" class="admin-action-input" name="new_status" id="actionReturn" value="Pending Staff Review" required>
                        <label for="actionReturn" class="admin-action-card" style="--aac:#b45309; --aac-bg:#fff7e6; --aac-ring:rgba(180,83,9,.18);">
                            <span class="aac-icon" style="color:#b45309;"><i class="fas fa-rotate-left"></i></span>
                            <span class="aac-body">
                                <span class="aac-title">Return to Staff</span>
                                <span class="aac-desc">Send back for corrections</span>
                            </span>
                            <i class="fas fa-circle-check aac-check"></i>
                        </label>
                    </div>
                    <div class="col-md-4 admin-action-wrap">
                        <input type="radio" class="admin-action-input" name="new_status" id="actionDecline" value="Rejected" required>
                        <label for="actionDecline" class="admin-action-card" style="--aac:#dc3545; --aac-bg:#fdecec; --aac-ring:rgba(220,53,69,.18);">
                            <span class="aac-icon" style="color:#dc3545;"><i class="fas fa-ban"></i></span>
                            <span class="aac-body">
                                <span class="aac-title">Decline / Cancel</span>
                                <span class="aac-desc">Reject this request</span>
                            </span>
                            <i class="fas fa-circle-check aac-check"></i>
                        </label>
                    </div>
                </div>
                <div id="deductWarning" class="d-none alert alert-warning d-flex align-items-center gap-2 py-2 px-3 mt-3 mb-0 shadow-sm">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                    <div class="small">Approving will automatically <strong>deduct the Issued Quantity</strong> of each item from the master inventory.</div>
                </div>
            </div>
        @endif

    </div>

    <div class="modal-footer border-0 bg-white rounded-bottom flex-shrink-0">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
        @if(!in_array($req->status, ['Rejected', 'Declined', 'Cancelled', 'Approved']))
            <button type="submit" id="confirmActionBtn" class="btn btn-success px-4 fw-bold"><i class="fas fa-check-circle me-1"></i> Confirm & Save</button>
        @endif
    </div>
</form>
