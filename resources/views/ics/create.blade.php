<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Request Form - DepEd ROV</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
            overflow-x: hidden;
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
            display: block;
        }

        .form-control, .form-select { 
            border-radius: 8px; 
            border: 1px solid #ced4da;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .form-control[readonly] {
            background-color: var(--light-bg);
        }

        .btn-print { background-color: #607d8b; color: white; }
        .btn-submit { background-color: var(--deped-blue); color: white; font-weight: 600; }

        .sig-line {
            border: none;
            border-bottom: 2px solid #333;
            border-radius: 0;
            font-weight: bold;
            background: transparent;
            text-align: center;
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
            display: block;
        }

        .item-row {
            position: relative;
            padding-top: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .btn-remove-row {
            color: #dc3545;
            cursor: pointer;
            font-size: 0.8rem;
            text-decoration: none;
            float: right;
            margin-top: -5px;
        }

        .btn-remove-row:hover { text-decoration: underline; }

        .category-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
            padding: 8px 12px;
            border-radius: 8px;
            background: var(--light-bg);
            border: 1px solid #eee;
        }

        .custom-radio {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 500;
            color: #444;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .custom-radio input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--deped-gold);
            cursor: pointer;
        }

        .select2-container .select2-selection--single {
            height: 40px !important;
            border-radius: 8px !important;
            border: 1px solid #ced4da !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        #print-area { display: none; }

        @media print {
            @page { size: A4 portrait; margin: 10mm; }

            body { background: white !important; margin: 0; padding: 0; }
            body * { visibility: hidden; }
            
            .no-print, .sidebar, .top-bar, .main-content, .btn, .btn-remove-row, .select2-container { 
                display: none !important; 
                margin: 0 !important;
                padding: 0 !important;
            }
            
            #print-area, #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
                width: 100%;
                color: black;
                font-family: Arial, sans-serif;
                font-size: 10pt;
                display: block;
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

        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    @include('layouts.header')
    @include('layouts.sidebar')

    <div class="main-content no-print">
        
        <div class="top-bar no-print">
            <div><i class="fa-solid fa-building-shield me-2"></i> <strong>DEPED REGION V - ASSET SYSTEM</strong></div>
            <div id="clock-display"><i class="fa-regular fa-clock me-2"></i> Loading time...</div>
        </div>

        <form action="{{ url('/ics') }}" method="POST" id="icsForm" class="no-print">
            @csrf
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold m-0" id="form-main-title" style="color: var(--deped-blue);">INVENTORY CUSTODIAN SLIP</h3>
                    <p class="text-muted small">Appendix 63 - Government Accounting Manual</p>
                </div>
                <div class="no-print">
                    <button type="button" class="btn btn-print me-2 shadow-sm" onclick="prepareAndPrint()">
                        <i class="fa-solid fa-print me-1"></i> Print PDF
                    </button>
                    <button type="submit" class="btn btn-submit shadow-sm">
                        <i class="fa-solid fa-paper-plane me-1"></i> Save Form
                    </button>
                </div>
            </div>

            <div class="section-box">
                <h6 class="section-title"><i class="fa-solid fa-circle-info"></i> General Information</h6>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label>Entity Name</label>
                        <input type="text" class="form-control" value="Department of Education ROV" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Fund Cluster</label>
                        <input type="text" name="fund_cluster" id="fund_cluster" class="form-control" placeholder="e.g. 01">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label>Reference Type</label>
                        <select name="po_type" id="po_type" class="form-select">
                            <option value="P.O. No.">P.O. No.</option>
                            <option value="Contract No.">Contract No.</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Reference Number (Optional)</label>
                        <input type="text" name="po_no" id="po_no" class="form-control" placeholder="e.g. 12345">
                    </div>
                    <div class="col-md-4">
                        <label>Reference Date (Optional)</label>
                        <input type="date" name="po_date" id="po_date" class="form-control">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Category<span class="text-danger">*</span></label>
                        <div class="category-group">
                            <label class="custom-radio">
                                <input type="checkbox" name="item_category" value="PPE" onclick="selectOnlyThis(this)"> PPE (PAR)
                            </label>
                            <label class="custom-radio">
                                <input type="checkbox" name="item_category" value="High - Valued" onclick="selectOnlyThis(this)"> High - Valued (ICS)
                            </label>
                            <label class="custom-radio">
                                <input type="checkbox" name="item_category" value="Low - Valued" onclick="selectOnlyThis(this)" checked> Low - Valued (ICS)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label id="number_label">ICS Number</label>
                        <input type="text" name="ics_no" id="ics_no" class="form-control fw-bold text-danger" value="{{ $splvNumber }}" readonly>
                        <small class="text-muted" style="font-size: 0.7rem;">Auto-generates based on Category.</small>
                    </div>
                </div>
            </div>

            <div class="section-box requisition-block">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="section-title mb-0 border-0 pb-0"><i class="fa-solid fa-list-check"></i> Item Details</h6>
                </div>

                <div id="items-container">
                    <div class="item-row">
                        <a href="javascript:void(0)" class="btn-remove-row no-print" onclick="removeRow(this)"><i class="fa-solid fa-trash-can"></i> Remove Item</a>
                        
                        <div class="row g-3 mb-2 mt-1">
                            <div class="col-md-2">
                                <label>Quantity<span class="text-danger">*</span></label>
                                <input type="number" name="qty[]" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-md-2">
                                <label>Unit<span class="text-danger">*</span></label>
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
                                <label>Article (Name)<span class="text-danger">*</span></label>
                                <input type="text" name="article[]" class="form-control article-input" required placeholder="e.g. Laptop">
                            </div>
                            <div class="col-md-5">
                                <label>Item Description <small class="text-muted fw-normal">(Search assets)</small></span></label>
                                <select name="desc[]" class="form-control asset-select" >
                                    <option value=""></option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->description }}" 
                                                data-article="{{ $asset->article }}"
                                                data-unit="{{ $asset->unit_measure }}" 
                                                data-cost="{{ $asset->unit_value }}" 
                                                data-prop="{{ $asset->barcode_id }}">
                                            {{ $asset->article }} - {{ $asset->description }} [{{ $asset->barcode_id }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label>Specifications (Optional)</label>
                                <input type="text" name="specs[]" class="form-control" placeholder="e.g. Core i5, 8GB RAM, 256GB SSD, Windows 11...">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="lbl-inv-prop">Inventory Item No.</label>
                                <input type="text" name="inv_no[]" class="form-control" placeholder="Enter Item No.">
                            </div>
                            <div class="col-md-4">
                                <label class="lbl-est-date">Estimated Useful Life</label>
                                <input type="text" name="est_life[]" class="form-control est-date-input" placeholder="e.g. 5 Years">
                            </div>
                            <div class="col-md-2 col-unit-cost">
                                <label>Unit Cost<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="unit_cost[]" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-2 col-total-cost">
                                <label class="lbl-amount">Total Cost</label>
                                <input type="number" step="0.01" name="total_cost[]" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="no-print mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addMoreItem()">
                        <i class="fa-solid fa-plus me-1"></i> Add Another Item
                    </button>
                </div>
            </div>

            <div class="section-box">
                <h6 class="section-title"><i class="fa-solid fa-file-signature"></i> Signatures</h6>

                <div class="row text-center g-4 mb-4 mt-2">
                    <div class="col-md-6 border-end">
                        <label class="d-block mb-3 text-uppercase small text-muted" id="sig_left_label">Received From</label>
                        <input type="text" name="sig_from_name" id="sig_from_name" class="form-control sig-line" placeholder="Printed Name">
                        <input type="text" name="sig_from_pos" id="sig_from_pos" class="form-control desig-input mt-2 mb-3" placeholder="Position / Title">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="d-block mb-3 text-uppercase small text-muted" id="sig_right_label">Received By</label>
                        <input type="text" name="sig_by_name" id="sig_by_name" class="form-control sig-line" placeholder="Printed Name">
                        <input type="text" name="sig_by_pos" id="sig_by_pos" class="form-control desig-input mt-2 mb-3" placeholder="Position / Title">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="print-area">
        <div style="text-align: center; margin-bottom: 15px;">
            <img src="{{ asset('assets/images/DepEdseal.png') }}" style="width: 70px; margin: 0 auto 5px auto; display: block;">
            <div style="font-size: 9pt; font-family: 'Times New Roman', Times, serif;">Republic of the Philippines</div>
            <div style="font-size: 20pt; font-family: 'Old English Text MT', 'Engravers Old English', serif; line-height: 1.1;">Department of Education</div>
            <div style="font-size: 10pt; font-family: 'Times New Roman', Times, serif; text-transform: uppercase;">REGION V - BICOL</div>
            <div id="print_doc_title" style="font-size: 12pt; font-weight: bold; margin-top: 15px;">INVENTORY CUSTODIAN SLIP</div>
            <div id="p_category_value" style="font-size: 12pt; color: #777; font-weight: bold; margin-top: -2px; min-height: 20px; display: none;">Value</div>
        </div>

        <table style="width: 100%; border: none; font-size: 10.5pt; margin-bottom: 5px;">
            <tr>
                <td style="width: 15%; white-space: nowrap;">Entity Name:</td>
                <td style="width: 45%; border-bottom: 0px solid black;" id="p_entity_name">Department of Education ROV</td>
                <td id="print_doc_label" style="width: 15%; text-align: right; padding-right: 10px; white-space: nowrap;">ICS No:</td>
                <td style="width: 25%; border-bottom: 1px solid black; font-weight: bold;" id="p_ics_no"></td>
            </tr>
            <tr>
                <td style="width: 15%; white-space: nowrap;">Fund Cluster:</td>
                <td style="width: 45%; border-bottom: 1px solid black;" id="p_fund_cluster"></td>
                <td colspan="2"></td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 10pt;">
            <thead id="p_table_head"></thead>
            <tbody id="p_items_body"></tbody>
            <tfoot id="p_table_foot"></tfoot>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        const parNo = "{{ $parNumber }}";
        const sphvNo = "{{ $sphvNumber }}";
        const splvNo = "{{ $splvNumber }}";

        let isPARMode = false;

        function updateDate() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const clockEl = document.getElementById('clock-display');
            if(clockEl) clockEl.innerHTML = '<i class="fa-regular fa-calendar-check me-2"></i> ' + now.toLocaleDateString('en-US', options);
        }
        setInterval(updateDate, 1000);
        updateDate();

        document.getElementById('icsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Submit Request?',
                text: "Are you sure you want to save this form and generate the document?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a237e',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, save it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        function initSelect2() {
            $('.asset-select').select2({
                tags: true, 
                placeholder: "- Search by Name, Property No, or Description -",
                width: '100%'
            });
        }

        $(document).on('change', '.asset-select', function() {
            let selectedOption = $(this).find('option:selected');
            let row = $(this).closest('.item-row');
            
            if (!selectedOption.val()) return;

            let unit = selectedOption.data('unit');
            let cost = selectedOption.data('cost');
            let prop = selectedOption.data('prop');
            let article = selectedOption.data('article');
            
            if (unit) row.find('select[name="unit[]"]').val(unit.toLowerCase());
            if (prop) row.find('input[name="inv_no[]"]').val(prop);
            if (article) row.find('input[name="article[]"]').val(article);
            
            let qtyInput = row.find('input[name="qty[]"]');
            qtyInput.val(1);
            
            if (cost) {
                let costInput = row.find('input[name="unit_cost[]"]');
                costInput.val(parseFloat(cost).toFixed(2));
                
                if (!isPARMode) {
                    let totalCost = row.find('input[name="total_cost[]"]');
                    totalCost.val((1 * parseFloat(cost)).toFixed(2));
                }
            }
        });

        function addMoreItem() {
            $('.asset-select').select2('destroy');
            
            const container = document.getElementById('items-container');
            const rows = container.querySelectorAll('.item-row');
            const firstRow = rows[0];
            const newRow = firstRow.cloneNode(true);
            
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            newRow.querySelectorAll('select').forEach(select => $(select).val(null));
            
            container.appendChild(newRow);
            
            initSelect2();
            toggleFields(isPARMode);
        }

        function removeRow(link) {
            const container = document.getElementById('items-container');
            const rows = container.querySelectorAll('.item-row');
            if (rows.length > 1) {
                if (confirm("Are you sure you want to delete this item?")) {
                    link.closest('.item-row').remove();
                }
            } else {
                alert("The form must have at least one item.");
            }
        }

        let selectedCategoryValue = 'Low - Valued'; 
        
        function toggleFields(isPAR) {
            isPARMode = isPAR;
            
            document.querySelectorAll('.lbl-inv-prop').forEach(el => el.innerText = isPAR ? 'Property Number' : 'Inventory Item No.');
            
            document.querySelectorAll('.lbl-est-date').forEach(el => {
                el.innerText = isPAR ? 'Date Acquired' : 'Estimated Useful Life';
            });
            
            document.querySelectorAll('.est-date-input').forEach(el => {
                el.type = isPAR ? 'date' : 'text';
                el.placeholder = isPAR ? '' : 'e.g. 5 Years';
            });
            
            document.querySelectorAll('.col-unit-cost').forEach(el => el.style.display = isPAR ? 'none' : 'block');
            document.querySelectorAll('.lbl-amount').forEach(el => el.innerText = isPAR ? 'Amount' : 'Total Cost');
            
            document.querySelectorAll('.col-total-cost').forEach(el => {
                el.className = isPAR ? 'col-md-4 col-total-cost' : 'col-md-2 col-total-cost';
            });

            document.querySelectorAll('input[name="total_cost[]"]').forEach(el => {
                if (isPAR) {
                    el.removeAttribute('readonly');
                    el.style.backgroundColor = '';
                } else {
                    el.setAttribute('readonly', true);
                    el.style.backgroundColor = 'var(--light-bg)';
                    
                    const row = el.closest('.item-row');
                    const qty = parseFloat(row.querySelector('input[name="qty[]"]').value) || 0;
                    const unitCost = parseFloat(row.querySelector('input[name="unit_cost[]"]').value) || 0;
                    if (qty > 0 && unitCost > 0) {
                        el.value = (qty * unitCost).toFixed(2);
                    } else {
                        el.value = '';
                    }
                }
            });

            document.getElementById('sig_left_label').innerText = isPAR ? 'RECEIVED BY' : 'RECEIVED FROM';
            document.getElementById('sig_right_label').innerText = isPAR ? 'ISSUED BY' : 'RECEIVED BY';
        }

        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name="qty[]"], input[name="unit_cost[]"]')) {
                if (isPARMode) return; 
                
                const row = e.target.closest('.item-row');
                const qty = parseFloat(row.querySelector('input[name="qty[]"]').value) || 0;
                const unitCost = parseFloat(row.querySelector('input[name="unit_cost[]"]').value) || 0;
                const totalCost = row.querySelector('input[name="total_cost[]"]');
                
                if (qty > 0 && unitCost > 0) {
                    totalCost.value = (qty * unitCost).toFixed(2);
                } else {
                    totalCost.value = '';
                }
            }
        });

        function selectOnlyThis(clickedCheckbox) {
            let checkboxes = document.getElementsByName('item_category');
            checkboxes.forEach((item) => {
                if (item !== clickedCheckbox) item.checked = false;
            });
            
            if(!clickedCheckbox.checked) {
                document.querySelector('input[value="Low - Valued"]').checked = true;
                clickedCheckbox = document.querySelector('input[value="Low - Valued"]');
            }
            
            let labelEl = document.getElementById('number_label');
            let inputEl = document.getElementById('ics_no');
            let printTitle = document.getElementById('print_doc_title');
            let printLabel = document.getElementById('print_doc_label');
            let mainFormTitle = document.getElementById('form-main-title');
            
            selectedCategoryValue = clickedCheckbox.value;
            
            if (clickedCheckbox.value === 'PPE') {
                labelEl.innerText = 'PAR Number';
                inputEl.value = parNo;
                printTitle.innerText = 'PROPERTY ACKNOWLEDGMENT RECEIPT';
                mainFormTitle.innerText = 'PROPERTY ACKNOWLEDGMENT RECEIPT';
                printLabel.innerText = 'PAR No:';
                
                toggleFields(true);
                
                document.getElementById('sig_from_name').value = 'SALVADOR DEYTO JR.';
                document.getElementById('sig_from_pos').value = 'ITO';
                document.getElementById('sig_by_name').value = 'JEFFREY PAGATPAT';
                document.getElementById('sig_by_pos').value = 'Administrative Officer V- AMS';
                
            } else if (clickedCheckbox.value === 'High - Valued') {
                labelEl.innerText = 'ICS Number';
                inputEl.value = sphvNo;
                printTitle.innerText = 'INVENTORY CUSTODIAN SLIP';
                mainFormTitle.innerText = 'INVENTORY CUSTODIAN SLIP';
                printLabel.innerText = 'ICS No:';
                
                toggleFields(false);
                clearSignatures();
                
            } else if (clickedCheckbox.value === 'Low - Valued') {
                labelEl.innerText = 'ICS Number';
                inputEl.value = splvNo;
                printTitle.innerText = 'INVENTORY CUSTODIAN SLIP';
                mainFormTitle.innerText = 'INVENTORY CUSTODIAN SLIP';
                printLabel.innerText = 'ICS No:';
                
                toggleFields(false);
                clearSignatures();
            }
        }
        
        function clearSignatures() {
            document.getElementById('sig_from_name').value = '';
            document.getElementById('sig_from_pos').value = '';
            document.getElementById('sig_by_name').value = '';
            document.getElementById('sig_by_pos').value = '';
        }

        document.addEventListener("DOMContentLoaded", () => {
            initSelect2();
            toggleFields(false); 
        });

        function prepareAndPrint() {
            const isPAR = selectedCategoryValue === 'PPE';
            
            document.getElementById('p_fund_cluster').innerText = document.getElementById('fund_cluster')?.value || '';
            document.getElementById('p_ics_no').innerText = document.getElementById('ics_no')?.value || '';

            let thead = document.getElementById('p_table_head');
            let tbody = document.getElementById('p_items_body');
            let tfoot = document.getElementById('p_table_foot');
            
            if (isPAR) {
                thead.innerHTML = `
                    <tr>
                        <th style="width: 10%; border: 1px solid black; padding: 10px 6px; text-align: center;">Quantity</th>
                        <th style="width: 10%; border: 1px solid black; padding: 10px 6px; text-align: center;">Unit</th>
                        <th style="width: 35%; border: 1px solid black; padding: 10px 6px; text-align: center;">Description</th>
                        <th style="width: 15%; border: 1px solid black; padding: 10px 6px; text-align: center;">Property<br>Number</th>
                        <th style="width: 15%; border: 1px solid black; padding: 10px 6px; text-align: center;">Date<br>Acquired</th>
                        <th style="width: 15%; border: 1px solid black; padding: 10px 6px; text-align: center;">Amount</th>
                    </tr>
                `;
            } else {
                thead.innerHTML = `
                    <tr>
                        <th rowspan="2" style="width: 8%; border: 1px solid black; padding: 4px; text-align: center;">Quantity</th>
                        <th rowspan="2" style="width: 8%; border: 1px solid black; padding: 4px; text-align: center;">Unit</th>
                        <th colspan="2" style="width: 20%; border: 1px solid black; padding: 4px; text-align: center;">Amount</th>
                        <th rowspan="2" style="width: 34%; border: 1px solid black; padding: 4px; text-align: center;">Description</th>
                        <th rowspan="2" style="width: 15%; border: 1px solid black; padding: 4px; text-align: center;">Inventory<br>Item Nos.</th>
                        <th rowspan="2" style="width: 15%; border: 1px solid black; padding: 4px; text-align: center;">Estimated<br>Useful Life</th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; padding: 4px; text-align: center;">Unit Cost</th>
                        <th style="border: 1px solid black; padding: 4px; text-align: center;">Total Cost</th>
                    </tr>
                `;
            }
            
            tbody.innerHTML = ''; 
            let qtys = document.getElementsByName('qty[]');
            let units = document.getElementsByName('unit[]');
            let articles = document.getElementsByName('article[]');
            let descs = document.getElementsByName('desc[]');
            let specsList = document.getElementsByName('specs[]');
            let uCosts = document.getElementsByName('unit_cost[]');
            let tCosts = document.getElementsByName('total_cost[]');
            let invs = document.getElementsByName('inv_no[]');
            let ests = document.getElementsByName('est_life[]');

            let rowsAdded = 0;
            for(let i = 0; i < qtys.length; i++) {
                if(qtys[i].value || units[i].value || descs[i].value) {
                    
                    let artVal = articles[i] ? articles[i].value : '';
                    let descVal = descs[i] ? descs[i].value : '';
                    let specsVal = (specsList[i] && specsList[i].value) ? `<br><span style="font-size: 8pt; color: #555;">Specs: ${specsList[i].value}</span>` : '';
                    let fullDesc = `<strong>${artVal}</strong><br>${descVal}${specsVal}`;

                    if (isPAR) {
                        tbody.innerHTML += `
                            <tr>
                                <td style="border: 1px solid black; padding: 6px; text-align: center;">${qtys[i].value}</td>
                                <td style="border: 1px solid black; padding: 6px; text-align: center;">${units[i].value}</td>
                                <td style="border: 1px solid black; padding: 6px; text-align: left; vertical-align: top;">${fullDesc}</td>
                                <td style="border: 1px solid black; padding: 6px; text-align: center;">${invs[i].value}</td>
                                <td style="border: 1px solid black; padding: 6px; text-align: center;">${ests[i].value}</td>
                                <td style="border: 1px solid black; padding: 6px; text-align: right;">${tCosts[i].value}</td>
                            </tr>
                        `;
                    } else {
                        tbody.innerHTML += `
                            <tr>
                                <td style="border: 1px solid black; padding: 4px; text-align: center;">${qtys[i].value}</td>
                                <td style="border: 1px solid black; padding: 4px; text-align: center;">${units[i].value}</td>
                                <td style="border: 1px solid black; padding: 4px; text-align: right;">${uCosts[i].value}</td>
                                <td style="border: 1px solid black; padding: 4px; text-align: right;">${tCosts[i].value}</td>
                                <td style="border: 1px solid black; padding: 4px; text-align: left; vertical-align: top;">${fullDesc}</td>
                                <td style="border: 1px solid black; padding: 4px; text-align: center;">${invs[i].value}</td>
                                <td style="border: 1px solid black; padding: 4px; text-align: center;">${ests[i].value}</td>
                            </tr>
                        `;
                    }
                    rowsAdded++;
                }
            }

            let poType = document.getElementById('po_type')?.value || 'P.O. No.';
            let poNo = document.getElementById('po_no')?.value || '';
            let poDate = document.getElementById('po_date')?.value || '';
            
            let formattedDate = '';
            if (poDate) {
                let d = new Date(poDate);
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                formattedDate = monthNames[d.getMonth()] + ' ' + String(d.getDate()).padStart(2, '0') + ', ' + d.getFullYear();
            }
            let poText = poNo ? `${poType} ${poNo} ${formattedDate}` : '';

            let minRows = isPAR ? 10 : 15; 
            for(let i = rowsAdded; i < minRows; i++) {
                let isLast = (i === minRows - 1);
                let descText = (isLast && poText) ? `<div style='margin-top: 10px; font-weight: bold;'>${poText}</div>` : "&nbsp;";
                let borderStyle = isLast 
                    ? "border-left: 1px solid black; border-right: 1px solid black; border-top: none; border-bottom: 1px solid black;" 
                    : "border-left: 1px solid black; border-right: 1px solid black; border-top: none; border-bottom: none;";

                if (isPAR) {
                    tbody.innerHTML += `
                        <tr>
                            <td style="${borderStyle} padding: 10px 6px;">&nbsp;</td>
                            <td style="${borderStyle} padding: 10px 6px;"></td>
                            <td style="${borderStyle} padding: 10px 6px; text-align: left; vertical-align: bottom;">${descText}</td>
                            <td style="${borderStyle} padding: 10px 6px;"></td>
                            <td style="${borderStyle} padding: 10px 6px;"></td>
                            <td style="${borderStyle} padding: 10px 6px;"></td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML += `
                        <tr>
                            <td style="${borderStyle} padding: 6px;">&nbsp;</td>
                            <td style="${borderStyle} padding: 6px;"></td>
                            <td style="${borderStyle} padding: 6px;"></td>
                            <td style="${borderStyle} padding: 6px;"></td>
                            <td style="${borderStyle} padding: 6px; text-align: left; vertical-align: bottom;">${descText}</td>
                            <td style="${borderStyle} padding: 6px;"></td>
                            <td style="${borderStyle} padding: 6px;"></td>
                        </tr>
                    `;
                }
            }

            let leftLabel = isPAR ? 'Received by:' : 'Received from:';
            let rightLabel = isPAR ? 'Issued by:' : 'Received by:';
            
            let accountabilityText = isPAR ? `
                <tr>
                    <td colspan="6" style="border: 1px solid black; padding: 8px 12px; font-size: 8.5pt; text-align: justify; line-height: 1.4;">
                        Accountability over Property, Plant and Equipment (PPE). Property Acknowledgment Receipt shall be issued to end-user of Property, Plant and Equipment to establish accountability. Accountability shall be extinguished upon return of the item to the Assets Management Section (AMS) or in case of loss, upon approval of the relief from property accountability.
                    </td>
                </tr>
            ` : '';

            let colspanLeft = isPAR ? 3 : 4;
            let colspanRight = isPAR ? 3 : 3;

            tfoot.innerHTML = `
                ${accountabilityText}
                <tr>
                    <td colspan="${colspanLeft}" style="border: 1px solid black; padding: 5px 5px 0px 5px; text-align: left; font-size: 9pt;">${leftLabel}</td>
                    <td colspan="${colspanRight}" style="border: 1px solid black; padding: 5px 5px 0px 5px; text-align: left; font-size: 9pt;">${rightLabel}</td>
                </tr>
                <tr>
                    <td colspan="${colspanLeft}" style="border: 1px solid black; border-bottom: none; border-top: none; padding: 15px 5px 0px 5px; text-align: center;">
                        <span style="display:inline-block; width: 75%; border-bottom: 1px solid black; min-height: 15px; font-weight: bold; font-size: 11pt; text-transform: uppercase;">${document.getElementById('sig_from_name').value}</span><br>
                        <span style="font-size: 8pt;">Signature over Printed Name</span>
                    </td>
                    <td colspan="${colspanRight}" style="border: 1px solid black; border-bottom: none; border-top: none; padding: 15px 5px 0px 5px; text-align: center;">
                        <span style="display:inline-block; width: 75%; border-bottom: 1px solid black; min-height: 15px; font-weight: bold; font-size: 11pt; text-transform: uppercase;">${document.getElementById('sig_by_name').value}</span><br>
                        <span style="font-size: 8pt;">Signature over Printed Name of Supply/Property Officer</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="${colspanLeft}" style="border: 1px solid black; border-top: none; border-bottom: none; padding: 5px 5px 0px 5px; text-align: center;">
                        <span style="display:inline-block; width: 75%; border-bottom: 1px solid black; min-height: 15px; font-weight: normal; font-size: 10pt;">${document.getElementById('sig_from_pos').value}</span><br>
                        <span style="font-size: 8pt;">Position/Office</span>
                    </td>
                    <td colspan="${colspanRight}" style="border: 1px solid black; border-top: none; border-bottom: none; padding: 5px 5px 0px 5px; text-align: center;">
                        <span style="display:inline-block; width: 75%; border-bottom: 1px solid black; min-height: 15px; font-weight: normal; font-size: 10pt;">${document.getElementById('sig_by_pos').value}</span><br>
                        <span style="font-size: 8pt;">Position/Office</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="${colspanLeft}" style="border: 1px solid black; border-top: none; padding: 5px 5px 8px 5px; text-align: center;">
                        <span style="display:inline-block; width: 75%; border-bottom: 1px solid black; min-height: 15px;"></span><br>
                        <span style="font-size: 8pt;">Date</span>
                    </td>
                    <td colspan="${colspanRight}" style="border: 1px solid black; border-top: none; padding: 5px 5px 8px 5px; text-align: center;">
                        <span style="display:inline-block; width: 75%; border-bottom: 1px solid black; min-height: 15px;"></span><br>
                        <span style="font-size: 8pt;">Date</span>
                    </td>
                </tr>
            `;
            
            window.print();
        }
    </script>
</body>
</html>