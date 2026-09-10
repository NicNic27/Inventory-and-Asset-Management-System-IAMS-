<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create RIS - Staff | DepEd ROV</title>
    
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

        /* ===== Requisition item cards ===== */
        .item-card {
            border: 1px solid #e8eaf3;
            border-radius: 12px;
            padding: 16px 18px 18px;
            margin-bottom: 14px;
            background: linear-gradient(180deg, #fbfcff 0%, #ffffff 60%);
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .item-card:hover {
            border-color: #c5cae9;
            box-shadow: 0 3px 12px rgba(26, 35, 126, .07);
        }

        .item-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #dfe3f0;
        }

        .item-badge {
            background: var(--deped-blue);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .6px;
            padding: 4px 14px;
            border-radius: 20px;
        }

        .btn-remove-item {
            color: #dc3545;
            font-size: .75rem;
            font-weight: 600;
            text-decoration: none;
            background: #fdeef0;
            border: 1px solid #f6c9cf;
            padding: 4px 12px;
            border-radius: 20px;
            transition: all .15s ease;
        }

        .btn-remove-item:hover { background: #dc3545; color: #fff; }

        .amount-chip {
            font-weight: 700;
            color: #1b5e20;
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: .9rem;
            white-space: nowrap;
            height: 39px;
            min-width: 120px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .amount-chip.is-empty {
            color: #9e9e9e;
            background: #f5f5f5;
            border-color: #e0e0e0;
            font-weight: 600;
        }

        .summary-strip {
            background: #fff;
            border: 1px solid #e8eaf3;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: .85rem;
            color: #555;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        }

        .select2-results__option .opt-desc { font-size: .78rem; color: #8a8f98; }

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

    @include('layouts.header')
    @include('layouts.sidebar')

<div class="main-content">
    <div class="top-bar">
        <div><i class="fa-solid fa-building-shield me-2"></i> <strong>ASSET MANAGEMENT/SUPPLY SECTION SYSTEM</strong></div>
        <div id="clock"><i class="fa-regular fa-clock me-2"></i> Loading time...</div>
    </div>

    <form action="{{ url('/ris') }}" id="requisitionForm" method="POST">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ url('/ris') }}" class="btn btn-outline-secondary fw-bold shadow-sm mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Queue
                </a>
                <h3 class="fw-bold m-0" style="color: var(--deped-blue);">CREATE RIS FROM PHYSICAL FORM</h3>
                <p class="text-muted small">Enter the details from the requestor's submitted RIS form.</p>
            </div>
            <div>
                <button type="button" class="btn btn-submit shadow-sm" onclick="showConfirmModal()">
                    <i class="fa-solid fa-paper-plane me-1"></i> Submit RIS
                </button>
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
                        <input type="text" name="fund_cluster" id="fund_cluster" class="form-control" placeholder="Enter fund cluster if applicable">
                    </div>
                    <div class="mb-3">
                        <label>Responsible Center Code</label>
                        <input type="text" name="center_code" id="center_code" class="form-control" placeholder="Enter RCC if applicable">
                    </div>
                    <div>
                        <label>RIS Number</label>
                        <input type="text" name="ris_no" id="ris_no" class="form-control fw-bold text-danger bg-light" value="{{ $risNumber }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-box requisition-block">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <h6 class="section-title mb-0" style="border-bottom: none;"><i class="fa-solid fa-list-check"></i> Requisition Details</h6>
                <span class="text-muted small"><i class="fa-solid fa-wand-magic-sparkles me-1 text-warning"></i> Pick an item — stock no., unit & price auto-fill. The amount computes live as you type the quantity.</span>
            </div>

            <div id="items-container">
                <div class="item-card item-row">
                    <div class="item-card-header">
                        <span class="item-badge item-index">ITEM 1</span>
                        <a href="javascript:void(0)" class="btn-remove-item" onclick="removeRow(this)"><i class="fa-solid fa-trash-can me-1"></i>Remove</a>
                    </div>
                    <div class="row g-3">
                        <div class="col-lg-5">
                            <label>Item Description <span class="text-danger">*</span></label>
                            <select name="description[]" class="form-select select2-supply" required>
                                <option value="" selected disabled>-- Select Supply Item --</option>
                                <option value="Others" class="fw-bold text-primary">Others (Please specify)</option>
                                @foreach($supplies as $supply)
                                    <option value="{{ $supply->article }}, {{ $supply->description }}" data-barcode="{{ $supply->barcode_id }}" data-qty="{{ $supply->quantity }}" data-unit="{{ $supply->unit_measure }}" data-value="{{ $supply->unit_value }}" data-article="{{ $supply->article }}">{{ $supply->article }} - {{ $supply->description }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="manual_description[]" class="form-control mt-2 manual-desc-input shadow-sm border-primary" style="display: none;" placeholder="Specify custom item name and description">
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <label>Stock No.</label>
                            <input type="text" name="stock_no[]" class="form-control bg-light stock-input" readonly placeholder="Auto-filled">
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label">Unit Measure <span class="text-danger">*</span></label>
                            <input type="text" name="unit_measure[]" class="form-control bg-light unit-input" readonly placeholder="Auto-filled" required>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label>Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity[]" class="form-control qty-input" min="1" placeholder="0" required>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-end gap-3 flex-wrap">
                                <div class="flex-grow-1" style="min-width: 240px;">
                                    <label>Remarks</label>
                                    <input type="text" name="remarks[]" class="form-control" placeholder="e.g. condition, notes from the physical form...">
                                </div>
                                <div>
                                    <label class="text-muted small">Amount <span class="fw-normal">(auto)</span></label>
                                    <div class="amount-chip is-empty" data-amount="0"><span class="amount-value">—</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem()">
                    <i class="fa-solid fa-plus me-1"></i> Add Item Row
                </button>
                <div class="summary-strip">
                    <i class="fa-solid fa-calculator me-2 text-primary"></i>
                    <span id="summaryCount">1 item</span>
                    <span class="mx-2 text-muted">•</span>
                    Estimated Total: <strong class="ms-1 text-success" id="summaryTotal">₱0.00</strong>
                </div>
            </div>
        </div>
        <div class="section-box purpose-block">
            <div class="col-md-13">
                <label>Purpose <span class="text-danger">*</span></label>
                <textarea name="purpose[]" class="form-control" rows="2" placeholder="Enter the purpose from the physical RIS form..." required></textarea>
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
                <h5 class="fw-bold text-dark">Submit RIS Request?</h5>
                <p class="text-muted mb-0">Are you sure all the details match the physical RIS form? This will create the RIS in the system.</p>
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
    function showConfirmModal() {
        const form = document.getElementById('requisitionForm');
        if (form.checkValidity()) {
            document.getElementById('hiddenSubmitTrigger').click();
        } else {
            form.reportValidity(); 
        }
    }

    function executeSubmit() {
        const btn = document.getElementById('confirmSubmitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
        btn.disabled = true;
        document.getElementById('requisitionForm').submit();
    }

    function fmtPeso(n) {
        return '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatSupplyOption(state) {
        if (!state.id) { return state.text; }

        if (state.id === 'Others') {
            return $(`<span class="text-primary fw-bold"><i class="fas fa-pen me-2"></i>Others (Please specify)</span>`);
        }

        const $el = $(state.element);
        const article = String($el.data('article') || '');
        const fullText = state.text;
        const desc = fullText.startsWith(article + ' - ') ? fullText.slice(article.length + 3) : fullText;
        const qty = parseInt($el.data('qty')) || 0;
        const price = parseFloat($el.data('value'));

        let priceHtml = '';
        if (!isNaN(price) && price > 0) {
            priceHtml = `<span class="badge py-1" style="font-size:0.68rem;background:#fff8e1;color:#9a7b0a;border:1px solid #ffe082;"><i class="fas fa-tag me-1"></i>${fmtPeso(price)}</span>`;
        }

        let stockHtml = '';
        if (qty > 0) {
            stockHtml = `<span class="badge bg-success-subtle text-success border border-success-subtle py-1" style="font-size:0.68rem;"><i class="fas fa-box-open me-1"></i>${qty} in stock</span>`;
        } else {
            stockHtml = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1" style="font-size:0.68rem;"><i class="fas fa-xmark me-1"></i>Out of Stock</span>`;
        }

        return $(`<div class="d-flex justify-content-between align-items-center gap-2" style="width:100%;">
            <div class="text-truncate" title="${fullText.replace(/"/g, '&quot;')}">
                <span class="fw-semibold">${article}</span>
                <span class="opt-desc"> — ${desc}</span>
            </div>
            <div class="text-nowrap">${priceHtml}${stockHtml}</div>
        </div>`);
    }

    function updateAmount(row) {
        const $row = $(row);
        const $chip = $row.find('.amount-chip');
        const $sel = $row.find('.select2-supply');
        const selectedVal = $sel.val();
        let amount = null;

        if (selectedVal && selectedVal !== 'Others') {
            const data = $sel.select2('data');
            const opt = data && data.length ? data[0].element : null;
            const price = opt ? parseFloat($(opt).data('value')) : NaN;
            const qty = parseInt($row.find('input[name="quantity[]"]').val()) || 0;
            if (!isNaN(price) && qty > 0) { amount = price * qty; }
        }

        const $value = $chip.find('.amount-value');
        if (amount === null) {
            $value.text('—');
            $chip.addClass('is-empty').attr('data-amount', '0');
        } else {
            $value.text(fmtPeso(amount));
            $chip.removeClass('is-empty').attr('data-amount', amount);
        }
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0, count = 0;
        $('#items-container .item-row').each(function () {
            count++;
            total += parseFloat($(this).find('.amount-chip').attr('data-amount')) || 0;
        });
        $('#summaryCount').text(count + (count === 1 ? ' item' : ' items'));
        $('#summaryTotal').text(fmtPeso(total));
    }

    function renumberItems() {
        const rows = document.querySelectorAll('#items-container .item-row');
        rows.forEach((r, i) => {
            const badge = r.querySelector('.item-index');
            if (badge) badge.textContent = 'ITEM ' + (i + 1);
            const removeBtn = r.querySelector('.btn-remove-item');
            if (removeBtn) removeBtn.style.visibility = rows.length <= 1 ? 'hidden' : 'visible';
        });
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

        $('#items-container').on('select2:select', '.select2-supply', function (e) {
            const selectedVal = $(this).val();
            const row = $(this).closest('.item-row');
            const manualInput = row.find('.manual-desc-input');
            const unitInput = row.find('.unit-input');
            const stockInput = row.find('.stock-input');
            const qtyInput = row.find('input[name="quantity[]"]');
            
            if (selectedVal === 'Others') {
                manualInput.show().attr('required', true);
                stockInput.val('');
                unitInput.val('').removeAttr('readonly').attr('placeholder', 'Type unit manually').removeClass('bg-light');
                manualInput.trigger('focus');
            } else {
                const duplicateSelect = $('#items-container .select2-supply').filter(function () {
                    return this !== e.target && $(this).val() === selectedVal;
                }).first();

                if (duplicateSelect.length) {
                    $(this).val(null).trigger('change');
                    manualInput.hide().attr('required', false).val('');
                    stockInput.val('');
                    unitInput.val('').attr('readonly', true).attr('placeholder', 'Auto-filled').addClass('bg-light');
                    updateAmount(row);
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
                qtyInput.trigger('focus');
            }

            updateAmount(row);
        });

        // Live amount: recompute whenever the quantity changes
        $('#items-container').on('input change', 'input[name="quantity[]"]', function () {
            updateAmount($(this).closest('.item-row'));
        });
    }

    $(document).ready(function() {
        initSelect2Fields();
        renumberItems();
        updateGrandTotal();
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
        
        $(firstRow).find('.select2-supply').select2('destroy');
        
        const newRow = firstRow.cloneNode(true);
        
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
        
        initSelect2Fields();
        updateAmount(newRow);
        renumberItems();
    }

    function removeRow(link) {
        const container = document.getElementById('items-container');
        const rows = container.querySelectorAll('.item-row');
        if (rows.length > 1) {
            $(link).closest('.item-row').find('.select2-supply').select2('destroy');
            link.closest('.item-row').remove();
            renumberItems();
            updateGrandTotal();
        } else {
            alert("The form must have at least one item.");
        }
    }
</script>
</body>
</html>
