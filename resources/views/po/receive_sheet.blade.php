{{-- Whole-PO Delivery Receiving Sheet --}}
<div class="modal fade no-print" id="receiveDeliveryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 14px;">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-primary m-0 d-flex align-items-center gap-2"><i class="fas fa-truck-loading"></i> Receive Delivery</h5>
                    <div class="small text-muted mt-1">
                        <span id="rsPoNo" class="fw-bold text-dark"></span>
                        &middot; <span id="rsSupplier"></span>
                        &middot; Current status: <span id="rsStatusBadge" class="badge bg-secondary align-middle"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="receiveDeliveryForm">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" id="rs_po_id">

                    {{-- Delivery header --}}
                    <div class="row g-3 mb-3 p-3 rounded-3" style="background:#f4f7ff;border:1px solid #e3e9ff;">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small">Delivery Receipt (DR) No. <span class="text-danger">*</span></label>
                            <input type="text" id="rs_dr_number" class="form-control" placeholder="e.g. DR-2026-001" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" id="rs_dr_date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Remarks</label>
                            <input type="text" id="rs_remarks" class="form-control" placeholder="Optional">
                        </div>
                    </div>

                    {{-- Receiving lines --}}
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-2" style="font-size:.85rem;">
                            <thead>
                                <tr class="text-muted text-uppercase" style="font-size:.7rem;letter-spacing:.5px;">
                                    <th style="width:34%;">Item</th>
                                    <th class="text-center">Ordered</th>
                                    <th class="text-center">Received</th>
                                    <th class="text-center">Remaining</th>
                                    <th class="text-center" style="width:130px;">Receiving Now</th>
                                    <th class="text-center">History</th>
                                </tr>
                            </thead>
                            <tbody id="rsItemsBody"></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success" id="rsReceiveAllBtn">
                            <i class="fas fa-check-double me-1"></i> Receive All Remaining
                        </button>
                        <div class="small text-muted">
                            P.O. will become
                            <strong id="rsPreviewStatus" class="text-dark">&mdash;</strong>
                            after saving &middot; received quantities post to inventory immediately
                        </div>
                    </div>

                    {{-- Per-item delivery history --}}
                    <div id="rsHistoryPanel" class="mt-3 border rounded-3 p-3 d-none" style="background:#fafbfd;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="small text-dark"><i class="fas fa-clock-rotate-left text-muted me-1"></i> Delivery history — <span id="rsHistoryItem"></span></strong>
                            <button type="button" class="btn-close small" onclick="document.getElementById('rsHistoryPanel').classList.add('d-none')"></button>
                        </div>
                        <div id="rsHistoryBody" class="small"></div>
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
/* ============ Whole-PO Delivery Receiving Sheet ============ */
window.rsPoData = null;

window.openReceiveSheet = function(poId, poNo) {
    document.getElementById('rs_po_id').value = poId;
    document.getElementById('rsPoNo').textContent = poNo || ('PO #' + poId);
    document.getElementById('rsSupplier').textContent = 'Loading\u2026';
    document.getElementById('rsItemsBody').innerHTML =
        '<tr><td colspan="6" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading items\u2026</td></tr>';
    document.getElementById('rsHistoryPanel').classList.add('d-none');
    document.getElementById('receiveDeliveryForm').reset();

    fetch('/po/' + poId + '/receive-sheet', { headers: { 'Accept': 'application/json' } })
        .then(r => { if (!r.ok) throw new Error('Failed to load P.O.'); return r.json(); })
        .then(data => {
            window.rsPoData = data;
            document.getElementById('rsSupplier').textContent = data.supplier_name || '';
            rsRenderStatus(data.status);
            rsRenderItems(data.items || []);
        })
        .catch(err => {
            document.getElementById('rsItemsBody').innerHTML =
                '<tr><td colspan="6" class="text-center text-danger py-4">Could not load the P.O. items.</td></tr>';
            if (window.Swal) Swal.fire('Error', err.message || 'Failed to load P.O.', 'error');
        });

    new bootstrap.Modal(document.getElementById('receiveDeliveryModal')).show();
};

function rsRenderStatus(status) {
    const badge = document.getElementById('rsStatusBadge');
    badge.textContent = status || 'Pending';
    badge.className = 'badge align-middle ' + (status === 'Complete' ? 'bg-success' : status === 'Partial' ? 'bg-warning text-dark' : 'bg-secondary');
}

