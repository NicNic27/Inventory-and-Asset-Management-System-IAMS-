<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS / PAR History - DepEd ROV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', 'Inter', sans-serif; overflow: hidden; height: 100vh; margin: 0; }
        .main-content { margin-left: 250px; padding: 20px; padding-top: 80px !important; transition: all 0.3s; height: 100vh; display: flex; flex-direction: column; }
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); flex-grow: 1; display: flex; flex-direction: column; min-height: 0; }
        .table-responsive { flex-grow: 1; overflow-y: auto; margin-bottom: 10px; }
        .table thead th { position: sticky; top: 0; background-color: #f8f9fa; color: #6c757d; font-size: 0.85rem; letter-spacing: 0.5px; z-index: 1; border-bottom: 2px solid #dee2e6; }
        .clickable-row:hover td { background-color: #f8f9fa !important; cursor: pointer; }

        .bg-deped-blue { background-color: #101954 !important; color: white; }

        .sticker-container-preview {
            width: 3in; height: 4in; background: white; padding: 4px;
            border: 3px solid #101954; box-sizing: border-box; position: relative; margin: 0 auto; overflow: hidden;
        }
        .sticker-inner { border: 1px solid #101954; width: 100%; height: 100%; box-sizing: border-box; display: flex; flex-direction: column; padding: 2px; }
        .sticker-header { text-align: center; padding-top: 1px; }
        .sticker-header img { width: 30px; height: 30px; }
        .rp-text { font-size: 6.5pt; color: black; }
        .deped-text { font-family: 'Old English Text MT', serif; font-size: 11pt; font-weight: bold; line-height: 1; color: black; margin: 1px 0; }
        .region-text { font-size: 7pt; text-transform: uppercase; color: black; }
        .sticker-table { width: 100%; border-collapse: collapse; font-size: 7pt; flex-grow: 1; margin-top: 2px; }
        .sticker-table td { border: 1px solid #101954; padding: 2px 4px; vertical-align: middle; color: black; }
        .lbl { font-size: 6.5pt; color: #333; margin-bottom: 0; line-height: 1; }
        .no-border-input { border: none; width: 100%; font-size: 8pt; font-weight: bold; background: transparent; outline: none; color: black; height: 14px; }
        .req-field-empty { border-bottom: 2px solid #dc3545 !important; }
        .val-list { font-size: 7pt; line-height: 1.2; margin-top: 2px; }
        .val-list input { font-weight: normal; font-size: 7.5pt; height: 12px; }

        .digital-form-container { background: white; width: 8.5in; min-height: 11in; padding: 0.5in; margin: 0 auto; color: black; font-family: Arial, sans-serif; border: 1px solid #ddd; }
        .form-header-img { width: 70px; display: block; margin: 0 auto 5px; }
        .form-title { font-size: 12pt; font-weight: bold; text-align: center; margin: 15px 0; border-top: 1px solid transparent; }
        .form-table-main { width: 100%; border-collapse: collapse; border: 1px solid black; table-layout: fixed; }
        .form-table-main th, .form-table-main td { border: 1px solid black; padding: 5px; font-size: 9pt; }

        .scrollable-modal-body { max-height: 75vh; overflow-y: auto; background: #555; padding: 20px; }
        
        .modal { background: rgba(0,0,0,0.4); z-index: 1050 !important; }
        .modal-backdrop { display: none !important; }

        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    @include('layouts.header')
    @include('layouts.sidebar')

    <div class="main-content">
        <div class="row align-items-center mb-3">
            <div class="col-md-8">
                <h3 class="fw-bold text-dark mb-0"><i class="fas fa-file-signature text-primary me-2"></i>ICS & PAR History</h3>
            </div>
        </div>

        @if(session('msg'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2">
                <i class="fas fa-check-circle me-2"></i> {{ session('msg') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-container">
            <form action="{{ url('/ics/history') }}" method="GET" class="row g-2 mb-3" onsubmit="return false;">
                <div class="col-12 col-md-5">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" name="search" class="form-control border-start-0" placeholder="Search Document No. or Person Accountable..." value="{{ request('search') }}">
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="historyTable">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>Document No.</th>
                            <th>Category</th>
                            <th>Person Accountable</th>
                            <th>Date Created</th>
                            <th class="text-center">Actual Doc</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                        @php
                            $accountablePerson = $req->category == 'PPE' 
                                ? ($req->sig_received_from_name ?: 'N/A') 
                                : ($req->sig_received_by_name ?: 'N/A');
                        @endphp
                        <tr>
                            <td class="fw-bold text-primary" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#viewChoiceModal{{ $req->id }}">{{ $req->ics_no }}</td>
                            <td><span class="badge bg-deped-blue px-3 py-1">{{ $req->category }}</span></td>
                            <td class="fw-bold text-dark">{{ $accountablePerson }}</td>
                            <td class="text-muted small">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                @if($req->signed_document)
                                    <span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> Uploaded</span>
                                @else
                                    <button class="btn btn-sm btn-light border text-danger fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal{{ $req->id }}">
                                        <i class="fas fa-upload me-1"></i> Upload
                                    </button>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-light border text-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#viewChoiceModal{{ $req->id }}"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-light border text-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#itemsModal{{ $req->id }}"><i class="fas fa-tags"></i> Items</button>
                                    <button class="btn btn-sm btn-light border text-success shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $req->id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-light border text-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteDocModal{{ $req->id }}"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach($requests as $req)
        <div class="modal fade" id="digitalModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Digital View: {{ $req->ics_no }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body scrollable-modal-body">
                        <div class="digital-form-container shadow">
                            <div class="text-center">
                                <img src="{{ asset('assets/images/DepEdseal.png') }}" class="form-header-img">
                                <div style="font-size: 8pt;">Republic of the Philippines</div>
                                <div class="deped-text">Department of Education</div>
                                <div style="font-size: 9pt;">REGION V - BICOL</div>
                                <div class="form-title">{{ $req->category == 'PPE' ? 'PROPERTY ACKNOWLEDGMENT RECEIPT' : 'INVENTORY CUSTODIAN SLIP' }}</div>
                            </div>
                            
                            <table style="width: 100%; font-size: 10pt; margin-bottom: 10px; border: none;">
                                <tr>
                                    <td style="width: 15%; padding: 2px;">Entity Name:</td>
                                    <td style="width: 45%; padding: 2px; border-bottom: 1px solid black;">Department of Education ROV</td>
                                    <td style="width: 15%; text-align: right; padding: 2px 10px;">{{ $req->category == 'PPE' ? 'PAR No:' : 'ICS No:' }}</td>
                                    <td style="width: 25%; padding: 2px; border-bottom: 1px solid black; font-weight: bold;">{{ $req->ics_no }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px;">Fund Cluster:</td>
                                    <td style="padding: 2px; border-bottom: 1px solid black;">{{ $req->fund_cluster }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>

                            <table class="form-table-main">
                                <thead>
                                    @if($req->category == 'PPE')
                                        <tr>
                                            <th style="width: 10%; text-align: center;">Quantity</th>
                                            <th style="width: 10%; text-align: center;">Unit</th>
                                            <th style="width: 35%; text-align: center;">Description</th>
                                            <th style="width: 15%; text-align: center;">Property Number</th>
                                            <th style="width: 15%; text-align: center;">Date Acquired</th>
                                            <th style="width: 15%; text-align: center;">Amount</th>
                                        </tr>
                                    @else
                                        <tr>
                                            <th rowspan="2" style="width: 8%; text-align: center; vertical-align: middle;">Quantity</th>
                                            <th rowspan="2" style="width: 8%; text-align: center; vertical-align: middle;">Unit</th>
                                            <th colspan="2" style="width: 20%; text-align: center;">Amount</th>
                                            <th rowspan="2" style="width: 34%; text-align: center; vertical-align: middle;">Description</th>
                                            <th rowspan="2" style="width: 15%; text-align: center; vertical-align: middle;">Inventory<br>Item Nos.</th>
                                            <th rowspan="2" style="width: 15%; text-align: center; vertical-align: middle;">Estimated<br>Useful Life</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 10%; text-align: center;">Unit Cost</th>
                                            <th style="width: 10%; text-align: center;">Total Cost</th>
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    @foreach($req->items_json as $item)
                                    <tr>
                                        <td class="text-center" style="border-bottom: none; border-top: none;">{{ $item['qty'] }}</td>
                                        <td class="text-center" style="border-bottom: none; border-top: none;">{{ $item['unit'] }}</td>
                                        @if($req->category != 'PPE')
                                            <td class="text-end" style="border-bottom: none; border-top: none;">{{ number_format((float)($item['unit_cost'] ?? 0), 2) }}</td>
                                            <td class="text-end" style="border-bottom: none; border-top: none;">{{ number_format((float)($item['total_cost'] ?? 0), 2) }}</td>
                                        @endif
                                        <td style="border-bottom: none; border-top: none; vertical-align: top;">
                                            @if(!empty($item['article']))
                                                <strong>{{ $item['article'] }}</strong><br>
                                            @endif
                                            {{ $item['desc'] ?? '' }}
                                            @if(!empty($item['specs']))
                                                <br><span style="font-size: 8pt; color: #555;">Specs: {{ $item['specs'] }}</span>
                                            @endif
                                        </td>
                                        <td style="border-bottom: none; border-top: none; text-align:center;">{{ $item['inv_no'] }}</td>
                                        <td style="border-bottom: none; border-top: none; text-align:center;">{{ $item['est_life'] }}</td>
                                        @if($req->category == 'PPE') 
                                            <td class="text-end" style="border-bottom: none; border-top: none;">{{ number_format((float)($item['total_cost'] ?? 0), 2) }}</td> 
                                        @endif
                                    </tr>
                                    @endforeach
                                    
                                    @php $minRows = $req->category == 'PPE' ? 12 : 15; @endphp
                                    @for($i = count($req->items_json); $i < $minRows; $i++)
                                        <tr style="height: 25px;">
                                            @for($j = 0; $j < ($req->category == 'PPE' ? 6 : 7); $j++) 
                                                <td style="border-top: none; border-bottom: {{ $i == ($minRows - 1) ? '1px solid black' : 'none' }}; vertical-align: bottom;">
                                                    @if($i == ($minRows - 1))
                                                        @if(($req->category == 'PPE' && $j == 2) || ($req->category != 'PPE' && $j == 4))
                                                            @if(!empty($req->po_no))
                                                                <strong>{{ $req->po_type ?? 'P.O. No.' }}/Date: {{ $req->po_no }} / {{ $req->po_date ? \Carbon\Carbon::parse($req->po_date)->format('M d, Y') : '' }}</strong>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </td> 
                                            @endfor
                                        </tr>
                                    @endfor
                                </tbody>
                                
                                <tfoot>
                                    @if($req->category == 'PPE')
                                        <tr>
                                            <td colspan="6" style="padding: 10px; font-size: 8pt; text-align: justify; line-height: 1.4;">
                                                Accountability over Property, Plant and Equipment (PPE). Property Acknowledgment Receipt shall be issued to end-user of Property, Plant and Equipment to establish accountability. Accountability shall be extinguished upon return of the item to the Assets Management Section (AMS) or in case of loss, upon approval of the relief from property accountability.
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        @php
                                            $colLeft = $req->category == 'PPE' ? 3 : 4;
                                            $colRight = $req->category == 'PPE' ? 3 : 3;
                                        @endphp
                                        <td colspan="{{ $colLeft }}" style="border-bottom: none; padding-top: 5px;">{{ $req->category == 'PPE' ? 'Received by:' : 'Received from:' }}</td>
                                        <td colspan="{{ $colRight }}" style="border-bottom: none; padding-top: 5px;">{{ $req->category == 'PPE' ? 'Issued by:' : 'Received by:' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="{{ $colLeft }}" style="border-top: none; text-align: center; padding-bottom: 15px;">
                                            <div style="width: 80%; border-bottom: 1px solid black; margin: 25px auto 5px; font-weight: bold; font-size: 11pt; text-transform: uppercase; min-height:18px;">
                                                {{ $req->sig_received_from_name }}
                                            </div>
                                            <div style="font-size: 8pt;">Signature over Printed Name</div>
                                            
                                            <div style="width: 80%; border-bottom: 1px solid black; margin: 15px auto 5px; min-height:18px;">
                                                {{ $req->sig_received_from_pos }}
                                            </div>
                                            <div style="font-size: 8pt;">Position/Office</div>
                                            
                                            <div style="width: 80%; border-bottom: 1px solid black; margin: 15px auto 5px; min-height:18px;"></div>
                                            <div style="font-size: 8pt;">Date</div>
                                        </td>
                                        <td colspan="{{ $colRight }}" style="border-top: none; text-align: center; padding-bottom: 15px;">
                                            <div style="width: 80%; border-bottom: 1px solid black; margin: 25px auto 5px; font-weight: bold; font-size: 11pt; text-transform: uppercase; min-height:18px;">
                                                {{ $req->sig_received_by_name }}
                                            </div>
                                            <div style="font-size: 8pt;">Signature over Printed Name of Supply/Property Officer</div>
                                            
                                            <div style="width: 80%; border-bottom: 1px solid black; margin: 15px auto 5px; min-height:18px;">
                                                {{ $req->sig_received_by_pos }}
                                            </div>
                                            <div style="font-size: 8pt;">Position/Office</div>
                                            
                                            <div style="width: 80%; border-bottom: 1px solid black; margin: 15px auto 5px; min-height:18px;"></div>
                                            <div style="font-size: 8pt;">Date</div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="viewChoiceModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">View Options: {{ $req->ics_no }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="row g-3">
                            <div class="col-6"><button type="button" class="btn btn-outline-primary w-100 py-3" data-bs-toggle="modal" data-bs-target="#digitalModal{{ $req->id }}" data-bs-dismiss="modal"><i class="fas fa-file-invoice fa-2x d-block mb-2"></i>Digital Form</button></div>
                            <div class="col-6"><button type="button" class="btn btn-outline-success w-100 py-3" {{ $req->signed_document ? '' : 'disabled' }} data-bs-toggle="modal" data-bs-target="#viewDocModal{{ $req->id }}" data-bs-dismiss="modal"><i class="fas fa-file-image fa-2x d-block mb-2"></i>Actual File</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteDocModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form action="{{ url('/ics/'.$req->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-header bg-danger text-white"><h5>Delete Record</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body p-4 text-center">Delete <span class="fw-bold text-danger">{{ $req->ics_no }}</span>?</div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button><button type="submit" class="btn btn-danger">Yes, Delete</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="background-color: #f0f2f5;">
                    <form action="{{ url('/ics/'.$req->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Document: {{ $req->ics_no }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        
                        <div class="modal-body p-4 scrollable-modal-body" style="background-color: #f0f2f5; max-height: 80vh; overflow-y: auto;">
                            
                            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                <h6 class="fw-bold mb-3" style="color: #1a237e; border-bottom: 2px solid #eee; padding-bottom: 10px;"><i class="fa-solid fa-circle-info me-2" style="color: #fbc02d;"></i> General Information</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6"><label class="form-label small fw-bold text-muted">Entity Name</label><input type="text" class="form-control" value="Department of Education ROV" readonly style="background-color: #f8f9fa;"></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold text-muted">Fund Cluster</label><input type="text" name="fund_cluster" class="form-control" value="{{ $req->fund_cluster }}"></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold text-muted">Category</label><input type="text" class="form-control" value="{{ $req->category }}" readonly style="background-color: #f8f9fa;"></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold text-muted">Document Number</label><input type="text" class="form-control fw-bold text-danger" value="{{ $req->ics_no }}" readonly style="background-color: #f8f9fa;"></div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Reference Type</label>
                                        <select name="po_type" class="form-select">
                                            <option value="P.O. No." {{ ($req->po_type == 'P.O. No.') ? 'selected' : '' }}>P.O. No.</option>
                                            <option value="Contract No." {{ ($req->po_type == 'Contract No.') ? 'selected' : '' }}>Contract No.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Reference Number (Optional)</label>
                                        <input type="text" name="po_no" class="form-control" value="{{ $req->po_no }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Reference Date (Optional)</label>
                                        <input type="date" name="po_date" class="form-control" value="{{ $req->po_date }}">
                                    </div>
                                </div>
                            </div>

                            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                <h6 class="fw-bold mb-3" style="color: #1a237e; border-bottom: 2px solid #eee; padding-bottom: 10px;"><i class="fa-solid fa-list-check me-2" style="color: #fbc02d;"></i> Item Details</h6>
                                <div id="edit-items-container-{{ $req->id }}">
                                    @foreach($req->items_json as $item)
                                    <div class="edit-item-row position-relative border-bottom pb-3 mb-3">
                                        <a href="javascript:void(0)" class="text-danger small text-decoration-none position-absolute end-0 top-0" onclick="this.closest('.edit-item-row').remove()"><i class="fa-solid fa-trash-can"></i> Remove</a>
                                        
                                        <div class="row g-3 mb-2 mt-1">
                                            <div class="col-md-1">
                                                <label class="form-label small fw-bold text-muted">Qty<span class="text-danger">*</span></label>
                                                <input type="number" name="qty[]" class="form-control edit-qty" value="{{ $item['qty'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold text-muted">Unit<span class="text-danger">*</span></label>
                                                <select name="unit[]" class="form-select" required>
                                                    <option value="pcs" {{ ($item['unit'] ?? '') == 'pcs' ? 'selected' : '' }}>pcs</option>
                                                    <option value="boxes" {{ ($item['unit'] ?? '') == 'boxes' ? 'selected' : '' }}>boxes</option>
                                                    <option value="kg" {{ ($item['unit'] ?? '') == 'kg' ? 'selected' : '' }}>kg</option>
                                                    <option value="unit" {{ ($item['unit'] ?? '') == 'unit' ? 'selected' : '' }}>unit</option>
                                                    <option value="set" {{ ($item['unit'] ?? '') == 'set' ? 'selected' : '' }}>set</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold text-muted">Article<span class="text-danger">*</span></label>
                                                <input type="text" name="article[]" class="form-control" value="{{ $item['article'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Description<span class="text-danger">*</span></label>
                                                <input type="text" name="desc[]" class="form-control" value="{{ $item['desc'] ?? '' }}" required>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-2">
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-muted">Specifications (Optional)</label>
                                                <input type="text" name="specs[]" class="form-control" value="{{ $item['specs'] ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-4"><label class="form-label small fw-bold text-muted">{{ $req->category == 'PPE' ? 'Property Number' : 'Inventory Item No.' }}</label><input type="text" name="inv_no[]" class="form-control" value="{{ $item['inv_no'] ?? '' }}"></div>
                                            <div class="col-md-4"><label class="form-label small fw-bold text-muted">{{ $req->category == 'PPE' ? 'Date Acquired' : 'Estimated Useful Life' }}</label><input type="{{ $req->category == 'PPE' ? 'date' : 'text' }}" name="est_life[]" class="form-control" value="{{ $item['est_life'] ?? '' }}"></div>
                                            <div class="col-md-2" style="{{ $req->category == 'PPE' ? 'display:none;' : '' }}"><label class="form-label small fw-bold text-muted">Unit Cost<span class="text-danger">*</span></label><input type="number" step="0.01" name="unit_cost[]" class="form-control edit-unit-cost" value="{{ $item['unit_cost'] ?? '' }}" {{ $req->category != 'PPE' ? 'required' : '' }}></div>
                                            <div class="col-md-{{ $req->category == 'PPE' ? '4' : '2' }}"><label class="form-label small fw-bold text-muted">{{ $req->category == 'PPE' ? 'Amount' : 'Total Cost' }}</label><input type="number" step="0.01" name="total_cost[]" class="form-control edit-total-cost" value="{{ $item['total_cost'] ?? '' }}" {{ $req->category != 'PPE' ? 'readonly style=background-color:#f8f9fa;' : '' }}></div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addEditItemRow({{ $req->id }}, '{{ $req->category }}')"><i class="fa-solid fa-plus me-1"></i> Add Another Item</button>
                            </div>

                            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                <h6 class="fw-bold mb-3" style="color: #1a237e; border-bottom: 2px solid #eee; padding-bottom: 10px;"><i class="fa-solid fa-file-signature me-2" style="color: #fbc02d;"></i> Signatures</h6>
                                <div class="row text-center g-4 mt-2">
                                    <div class="col-md-6 border-end">
                                        <label class="d-block mb-3 text-uppercase small text-muted">{{ $req->category == 'PPE' ? 'Received By' : 'Received From' }}</label>
                                        <input type="text" name="sig_from_name" class="form-control text-center border-0 border-bottom border-dark rounded-0 fw-bold mx-auto w-75 shadow-none" value="{{ $req->sig_received_from_name }}" placeholder="Printed Name">
                                        <input type="text" name="sig_from_pos" class="form-control text-center border-0 border-bottom border-secondary border-opacity-50 rounded-0 small text-muted mx-auto w-50 mt-2 shadow-none" value="{{ $req->sig_received_from_pos }}" placeholder="Position / Title">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="d-block mb-3 text-uppercase small text-muted">{{ $req->category == 'PPE' ? 'Issued By' : 'Received By' }}</label>
                                        <input type="text" name="sig_by_name" class="form-control text-center border-0 border-bottom border-dark rounded-0 fw-bold mx-auto w-75 shadow-none" value="{{ $req->sig_received_by_name }}" placeholder="Printed Name">
                                        <input type="text" name="sig_by_pos" class="form-control text-center border-0 border-bottom border-secondary border-opacity-50 rounded-0 small text-muted mx-auto w-50 mt-2 shadow-none" value="{{ $req->sig_received_by_pos }}" placeholder="Position / Title">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-white border-top">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success text-white px-4 fw-bold"><i class="fas fa-save me-1"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uploadDocModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form action="{{ url('/ics/'.$req->id.'/upload-signed') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Actual Signed File</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <input type="file" name="signed_document" class="form-control shadow-sm" accept="image/*" required>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Upload Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($req->signed_document)
        <div class="modal fade" id="viewDocModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Actual File: {{ $req->ics_no }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-0 bg-secondary" style="max-height: 75vh; overflow-y: auto;">
                        <img src="{{ asset('storage/ics_signed/'.$req->signed_document) }}" class="img-fluid">
                    </div>
                    <div class="modal-footer d-flex justify-content-between bg-white border-top">
                        <button type="button" class="btn btn-secondary px-4" data-bs-toggle="modal" data-bs-target="#viewChoiceModal{{ $req->id }}">Back</button>
                        <button type="button" class="btn btn-warning px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal{{ $req->id }}">
                            <i class="fas fa-file-upload me-1"></i> Change File
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="modal fade" id="itemsModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold"><i class="fas fa-tags me-2"></i> Document Items Overview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Property No.</th>
                                    <th>Description</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($req->items_json as $index => $item)
                                @php
                                    $transferStatus = $item['transfer_status'] ?? 'Active';
                                    $isTransferred = str_contains($transferStatus, 'Transferred') || $transferStatus === 'Returned to Inventory';
                                @endphp
                                <tr>
                                    <td class="font-monospace fw-bold text-primary ps-4">{{ $item['inv_no'] ?? 'N/A' }}</td>
                                    <td>
                                        @if(!empty($item['article'])) <strong>{{ $item['article'] }}</strong><br> @endif
                                        {{ $item['desc'] ?? 'Unknown Item' }}
                                        @if(!empty($item['specs'])) <br><small class="text-muted">{{ $item['specs'] }}</small> @endif
                                    </td>
                                    <td class="text-center">
                                        @if($isTransferred)
                                            <span class="badge bg-secondary px-2 py-1"><i class="fas fa-exchange-alt me-1"></i> {{ $transferStatus }}</span>
                                        @else
                                            <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Available/Active</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-primary fw-bold px-2" data-bs-toggle="modal" data-bs-target="#stickerModal{{ $req->id }}_{{ $index }}" title="Print 3x4 Sticker">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            @if(!$isTransferred && !empty($item['inv_no']))
                                                <button type="button" class="btn btn-sm btn-dark fw-bold px-2" data-bs-toggle="modal" data-bs-target="#transferModal{{ $req->id }}_{{ $index }}" title="Transfer Asset to New Person">
                                                    <i class="fas fa-people-arrows"></i> Transfer
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @foreach($req->items_json as $index => $item)
        @if(($item['transfer_status'] ?? 'Active') === 'Active' && !empty($item['inv_no']))
        <div class="modal fade" id="transferModal{{ $req->id }}_{{ $index }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form action="{{ url('/ics/'.$req->id.'/transfer/'.$index) }}" method="POST">
                        @csrf
                        <div class="modal-header" style="background-color: #101954; color: white;">
                            <h5 class="modal-title fw-bold"><i class="fas fa-people-arrows me-2"></i> Transfer Asset</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-light border border-warning shadow-sm mb-4">
                                <h6 class="fw-bold text-dark mb-1">{{ $item['article'] ?? '' }} - {{ $item['desc'] ?? 'Unknown Item' }}</h6>
                                <div class="font-monospace text-primary small">{{ $item['inv_no'] ?? 'N/A' }}</div>
                            </div>
                            
                            @php
                                $currentHolder = $req->category == 'PPE' 
                                    ? ($req->sig_received_from_name ?: 'the current holder') 
                                    : ($req->sig_received_by_name ?: 'the current holder');
                            @endphp

                            <p class="text-muted small mb-4">
                                Transferring this asset will permanently shift accountability from 
                                <b class="text-dark">{{ $currentHolder }}</b> 
                                to a new person by generating a brand new document.
                            </p>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">New Accountable Person <span class="text-danger">*</span></label>
                                <input type="text" name="new_accountable_person" class="form-control border-dark" required placeholder="Enter full name of receiver">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">New Position / Title <span class="text-danger">*</span></label>
                                <input type="text" name="new_position" class="form-control" required placeholder="e.g. Teacher III">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Date of Transfer <span class="text-danger">*</span></label>
                                <input type="date" name="transfer_date" class="form-control bg-light" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-secondary px-4" data-bs-toggle="modal" data-bs-target="#itemsModal{{ $req->id }}">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4" style="background-color: #101954;"><i class="fas fa-check-circle me-2"></i> Confirm Transfer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="modal fade" id="stickerModal{{ $req->id }}_{{ $index }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="width: max-content;">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Sticker Preview</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-light d-flex justify-content-center">
                        <div id="sticker_node_{{ $req->id }}_{{ $index }}" class="sticker-container-preview">
                            <div class="sticker-inner">
                                <div class="sticker-header">
                                    <img src="{{ asset('assets/images/DepEdseal.png') }}" alt="Seal">
                                    <div class="rp-text">Republic of the Philippines</div>
                                    <div class="deped-text">Department of Education</div>
                                    <div class="region-text">REGION V - BICOL</div>
                                </div>
                                <table class="sticker-table">
                                    <tr><td colspan="2"><div class="lbl">Date of Inventory:</div><input type="text" class="no-border-input sync-input req-field" data-name="Date of Inventory" value="" placeholder="Type Date..."></td></tr>
                                    <tr><td colspan="2"><div class="lbl">Description of Property Plant & Equipment(PPE):</div><input type="text" class="no-border-input sync-input req-field" data-name="Description" value="{{ $item['article'] ?? '' }} - {{ $item['desc'] ?? '' }}"></td></tr>
                                    <tr><td colspan="2"><div class="lbl">Property No:</div><input type="text" class="no-border-input sync-input req-field" data-name="Property No" value="{{ $item['inv_no'] ?? '' }}"></td></tr>
                                    <tr><td style="width: 50%;"><div class="lbl">Model:</div><input type="text" class="no-border-input sync-input req-field" data-name="Model" placeholder="Type Model..."></td><td style="width: 50%;"><div class="lbl">Serial Number:</div><input type="text" class="no-border-input sync-input req-field" data-name="Serial Number" placeholder="Type S/N..."></td></tr>
                                    <tr><td><div class="lbl">Acquisition Date:</div><input type="text" class="no-border-input sync-input req-field" data-name="Acquisition Date" value="{{ $item['est_life'] ?? '' }}"></td><td><div class="lbl">Unit Cost:</div><input type="text" class="no-border-input sync-input req-field" data-name="Unit Cost" value="₱{{ number_format((float)($item['unit_cost'] ?? 0), 2) }}"></td></tr>
                                    <tr><td colspan="2"><div class="lbl">Person Accountable:</div><input type="text" class="no-border-input sync-input" value="{{ $req->category == 'PPE' ? ($req->sig_received_from_name ?? '') : ($req->sig_received_by_name ?? '') }}"></td></tr>
                                    <tr>
                                        <td colspan="2" style="padding-bottom: 2px;">
                                            <div class="lbl">Validations/Signatory of Inventory Committees:</div>
                                            <div class="val-list">
                                                1. <input type="text" class="no-border-input sync-input" style="width: 85%;"><br>
                                                2. <input type="text" class="no-border-input sync-input" style="width: 85%;"><br>
                                                3. <input type="text" class="no-border-input sync-input" style="width: 85%;">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-toggle="modal" data-bs-target="#itemsModal{{ $req->id }}">Back</button>
                        <button type="button" class="btn btn-primary px-4 fw-bold" onclick="printSticker('sticker_node_{{ $req->id }}_{{ $index }}')">Print 3x4 Sticker</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endforeach

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $("#historyTable tbody tr").each(function() {
                    if ($(this).find('td').length > 1) {
                        var docNo = $(this).find('td:eq(0)').text().toLowerCase();
                        var person = $(this).find('td:eq(2)').text().toLowerCase();
                        $(this).toggle(docNo.indexOf(value) > -1 || person.indexOf(value) > -1);
                    }
                });
            });

            if($('#searchInput').val()) {
                $('#searchInput').trigger('keyup');
            }
        });

        $(document).on('show.bs.modal', '.modal', function () {
            const zIndex = 1050 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
        });

        function addEditItemRow(reqId, category) {
            const container = document.getElementById('edit-items-container-' + reqId);
            const isPAR = category === 'PPE';
            
            let unitCostHtml = isPAR ? 
                `<div class="col-md-2" style="display:none;"><input type="number" step="0.01" name="unit_cost[]" class="form-control edit-unit-cost"></div>` :
                `<div class="col-md-2"><label class="form-label small fw-bold text-muted">Unit Cost<span class="text-danger">*</span></label><input type="number" step="0.01" name="unit_cost[]" class="form-control edit-unit-cost" required></div>`;
                
            let totalCostHtml = isPAR ?
                `<div class="col-md-4"><label class="form-label small fw-bold text-muted">Amount</label><input type="number" step="0.01" name="total_cost[]" class="form-control edit-total-cost"></div>` :
                `<div class="col-md-2"><label class="form-label small fw-bold text-muted">Total Cost</label><input type="number" step="0.01" name="total_cost[]" class="form-control edit-total-cost" readonly style="background-color:#f8f9fa;"></div>`;

            let invLabel = isPAR ? 'Property Number' : 'Inventory Item No.';
            let dateLabel = isPAR ? 'Date Acquired' : 'Estimated Useful Life';
            let dateInputType = isPAR ? 'date' : 'text';

            const rowHtml = `
                <div class="edit-item-row position-relative border-bottom pb-3 mb-3">
                    <a href="javascript:void(0)" class="text-danger small text-decoration-none position-absolute end-0 top-0" onclick="this.closest('.edit-item-row').remove()">
                        <i class="fa-solid fa-trash-can"></i> Remove
                    </a>
                    
                    <div class="row g-3 mb-2 mt-1">
                        <div class="col-md-1">
                            <label class="form-label small fw-bold text-muted">Qty<span class="text-danger">*</span></label>
                            <input type="number" name="qty[]" class="form-control edit-qty" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Unit<span class="text-danger">*</span></label>
                            <select name="unit[]" class="form-select" required>
                                <option value="">- Select -</option>
                                <option value="pcs">pcs</option>
                                <option value="boxes">boxes</option>
                                <option value="kg">kg</option>
                                <option value="unit">unit</option>
                                <option value="set">set</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Article<span class="text-danger">*</span></label>
                            <input type="text" name="article[]" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Description<span class="text-danger">*</span></label>
                            <input type="text" name="desc[]" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">Specifications (Optional)</label>
                            <input type="text" name="specs[]" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">${invLabel}</label>
                            <input type="text" name="inv_no[]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">${dateLabel}</label>
                            <input type="${dateInputType}" name="est_life[]" class="form-control">
                        </div>
                        ${unitCostHtml}
                        ${totalCostHtml}
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHtml);
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('edit-qty') || e.target.classList.contains('edit-unit-cost')) {
                const row = e.target.closest('.edit-item-row');
                const qtyInput = row.querySelector('.edit-qty');
                const costInput = row.querySelector('.edit-unit-cost');
                const totalInput = row.querySelector('.edit-total-cost');
                
                if (totalInput && totalInput.hasAttribute('readonly')) {
                    const qty = parseFloat(qtyInput.value) || 0;
                    const cost = parseFloat(costInput.value) || 0;
                    if (qty > 0 && cost > 0) {
                        totalInput.value = (qty * cost).toFixed(2);
                    } else {
                        totalInput.value = '';
                    }
                }
            }
        });

        function printSticker(elementId) {
            const container = document.getElementById(elementId);
            let isValid = true;
            let missingFields = [];
            
            container.querySelectorAll('.req-field').forEach(input => {
                let val = input.value.replace(/[₱,]/g, '').trim(); 
                if (!val) {
                    isValid = false;
                    missingFields.push(input.getAttribute('data-name'));
                    input.classList.add('req-field-empty');
                } else {
                    input.classList.remove('req-field-empty');
                }
            });

            if (!isValid) {
                Swal.fire({ icon: 'warning', title: 'Fields Missing', text: 'Fill in: ' + missingFields.join(', '), confirmButtonColor: '#101954' });
                return;
            }
            
            container.querySelectorAll('.sync-input').forEach(input => { input.setAttribute('value', input.value); });
            const content = container.innerHTML;
            let win = window.open('', '', 'width=400,height=500');
            win.document.write(`
                <html><head><title>Print</title><style>
                @page { margin: 0; }
                body { margin: 0; padding: 0; background: white; font-family: Arial, sans-serif; text-align: left; }
                .sticker-container-preview { width: 3in; height: 4in; padding: 4px; border: 3px solid #101954; box-sizing: border-box; position: absolute; top: 0; left: 0; margin: 0; display: block; overflow: hidden; }
                .sticker-inner { border: 1px solid #101954; width: 100%; height: 100%; box-sizing: border-box; display: flex; flex-direction: column; padding: 2px; }
                .sticker-header { text-align: center; padding-top: 1px; }
                .sticker-header img { width: 30px; height: 30px; }
                .rp-text { font-size: 6.5pt; color: black;}
                .deped-text { font-family: 'Old English Text MT', serif; font-size: 11pt; font-weight: bold; line-height: 1; color: black; margin: 1px 0;}
                .region-text { font-size: 7pt; text-transform: uppercase; color: black;}
                .sticker-table { width: 100%; border-collapse: collapse; font-size: 7pt; flex-grow: 1; margin-top: 2px; }
                .sticker-table td { border: 1px solid #101954; padding: 2px 4px; vertical-align: middle; color: black;}
                .lbl { font-size: 6.5pt; color: #333; margin-bottom: 0; line-height: 1; }
                .no-border-input { border: none; width: 100%; font-family: Arial, sans-serif; font-size: 8pt; font-weight: bold; background: transparent; outline: none; padding: 0; margin: 0; color: black; height: 14px; }
                .val-list { font-size: 7pt; line-height: 1.2; margin-top: 2px; }
                .val-list input { font-weight: normal; font-size: 7.5pt; height: 12px; }
                </style></head><body><div class="sticker-container-preview">${content}</div><script>setTimeout(function(){ window.print(); window.close(); }, 500);<\/script></body></html>`);
            win.document.close();
        }
    </script>
</body>
</html>