<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd ROV - Requisition and Issue Slip</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --deped-blue: #1a237e;
            --deped-gold: #fbc02d;
            --light-bg: #f8f9fa;
            --border-color: #e0e0e0;
        }

        body { 
            background-color: #f0f2f5; 
            font-family: 'Inter', sans-serif; 
            color: #444; 
            margin: 0;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

        .top-bar { 
            background: linear-gradient(90deg, var(--deped-blue) 0%, #283593 100%);
            color: white; 
            padding: 12px 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .section-box { 
            border: none;
            padding: 25px; 
            margin-bottom: 25px; 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            position: relative;
        }

        .section-title { 
            color: var(--deped-blue); 
            font-weight: 700; 
            margin-bottom: 20px; 
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            display: flex;
            align-items: center;
            font-size: 1rem;
        }
        
        .section-title i { margin-right: 10px; color: var(--deped-gold); }

        label { 
            font-weight: 600; 
            font-size: 0.85rem; 
            color: #555; 
            margin-bottom: 6px;
        }

        .form-control, .form-select { 
            border-radius: 8px; 
            border: 1px solid #ced4da;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .btn-submit { background-color: var(--deped-blue); color: white; font-weight: 600; }

        .sig-line {
            border: none;
            border-bottom: 2px solid #333;
            border-radius: 0;
            font-weight: bold;
            background: transparent;
        }

        .desig-input {
            border: none;
            border-bottom: 1px dashed #aaa;
            border-radius: 0;
            background: transparent;
            text-align: center;
            font-size: 0.85rem;
            color: #666;
            padding: 2px;
            width: 80%;
            margin: 0 auto;
        }

        .item-row {
            position: relative;
            padding-top: 10px;
        }

        .btn-remove-row {
            color: #dc3545;
            cursor: pointer;
            font-size: 0.8rem;
            text-decoration: none;
            float: right;
            margin-top: -5px;
            padding-top: 10px;
        }

        .btn-remove-row:hover { text-decoration: underline; }

        .select2-container--bootstrap-5 .select2-selection--single {
            border-radius: 8px !important;
            min-height: 39px !important;
            padding: 4px 0px !important;
            border-color: #ced4da !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 12px !important;
            padding-top: 2px !important;
            color: #444 !important;
        }
        .select2-container--bootstrap-5 .select2-selection { box-shadow: none !important; }
        
        .select2-results__option {
            padding: 8px 12px !important;
            border-bottom: 1px solid #f1f1f1;
        }

        /* Fix Select2 dropdown cutting off or losing focus behind Modals */
        .select2-container { z-index: 9999 !important; }

        /* Modal Stack Fix */
        .modal { z-index: 1060 !important; }
        .modal-backdrop { z-index: 1055 !important; }

        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    @include('layouts.user_header')
    @include('layouts.user_sidebar')

<div class="main-content">
    <div class="top-bar">
        <div><i class="fa-solid fa-building-shield me-2"></i> <strong>ASSET MANAGEMENT/SUPPLY SECTION SYSTEM</strong></div>
        <div id="clock"><i class="fa-regular fa-clock me-2"></i> Loading time...</div>
    </div>

    <form action="{{ url('/user/ris') }}" id="requisitionForm" method="POST">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--deped-blue);">REQUISITION AND ISSUE SLIP</h3>
                <p class="text-muted small">RIS before Release!</p>
            </div>
            <div>
                <button type="button" class="btn btn-submit shadow-sm" onclick="showConfirmModal()"><i class="fa-solid fa-paper-plane me-1"></i> Submit Request</button>
            </div>
        </div>

        <div class="section-box">
            <h6 class="section-title"><i class="fa-solid fa-circle-info"></i> General Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Entity Name</label>
                        <input type="text" name="entity_name" id="entity_name" class="form-control bg-light" value="Department of Education - ROV" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Office Name  <span class="text-danger">*</span></label>
                        <select name="office" id="officeSelect" class="form-select" onchange="updateUnits()" required>
                            <option value="">-- Select Office --</option>
                            <option value="Administrative Division">Administrative Division</option>
                            <option value="Curriculum and Learning Management Division">Curriculum and Learning Management Division</option>
                            <option value="Education Support Services Division">Education Support Services Division</option>
                            <option value="Field Technical Assistance Division">Field Technical Assistance Division</option>
                            <option value="Finance Division">Finance Division</option>
                            <option value="Human Resource Development Division">Human Resource Development Division</option>
                            <option value="Office of the Assistant Regional Director">Office of the Assistant Regional Director</option>
                            <option value="Office of the Regional Director">Office of the Regional Director</option>
                            <option value="Policy Planning and Research Division">Policy Planning and Research Division</option>
                            <option value="Quality Assurance Division">Quality Assurance Division</option>
                        </select>
                    </div>
                    <div>
                        <label>Unit / Section</label>
                        <select name="unit_section" id="unitSelect" class="form-select">
                            <option value="">-- Select Office First --</option>
                        </select>
                    </div>
                </div>
    
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Fund Cluster</label>
                        <input type="text" name="fund_cluster" id="fund_cluster" class="form-control" readonly placeholder="Leave it blank">
                    </div>
                    <div class="mb-3">
                        <label>Responsible Center Code</label>
                        <input type="text" name="center_code" id="center_code" class="form-control" readonly placeholder="Leave it blank">
                    </div>
                    <div>
                        <label>RIS Number</label>
                        <input type="text" name="ris_no" id="ris_no" class="form-control fw-bold text-danger bg-light" value="{{ $risNumber }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-box requisition-block">
            <h6 class="section-title"><i class="fa-solid fa-list-check"></i> Requisition Details</h6>
            
            <div id="items-container">
                <div class="row g-3 mb-4 item-row border-bottom pb-3">
                    <div class="col-md-12 text-end">
                        <a href="javascript:void(0)" class="btn-remove-row" onclick="removeRow(this)"><i class="fa-solid fa-trash-can"></i> Remove Item</a>
                    </div>
                    <div class="col-md-2">
                        <label>Stock No.</label>
                        <input type="text" name="stock_no[]" class="form-control bg-light stock-input" readonly placeholder="Auto-filled">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit Measure <span class="text-danger">*</span></label>
                        <input type="text" name="unit_measure[]" class="form-control bg-light unit-input" readonly placeholder="Auto-filled" required>
                    </div>
                    <div class="col-md-2">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity[]" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label>Item Description <span class="text-danger">*</span></label>
                        <select name="description[]" class="form-select select2-supply" required>
                            <option value="" selected disabled>-- Select Supply Item --</option>
                            <option value="Others" class="fw-bold text-primary">Others (Please specify)</option>
                            @foreach($supplies as $supply)
                                <option value="{{ $supply->article }}, {{ $supply->description }}" data-barcode="{{ $supply->barcode_id }}" data-qty="{{ $supply->quantity }}" data-unit="{{ $supply->unit_measure }}">{{ $supply->article }} - {{ $supply->description }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="manual_description[]" class="form-control mt-2 manual-desc-input shadow-sm border-primary" style="display: none;" placeholder="Specify custom item name and description">
                    </div>
                    <div class="col-md-12">
                        <label>Remarks</label>
                        <input type="text" name="remarks[]" class="form-control" readonly placeholder="Leave it blank">
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem()">
                    <i class="fa-solid fa-plus me-1"></i> Add Item Row
                </button>
            </div>
        </div>
        <div class="section-box purpose-block">
            <div class="col-md-13">
                <label>Purpose <span class="text-danger">*</span></label>
                <textarea name="purpose[]" class="form-control" rows="1" placeholder="Enter your purpose..." required></textarea>
            </div>
        </div>

        <div class="section-box">
            <h6 class="section-title"><i class="fa-solid fa-file-signature"></i> Signatures</h6>
            <div class="row text-center g-4">
                <div class="col-md-3">
                    <label class="d-block mb-3 text-uppercase small text-muted">Requested By <span class="text-danger">*</span></label>
                    <input type="text" name="requested_by" id="req_by" class="form-control sig-line text-center" placeholder="Printed Name" required>
                    <input type="text" name="desig_requested" id="desig_req" class="form-control desig-input mt-2" placeholder="Enter Designation" required>
                </div>
                <div class="col-md-3">
                    <label class="d-block mb-3 text-uppercase small text-muted">Approved By</label>
                    <input type="text" name="approved_by" id="app_by" class="form-control sig-line text-center" value="JEFFREY B. PAGATPAT" readonly>
                    <input type="text" name="desig_approved" id="desig_app" class="form-control desig-input mt-2" value="Admin, Officer V (Supply Officer)" readonly>
                </div>
                <div class="col-md-3">
                    <label class="d-block mb-3 text-uppercase small text-muted">Issued By</label>
                    <input type="text" name="issued_by" id="iss_by" class="form-control sig-line text-center" value="ALDRIN RELLAMA" readonly>
                    <input type="text" name="desig_issued" id="desig_iss" class="form-control desig-input mt-2" value="AA-VI (Storekeeper II)" readonly>
                </div>
                <div class="col-md-3">
                    <label class="d-block mb-3 text-uppercase small text-muted">Received By <span class="text-danger">*</span></label>
                    <input type="text" name="received_by" id="rec_by" class="form-control sig-line text-center" placeholder="Printed Name" required>
                    <input type="text" name="desig_received" id="desig_rec" class="form-control desig-input mt-2" placeholder="Enter Designation" required>
                </div>
            </div>
        </div>
    </form>
</div>

<button type="button" id="hiddenSubmitTrigger" class="d-none" data-bs-toggle="modal" data-bs-target="#submitConfirmModal"></button>

<div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: var(--deped-blue);">
                <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Confirm Submission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-circle-question text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">Submit Requisition Request?</h5>
                <p class="text-muted mb-0">Are you sure all the details and items in this RIS form are correct? Once submitted, it will be forwarded for staff review.</p>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center py-3">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Review Again</button>
                <button type="button" class="btn px-4 fw-bold text-white" style="background-color: var(--deped-blue);" id="confirmSubmitBtn" onclick="executeSubmit()">Yes, Submit Now</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // --- Safe Modal Trigger Logic ---
    function showConfirmModal() {
        const form = document.getElementById('requisitionForm');
        // Validate form manually
        if (form.checkValidity()) {
            // If valid, click the hidden native bootstrap toggle to prevent backdrop bugs
            document.getElementById('hiddenSubmitTrigger').click();
        } else {
            // Show standard HTML5 validation messages
            form.reportValidity(); 
        }
    }

    function executeSubmit() {
        const btn = document.getElementById('confirmSubmitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
        btn.disabled = true;
        // Submit the form
        document.getElementById('requisitionForm').submit();
    }


    function formatSupplyOption(state) {
        if (!state.id) { return state.text; }
        
        if (state.id === 'Others') {
            return $(`<span class="text-primary fw-bold"><i class="fas fa-pen me-2"></i>${state.text}</span>`);
        }
        
        let qty = $(state.element).data('qty');
        let badgeHtml = '';
        
        if (qty !== undefined) {
            if (parseInt(qty) > 0) {
                badgeHtml = `<span class="badge bg-success ms-2 py-1" style="font-size:0.7rem;">Available</span>`;
            } else {
                badgeHtml = `<span class="badge bg-danger ms-2 py-1" style="font-size:0.7rem;">Out of Stock</span>`;
            }
        }
        
        return $(`<span>${state.text} ${badgeHtml}</span>`);
    }

    function initSelect2Fields() {
        $('.select2-supply:not(.select2-hidden-accessible)').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Select Supply Item --',
            templateResult: formatSupplyOption, 
            templateSelection: formatSupplyOption, 
            escapeMarkup: function(m) { return m; } 
        });

        // Use event delegation so dynamic rows automatically inherit the event listener
        $('#items-container').on('select2:select', '.select2-supply', function (e) {
            const selectedVal = $(this).val();
            const row = $(this).closest('.item-row');
            const manualInput = row.find('.manual-desc-input');
            const unitInput = row.find('.unit-input');
            const stockInput = row.find('.stock-input');
            
            if (selectedVal === 'Others') {
                manualInput.show().attr('required', true);
                stockInput.val('');
                unitInput.val('').removeAttr('readonly').attr('placeholder', 'Type unit manually').removeClass('bg-light');
            } else {
                manualInput.hide().attr('required', false).val('');
                unitInput.attr('readonly', true).attr('placeholder', 'Auto-filled').addClass('bg-light');
                
                const selectedOption = $(this).select2('data')[0].element; 
                const barcode = $(selectedOption).data('barcode'); 
                const unit = $(selectedOption).data('unit'); 
                
                stockInput.val(barcode || '');
                unitInput.val(unit || '');
            }
        });
    }

    $(document).ready(function() {
        initSelect2Fields();
    });

    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const clockEl = document.getElementById('clock');
        if(clockEl) clockEl.innerHTML = '<i class="fa-regular fa-calendar-check me-2"></i> ' + now.toLocaleDateString('en-US', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    const officeMapping = {
        "Administrative Division": ["Asset Management Section", "General Services Unit", "Payroll Services Unit", "Records Section", "Personnel Section", "Cash Section"],
        "Curriculum and Learning Management Division": ["Learning Resource Management Section"],
        "Education Support Services Division": ["Health and Nutrition", "Programs and Projects", "Facilities"],
        "Finance Division": ["Budget Section", "Accounting Section"],
        "Human Resource Development Division": ["NEAP"],
        "Office of the Regional Director": ["Procurement Unit", "Information and Communications Technology Unit", "Public Affairs Unit", "Legal Unit"]
    };

    function updateUnits() {
        const officeSelect = document.getElementById("officeSelect");
        const unitSelect = document.getElementById("unitSelect");
        const selectedOffice = officeSelect.value;
        unitSelect.innerHTML = '<option value="">-- Select Unit/Section --</option>';

        if (selectedOffice && officeMapping[selectedOffice]) {
            officeMapping[selectedOffice].forEach(unit => {
                const option = document.createElement("option");
                option.value = unit;
                option.textContent = unit;
                unitSelect.appendChild(option);
            });
        } else {
            unitSelect.innerHTML = '<option value="N/A">General Office Use</option>';
        }
    }

    function addItem() {
        const container = document.getElementById('items-container');
        const firstRow = container.querySelector('.item-row');
        
        // Destroy select2 on the original row temporarily to clone it cleanly
        $(firstRow).find('.select2-supply').select2('destroy');
        
        const newRow = firstRow.cloneNode(true);
        
        // Reset dynamic fields in the cloned row
        newRow.querySelectorAll('input, textarea').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });
        
        const manualDesc = newRow.querySelector('.manual-desc-input');
        if(manualDesc) {
            manualDesc.style.display = 'none';
            manualDesc.required = false;
        }
        
        const unitInp = newRow.querySelector('.unit-input');
        if(unitInp) {
            unitInp.readOnly = true;
            unitInp.classList.add('bg-light');
            unitInp.placeholder = 'Auto-filled';
        }
        
        container.appendChild(newRow);
        
        // Re-initialize select2 on ALL select fields (both original and new)
        initSelect2Fields();
    }

    function removeRow(link) {
        const container = document.getElementById('items-container');
        const rows = container.querySelectorAll('.item-row');
        if (rows.length > 1) {
            // Must destroy select2 instance before removing from DOM to prevent memory leaks
            $(link).closest('.item-row').find('.select2-supply').select2('destroy');
            link.closest('.item-row').remove();
        } else {
            alert("The form must have at least one item.");
        }
    }
</script>
</body>
</html>