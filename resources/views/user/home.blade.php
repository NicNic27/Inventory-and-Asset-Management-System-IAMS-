@php $user = auth()->user(); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - DepEd AMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --deped-blue: #101954;
            --deped-blue-2: #0a4d9c;
            --deped-gold: #fbc02d;
        }

        body {
            background-color: #f4f6f9;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #444;
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            padding-top: 105px; /* clears the fixed header */
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* --- Welcome banner --- */
        .welcome-banner {
            background: linear-gradient(135deg, var(--deped-blue) 0%, var(--deped-blue-2) 100%);
            color: white;
            padding: 35px 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 6px 20px rgba(16, 25, 84, 0.25);
            position: relative;
            overflow: hidden;
        }
        .welcome-banner::after {
            content: '\f49e'; /* fa-circle-nodes decorative watermark */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: -20px;
            bottom: -35px;
            font-size: 10rem;
            opacity: 0.08;
            pointer-events: none;
        }
        .welcome-banner h1 {
            font-weight: 700;
            font-size: 1.9rem;
            margin-bottom: 6px;
        }
        .welcome-banner p {
            margin: 0;
            opacity: 0.85;
        }
        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        /* --- Section heading --- */
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--deped-blue);
            font-size: 1.15rem;
            margin-bottom: 18px;
        }
        .section-title i {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 25, 84, 0.08);
            border-radius: 10px;
            font-size: 1rem;
        }

        /* --- How-it-works steps --- */
        .step-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 24px;
            height: 100%;
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .step-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(16, 25, 84, 0.1);
        }
        .step-number {
            position: absolute;
            top: -14px;
            left: 20px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--deped-blue), var(--deped-blue-2));
            color: white;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(16, 25, 84, 0.3);
            font-size: 0.95rem;
        }
        .step-icon {
            font-size: 1.6rem;
            color: var(--deped-blue-2);
            margin-bottom: 14px;
            margin-top: 6px;
        }
        .step-card h6 {
            font-weight: 700;
            color: #212529;
            margin-bottom: 8px;
        }
        .step-card p {
            font-size: 0.87rem;
            color: #6c757d;
            margin: 0;
            line-height: 1.55;
        }

        /* --- Status legend --- */
        .status-legend-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 24px;
        }
        .legend-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
        }
        .legend-item + .legend-item {
            border-top: 1px dashed #e9ecef;
        }
        .legend-badge {
            flex-shrink: 0;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            min-width: 150px;
            text-align: center;
        }
        .legend-desc {
            font-size: 0.85rem;
            color: #6c757d;
            padding-top: 2px;
        }

        /* --- Quick action CTA --- */
        .cta-card {
            background: white;
            border: 2px dashed #c9d0da;
            border-radius: 14px;
            padding: 28px;
            text-align: center;
        }
        .cta-card h6 {
            font-weight: 700;
            color: #212529;
        }
        .cta-card p {
            font-size: 0.85rem;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; }
            .welcome-banner { padding: 25px; }
            .welcome-banner h1 { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

    @include('layouts.user_header')
    @include('layouts.user_sidebar')

    <div class="main-content">

        <!-- Welcome banner -->
        <div class="welcome-banner">
            <span class="welcome-badge"><i class="fas fa-handshake-angle"></i> Welcome to the Asset Management Section</span>
            <h1>Welcome, {{ $user->firstname }} {{ $user->lastname }} 👋</h1>
            <p>This is your Division's portal for requesting supplies and materials from the Asset Management Section — Department of Education, Region V.</p>
        </div>

        <!-- How this system works -->
        <div class="section-title">
            <i class="fas fa-circle-question"></i> How to Request Supplies
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="step-card">
                    <span class="step-number">1</span>
                    <div class="step-icon"><i class="fas fa-file-pen"></i></div>
                    <h6>Prepare Your RIS Form</h6>
                    <p>Get a <strong>Requisition and Issue Slip (RIS)</strong> form. You may print a blank copy through this system to make sure you're using the official format.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="step-card">
                    <span class="step-number">2</span>
                    <div class="step-icon"><i class="fas fa-pen-to-square"></i></div>
                    <h6>Fill Out &amp; Sign</h6>
                    <p>Fill in your Division, Office/Unit, and the items you need. Sign the form and have it signed by your approved signatories.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="step-card">
                    <span class="step-number">3</span>
                    <div class="step-icon"><i class="fas fa-envelope-open-text"></i></div>
                    <h6>Submit to the Office</h6>
                    <p>Submit the signed RIS to the <strong>Asset Management Section</strong> office. Our staff will receive and encode your request into the system.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="step-card">
                    <span class="step-number">4</span>
                    <div class="step-icon"><i class="fas fa-boxes-packing"></i></div>
                    <h6>Claim Your Items</h6>
                    <p>Once your request is approved and processed, proceed to the Asset Management Section office to claim your items.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Reminders -->
            <div class="col-lg-7">
                <div class="status-legend-card h-100">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2 text-primary"></i>Reminders</h6>

                    <div class="legend-item">
                        <span class="legend-badge" style="background:#fff3cd; color:#856404; border:1px solid #ffeeba;">Complete the Form</span>
                        <span class="legend-desc">Fill out all required fields — incomplete RIS forms may be returned to you.</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-badge" style="background:#cff4fc; color:#055160; border:1px solid #b6effb;">Signatures Required</span>
                        <span class="legend-desc">The form must bear your signature and your approved signatories before submission.</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-badge" style="background:#d1e7dd; color:#0f5132; border:1px solid #badbcc;">Check Item Availability</span>
                        <span class="legend-desc">You may ask the Asset Management Section about the availability of stocks before submitting.</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-badge" style="background:#f8d7da; color:#842029; border:1px solid #f5c2c7;">Keep Your Copy</span>
                        <span class="legend-desc">Retain your copy of the RIS for reference when claiming your items.</span>
                    </div>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="col-lg-5">
                <div class="cta-card h-100 d-flex flex-column justify-content-center">
                    <div class="mb-3"><i class="fas fa-print fa-2x" style="color: var(--deped-blue-2);"></i></div>
                    <h6>Need a blank RIS form?</h6>
                    <p class="mb-4">Print an official Requisition and Issue Slip form to fill out and sign by hand.</p>
                    <div class="d-flex justify-content-center">
                        <a href="{{ url('/user/ris/create') }}" class="btn btn-primary px-4 fw-bold shadow-sm" style="background: linear-gradient(135deg, var(--deped-blue), var(--deped-blue-2)); border: none;">
                            <i class="fas fa-print me-1"></i> Print RIS Form
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
