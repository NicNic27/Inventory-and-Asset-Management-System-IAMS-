<style>
    /* Modal & Form Styles */
    .modal { z-index: 1060 !important; }
    .modal-backdrop { z-index: 1055 !important; }
    .form-label { font-weight: 600; color: #475569; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .custom-card { background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .btn-add-item { background-color: #10b981; color: white; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 600; }

    /* --- OFFICIAL PRINT LAYOUT STYLES --- */
    #printableArea { display: none; }

    @media print {
        @page { size: portrait; margin: 0.4in; }
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea {
            display: block !important;
            position: absolute;
            left: 0; top: 0; width: 100%;
            color: black;
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
        }
        .p-header { text-align: center; margin-bottom: 5px; }
        .p-header img { width: 60px; }
        .p-header h4 { margin: 0; font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .entity-line { border-bottom: 1px solid black; display: inline-block; min-width: 300px; text-align: center; font-weight: bold; margin-bottom: 10px; }
        
        .info-table, .main-table, .acc-table { width: 100%; border-collapse: collapse; border: 1px solid black; }
        .info-table td, .main-table th, .main-table td, .acc-table td { border: 1px solid black; padding: 4px 8px; vertical-align: top; }
        
        .main-table th { background: #e5e7eb !important; -webkit-print-color-adjust: exact; text-align: center; }
        .main-table td { text-align: center; }
        .empty-row td { height: 22px; border: 1px solid black; } 
        
        .footer-note { font-size: 9px; border: 1px solid black; border-top: none; padding: 5px; font-style: italic; }
        
        .sig-table-print { margin-top: -1px; width: 100%; border-collapse: collapse; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; }
        .sig-table-print td { padding: 10px; vertical-align: top; border: none; }
        
        .rd-print-box { text-align: left !important; padding-left: 30px !important; }
        .sig-line-print { border-bottom: 1px solid black; width: 85%; margin: 35px auto 2px auto; text-align: center; font-weight: bold; text-transform: uppercase; display: block; }
        .sig-sub-print { text-align: center; font-size: 10px; display: block; width: 85%; }
        
        .acc-table { margin-top: -1px; border-top: none; }
    }
</style>

<div class="modal fade no-print" id="receivePoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary align-items-center d-flex m-0">
                    <i class="fas fa-file-invoice me-2"></i> Receive Purchase Order (Admin)
                    <span id="po_type_badge" class="badge ms-3 fs-6 rounded-pill text-white" style="display: none; background-color: #101954;"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light" style="padding: 20px 30px;">
                
                <form id="poForm">
                    <input type="hidden" id="po_type" name="po_type" value="">

                    <div class="custom-card">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Document Details</h6>
                        <div class="row g-3">
                            <input type="hidden" id="modal_po_id" value="">
                            <div class="col-md-8">
                                <label class="form-label">Entity Name</label>
                                <input type="text" id="in-entity" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">PO Number <span class="text-danger">*</span></label>
                                <input type="text" id="po_no" class="form-control fw-bold" placeholder="YYYY-MM-XXXX" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" id="in-supplier" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Supplier Address <span class="text-danger">*</span></label>
                                <input type="text" id="in-address" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PO Date <span class="text-danger">*</span></label>
                                <input type="date" id="in-date" class="form-control" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Procurement Mode <span class="text-danger">*</span></label>
                                <select id="in-mode" class="form-select" required>
                                    <option value="">Choose...</option>
                                    <option>Small Value Procurement</option>
                                    <option>Public Bidding</option>
                                    <option>Shopping</option>
                                    <option>Direct Contracting</option>
                                    <option>Negotiated Procurement</option>
                                    <option>Negotiated SVP</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-primary fw-bold">P.O. Status (Auto-Calculated)</label>
                                <select id="in-status" class="form-select border-primary shadow-sm fw-bold" style="background-color: #f1f5f9; pointer-events: none;" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Partial">Partial</option>
                                    <option value="Complete">Complete</option>
                                </select>
                                <small class="text-muted" style="font-size: 11px;">Check off items below to update status.</small>
                            </div>
                            
                            <div class="col-md-12 mt-4"><h6 class="fw-bold text-dark border-bottom pb-2">Delivery Information</h6></div>
                            <div class="col-md-6">
                                <label class="form-label">Place of Delivery</label>
                                <input type="text" id="in-place-delivery" class="form-control" placeholder="e.g. Regional Office">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Delivery</label>
                                <input type="text" id="in-date-delivery" class="form-control" placeholder="e.g. Within 15 Days">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Delivery Term</label>
                                <input type="text" id="in-delivery-term" class="form-control" placeholder="e.g. FOB Destination">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Term</label>
                                <input type="text" id="in-payment-term" class="form-control" placeholder="e.g. 30 Days">
                            </div>
                        </div>
                    </div>

                    <div class="custom-card">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Signatories</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Authorized Official <span class="text-danger">*</span></label>
                                <input type="text" id="in-auth-name" class="form-control" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-primary">Official Designation <span class="text-danger">*</span></label>
                                <input type="text" id="in-auth-designation" class="form-control fw-bold" value="REGIONAL DIRECTOR" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label border-top pt-3 w-100">Chief Accountant <span class="text-danger">*</span></label>
                                <input type="text" id="in-acc-name" class="form-control" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label border-top pt-3 w-100 text-primary">Accountant Designation <span class="text-danger">*</span></label>
                                <input type="text" id="in-acc-designation" class="form-control fw-bold" value="ACCOUNTANT II" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Ordered Items</h6>
                        <button type="button" id="addItemBtn" class="btn btn-add-item"><i class="fa-solid fa-plus me-1"></i> Add Item</button>
                    </div>
                    
                    <div id="itemsContainer"></div>
                </form>

            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="poForm" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Save P.O. Record</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = () => { if (typeof window.addEmptyItemRow === "function") window.addEmptyItemRow(); };

    window.autoUpdatePoStatus = function() {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length === 0) return;
        
        let checkedCount = 0;
        rows.forEach(row => {
            if (row.querySelector('.item-delivered-cb').checked) checkedCount++;
        });
        
        const statusSelect = document.getElementById('in-status');
        if (checkedCount === 0) {
            statusSelect.value = 'Pending';
        } else if (checkedCount === rows.length) {
            statusSelect.value = 'Complete';
        } else {
            statusSelect.value = 'Partial';
        }
    };

    window.addEmptyItemRow = function(data = {unit: 'pc', desc: '', qty: 0, cost: 0.00, is_delivered: false, item_type: 'supply', source_type: 'procurement_stock'}) {
        const container = document.getElementById('itemsContainer');
        const q = parseFloat(data.qty) || 0;
        const c = parseFloat(data.cost) || 0;
        const total = (q * c).toLocaleString(undefined, {minimumFractionDigits: 2});
        const isSelected = (val) => data.unit === val ? 'selected' : '';
        const isChecked = data.is_delivered ? 'checked' : '';
        const itemType = data.item_type || 'supply';
        const sourceType = data.source_type || 'procurement_stock';
        const cardBorderClass = sourceType === 'direct_issuance' ? 'item-card direct-card' : 'item-card supply-card';
        const subtitleText = sourceType === 'direct_issuance' ? 'Supply \u2014 Direct Issuance' : 'Supply \u2014 For Inventory';
        const officeVisible = sourceType === 'direct_issuance' ? '' : 'display:none;';

        const templateHtml = `
            <div class="${cardBorderClass} card position-relative item-row p-3 mb-3 border-0 shadow-sm border-start border-4 border-success">
                <div class="row g-3 align-items-center">
                    <div class="col-md-1 text-center pt-2">
                        <label class="form-label d-block text-success mb-2" title="Mark as Delivered">RCVD</label>
                        <input type="checkbox" class="form-check-input item-delivered-cb shadow-sm border-secondary" style="width: 22px; height: 22px; cursor: pointer;" ${isChecked} onchange="autoUpdatePoStatus()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select unit-select" required>
                            <option value="pc" ${isSelected('pc')}>pc</option>
                            <option value="pcs" ${isSelected('pcs')}>pcs</option>
                            <option value="unit" ${isSelected('unit')}>unit</option>
                            <option value="set" ${isSelected('set')}>set</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control desc-input" value="${data.desc}" placeholder="Enter item details..." required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control qty-input" value="${q}" required oninput="autoUpdatePoStatus()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Cost <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" class="form-control cost-input" value="${c}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total Amount</label>
                        <input type="text" class="form-control bg-light fw-bold total-output" readonly value="${total}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Fulfillment</label>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="d-inline-flex rounded border overflow-hidden" style="border-color: #e2e8f0;">
                                <button type="button" class="btn btn-sm ${sourceType === 'procurement_stock' ? 'text-white' : ''}" style="font-size:0.75rem; font-weight:600; background:${sourceType === 'procurement_stock' ? '#059669' : '#f8fafc'}; color:${sourceType === 'procurement_stock' ? 'white' : '#64748b'};" onclick="onFulfillmentToggle(this, 'procurement_stock')"><i class="fas fa-warehouse me-1"></i> Inventory</button>
                                <button type="button" class="btn btn-sm ${sourceType === 'direct_issuance' ? 'text-white' : ''}" style="font-size:0.75rem; font-weight:600; background:${sourceType === 'direct_issuance' ? '#ea580c' : '#f8fafc'}; color:${sourceType === 'direct_issuance' ? 'white' : '#64748b'};" onclick="onFulfillmentToggle(this, 'direct_issuance')"><i class="fas fa-truck me-1"></i> Direct</button>
                            </div>
                            <input type="hidden" class="source-type-select" value="${sourceType}">
                            <input type="hidden" class="item-type-select" value="${itemType}">
                        </div>
                    </div>
                    <div class="col-md-12 office-wrapper" style="${officeVisible}">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Requesting Division <span class="text-danger">*</span></label>
                                <select class="form-select requesting-division-select" onchange="onOfficeChanged(this)" required>
                                    <option value="">-- Select Division --</option>
                                    ${Object.keys(window.officeMapping || {}).map(d => `<option value="${d}" ${data.requesting_office && data.requesting_office.startsWith(d) ? 'selected' : ''}>${d}</option>`).join('')}
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Office / Section <span class="text-danger">*</span></label>
                                <select class="form-select requesting-unit-select" required>
                                    <option value="">-- Select Division First --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; border-radius: 50%; width: 30px; height: 30px;" onclick="this.closest('.item-row').remove(); autoUpdatePoStatus();"><i class="fa-solid fa-trash"></i></button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', templateHtml);
        autoUpdatePoStatus(); 
    };

    window.officeMapping = {
        "Administrative Division": ["Asset Management Section", "General Services Unit", "Payroll Services Unit", "Records Section", "Personnel Section", "Cash Section"],
        "Curriculum and Learning Management Division": ["Learning Resource Management Section"],
        "Education Support Services Division": ["Health and Nutrition", "Programs and Projects", "Facilities"],
        "Finance Division": ["Budget Section", "Accounting Section"],
        "Human Resource Development Division": ["NEAP"],
        "Office of the Regional Director": ["Procurement Unit", "Information and Communications Technology Unit", "Public Affairs Unit", "Legal Unit"]
    };

    window.onOfficeChanged = function(divisionSelect) {
        const row = divisionSelect.closest('.item-row');
        const unitSelect = row.querySelector('.requesting-unit-select');
        const selectedDivision = divisionSelect.value;
        unitSelect.innerHTML = '<option value="">-- Select Office/Section --</option>';
        if (selectedDivision && window.officeMapping[selectedDivision]) {
            window.officeMapping[selectedDivision].forEach(function(unit) {
                const opt = document.createElement('option');
                opt.value = unit; opt.textContent = unit;
                unitSelect.appendChild(opt);
            });
        }
    };

    window.parseRequestingOffice = function(row, value) {
        if (!value) return;
        const parts = value.split(' > ');
        const divSel = row.querySelector('.requesting-division-select');
        const unitSel = row.querySelector('.requesting-unit-select');
        if (divSel && parts[0]) {
            divSel.value = parts[0];
            window.onOfficeChanged(divSel);
            if (unitSel && parts[1]) unitSel.value = parts[1];
        }
    };

    window.onFulfillmentToggle = function(btn, sourceType) {
        const row = btn.closest('.item-row');
        const toggleBtns = btn.parentElement.querySelectorAll('.btn');
        toggleBtns.forEach(b => {
            b.classList.remove('text-white');
            b.style.background = '#f8fafc';
            b.style.color = '#64748b';
        });
        btn.classList.add('text-white');
        btn.style.background = sourceType === 'procurement_stock' ? '#059669' : '#ea580c';
        btn.style.color = 'white';
        row.querySelector('.source-type-select').value = sourceType;
        const wrapper = row.querySelector('.office-wrapper');
        if (wrapper) wrapper.style.display = sourceType === 'direct_issuance' ? '' : 'none';
    };

    // Attach Calculation Listeners globally
    document.addEventListener('input', (e) => {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('cost-input')) {
            const row = e.target.closest('.item-row');
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const c = parseFloat(row.querySelector('.cost-input').value) || 0;
            row.querySelector('.total-output').value = (q * c).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Re-attach Add Item Button Listener
        const addItemBtn = document.getElementById('addItemBtn');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', () => { if (typeof window.addEmptyItemRow === "function") window.addEmptyItemRow(); });
        }
    });

    // Submitting the Form
    document.getElementById('poForm').addEventListener('submit', function(e) {
        e.preventDefault(); 

        let formData = {
            _token: '{{ csrf_token() }}',
            po_type: document.getElementById('po_type').value, // <--- Passing the PO Type
            entity_name: document.getElementById('in-entity').value,
            po_no: document.getElementById('po_no').value,
            supplier_name: document.getElementById('in-supplier').value,
            supplier_address: document.getElementById('in-address').value,
            po_date: document.getElementById('in-date').value,
            procurement_mode: document.getElementById('in-mode').value,
            
            auth_official: document.getElementById('in-auth-name').value,
            auth_official_designation: document.getElementById('in-auth-designation').value,
            chief_accountant: document.getElementById('in-acc-name').value,
            chief_accountant_designation: document.getElementById('in-acc-designation').value,
            
            place_of_delivery: document.getElementById('in-place-delivery').value,
            date_of_delivery: document.getElementById('in-date-delivery').value,
            delivery_term: document.getElementById('in-delivery-term').value,
            payment_term: document.getElementById('in-payment-term').value,
            
            status: document.getElementById('in-status').value,
            total_amount: 0,
            items: []
        };

        let po_id = document.getElementById('modal_po_id').value;
        let method = po_id ? 'PUT' : 'POST';
        let url = po_id ? `/admin/po/${po_id}` : '{{ url('/admin/po') }}';

        const rows = document.querySelectorAll('.item-row');
        if(rows.length === 0) {
            Swal.fire('Warning', 'You must add at least one item.', 'warning');
            return;
        }

        rows.forEach(row => {
            let q = parseFloat(row.querySelector('.qty-input').value) || 0;
            let c = parseFloat(row.querySelector('.cost-input').value) || 0;
            let isD = row.querySelector('.item-delivered-cb').checked;
            let subtotal = q * c;
            formData.total_amount += subtotal;

            formData.items.push({
                unit: row.querySelector('.unit-select').value,
                description: row.querySelector('.desc-input').value,
                qty: q,
                cost: c,
                is_delivered: isD,
                item_type: row.querySelector('.item-type-select')?.value || 'supply',
                supply_id: row.querySelector('.supply-select')?.value || null,
                source_type: row.querySelector('.source-type-select')?.value || 'procurement_stock',
                requesting_office: (() => { const d = row.querySelector('.requesting-division-select')?.value; const u = row.querySelector('.requesting-unit-select')?.value; return d ? (u ? d + ' > ' + u : d) : null; })()
            });
        });

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Saved!', data.message, 'success').then(() => { window.location.reload(); });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to save to database.', 'error');
        });
    });
</script>