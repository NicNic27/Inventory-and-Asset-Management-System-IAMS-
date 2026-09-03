<style>
    .form-label { font-weight: 600; color: #475569; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .custom-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: all 0.2s ease; }
    .custom-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn-add-item { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; transition: all 0.2s ease; box-shadow: 0 2px 6px rgba(16,185,129,0.3); }
    .btn-add-item:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16,185,129,0.4); background: linear-gradient(135deg, #059669, #047857); }
    .po-stepper { display: flex; justify-content: center; gap: 0; margin-bottom: 24px; padding: 0 20px; }
    .po-stepper .step { display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 8px; cursor: pointer; transition: all 0.25s ease; font-size: 0.85rem; font-weight: 500; color: #94a3b8; }
    .po-stepper .step:hover { background: #f1f5f9; color: #475569; }
    .po-stepper .step.active { background: #eff6ff; color: #2563eb; font-weight: 600; }
    .po-stepper .step.completed { color: #10b981; }
    .po-stepper .step .step-number { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; background: #e2e8f0; color: #64748b; transition: all 0.25s ease; flex-shrink: 0; }
    .po-stepper .step.active .step-number { background: #2563eb; color: white; box-shadow: 0 0 0 4px rgba(37,99,235,0.15); }
    .po-stepper .step.completed .step-number { background: #10b981; color: white; }
    .po-stepper .step-divider { width: 40px; height: 2px; background: #e2e8f0; align-self: center; flex-shrink: 0; transition: background 0.25s ease; }
    .po-stepper .step-divider.active { background: #2563eb; }
    .step-panel { display: none; animation: fadeSlideIn 0.3s ease; }
    .step-panel.active { display: block; }
    @keyframes fadeSlideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .item-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; position: relative; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .item-card:hover { border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .item-card .item-number { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
    .item-card .remove-item-btn { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; border: none; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; cursor: pointer; font-size: 0.85rem; }
    .item-card .remove-item-btn:hover { background: #ef4444; color: white; transform: scale(1.1); }
    .form-control, .form-select { border-radius: 8px; border: 1.5px solid #e2e8f0; transition: all 0.2s ease; font-size: 0.9rem; }
    .form-control:focus, .form-select:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .grand-total-bar { background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 10px; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; margin-top: 16px; }
    .grand-total-bar .total-label { font-size: 0.85rem; opacity: 0.8; }
    .grand-total-bar .total-amount { font-size: 1.4rem; font-weight: 700; color: #4ade80; }
    .section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .section-header .section-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .section-header .section-icon.blue { background: #eff6ff; color: #2563eb; }
    .section-header .section-icon.green { background: #f0fdf4; color: #16a34a; }
    .section-header .section-icon.purple { background: #faf5ff; color: #9333ea; }
    .section-header .section-icon.orange { background: #fff7ed; color: #ea580c; }
    .section-header h6 { margin: 0; font-weight: 700; color: #1e293b; font-size: 0.95rem; }
    .section-header small { color: #94a3b8; font-size: 0.78rem; }
    .empty-items-state { text-align: center; padding: 40px 20px; color: #94a3b8; }
    .empty-items-state i { font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4; }
    .empty-items-state p { margin: 0; font-size: 0.9rem; }

    /* Item type toggle */
    .item-type-toggle { display: inline-flex; border-radius: 8px; overflow: hidden; border: 1.5px solid #e2e8f0; }
    .item-type-toggle .toggle-btn { padding: 6px 14px; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; border: none; background: #f8fafc; color: #64748b; display: flex; align-items: center; gap: 5px; }
    .item-type-toggle .toggle-btn:hover { background: #f1f5f9; }
    .item-type-toggle .toggle-btn.active-supply { background: #2563eb; color: white; }
    .item-type-toggle .toggle-btn.active-asset { background: #7c3aed; color: white; }

    /* Fulfillment toggle */
    .fulfillment-toggle { display: inline-flex; border-radius: 8px; overflow: hidden; border: 1.5px solid #e2e8f0; }
    .fulfillment-toggle .toggle-btn { padding: 6px 12px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; border: none; background: #f8fafc; color: #64748b; display: flex; align-items: center; gap: 5px; }
    .fulfillment-toggle .toggle-btn:hover { background: #f1f5f9; }
    .fulfillment-toggle .toggle-btn.active-inventory { background: #059669; color: white; }
    .fulfillment-toggle .toggle-btn.active-direct { background: #ea580c; color: white; }

    /* Global issuance toggle */
    .global-issuance-bar { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .global-issuance-bar .label { font-size: 0.8rem; font-weight: 600; color: #475569; white-space: nowrap; }

    /* Item card border variants */
    .item-card.supply-card { border-left: 4px solid #2563eb; }
    .item-card.asset-card { border-left: 4px solid #7c3aed; }
    .item-card.direct-card { border-left: 4px solid #ea580c; }

    /* Inventory destination preview */
    .inventory-preview { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #93c5fd; border-radius: 8px; padding: 8px 12px; font-size: 0.78rem; color: #1e40af; display: flex; align-items: center; gap: 6px; }
    .inventory-preview i { font-size: 0.9rem; }

    /* Asset fields */
    .asset-fields { background: #faf5ff; border: 1px solid #d8b4fe; border-radius: 8px; padding: 10px 14px; margin-top: 8px; }
    .asset-fields .form-label { color: #7c3aed; }

    /* Grouped supply select */
    .supply-select-wrapper { position: relative; }
    .supply-select-wrapper .supply-search { width: 100%; padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem; margin-bottom: 4px; }
    .supply-select-wrapper .supply-search:focus { outline: none; border-color: #93c5fd; }
    #printableArea { display: none; }
    @media print { @page { size: portrait; margin: 0.4in; } body * { visibility: hidden; } #printableArea, #printableArea * { visibility: visible; } #printableArea { display: block !important; position: absolute; left: 0; top: 0; width: 100%; color: black; font-family: "Times New Roman", Times, serif; font-size: 11px; } .p-header { text-align: center; margin-bottom: 5px; } .p-header img { width: 60px; } .p-header h4 { margin: 0; font-weight: bold; font-size: 14px; text-transform: uppercase; } .entity-line { border-bottom: 1px solid black; display: inline-block; min-width: 300px; text-align: center; font-weight: bold; margin-bottom: 10px; } .info-table, .main-table, .acc-table { width: 100%; border-collapse: collapse; border: 1px solid black; } .info-table td, .main-table th, .main-table td, .acc-table td { border: 1px solid black; padding: 4px 8px; vertical-align: top; } .main-table th { background: #e5e7eb !important; -webkit-print-color-adjust: exact; text-align: center; } .main-table td { text-align: center; } .empty-row td { height: 22px; border: 1px solid black; } .footer-note { font-size: 9px; border: 1px solid black; border-top: none; padding: 5px; font-style: italic; } }
    @media (max-width: 768px) { .po-stepper { flex-wrap: wrap; gap: 4px; } .po-stepper .step { padding: 8px 12px; font-size: 0.78rem; } .po-stepper .step-divider { width: 20px; } .item-card { padding: 12px; } }
</style>

<div class="modal fade no-print" id="receivePoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title fw-bold text-primary m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-file-invoice"></i>
                        <span id="modalTitleText">Add Purchase Order</span>
                    </h5>
                    <span id="po_type_badge" class="badge rounded-pill text-white" style="display: none; background: linear-gradient(135deg, #1e40af, #7c3aed); font-size: 0.75rem;"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light" style="padding: 20px 24px;">
                <div class="po-stepper">
                    <div class="step active" data-step="1" onclick="poWizard.goToStep(1)"><div class="step-number">1</div><span class="d-none d-sm-inline">PO Details</span></div>
                    <div class="step-divider"></div>
                    <div class="step" data-step="2" onclick="poWizard.goToStep(2)"><div class="step-number">2</div><span class="d-none d-sm-inline">Delivery Terms</span></div>
                    <div class="step-divider"></div>
                    <div class="step" data-step="3" onclick="poWizard.goToStep(3)"><div class="step-number">3</div><span class="d-none d-sm-inline">Signatories</span></div>
                    <div class="step-divider"></div>
                    <div class="step" data-step="4" onclick="poWizard.goToStep(4)"><div class="step-number">4</div><span class="d-none d-sm-inline">Items</span></div>
                </div>
                <form id="poForm">
                    <input type="hidden" id="po_type" name="po_type" value="">
                    <input type="hidden" id="modal_po_id" value="">

                    <!-- Step 1: PO Details -->
                    <div class="step-panel active" data-panel="1">
                        <div class="custom-card">
                            <div class="section-header">
                                <div class="section-icon blue"><i class="fas fa-file-alt"></i></div>
                                <div><h6>Purchase Order Details</h6><small>Core document information</small></div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-8"><label class="form-label">Entity Name</label><input type="text" id="in-entity" class="form-control" ></div>
                                <div class="col-12 col-md-4"><label class="form-label">PO Number <span class="text-danger">*</span></label><input type="text" id="po_no" class="form-control fw-bold" placeholder="YYYY-MM-XXXX" required></div>
                                <div class="col-12 col-md-6"><label class="form-label">Supplier Name <span class="text-danger">*</span></label><input type="text" id="in-supplier" class="form-control" placeholder="Enter supplier name" required></div>
                                <div class="col-12 col-md-6"><label class="form-label">Supplier Address <span class="text-danger">*</span></label><input type="text" id="in-address" class="form-control" placeholder="Supplier Address" required></div>
                                <div class="col-12 col-md-4"><label class="form-label">PO Date <span class="text-danger">*</span></label><input type="date" id="in-date" class="form-control" required></div>
                                <div class="col-12 col-md-8"><label class="form-label">Mode of Procurement <span class="text-danger">*</span></label>
                                    <select id="in-mode" class="form-select" required><option value="">Select procurement mode...</option><option>Small Value Procurement</option><option>Public Bidding</option><option>Shopping</option><option>Direct Contracting</option><option>Negotiated Procurement</option><option>Negotiated SVP</option></select>
                                </div>
                                <div class="col-12"><label class="form-label">Status</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <select id="in-status" class="form-select" style="max-width: 200px; pointer-events: none; background-color: #d5e2ec; border: 1px solid #1975ff;" required><option value="Pending">Pending</option><option value="Partial">Partial</option><option value="Complete">Complete</option></select>
                                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Auto-calculated from delivered items</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Delivery Terms -->
                    <div class="step-panel" data-panel="2">
                        <div class="custom-card">
                            <div class="section-header">
                                <div class="section-icon green"><i class="fas fa-truck"></i></div>
                                <div><h6>Delivery Information</h6><small>Shipping and payment terms</small></div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label class="form-label">Place of Delivery</label><input type="text" id="in-place-delivery" class="form-control" placeholder="e.g. Regional Office, Legazpi City"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Date of Delivery</label><input type="text" id="in-date-delivery" class="form-control" placeholder="e.g. Within 15 calendar days"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Delivery Term</label><input type="text" id="in-delivery-term" class="form-control" placeholder="e.g. FOB Destination"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Payment Term</label><input type="text" id="in-payment-term" class="form-control" placeholder="e.g. Net 30 days upon delivery"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Signatories -->
                    <div class="step-panel" data-panel="3">
                        <div class="custom-card">
                            <div class="section-header">
                                <div class="section-icon purple"><i class="fas fa-signature"></i></div>
                                <div><h6>Authorized Signatories</h6><small>Officials who will sign the PO</small></div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label class="form-label">Authorized Official <span class="text-danger">*</span></label><input type="text" id="in-auth-name" class="form-control" value="GILBERT T. SADSAD" required></div>
                                <div class="col-12 col-md-6"><label class="form-label">Official Designation <span class="text-danger">*</span></label><input type="text" id="in-auth-designation" class="form-control fw-bold" value="REGIONAL DIRECTOR" required></div>
                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-12 col-md-6"><label class="form-label">Chief Accountant <span class="text-danger">*</span></label><input type="text" id="in-acc-name" class="form-control" placeholder="Full Name" required></div>
                                <div class="col-12 col-md-6"><label class="form-label">Accountant Designation <span class="text-danger">*</span></label><input type="text" id="in-acc-designation" class="form-control fw-bold" value="ACCOUNTANT II" required></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Items -->
                    <div class="step-panel" data-panel="4">
                        <div class="custom-card" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                            <!-- Global Issuance Toggle -->
                            <div class="global-issuance-bar">
                                <span class="label"><i class="fas fa-globe me-1"></i> Default Issuance Mode:</span>
                                <div class="fulfillment-toggle">
                                    <button type="button" class="toggle-btn active-inventory" id="globalInventoryBtn" onclick="setGlobalIssuance('procurement_stock')"><i class="fas fa-warehouse"></i> For Inventory</button>
                                    <button type="button" class="toggle-btn" id="globalDirectBtn" onclick="setGlobalIssuance('direct_issuance')"><i class="fas fa-truck"></i> All Direct Issuance</button>
                                </div>
                                <small class="text-muted ms-2">Sets default for new items. Override per-item below.</small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="section-header mb-0">
                                    <div class="section-icon orange"><i class="fas fa-boxes-stacked"></i></div>
                                    <div><h6>Ordered Items</h6><small id="itemCountLabel">No items added</small></div>
                                </div>
                                <button type="button" id="addItemBtn" class="btn btn-add-item" onclick="window.addEmptyItemRow()"><i class="fa-solid fa-plus me-1"></i> Add Item</button>
                            </div>
                            <div id="itemsContainer">
                                <div class="empty-items-state" id="emptyItemsState"><i class="fas fa-inbox d-block"></i><p>No items yet. Click <strong>"Add Item"</strong> to start building your purchase order.</p></div>
                            </div>
                            <div class="grand-total-bar" id="grandTotalBar" style="display: none;">
                                <div><div class="total-label">GRAND TOTAL</div><div style="font-size: 0.78rem; opacity: 0.6;" id="grandTotalWords">-</div></div>
                                <div class="total-amount" id="grandTotalAmount">₱0.00</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-between">
                <div><button type="button" class="btn btn-outline-secondary px-3 fw-bold" id="prevStepBtn" onclick="poWizard.prev()" style="display: none;"><i class="fas fa-arrow-left me-1"></i> Back</button></div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-outline-primary px-4 fw-bold" id="nextStepBtn" onclick="poWizard.next()">Next <i class="fas fa-arrow-right ms-1"></i></button>
                    <button type="submit" form="poForm" class="btn btn-primary px-4 fw-bold shadow-sm" id="savePoBtn" style="display: none;"><i class="fa-solid fa-save me-2"></i>Save Purchase Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade no-print" id="recordDeliveryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.12);">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-primary m-0 d-flex align-items-center gap-2"><i class="fas fa-truck-loading"></i> Record Delivery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="recordDeliveryForm">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" id="dr_po_item_id">
                    <div class="mb-3 bg-light border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><span class="text-muted small">Item</span><br><strong id="dr_item_desc" class="text-dark">-</strong></div>
                            <div class="text-end"><span class="text-muted small">Delivered so far</span><br><strong id="dr_item_progress" class="text-primary">-</strong></div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label">DR Number <span class="text-danger">*</span></label><input type="text" id="dr_number" class="form-control" placeholder="e.g. DR-2026-001" required></div>
                        <div class="col-6"><label class="form-label">DR Date <span class="text-danger">*</span></label><input type="date" id="dr_date" class="form-control" required></div>
                        <div class="col-6"><label class="form-label">Quantity Delivered <span class="text-danger">*</span></label><input type="number" id="dr_quantity" class="form-control" min="1" placeholder="0" required></div>
                        <div class="col-6"><label class="form-label">Unit Price</label><div class="input-group"><span class="input-group-text bg-light">₱</span><input type="number" step="0.01" id="dr_unit_price" class="form-control" placeholder="Auto from PO"></div></div>
                        <div class="col-12" id="dr_office_wrapper" style="display:none;"><label class="form-label">Requesting Office <span class="text-danger">*</span></label><input type="text" id="dr_requesting_office" class="form-control" placeholder="Office name"></div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top px-4 py-3">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fas fa-check me-1"></i> Save Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/* Global issuance mode */
window.globalIssuanceMode = 'procurement_stock';
window.setGlobalIssuance = function(mode) {
    window.globalIssuanceMode = mode;
    const invBtn = document.getElementById('globalInventoryBtn');
    const dirBtn = document.getElementById('globalDirectBtn');
    if (mode === 'procurement_stock') {
        invBtn.className = 'toggle-btn active-inventory';
        dirBtn.className = 'toggle-btn';
    } else {
        invBtn.className = 'toggle-btn';
        dirBtn.className = 'toggle-btn active-direct';
    }
    // Apply to all existing items
    document.querySelectorAll('.item-row').forEach(row => {
        const toggleBtns = row.querySelectorAll('.fulfillment-toggle .toggle-btn');
        if (toggleBtns.length >= 2) {
            toggleBtns.forEach(b => b.classList.remove('active-inventory', 'active-direct'));
            if (mode === 'procurement_stock') {
                toggleBtns[0].classList.add('active-inventory');
            } else {
                toggleBtns[1].classList.add('active-direct');
            }
        }
        row.querySelector('.source-type-select').value = mode;
        updateItemCardVisuals(row);
        // Show/hide office wrapper
        const wrapper = row.querySelector('.office-wrapper');
        if (wrapper) wrapper.style.display = mode === 'direct_issuance' ? '' : 'none';
        if (mode === 'direct_issuance') {
            loadReferralOptions(row, row.querySelector('.supply-select')?.value || '', '', '', '');
        }
    });
};

window.poWizard = {
    currentStep: 1, totalSteps: 4,
    goToStep(step) { if (step < 1 || step > this.totalSteps) return; this.currentStep = step; this.render(); },
    next() { if (this.currentStep < this.totalSteps) { this.currentStep++; this.render(); } },
    prev() { if (this.currentStep > 1) { this.currentStep--; this.render(); } },
    render() {
        document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
        const ap = document.querySelector('.step-panel[data-panel="' + this.currentStep + '"]');
        if (ap) ap.classList.add('active');
        document.querySelectorAll('.po-stepper .step').forEach(s => {
            const sn = parseInt(s.dataset.step);
            s.classList.remove('active', 'completed');
            if (sn === this.currentStep) s.classList.add('active');
            else if (sn < this.currentStep) s.classList.add('completed');
        });
        document.querySelectorAll('.po-stepper .step-divider').forEach((d, i) => { d.classList.toggle('active', i < this.currentStep - 1); });
        document.getElementById('prevStepBtn').style.display = this.currentStep > 1 ? '' : 'none';
        document.getElementById('nextStepBtn').style.display = this.currentStep < this.totalSteps ? '' : 'none';
        document.getElementById('savePoBtn').style.display = this.currentStep === this.totalSteps ? '' : 'none';
    }
};

function updateItemCount() {
    const count = document.querySelectorAll('.item-row').length;
    const label = document.getElementById('itemCountLabel');
    const emptyState = document.getElementById('emptyItemsState');
    const totalBar = document.getElementById('grandTotalBar');
    if (label) label.textContent = count === 0 ? 'No items added' : count + ' item' + (count !== 1 ? 's' : '') + ' in this order';
    if (emptyState) emptyState.style.display = count === 0 ? '' : 'none';
    if (totalBar) totalBar.style.display = count > 0 ? '' : 'none';
    document.querySelectorAll('.item-card .item-number').forEach((el, i) => { el.textContent = i + 1; });
    recalcGrandTotal();
}

function recalcGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const q = parseFloat(row.querySelector('.qty-input').value) || 0;
        const c = parseFloat(row.querySelector('.cost-input').value) || 0;
        total += q * c;
    });
    const totalEl = document.getElementById('grandTotalAmount');
    const wordsEl = document.getElementById('grandTotalWords');
    if (totalEl) totalEl.textContent = '\u20B1' + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
    if (wordsEl) wordsEl.textContent = numberToWords(total) + ' PESOS ONLY';
}

// autoUpdatePoStatus is defined in index.blade.php; updateItemCount is called from there

/* Update visual border class on an item card based on type + fulfillment */
window.updateItemCardVisuals = function(row) {
    const typeSel = row.querySelector('.item-type-select');
    const srcSel = row.querySelector('.source-type-select');
    if (!typeSel || !srcSel) return;
    row.classList.remove('supply-card', 'asset-card', 'direct-card');
    if (typeSel.value === 'asset') {
        row.classList.add('asset-card');
    } else if (srcSel.value === 'direct_issuance') {
        row.classList.add('direct-card');
    } else {
        row.classList.add('supply-card');
    }
    // Update subtitle
    const subtitle = row.querySelector('.item-subtitle');
    if (subtitle) {
        if (typeSel.value === 'asset') subtitle.textContent = 'Asset / Equipment';
        else if (srcSel.value === 'direct_issuance') subtitle.textContent = 'Supply — Direct Issuance';
        else subtitle.textContent = 'Supply — For Inventory';
    }
};

/* Build grouped supply <optgroup> HTML */
window.buildGroupedSupplyOptions = function(selectedId) {
    const supplies = window.SUPPLIES_LIST || [];
    if (supplies.length === 0) return '<option value="">— No supplies available —</option>';
    // Group by article
    const groups = {};
    supplies.forEach(s => {
        const key = s.article || 'Other';
        if (!groups[key]) groups[key] = [];
        groups[key].push(s);
    });
    let html = '<option value="">— Not linked —</option>';
    Object.keys(groups).sort().forEach(article => {
        html += '<optgroup label="' + article.replace(/"/g, '&quot;') + '">';
        groups[article].forEach(s => {
            const label = (s.classification || s.description) + ' (' + s.unit_measure + ')';
            html += '<option value="' + s.id + '"' + (String(selectedId) === String(s.id) ? ' selected' : '') + '>' + label.replace(/</g, '&lt;') + '</option>';
        });
        html += '</optgroup>';
    });
    return html;
};

/* Show inventory destination preview based on selected supply */
window.updateInventoryPreview = function(row) {
    const wrapper = row.querySelector('.inventory-preview-wrapper');
    if (!wrapper) return;
    const supplyId = row.querySelector('.supply-select')?.value;
    if (!supplyId) { wrapper.innerHTML = ''; return; }
    const supply = (window.SUPPLIES_LIST || []).find(s => String(s.id) === String(supplyId));
    if (!supply) { wrapper.innerHTML = ''; return; }
    wrapper.innerHTML = '<div class="inventory-preview"><i class="fas fa-arrow-right"></i> Will be added to: <strong>' + (supply.article || '—') + ' &rsaquo; ' + (supply.classification || supply.description || '—') + '</strong></div>';
};
</script>
