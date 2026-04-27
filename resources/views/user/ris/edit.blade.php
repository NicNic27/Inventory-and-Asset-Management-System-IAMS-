<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit RIS - DepEd ROV</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --deped-blue: #1a237e;
            --deped-gold: #fbc02d;
        }
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; color: #444; }
        .main-content { margin-left: 260px; padding: 20px; }
        .section-box { background: white; padding: 25px; margin-bottom: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .section-title { color: var(--deped-blue); font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee; }
        .btn-remove-row { color: #dc3545; cursor: pointer; font-size: 0.8rem; text-decoration: none; float: right; margin-top: -5px; }
        
        .select2-container--bootstrap-5 .select2-selection--single {
            border-radius: 8px !important;
            min-height: 39px !important;
            padding: 4px 0px !important;
            border-color: #ced4da !important;
        }
    </style>
</head>
<body>

    @include('layouts.user_header')
    @include('layouts.user_sidebar')

<div class="main-content">
    <form action="{{ url('/user/ris/'.$req->id.'/update') }}" method="POST">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0" style="color: #1a237e;">EDIT RIS: {{ $req->ris_no }}</h3>
            </div>
            <div>
                <a href="{{ url('/user/ris/'.$req->id) }}" class="btn btn-outline-secondary fw-bold shadow-sm"><i class="fas fa-arrow-left me-1"></i> Cancel Edit</a>
                <button type="submit" class="btn btn-success fw-bold shadow-sm"><i class="fa-solid fa-save me-1"></i> Save Changes</button>
            </div>
        </div>

        <div class="section-box">
            <h6 class="section-title"><i class="fa-solid fa-circle-info me-2 text-warning"></i> General Information</h6>
            <div class="row g-3">
                
                <div class="col-md-4">
                    <label class="fw-bold small text-muted">Office Name <span class="text-danger">*</span></label>
                    <select name="office" id="officeSelect" class="form-select" onchange="updateUnits()" required>
                        <option value="">-- Select Office --</option>
                        <option value="Administrative Division" {{ $req->office == 'Administrative Division' ? 'selected' : '' }}>Administrative Division</option>
                        <option value="Curriculum and Learning Management Division" {{ $req->office == 'Curriculum and Learning Management Division' ? 'selected' : '' }}>Curriculum and Learning Management Division</option>
                        <option value="Education Support Services Division" {{ $req->office == 'Education Support Services Division' ? 'selected' : '' }}>Education Support Services Division</option>
                        <option value="Field Technical Assistance Division" {{ $req->office == 'Field Technical Assistance Division' ? 'selected' : '' }}>Field Technical Assistance Division</option>
                        <option value="Finance Division" {{ $req->office == 'Finance Division' ? 'selected' : '' }}>Finance Division</option>
                        <option value="Human Resource Development Division" {{ $req->office == 'Human Resource Development Division' ? 'selected' : '' }}>Human Resource Development Division</option>
                        <option value="Office of the Assistant Regional Director" {{ $req->office == 'Office of the Assistant Regional Director' ? 'selected' : '' }}>Office of the Assistant Regional Director</option>
                        <option value="Office of the Regional Director" {{ $req->office == 'Office of the Regional Director' ? 'selected' : '' }}>Office of the Regional Director</option>
                        <option value="Policy Planning and Research Division" {{ $req->office == 'Policy Planning and Research Division' ? 'selected' : '' }}>Policy Planning and Research Division</option>
                        <option value="Quality Assurance Division" {{ $req->office == 'Quality Assurance Division' ? 'selected' : '' }}>Quality Assurance Division</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold small text-muted">Unit / Section</label>
                    <select name="unit_section" id="unitSelect" class="form-select">
                        <option value="">-- Select Office First --</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="fw-bold small text-muted">Fund Cluster</label>
                    <input type="text" name="fund_cluster" class="form-control" value="{{ $req->fund_cluster }}">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small text-muted">Responsible Center Code</label>
                    <input type="text" name="center_code" class="form-control" value="{{ $req->rcc }}">
                </div>
            </div>
        </div>

        <div class="section-box">
            <h6 class="section-title"><i class="fa-solid fa-list-check me-2 text-warning"></i> Edit Items</h6>
            
            <div id="items-container">
                @foreach($req->items as $i => $item)
                <div class="row g-3 mb-4 item-row border-bottom pb-3">
                    <div class="col-md-12 text-end">
                        <a href="javascript:void(0)" class="btn-remove-row" onclick="removeRow(this)"><i class="fa-solid fa-trash-can"></i> Remove</a>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small text-muted">Stock No.</label>
                        <input type="text" name="stock_no[]" class="form-control bg-light stock-input" value="{{ $item->stock_no }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small text-muted">Unit Measure</label>
                        <input type="text" name="unit_measure[]" class="form-control bg-light unit-input" value="{{ $item->unit }}" readonly placeholder="Auto-filled" required>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small text-muted">Quantity</label>
                        <input type="number" name="quantity[]" class="form-control" value="{{ $item->req_quantity }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="fw-bold small text-muted">Description <span class="text-danger">*</span></label>
                        <select name="description[]" class="form-select select2-supply" required>
                            <option value="" disabled>-- Select Supply Item --</option>
                            @foreach($supplies as $supply)
                                @php
                                    $supplyFullName = $supply->article . ', ' . $supply->description;
                                    $isSelected = ($item->description == $supplyFullName || $item->description == $supply->article) ? 'selected' : '';
                                @endphp
                                <option value="{{ $supplyFullName }}" data-barcode="{{ $supply->barcode_id }}" data-qty="{{ $supply->quantity }}" data-unit="{{ $supply->unit_measure }}" {{ $isSelected }}>
                                    {{ $supply->article }} - {{ $supply->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Remarks</label>
                        <input type="text" name="remarks[]" class="form-control" value="{{ $item->remarks }}">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem()"><i class="fa-solid fa-plus me-1"></i> Add Item Row</button>
            </div>
            
            <div class="col-md-12 mt-4">
                <label class="fw-bold small text-muted">Purpose</label>
                <textarea name="purpose[]" class="form-control" rows="2">{{ $req->purpose }}</textarea>
            </div>
            
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function formatSupplyOption(state) {
        if (!state.id) { return state.text; }
        
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
        $('.select2-supply').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Select Supply Item --',
            templateResult: formatSupplyOption, 
            templateSelection: formatSupplyOption, 
            escapeMarkup: function(m) { return m; } 
        });

        $('.select2-supply').on('select2:select', function (e) {
            const selectedOption = $(this).select2('data')[0].element; 
            const barcode = $(selectedOption).data('barcode'); 
            const unit = $(selectedOption).data('unit'); 
            const row = $(this).closest('.item-row');
            
            // Auto-fill Stock No and Unit Measure
            row.find('.stock-input').val(barcode || '');
            row.find('.unit-input').val(unit || '');
        });
    }

    $(document).ready(function() {
        initSelect2Fields();
    });

    // --- Office Mapping Logic ---
    const officeMapping = {
        "Administrative Division": ["Asset Management Section", "General Services Unit", "Payroll Services Unit", "Records Section", "Personnel Section", "Cash Section"],
        "Curriculum and Learning Management Division": ["Learning Resource Management Section"],
        "Education Support Services Division": ["Health and Nutrition", "Programs and Projects", "Facilities"],
        "Finance Division": ["Budget Section", "Accounting Section"],
        "Human Resource Development Division": ["NEAP"],
        "Office of the Regional Director": ["Procurement Unit", "Information and Communications Technology Unit", "Public Affairs Unit", "Legal Unit"]
    };

    function updateUnits(preSelectedUnit = null) {
        const officeSelect = document.getElementById("officeSelect");
        const unitSelect = document.getElementById("unitSelect");
        const selectedOffice = officeSelect.value;
        
        unitSelect.innerHTML = '<option value="">-- Select Unit/Section --</option>';

        if (selectedOffice && officeMapping[selectedOffice]) {
            officeMapping[selectedOffice].forEach(unit => {
                const option = document.createElement("option");
                option.value = unit;
                option.textContent = unit;
                
                if (unit === preSelectedUnit) {
                    option.selected = true;
                }
                
                unitSelect.appendChild(option);
            });
        } else {
            unitSelect.innerHTML = '<option value="N/A">General Office Use</option>';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const savedUnit = "{{ $req->division }}"; 
        updateUnits(savedUnit);
    });

    // --- Dynamic Items Logic ---
    function addItem() {
        const container = document.getElementById('items-container');
        const rows = container.querySelectorAll('.item-row');
        const firstRow = rows[0].cloneNode(true);
        
        firstRow.querySelectorAll('.select2-container').forEach(el => el.remove());
        
        const selectElement = firstRow.querySelector('.select2-supply');
        selectElement.classList.remove('select2-hidden-accessible');
        selectElement.removeAttribute('data-select2-id');
        selectElement.removeAttribute('aria-hidden');
        selectElement.removeAttribute('tabindex');
        
        firstRow.querySelectorAll('input, textarea').forEach(input => input.value = '');
        firstRow.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
            select.querySelectorAll('option').forEach(opt => opt.removeAttribute('data-select2-id'));
        });
        
        firstRow.removeAttribute('data-select2-id');
        
        container.appendChild(firstRow);
        
        initSelect2Fields();
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
</script>
</body>
</html>