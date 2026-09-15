{{-- Shared JS helpers for the PO item "Destination" picker (Section → Classification).
    Driven by window.SECTIONS_MAP ({ section: [classifications] }) built from the
    supply_sections registry merged with existing supplies. --}}
<script>
    /* Escape a value for safe interpolation into HTML attributes/text */
    window.escapeHtml = function(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    };

    /* Section <select> options; keeps the previously chosen section when present */
    window.buildSectionOptions = function(selectedSection) {
        const map = window.SECTIONS_MAP || {};
        const sections = Object.keys(map).sort((a, b) => a.localeCompare(b));
        let html = '<option value="">— Select Section —</option>';
        sections.forEach(section => {
            const selected = String(selectedSection ?? '') === section ? ' selected' : '';
            html += '<option value="' + window.escapeHtml(section) + '"' + selected + '>' + window.escapeHtml(section) + '</option>';
        });
        return html;
    };

    /* Classification <select> options for a section; supports a registered
       classification that is not yet in the map (selected + appended) */
    window.buildClassificationOptions = function(section, selectedClassification) {
        const map = window.SECTIONS_MAP || {};
        const classifications = [...(map[section] || [])].sort((a, b) => a.localeCompare(b));
        let html = '<option value="">— Select Classification —</option>';
        classifications.forEach(classification => {
            const selected = String(selectedClassification ?? '') === classification ? ' selected' : '';
            html += '<option value="' + window.escapeHtml(classification) + '"' + selected + '>' + window.escapeHtml(classification) + '</option>';
        });
        if (selectedClassification && !classifications.includes(selectedClassification)) {
            html += '<option value="' + window.escapeHtml(selectedClassification) + '" selected>' + window.escapeHtml(selectedClassification) + '</option>';
        }
        return html;
    };

    /* Blue "Will be filed under" preview line for a chosen destination */
    window.buildDestinationPreviewHtml = function(section, classification) {
        if (!section) return '';
        const target = section + (classification ? ' › ' + classification : '');
        return '<div class="col-12"><div class="inventory-preview"><i class="fas fa-folder-tree"></i> Will be filed under: <strong>'
            + window.escapeHtml(target) + '</strong></div></div>';
    };

    /* Rebuild one row's destination preview from its current selects */
    window.refreshDestinationPreview = function(row) {
        const existing = row.querySelector('.inventory-preview')?.closest('.col-12');
        if (existing) existing.remove();
        const section = row.querySelector('.dest-section-select')?.value || '';
        const classification = row.querySelector('.dest-classification-select')?.value || '';
        const deliveryCol = row.querySelector('.border-top');
        if (deliveryCol) {
            deliveryCol.insertAdjacentHTML('beforebegin', window.buildDestinationPreviewHtml(section, classification));
        }
    };

    /*
     * Native validation silently blocks submit when an invalid control sits
     * inside a display:none wrapper (it cannot be focused). Disabled controls
     * are exempt, so keep the office selects' disabled state in sync with the
     * wrapper's visibility — call this whenever the wrapper is shown/hidden.
     */
    window.syncOfficeControls = function(row) {
        const wrapper = row.querySelector('.office-wrapper');
        if (!wrapper) return;
        const hidden = getComputedStyle(wrapper).display === 'none';
        wrapper.querySelectorAll('select, input').forEach(el => { el.disabled = hidden; });
    };

    /* Wire a freshly rendered row's destination selects */
    window.wireDestinationSelects = function(row) {
        const sectionSel = row.querySelector('.dest-section-select');
        const classSel = row.querySelector('.dest-classification-select');
        if (!sectionSel || !classSel) return;

        classSel.disabled = !sectionSel.value;

        sectionSel.addEventListener('change', function() {
            classSel.innerHTML = window.buildClassificationOptions(sectionSel.value, '');
            classSel.disabled = !sectionSel.value;
            window.refreshDestinationPreview(row);
        });
        classSel.addEventListener('change', function() {
            window.refreshDestinationPreview(row);
        });
    };
</script>