function rsRenderItems(items) {
    const body = document.getElementById('rsItemsBody');
    body.innerHTML = '';

    if (items.length === 0) {
        body.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">This P.O. has no items.</td></tr>';
        rsUpdatePreview();
        return;
    }

    items.forEach(item => {
        const tr = document.createElement('tr');
        tr.dataset.poItemId = item.po_item_id;
        tr.dataset.remaining = item.remaining;
        tr.dataset.ordered = item.ordered;

        let receiveCell;
        if (item.is_asset) {
            receiveCell = '<span class="text-muted fst-italic small">via Asset Inventory</span>';
        } else if (item.remaining === 0) {
            receiveCell = '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Complete</span>';
        } else {
            receiveCell = `<input type="number" class="form-control form-control-sm text-center rs-qty" min="0" max="${item.remaining}" value="0" placeholder="0">`;
        }

        const historyBtn = (item.history || []).length
            ? `<button type="button" class="btn btn-sm btn-light border" title="View delivery history" onclick="rsShowHistory(${item.po_item_id})"><i class="fas fa-clock-rotate-left text-muted"></i></button>`
            : '<span class="text-muted">&mdash;</span>';

        tr.innerHTML = `
            <td><div class="fw-semibold text-dark">${rsEsc(item.description)}</div><div class="text-muted small">${rsEsc(item.unit || '')}</div></td>
            <td class="text-center fw-semibold">${item.ordered}</td>
            <td class="text-center">${item.received > 0 ? '<span class="text-success fw-bold">' + item.received + '</span>' : '<span class="text-muted">0</span>'}</td>
            <td class="text-center ${item.remaining === 0 ? 'text-muted' : 'text-danger fw-bold'}">${item.remaining}</td>
            <td class="text-center">${receiveCell}</td>
            <td class="text-center">${historyBtn}</td>`;
        body.appendChild(tr);
    });

    body.querySelectorAll('.rs-qty').forEach(input => {
        input.addEventListener('input', rsUpdatePreview);
    });

    rsUpdatePreview();
}

function rsCollectPayload() {
    const items = [];
    document.querySelectorAll('#rsItemsBody tr[data-po-item-id]').forEach(tr => {
        const input = tr.querySelector('.rs-qty');
        const qty = input ? (parseInt(input.value, 10) || 0) : 0;
        if (qty > 0) {
            items.push({ po_item_id: parseInt(tr.dataset.poItemId, 10), quantity: qty });
        }
    });

    return {
        _token: '{{ csrf_token() }}',
        po_id: document.getElementById('rs_po_id').value,
        dr_number: document.getElementById('rs_dr_number').value.trim(),
        dr_date: document.getElementById('rs_dr_date').value,
        remarks: document.getElementById('rs_remarks').value.trim(),
        items: items
    };
}

function rsComputePreview(items) {
    let allDone = true, anyDone = false;
    document.querySelectorAll('#rsItemsBody tr[data-po-item-id]').forEach(tr => {
        const input = tr.querySelector('.rs-qty');
        const qty = input ? (parseInt(input.value, 10) || 0) : 0;
        const remaining = parseInt(tr.dataset.remaining, 10) || 0;
        if (remaining === 0) { return; }           // already complete before this delivery
        if (qty >= remaining) { anyDone = true; }   // will finish with this delivery
        else { allDone = false; if (qty > 0) anyDone = true; }
    });
    if (!items.length) return null;                 // nothing received now \u2192 status unchanged
    return allDone ? 'Complete' : 'Partial';
}

function rsUpdatePreview() {
    const preview = document.getElementById('rsPreviewStatus');
    const payload = rsCollectPayload();

    if (payload.items.length === 0) {
        preview.textContent = window.rsPoData ? (window.rsPoData.status || 'Pending') : '\u2014';
        preview.className = 'text-dark';
        return;
    }

    const next = rsComputePreview(payload.items) || (window.rsPoData ? window.rsPoData.status : 'Pending');
    preview.textContent = next;
    preview.className = next === 'Complete' ? 'text-success' : 'text-warning';
}

document.getElementById('rsReceiveAllBtn').addEventListener('click', function() {
    document.querySelectorAll('#rsItemsBody .rs-qty').forEach(input => {
        const max = parseInt(input.getAttribute('max'), 10) || 0;
        input.value = max > 0 ? max : 0;
    });
    rsUpdatePreview();
});

document.getElementById('receiveDeliveryForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const payload = rsCollectPayload();
    if (payload.items.length === 0) {
        Swal.fire('Nothing to receive', 'Enter the delivered quantity for at least one item.', 'warning');
        return;
    }
    if (!payload.dr_number || !payload.dr_date) {
        Swal.fire('Missing details', 'The DR number and delivery date are required.', 'warning');
        return;
    }

    const poId = payload.po_id;
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving\u2026';

    fetch('/po/' + poId + '/receive', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json().then(data => ({ ok: r.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            bootstrap.Modal.getInstance(document.getElementById('receiveDeliveryModal')).hide();
            Swal.fire('Delivery Received', data.message, 'success').then(() => window.location.reload());
        } else {
            Swal.fire('Error', data.message || 'Failed to record the delivery.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Failed to record the delivery.', 'error'))
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Save Delivery';
    });
});

window.rsShowHistory = function(poItemId) {
    const item = (window.rsPoData ? window.rsPoData.items : []).find(i => i.po_item_id === poItemId);
    if (!item) return;

    document.getElementById('rsHistoryItem').textContent = item.description || '';
    const rows = (item.history || []).map(h =>
        `<div class="d-flex justify-content-between border-bottom py-1">
            <span><i class="fas fa-receipt text-muted me-2"></i><strong>${rsEsc(h.dr_number || '\u2014')}</strong></span>
            <span class="text-muted">${rsEsc(h.dr_date || '')}</span>
            <span class="fw-bold text-success">+${h.quantity}</span>
        </div>`).join('');

    document.getElementById('rsHistoryBody').innerHTML =
        rows || '<div class="text-muted">No deliveries recorded for this item yet.</div>';
    document.getElementById('rsHistoryPanel').classList.remove('d-none');
};

function rsEsc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
</script>
