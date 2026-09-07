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

        /* --- Compact Items Entry Table --- */
        .items-entry-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            min-width: 640px;
        }
        .items-entry-table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            font-weight: 700;
            padding: 4px 8px;
            border-bottom: 2px solid #e0e0e0;
            white-space: nowrap;
        }
        .items-entry-table td {
            padding: 2px 4px;
            vertical-align: middle;
        }
        .items-entry-table .form-control-sm { min-height: 34px; font-size: 0.85rem; }
        .items-entry-table .select2-container--bootstrap-5 .select2-selection--single {
            min-height: 34px !important;
            padding: 2px 0px !important;
            border-radius: 6px !important;
            font-size: 0.85rem;
        }
        .items-entry-table .btn-remove-row {
            float: none;
            font-size: 1.15rem;
            padding: 0;
            margin: 0;
            color: #dc3545;
        }
        .items-entry-table .btn-remove-row:hover { color: #a71d2a; }

        @media (max-width: 992px) { .main-content { margin-left: 0; } }

        /* Print Styles */
        #print-area { display: none; }

        @media print {
            @page { size: A4 portrait; margin: 10mm; }

            body * { visibility: hidden; }
            .sidebar, .main-content { display: none !important; margin: 0 !important; padding: 0 !important; }

            #print-area, #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                color: #000;
                display: block;
                font-family: 'Times New Roman', Times, serif;
                font-size: 10pt;
            }

            #print-area table {
                display: table !important;
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
            }
            #print-area thead { display: table-header-group !important; }
            #print-area tbody { display: table-row-group !important; }
            #print-area tr { display: table-row !important; page-break-inside: avoid; }
            #print-area th, #print-area td {
                display: table-cell !important;
                float: none !important;
            }
        }
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

    <form id="requisitionForm" onsubmit="return false;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--deped-blue);">REQUISITION AND ISSUE SLIP</h3>
                <p class="text-muted small">Fill out the form, then download/print to submit to AMS staff.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary shadow-sm" onclick="resetForm()"><i class="fa-solid fa-rotate-left me-1"></i> Reset</button>
                <button type="button" class="btn btn-submit shadow-sm" onclick="showDownloadModal()"><i class="fa-solid fa-download me-1"></i> Download / Print RIS</button>
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
                        <label>Division Name <span class="text-danger">*</span></label>
                        <select name="division" id="officeSelect" class="form-select" onchange="updateUnits()" required>
                            <option value="">-- Select Division --</option>
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
                        <label>Office Name / Unit / Section</label>
                        <select name="unit_section" id="unitSelect" class="form-select">
                            <option value="">-- Select Division First --</option>
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
                        <input type="text" id="ris_no" class="form-control bg-light text-muted" value="" readonly placeholder="Assigned by AMS upon receipt">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Blank for now — AMS staff assigns the RIS No. when your form is received.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-box requisition-block">
            <h6 class="section-title"><i class="fa-solid fa-list-check"></i> Requisition Details</h6>

            <div class="items-table-wrapper table-responsive">
                <table class="items-entry-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Stock No.</th>
                            <th style="width: 10%;">Unit</th>
                            <th style="width: 47%;">Item Description</th>
                            <th style="width: 14%;">Qty</th>
                            <th style="width: 8%;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        <tr class="item-row">
                            <td><input type="text" name="stock_no[]" class="form-control form-control-sm bg-light stock-input" readonly placeholder="Auto"></td>
                            <td><input type="text" name="unit_measure[]" class="form-control form-control-sm bg-light unit-input" readonly placeholder="Auto" required></td>
                            <td>
                                <select name="description[]" class="form-select form-select-sm select2-supply" required>
                                    <option value="" selected disabled>-- Search item... --</option>
                                    <option value="Others" class="fw-bold text-primary">Others (Please specify)</option>
                                    @foreach($supplies as $supply)
                                        <option value="{{ $supply->article }}, {{ $supply->description }}" data-barcode="{{ $supply->barcode_id }}" data-qty="{{ $supply->quantity }}" data-unit="{{ $supply->unit_measure }}">{{ $supply->article }} - {{ $supply->description }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="manual_description[]" class="form-control form-control-sm manual-desc-input mt-1 border-primary" style="display: none;" placeholder="Specify item name & description">
                            </td>
                            <td><input type="number" name="quantity[]" class="form-control form-control-sm text-center qty-input" min="1" required></td>
                            <td class="text-center align-middle">
                                <a href="javascript:void(0)" class="btn-remove-row" onclick="removeRow(this)" title="Remove item"><i class="fa-solid fa-circle-xmark"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem()">
                    <i class="fa-solid fa-plus me-1"></i> Add Item
                </button>
                <small class="text-muted"><i class="fas fa-box-open me-1"></i> Live stock counts are shown in the item dropdown.</small>
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
                    <label class="d-block mb-3 text-uppercase small text-muted">Received By</label>
                    <input type="text" name="received_by" id="rec_by" class="form-control sig-line text-center" placeholder="Printed Name">
                    <input type="text" name="desig_received" id="desig_rec" class="form-control desig-input mt-2" placeholder="Enter Designation">
                </div>
            </div>
        </div>
    </form>
</div>

<button type="button" id="hiddenDownloadTrigger" class="d-none" data-bs-toggle="modal" data-bs-target="#downloadConfirmModal"></button>

<div class="modal fade" id="downloadConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: var(--deped-blue);">
                <h5 class="modal-title"><i class="fas fa-download me-2"></i>Download / Print RIS</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-file-pdf text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark text-center">Ready to Download?</h5>
                <p class="text-muted text-center mb-3">This will generate a printable RIS form. Print it and submit to the AMS staff for processing.</p>
                <div class="alert alert-info border-0 mb-0">
                    <small><i class="fas fa-info-circle me-1"></i> <strong>Next steps:</strong></small>
                    <ol class="mb-0 mt-1" style="font-size: 0.85rem;">
                        <li>Print the generated RIS form</li>
                        <li>Sign the "Requested By" section</li>
                        <li>Submit the printed form to the AMS Unit</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center py-3">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Go Back</button>
                <button type="button" class="btn px-4 fw-bold text-white" style="background-color: var(--deped-blue);" id="confirmDownloadBtn" onclick="generateAndPrint()">
                    <i class="fas fa-print me-1"></i> Generate & Print
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function formatSupplyOption(state) {
        if (!state.id) { return state.text; }
        
        if (state.id === 'Others') {
            return $(`<span class="text-primary fw-bold"><i class="fas fa-pen me-2"></i>${state.text}</span>`);
        }
        
        let qty = parseInt($(state.element).data('qty')) || 0;
        let badgeHtml = '';
        
        if (qty > 0) {
            badgeHtml = `<span class="badge bg-success ms-2 py-1" style="font-size:0.7rem;"><i class="fas fa-box-open me-1"></i>${qty} available</span>`;
        } else {
            badgeHtml = `<span class="badge bg-danger ms-2 py-1" style="font-size:0.7rem;"><i class="fas fa-xmark me-1"></i>Out of Stock</span>`;
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
                const duplicateSelect = $('#items-container .select2-supply').filter(function () {
                    return this !== e.target && $(this).val() === selectedVal;
                }).first();

                if (duplicateSelect.length) {
                    $(this).val(null).trigger('change');
                    const duplicateRow = duplicateSelect.closest('.item-row');
                    const quantityInput = duplicateRow.find('input[name="quantity[]"]');
                    quantityInput.trigger('focus').select();
                    return;
                }

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

        // D: Auto-fill office/division from user profile
        const userDept = '{{ $user->department ?? '' }}';

        if (userDept) {
            // Find matching office in the dropdown
            const officeSelect = document.getElementById('officeSelect');
            for (let i = 0; i < officeSelect.options.length; i++) {
                if (officeSelect.options[i].value === userDept) {
                    officeSelect.selectedIndex = i;
                    break;
                }
            }
            // Trigger unit population
            updateUnits();
        }
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

    function showDownloadModal() {
        const form = document.getElementById('requisitionForm');
        if (!form.checkValidity()) {
            form.reportValidity(); 
            return;
        }

        // Check stock levels against requested quantities
        const issues = [];
        document.querySelectorAll('#items-container .item-row').forEach(row => {
            const sel = row.querySelector('.select2-supply');
            if (!sel.value || sel.value === 'Others') return;
            const stock = parseInt($(sel).find(':selected').data('qty')) || 0;
            const qty = parseInt(row.querySelector('input[name="quantity[]"]').value) || 0;
            if (stock <= 0) {
                issues.push(`<li class="text-danger fw-bold">${sel.value} — out of stock</li>`);
            } else if (qty > stock) {
                issues.push(`<li class="fw-bold">${sel.value} — requested ${qty}, only ${stock} on hand</li>`);
            }
        });

        if (issues.length > 0) {
            Swal.fire({
                title: 'Stock Availability Notice',
                html: `<ul class="text-start">${issues.join('')}</ul>
                       <p class="text-muted mb-0">Unavailable items may be referred to the Procurement Unit / BAC. You can still print and submit your RIS.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a237e',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Proceed Anyway',
                cancelButtonText: 'Go Back & Edit',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('hiddenDownloadTrigger').click();
                }
            });
            return;
        }

        document.getElementById('hiddenDownloadTrigger').click();
    }

    function resetForm() {
        if (confirm('Are you sure you want to reset the form? All entered data will be cleared.')) {
            document.getElementById('requisitionForm').reset();
            // Reset selects
            document.getElementById('officeSelect').selectedIndex = 0;
            document.getElementById('unitSelect').innerHTML = '<option value="">-- Select Division First --</option>';
            // Reset item rows to just one
            const container = document.getElementById('items-container');
            const rows = container.querySelectorAll('.item-row');
            for (let i = 1; i < rows.length; i++) {
                $(rows[i]).find('.select2-supply').select2('destroy');
                rows[i].remove();
            }
            initSelect2Fields();
        }
    }

    function generateAndPrint() {
        const btn = document.getElementById('confirmDownloadBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating...';
        btn.disabled = true;

        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('downloadConfirmModal'));
        modal.hide();

        // Small delay for modal to close
        setTimeout(() => {
            prepareAndPrint();
            btn.innerHTML = '<i class="fas fa-print me-1"></i> Generate & Print';
            btn.disabled = false;
        }, 300);
    }

    function prepareAndPrint() {
        // Map header fields
        document.getElementById('p-entity').innerText = document.getElementById('entity_name').value;
        document.getElementById('p-division').innerText = document.getElementById('officeSelect').value || '';
        document.getElementById('p-office').innerText = document.getElementById('unitSelect').value || '';
        document.getElementById('p-fund').innerText = document.getElementById('fund_cluster').value || '';
        document.getElementById('p-center').innerText = document.getElementById('center_code').value || '';
        document.getElementById('p-ris').innerText = document.getElementById('ris_no').value;

        // Map signatures
        document.getElementById('p-req-name').innerText = document.getElementById('req_by').value;
        document.getElementById('p-req-des').innerText = document.getElementById('desig_req').value;
        document.getElementById('p-app-name').innerText = document.getElementById('app_by').value;
        document.getElementById('p-app-des').innerText = document.getElementById('desig_app').value;
        document.getElementById('p-iss-name').innerText = document.getElementById('iss_by').value;
        document.getElementById('p-iss-des').innerText = document.getElementById('desig_iss').value;
        document.getElementById('p-rec-name').innerText = document.getElementById('rec_by').value || '';
        document.getElementById('p-rec-des').innerText = document.getElementById('desig_rec').value || '';

        // Map items (original fixed RIS format: REQUISITION | Stock Available? | ISSUE)
        const printBody = document.getElementById('print-items-body');
        printBody.innerHTML = '';
        let rowsAdded = 0;

        document.querySelectorAll('#items-container .item-row').forEach(row => {
            const stock = row.querySelector('.stock-input').value || '';
            const unit = row.querySelector('.unit-input').value || '';
            const qty = row.querySelector('input[name="quantity[]"]').value || '';
            const descSelect = row.querySelector('select[name="description[]"]');
            const manualInput = row.querySelector('.manual-desc-input');
            let desc = '';
            if (descSelect.value === 'Others' && manualInput) {
                desc = manualInput.value || 'Others';
            } else if (descSelect.value) {
                desc = descSelect.value;
            }

            if (desc || stock || qty) {
                printBody.innerHTML += `<tr>
                    <td style="border: 1px solid black; padding: 4px; text-align: center;">${stock || '&nbsp;'}</td>
                    <td style="border: 1px solid black; padding: 4px; text-align: center;">${unit || '&nbsp;'}</td>
                    <td style="border: 1px solid black; padding: 4px; text-align: left;">${desc || '&nbsp;'}</td>
                    <td style="border: 1px solid black; padding: 4px; text-align: center;">${qty || '&nbsp;'}</td>
                    <td style="border: 1px solid black; padding: 4px; text-align: center;">&nbsp;</td>
                    <td style="border: 1px solid black; padding: 4px; text-align: center;">&nbsp;</td>
                    <td style="border: 1px solid black; padding: 4px; text-align: center;">&nbsp;</td>
                    <td style="border: 1px solid black; padding: 4px; text-align: left;">&nbsp;</td>
                </tr>`;
                rowsAdded++;
            }
        });

        // Pad to min rows (open lines, no horizontal borders)
        for (let j = rowsAdded; j < 10; j++) {
            printBody.innerHTML += `<tr>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
                <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px;">&nbsp;</td>
            </tr>`;
        }

        // Set purpose
        document.getElementById('p-purpose').innerText = document.querySelector('textarea[name="purpose[]"]').value || '';

        window.print();
    }
</script>

<!-- PRINT AREA (hidden on screen, shown only when printing) -->
<div id="print-area">
    <div style="text-align: center; font-family: 'Times New Roman', Times, serif; margin-bottom: 5px;">
        <img src="{{ asset('assets/images/DepEdseal.png') }}" style="width: 60px; margin: 0 auto 2px auto; display: block;">
        <div style="font-size: 9pt; font-family: 'Old English Text MT', 'Engravers Old English', serif;">Republic of the Philippines</div>
        <div style="font-size: 18pt; font-family: 'Old English Text MT', 'Engravers Old English', serif; line-height: 1;">Department of Education</div>
        <div style="font-size: 10pt;">Region V - Bicol</div>
        <div style="font-size: 12pt; font-weight: bold; margin-top: 5px;">REQUISITION AND ISSUE SLIP</div>
    </div>

    <table style="width: 100%; border: none; font-family: 'Times New Roman', Times, serif; font-size: 10pt; margin-bottom: 5px;">
        <tr>
            <td style="width: 12%; white-space: nowrap; padding: 2px;">Entity Name:</td>
            <td style="width: 38%; border-bottom: 1px solid black; padding: 2px;" id="p-entity"></td>
            <td style="width: 25%; text-align: right; padding-right: 10px; white-space: nowrap;">Fund Cluster:</td>
            <td style="width: 25%; border-bottom: 1px solid black; padding: 2px;" id="p-fund"></td>
        </tr>
        <tr>
            <td style="white-space: nowrap; padding: 2px;">Division:</td>
            <td style="border-bottom: 1px solid black; padding: 2px;" id="p-division"></td>
            <td style="text-align: right; padding-right: 10px; white-space: nowrap;">Responsibility Center Code:</td>
            <td style="border-bottom: 1px solid black; padding: 2px;" id="p-center"></td>
        </tr>
        <tr>
            <td style="white-space: nowrap; padding: 2px;">Office:</td>
            <td style="border-bottom: 1px solid black; padding: 2px;" id="p-office"></td>
            <td style="text-align: right; padding-right: 10px; white-space: nowrap;">RIS No:</td>
            <td style="border-bottom: 1px solid black; font-weight: bold; padding: 2px;" id="p-ris"></td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', Times, serif; font-size: 10pt; border: 1px solid black; table-layout: fixed;">
        <colgroup>
            <col style="width: 11%;">
            <col style="width: 8%;">
            <col style="width: 35%;">
            <col style="width: 9%;">
            <col style="width: 5%;">
            <col style="width: 5%;">
            <col style="width: 8%;">
            <col style="width: 19%;">
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
        <tbody id="print-items-body"></tbody>
        <tbody>
            <tr>
                <td colspan="8" style="border: 1px solid black; padding: 3px; text-align: left;">
                    <b>Purpose:</b> <span id="p-purpose"></span>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', Times, serif; font-size: 10pt; border: 1px solid black; border-top: none; table-layout: fixed;">
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
                <td style="border: 1px solid black; padding: 3px; text-align: center;"><b id="p-req-name"></b></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;"><b id="p-app-name"></b></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;"><b id="p-iss-name"></b></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;"><b id="p-rec-name"></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 3px; text-align: left;">Designation</td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;" id="p-req-des"></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;" id="p-app-des"></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;" id="p-iss-des"></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;" id="p-rec-des"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 3px; text-align: left;">Date</td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;"></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;"></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;"></td>
                <td style="border: 1px solid black; padding: 3px; text-align: center;"></td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>