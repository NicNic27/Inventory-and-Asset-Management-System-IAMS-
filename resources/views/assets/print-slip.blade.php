<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Slip - {{ $asset->barcode_id }}</title>
    <style>
        @page {
            size: 7cm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #111;
            font-family: Arial, Helvetica, sans-serif;
        }

        .slip {
            width: 7cm;
            padding: 0.15cm;
            border: 1.5px solid #111;
            font-size: 7pt;
        }

        .header {
            text-align: center;
            padding-bottom: 0.12cm;
            line-height: 1.05;
        }
        .asset-value {
            color: #153AE8;
            font-weight: 900;
        }
        .header img {
            width: 0.72cm;
            height: 0.72cm;
            object-fit: contain;
            display: block;
            margin: 0 auto 0.04cm;
        }

        .republic,
        .department,
        .region {
            display: block;
            font-weight: 700;
        }

        .republic {
            font-family: "Old English MT", "Old English Text MT", serif;
            font-size: 6pt;
        }

        .department {
            font-family: "Old English MT", "Old English Text MT", serif;
            font-size: 8pt;
        }

        .region {
            font-family: "Trajan Pro", Trajan, serif;
            font-size: 5.5pt;
        }

        .field {
            border-top: 1px solid #111;
            padding: 0.06cm 0.07cm 0.08cm;
        }

        .field.tall {
            min-height: 1.1cm;
        }

        .field.signatories {
            padding-bottom: 0.04cm;
        }

        .label {
            display: block;
            font-weight: 700;
            font-size: 6pt;
            margin-bottom: 0.05cm;
        }

        .value {
            display: block;
            overflow-wrap: anywhere;
            font-size: 7pt;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-top: 1px solid #111;
        }

        .two-column .field {
            border-top: 0;
        }

        .two-column .field + .field {
            border-left: 1px solid #111;
        }

        .barcode {
            display: block;
            width: 100%;
            height: 0.75cm;
            object-fit: contain;
            object-position: center;
            margin-top: 0.08cm;
        }

        .property-number {
            display: block;
            text-align: center;
            font-family: "Courier New", monospace;
            font-weight: 700;
            font-size: 9pt;
            letter-spacing: 0.03cm;
            margin-top: 0.03cm;
        }

        .print-actions {
            position: fixed;
            top: 0.3cm;
            left: 9.5cm;
            display: flex;
            gap: 0.15cm;
        }

        .print-actions button {
            border: 1px solid #333;
            background: #fff;
            padding: 0.12cm 0.25cm;
            cursor: pointer;
        }

        @media print {
            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" onclick="window.print()">Print</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <main class="slip">
        <header class="header">
            <img src="{{ asset('assets/images/DepEdseal.png') }}" alt="Department of Education seal">
            <span class="republic">Republic of the Philippines</span>
            <span class="department">Department of Education</span>
            <span class="region">REGION V - BICOL</span>
        </header>

        <section class="field">
            <span class="label">Date of Inventory:</span>
            <span class="value">{{ $asset->inventory_date?->format('F d, Y') ?: 'N/A' }}</span>
        </section>

        <section class="field tall">
            <span class="label">Description of Property Plant &amp; Equipment (PPE):</span>
            <span class="value"><strong>Article / Name:</strong> <span class="asset-value">{{ $asset->article ?: 'N/A' }}</span></span>
            <span class="value"><strong>Description:</strong> {{ $asset->description ?: 'N/A' }}</span>
        </section>

        <section class="field tall">
            <span class="label">Property No:</span>
            <img class="barcode" src="https://bwipjs-api.metafloor.com/?bcid=code128&amp;text={{ urlencode($asset->barcode_id) }}&amp;scale=2&amp;height=8&amp;includetext=false" alt="Barcode {{ $asset->barcode_id }}">
            <span class="property-number">{{ $asset->barcode_id ?: 'N/A' }}</span>
        </section>

        <section class="two-column">
            <div class="field">
                <span class="label">Model:</span>
                <span class="value">{{ $asset->model ?: 'N/A' }}</span>
            </div>
            <div class="field">
                <span class="label">Serial No.:</span>
                <span class="value">{{ $asset->serial_number ?: 'N/A' }}</span>
            </div>
        </section>

        <section class="two-column">
            <div class="field">
                <span class="label">Acquisition Date:</span>
                <span class="value">{{ $asset->acquisition_date?->format('F d, Y') ?: 'N/A' }}</span>
            </div>
            <div class="field">
                <span class="label">Unit Cost:</span>
                <span class="value">{{ $asset->unit_value !== null ? 'PHP ' . number_format((float) $asset->unit_value, 2) : 'N/A' }}</span>
            </div>
        </section>

        <section class="field">
            <span class="label">Unit Measure:</span>
            <span class="value">{{ $asset->unit_measure ?: 'N/A' }}</span>
        </section>

        <section class="field">
            <span class="label">Person Accountable:</span>
            <span class="value">{{ $asset->person_accountable ?: 'N/A' }}</span>
        </section>

        <section class="field signatories">
            <span class="label">Validation/Signatory of Inventory Committees:</span>
            <span class="value">{!! nl2br(e($asset->validation_signatory ?: "1.\n2.\n3.")) !!}</span>
        </section>
    </main>
</body>
</html>
