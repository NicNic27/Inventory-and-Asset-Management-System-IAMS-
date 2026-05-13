<style>
    /* Modal & Form Styles */
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
            display: block !important; position: absolute; left: 0; top: 0; width: 100%;
            color: black; font-family: "Times New Roman", Times, serif; font-size: 11px;
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
        
        .acc-table { margin-top: -1px; border-top: none; }
    }
</style>

<div class="modal fade no-print" id="receivePoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary align-items-center d-flex m-0">
                    <i class="fas fa-file-invoice me-2"></i> Receive Purchase Order
                    <span id="po_type_badge" class="badge ms-3 fs-6 rounded-pill text-white" style="display: none; background-color: #101954;"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-3 p-md-4">
                
                <form id="poForm">
                    <input type="hidden" id="po_type" name="po_type" value="">

                    <div class="custom-card">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Document Details</h6>
                        <div class="row g-3">
                            <input type="hidden" id="modal_po_id" value="">
                            <div class="col-12 col-md-8">
                                <label class="form-label">Entity Name</label>
                                <input type="text" id="in-entity" class="form-control">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">PO Number <span class="text-danger">*</span></label>
                                <input type="text" id="po_no" class="form-control fw-bold" placeholder="YYYY-MM-XXXX" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" id="in-supplier" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label">Supplier Address <span class="text-danger">*</span></label>
                                <input type="text" id="in-address" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">PO Date <span class="text-danger">*</span></label>
                                <input type="date" id="in-date" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-8">
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
                            <div class="col-12 col-md-4">
                                <label class="form-label text-primary fw-bold">P.O. Status (Auto-Calculated)</label>
                                <select id="in-status" class="form-select border-primary shadow-sm fw-bold" style="background-color: #f1f5f9; pointer-events: none;" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Partial">Partial</option>
                                    <option value="Complete">Complete</option>
                                </select>
                                <small class="text-muted" style="font-size: 11px;">Check off items below to update status.</small>
                            </div>
                            
                            <div class="col-12 mt-4"><h6 class="fw-bold text-dark border-bottom pb-2">Delivery Information</h6></div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Place of Delivery</label>
                                <input type="text" id="in-place-delivery" class="form-control" placeholder="e.g. Regional Office">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Date of Delivery</label>
                                <input type="text" id="in-date-delivery" class="form-control" placeholder="e.g. Within 15 Days">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Delivery Term</label>
                                <input type="text" id="in-delivery-term" class="form-control" placeholder="e.g. FOB Destination">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Payment Term</label>
                                <input type="text" id="in-payment-term" class="form-control" placeholder="e.g. 30 Days">
                            </div>
                        </div>
                    </div>

                    <div class="custom-card">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Signatories</h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Authorized Official <span class="text-danger">*</span></label>
                                <input type="text" id="in-auth-name" class="form-control" placeholder="Full Name" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label text-primary">Official Designation <span class="text-danger">*</span></label>
                                <input type="text" id="in-auth-designation" class="form-control fw-bold" value="REGIONAL DIRECTOR" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label border-top pt-3 w-100">Chief Accountant <span class="text-danger">*</span></label>
                                <input type="text" id="in-acc-name" class="form-control" placeholder="Full Name" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label border-top pt-3 w-100 text-primary">Accountant Designation <span class="text-danger">*</span></label>
                                <input type="text" id="in-acc-designation" class="form-control fw-bold" value="ACCOUNTANT II" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                        <h6 class="fw-bold mb-0">Ordered Items</h6>
                        <button type="button" id="addItemBtn" class="btn btn-add-item px-4 fw-bold shadow-sm"><i class="fa-solid fa-plus me-1"></i> Add Item</button>
                    </div>
                    
                    <div id="itemsContainer"></div>
                </form>

            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="poForm" class="btn btn-primary px-4 fw-bold shadow-sm"><i class="fa-solid fa-save me-2"></i>Save P.O. Record</button>
            </div>
        </div>
    </div>
</div>