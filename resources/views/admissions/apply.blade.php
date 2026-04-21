<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Admission - School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #1a1a1a 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        .application-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            border: none;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 14px 16px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        .form-control:hover, .form-select:hover {
            border-color: #667eea;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-label i {
            color: #667eea;
        }
        .btn-apply {
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 700;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-apply:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
        }
        .school-logo {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        .section-title {
            color: #667eea;
            font-weight: 700;
            font-size: 1.15rem;
            margin-top: 2rem;
            margin-bottom: 1.2rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        /* Error styling */
        .form-control.error, .form-select.error {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .form-control.error:focus, .form-select.error:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .form-control.valid, .form-select.valid {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.66-.11.11z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .file-upload-wrapper {
            border: 2px dashed #667eea;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
        }
        .file-upload-wrapper:hover {
            border-color: #764ba2;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%);
            transform: scale(1.02);
        }
        .file-upload-wrapper input[type="file"] {
            display: none;
        }
        .file-upload-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 10px;
        }
        .file-upload-text {
            color: #6c757d;
            font-weight: 500;
        }
        /* Premium Success Display Styles */
        .success-display-container {
            animation: slideIn 0.6s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-header-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 20px;
            padding: 35px;
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
        }
        .success-icon-wrapper {
            flex-shrink: 0;
        }
        .checkmark-animation {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .checkmark-svg {
            width: 60px;
            height: 60px;
        }
        .checkmark-circle-bg {
            fill: rgba(255,255,255,0.3);
        }
        .checkmark {
            fill: none;
            stroke: #ffffff;
            stroke-width: 6;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: drawCheck 0.8s 0.3s forwards cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        @keyframes drawCheck {
            to { stroke-dashoffset: 0; }
        }
        .success-content {
            flex: 1;
            color: #ffffff;
        }
        .success-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0 0 8px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .success-subtitle {
            font-size: 1rem;
            margin: 0;
            opacity: 0.95;
        }
        .credentials-main-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            border: 2px solid #10b981;
        }
        .card-header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px 30px;
            color: #ffffff;
        }
        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0 0 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-subtitle {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        .card-body-custom {
            padding: 30px;
        }
        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .credential-item-large {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 15px;
            border: 2px solid #dee2e6;
        }
        .credential-label {
            font-weight: 700;
            color: #495057;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .credential-input-wrapper {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }
        .credential-input-display {
            flex: 1;
            padding: 14px 16px;
            border: 2px solid #ced4da;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            background: #ffffff;
            font-family: 'Courier New', monospace;
            transition: all 0.3s;
            letter-spacing: 3px;
        }
        .credential-input-display[type="text"] {
            letter-spacing: normal;
        }
        .credential-input-display:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }
        .action-buttons-group {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        .btn-action-icon {
            padding: 14px 18px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .btn-action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-action-icon.toggle {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .btn-action-icon.toggle:hover {
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
        .btn-action-icon i {
            font-size: 1.1rem;
        }
        .btn-action-icon .btn-text {
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-copy-credential, .btn-toggle-password {
            padding: 12px 18px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        .btn-copy-credential:hover, .btn-toggle-password:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .password-notice {
            margin-top: 12px;
            padding: 12px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            color: #92400e;
            font-size: 0.85rem;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .login-action-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #dee2e6;
            margin-bottom: 20px;
        }
        .login-url-label {
            font-weight: 700;
            color: #495057;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .login-url-box {
            margin-bottom: 15px;
        }
        .login-url-input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #ced4da;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            background: #ffffff;
            font-family: 'Courier New', monospace;
        }
        .login-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn-login-primary {
            flex: 1;
            min-width: 200px;
            padding: 16px 24px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-login-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45);
            color: #ffffff;
        }
        .btn-copy-url {
            padding: 16px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-copy-url:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .quick-access-info {
            margin-top: 20px;
        }
        .info-badge {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #3b82f6;
            padding: 18px;
            border-radius: 12px;
            display: flex;
            gap: 15px;
            color: #1e40af;
        }
        .info-badge i {
            font-size: 2rem;
            flex-shrink: 0;
        }
        .info-badge strong {
            display: block;
            margin-bottom: 5px;
            font-size: 1rem;
        }
        .info-badge p {
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .student-details-section {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        .details-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        .details-header i {
            font-size: 1.8rem;
            color: #10b981;
        }
        .details-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .detail-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: flex;
            gap: 12px;
            transition: all 0.3s;
        }
        .detail-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.15);
            border-color: #10b981;
        }
        .detail-item.highlight {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-color: #10b981;
        }
        .detail-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .detail-icon i {
            color: #ffffff;
            font-size: 1.2rem;
        }
        .detail-content {
            flex: 1;
        }
        .detail-content label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 6px;
        }
        .detail-content span {
            font-size: 1.05rem;
            color: #1e293b;
            font-weight: 600;
            display: block;
        }
        .detail-content .highlight-text {
            color: #10b981;
            font-size: 1.2rem;
            font-weight: 700;
        }
        .important-notice-box {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 5px solid #3b82f6;
            padding: 25px;
            border-radius: 15px;
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        .notice-icon {
            flex-shrink: 0;
        }
        .notice-icon i {
            font-size: 2.5rem;
            color: #1e40af;
        }
        .notice-content h4 {
            margin: 0 0 12px 0;
            color: #1e40af;
            font-size: 1.2rem;
            font-weight: 700;
        }
        .notice-list {
            margin: 0;
            padding-left: 20px;
        }
        .notice-list li {
            margin-bottom: 8px;
            line-height: 1.6;
            color: #1e40af;
        }
        .notice-list li strong {
            color: #1e3a8a;
        }
        .action-buttons-section {
            display: flex;
            gap: 15px;
            justify-content: center;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
        }
        .btn-action-primary {
            padding: 16px 32px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-action-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45);
            color: #ffffff;
        }
        .btn-action-secondary {
            padding: 16px 32px;
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-action-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(100, 116, 139, 0.4);
        }
        .btn-action-outline {
            padding: 16px 32px;
            background: transparent;
            color: #64748b;
            border: 2px solid #cbd5e1;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        .btn-action-outline:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #1e293b;
        }
        @media (max-width: 768px) {
            .success-header-card {
                flex-direction: column;
                text-align: center;
            }
            .credentials-grid {
                grid-template-columns: 1fr;
            }
            .details-grid {
                grid-template-columns: 1fr;
            }
            .action-buttons-section {
                flex-direction: column;
            }
            .action-buttons-section .btn-action-primary,
            .action-buttons-section .btn-action-secondary,
            .action-buttons-section .btn-action-outline {
                width: 100%;
                justify-content: center;
            }
        }
        @media print {
            body {
                background: white;
            }
            .container, .row, .col-md-10, .col-lg-8, .col-12, .col-xl-10 {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-body {
                padding: 20px !important;
            }
            .application-card {
                display: none !important;
            }
            .success-display-container {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .action-buttons-section, 
            .btn-copy-credential, 
            .btn-toggle-password, 
            .btn-copy-url,
            .quick-access-info,
            .important-notice-box,
            .btn-action-primary,
            .btn-action-secondary,
            .btn-action-outline,
            .login-buttons {
                display: none !important;
            }
            /* Show login URL in print but hide copy button */
            .login-action-section {
                display: block !important;
                margin-bottom: 20px !important;
                padding: 15px !important;
                border: 2px dashed #667eea !important;
                border-radius: 10px !important;
                background: #f8f9ff !important;
            }
            .login-url-display {
                display: block !important;
            }
            .login-url-display label {
                font-weight: bold !important;
                color: #333 !important;
            }
            .login-url-box {
                margin-top: 10px !important;
            }
            .login-url-input {
                border: 1px solid #667eea !important;
                padding: 10px !important;
                font-size: 14px !important;
            }
            .credentials-main-card, 
            .student-details-section {
                break-inside: avoid;
                page-break-inside: avoid;
                margin-bottom: 20px !important;
            }
            .credentials-main-card {
                border: 2px solid #10b981 !important;
            }
            .card-header-gradient {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .credential-item-large {
                border: 1px solid #dee2e6 !important;
            }
            .success-header-card {
                border: 2px solid #10b981 !important;
                margin-bottom: 20px !important;
            }
            .print-only-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 3px solid #667eea;
            }
            .print-only-header h2 {
                margin: 0 0 10px 0;
                color: #333;
                font-size: 24px;
            }
            .print-only-header p {
                margin: 0;
                color: #666;
                font-size: 14px;
            }
            .print-info-footer {
                display: block !important;
                margin-top: 30px;
                padding-top: 15px;
                border-top: 2px solid #dee2e6;
                text-align: center;
                font-size: 12px;
                color: #666;
            }
        }
        .print-only-header {
            display: none;
        }
        .print-info-footer {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid px-4">
        <div class="row justify-content-center py-4">
            <div class="col-12 col-xl-10">
                <div class="card application-card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="school-logo">
                                <i class="bi bi-mortarboard-fill text-white fs-1"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">Apply for Admission</h3>
                            <p class="text-muted mb-0">Fill out the form below to apply for admission</p>
                        </div>
                        
                        @if(session('success'))
                        <!-- Print Only Header -->
                        <div class="print-only-header">
                            <h2>🎓 Admission Confirmation</h2>
                            <p>Your admission has been successfully processed</p>
                        </div>
                        
                        <!-- Premium Success Display with Login Credentials -->
                        <div class="success-display-container mb-5">
                            <!-- Success Header -->
                            <div class="success-header-card">
                                <div class="success-icon-wrapper">
                                    <div class="checkmark-animation">
                                        <svg viewBox="0 0 100 100" class="checkmark-svg">
                                            <circle class="checkmark-circle-bg" cx="50" cy="50" r="45"/>
                                            <path class="checkmark" d="M30 50 L45 65 L70 35"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="success-content">
                                    <h2 class="success-title">🎉 Admission Submitted Successfully!</h2>
                                    <p class="success-subtitle">Your application has been received and processed successfully</p>
                                </div>
                            </div>

                            @if(session('login_credentials'))
                                @php $creds = session('login_credentials'); @endphp
                                
                                <!-- Login Credentials Card - PROMINENT DISPLAY -->
                                <div class="credentials-main-card">
                                    <div class="card-header-gradient">
                                        <h3 class="card-title">
                                            <i class="bi bi-key-fill"></i> Your Login Credentials
                                        </h3>
                                        <p class="card-subtitle">Save these credentials to access your student portal</p>
                                    </div>
                                    <div class="card-body-custom">
                                        <div class="credentials-grid">
                                            <!-- Username/Email -->
                                            <div class="credential-item-large">
                                                <label class="credential-label">
                                                    <i class="bi bi-person-circle"></i> Username / Email
                                                </label>
                                                <div class="credential-input-wrapper">
                                                    <input type="text" class="credential-input-display" id="loginEmail" value="{{ $creds['username'] }}" readonly>
                                                    <button class="btn-copy-credential" onclick="copyCredential('loginEmail', this)" title="Copy">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Password -->
                                            <div class="credential-item-large">
                                                <label class="credential-label">
                                                    <i class="bi bi-lock-fill"></i> Temporary Password
                                                </label>
                                                <div class="credential-input-wrapper">
                                                    <input type="password" class="credential-input-display" id="loginPassword" value="{{ $creds['password'] }}" readonly>
                                                    <div class="action-buttons-group">
                                                        <button class="btn-action-icon" onclick="copyCredential('loginPassword', this)" title="Copy Password">
                                                            <i class="bi bi-clipboard"></i>
                                                            <span class="btn-text">Copy</span>
                                                        </button>
                                                        <button class="btn-action-icon toggle" onclick="togglePasswordDisplay()" title="Show/Hide Password">
                                                            <i class="bi bi-eye" id="passwordEyeIcon"></i>
                                                            <span class="btn-text">Show</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="password-notice">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    <div>
                                                        <strong>Important:</strong> You must change this password after first login!
                                                        <br><small>This is a temporary password for security reasons.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Student Login Button - LARGE & PROMINENT -->
                                        <div class="login-action-section">
                                            <div class="login-url-display">
                                                <label class="login-url-label">
                                                    <i class="bi bi-box-arrow-in-right"></i> Student Login Portal
                                                </label>
                                                <div class="login-url-box">
                                                    <input type="text" class="login-url-input" id="loginUrl" value="{{ $creds['login_url'] }}" readonly>
                                                </div>
                                            </div>
                                            <div class="login-buttons">
                                                <a href="{{ $creds['login_url'] }}" target="_blank" class="btn-login-primary">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                    <span>Login to Student Portal</span>
                                                </a>
                                                <button class="btn-copy-url" onclick="copyCredential('loginUrl', this)">
                                                    <i class="bi bi-clipboard"></i> Copy Link
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Quick Access Info -->
                                        <div class="quick-access-info">
                                            <div class="info-badge">
                                                <i class="bi bi-lightning-charge-fill"></i>
                                                <div>
                                                    <strong>First Time Login?</strong>
                                                    <p>Use the credentials above to login. You will be prompted to change your password.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Student Details Section -->
                            @if(session('student_details'))
                                @php $details = session('student_details'); @endphp
                                <div class="student-details-section">
                                    <div class="details-header">
                                        <i class="bi bi-person-badge-fill"></i>
                                        <h3>Student Information</h3>
                                    </div>
                                    <div class="details-grid">
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="bi bi-person"></i></div>
                                            <div class="detail-content">
                                                <label>Full Name</label>
                                                <span>{{ $details['full_name'] }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-item highlight">
                                            <div class="detail-icon"><i class="bi bi-card-heading"></i></div>
                                            <div class="detail-content">
                                                <label>Admission Number</label>
                                                <span class="highlight-text">{{ $details['admission_number'] }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="bi bi-envelope"></i></div>
                                            <div class="detail-content">
                                                <label>Email</label>
                                                <span>{{ $details['email'] }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="bi bi-phone"></i></div>
                                            <div class="detail-content">
                                                <label>Mobile</label>
                                                <span>{{ $details['mobile_number'] }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="bi bi-mortarboard"></i></div>
                                            <div class="detail-content">
                                                <label>Program</label>
                                                <span>{{ $details['program'] }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="bi bi-people"></i></div>
                                            <div class="detail-content">
                                                <label>Division</label>
                                                <span>{{ $details['division'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Important Notice -->
                            <div class="important-notice-box">
                                <div class="notice-icon">
                                    <i class="bi bi-info-circle-fill"></i>
                                </div>
                                <div class="notice-content">
                                    <h4>Important Next Steps</h4>
                                    <ul class="notice-list">
                                        <li><strong>Save Your Credentials:</strong> Store your login details in a secure location</li>
                                        <li><strong>Login Immediately:</strong> Access your student portal and change your password</li>
                                        <li><strong>Complete Profile:</strong> Update any missing information in your profile</li>
                                        <li><strong>Check Email:</strong> Look for confirmation emails from the institution</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Print Only Footer -->
                            <div class="print-info-footer">
                                <p><strong>Print Date:</strong> {{ date('d-M-Y \a\t h:i A') }}</p>
                                <p>Please keep this admission confirmation for your records.</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons-section">
                                <a href="{{ session('login_credentials')['login_url'] ?? '#' }}" target="_blank" class="btn-action-primary">
                                    <i class="bi bi-box-arrow-up-right"></i> Login to Portal
                                </a>
                                <button onclick="window.print()" class="btn-action-secondary">
                                    <i class="bi bi-printer"></i> Print Details
                                </button>
                                <a href="{{ route('admissions.apply.form') }}" class="btn-action-outline">
                                    <i class="bi bi-arrow-left"></i> Back to Form
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        <form method="POST" action="{{ route('admissions.apply') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Personal Information -->
                            <div class="section-title">
                                <i class="bi bi-person-badge me-2"></i>Personal Information
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="first_name" class="form-label fw-semibold required-field">
                                        <i class="bi bi-person me-2"></i>First Name
                                    </label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @endif"
                                           id="first_name" name="first_name" value="{{ old('first_name') }}"
                                           placeholder="Enter first name (letters only)" required pattern="[a-zA-Z\s]+"
                                           title="Only letters are allowed">
                                    <span class="error-message" id="first_name_error"></span>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="middle_name" class="form-label fw-semibold">
                                        <i class="bi bi-person me-2"></i>Middle Name
                                    </label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @endif"
                                           id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                           placeholder="Enter middle name (letters only)" pattern="[a-zA-Z\s]+"
                                           title="Only letters are allowed">
                                    <span class="error-message" id="middle_name_error"></span>
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="last_name" class="form-label fw-semibold required-field">
                                        <i class="bi bi-person me-2"></i>Last Name
                                    </label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @endif"
                                           id="last_name" name="last_name" value="{{ old('last_name') }}"
                                           placeholder="Enter last name (letters only)" required pattern="[a-zA-Z\s]+"
                                           title="Only letters are allowed">
                                    <span class="error-message" id="last_name_error"></span>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label fw-semibold required-field">
                                        <i class="bi bi-calendar-date me-2"></i>Date of Birth
                                    </label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @endif"
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                           max="{{ date('Y-m-d') }}" required>
                                    <span class="error-message" id="date_of_birth_error"></span>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label fw-semibold required-field">
                                        <i class="bi bi-gender-ambiguous me-2"></i>Gender
                                    </label>
                                    <select class="form-select @error('gender') is-invalid @endif"
                                            id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <span class="error-message" id="gender_error"></span>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="blood_group" class="form-label fw-semibold">
                                        <i class="bi bi-droplet me-2"></i>Blood Group
                                    </label>
                                    <select class="form-select @error('blood_group') is-invalid @endif" 
                                            id="blood_group" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                        <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    </select>
                                    @error('blood_group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="religion" class="form-label fw-semibold">
                                        <i class="bi bi-bookmark me-2"></i>Religion
                                    </label>
                                    <select class="form-select @error('religion') is-invalid @endif" 
                                            id="religion" name="religion">
                                        <option value="">Select Religion</option>
                                        <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Muslim" {{ old('religion') == 'Muslim' ? 'selected' : '' }}>Muslim</option>
                                        <option value="Christian" {{ old('religion') == 'Christian' ? 'selected' : '' }}>Christian</option>
                                        <option value="Sikh" {{ old('religion') == 'Sikh' ? 'selected' : '' }}>Sikh</option>
                                        <option value="Buddhist" {{ old('religion') == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
                                        <option value="Jain" {{ old('religion') == 'Jain' ? 'selected' : '' }}>Jain</option>
                                        <option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('religion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label fw-semibold required-field">
                                        <i class="bi bi-people me-2"></i>Category
                                    </label>
                                    <select class="form-select @error('category') is-invalid @endif"
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="obc" {{ old('category') == 'obc' ? 'selected' : '' }}>OBC</option>
                                        <option value="sc" {{ old('category') == 'sc' ? 'selected' : '' }}>SC</option>
                                        <option value="st" {{ old('category') == 'st' ? 'selected' : '' }}>ST</option>
                                        <option value="ews" {{ old('category') == 'ews' ? 'selected' : '' }}>EWS</option>
                                    </select>
                                    <span class="error-message" id="category_error"></span>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="aadhar_number" class="form-label fw-semibold">
                                        <i class="bi bi-person-vcard me-2"></i>Aadhar Number
                                    </label>
                                    <input type="text" class="form-control @error('aadhar_number') is-invalid @endif" 
                                           id="aadhar_number" name="aadhar_number" value="{{ old('aadhar_number') }}" 
                                           placeholder="Enter 12-digit Aadhar number" maxlength="12" pattern="\d{12}"
                                           title="Must be 12 digits">
                                    @error('aadhar_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="section-title">
                                <i class="bi bi-telephone me-2"></i>Contact Information
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold required-field">
                                        <i class="bi bi-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @endif"
                                           id="email" name="email" value="{{ old('email') }}"
                                           placeholder="Enter email address" required>
                                    <span class="error-message" id="email_error"></span>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="mobile_number" class="form-label fw-semibold required-field">
                                        <i class="bi bi-phone me-2"></i>Mobile Number
                                    </label>
                                    <input type="tel" class="form-control @error('mobile_number') is-invalid @endif"
                                           id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}"
                                           placeholder="Enter 10-digit mobile (start with 6-9)" required pattern="[6-9]\d{9}"
                                           maxlength="10" title="Must be 10 digits, starting with 6-9">
                                    <span class="error-message" id="mobile_number_error"></span>
                                    @error('mobile_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="current_address" class="form-label fw-semibold required-field">
                                    <i class="bi bi-house me-2"></i>Current Address
                                </label>
                                <textarea class="form-control @error('current_address') is-invalid @endif"
                                          id="current_address" name="current_address" rows="2"
                                          placeholder="Enter complete address" required minlength="10">{{ old('current_address') }}</textarea>
                                <span class="error-message" id="current_address_error"></span>
                                @error('current_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="permanent_address" class="form-label fw-semibold">
                                    <i class="bi bi-house-door me-2"></i>Permanent Address
                                </label>
                                <textarea class="form-control @error('permanent_address') is-invalid @endif"
                                          id="permanent_address" name="permanent_address" rows="2"
                                          placeholder="Enter permanent address (same as current if not specified)">{{ old('permanent_address') }}</textarea>
                                <span class="error-message" id="permanent_address_error"></span>
                                @error('permanent_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Academic Information -->
                            <div class="section-title">
                                <i class="bi bi-book me-2"></i>Academic Information
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="program_id" class="form-label fw-semibold required-field">
                                        <i class="bi bi-mortarboard me-2"></i>Program / Course
                                    </label>
                                    <select class="form-select @error('program_id') is-invalid @endif"
                                            id="program_id" name="program_id" required 
                                            onchange="loadDivisions(this.value)">
                                        <option value="">Select Program</option>
                                        @foreach($programs ?? [] as $program)
                                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i> Select your desired program. Division will be assigned by admin later.
                                    </div>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="division_id" class="form-label fw-semibold">
                                        <i class="bi bi-people me-2"></i>Division / Class <span class="text-muted">(Optional - To be assigned by admin)</span>
                                    </label>
                                    <select class="form-select @error('division_id') is-invalid @endif"
                                            id="division_id" name="division_id">
                                        <option value="">Select Division (Optional)</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i> If you know your division, select it. Otherwise, admin will assign it later.
                                    </div>
                                    @error('division_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="academic_session_id" class="form-label fw-semibold required-field">
                                        <i class="bi bi-calendar-event me-2"></i>Academic Session
                                    </label>
                                    <select class="form-select @error('academic_session_id') is-invalid @endif" 
                                            id="academic_session_id" name="academic_session_id" required>
                                        <option value="">Select Academic Year</option>
                                        <option value="1" {{ old('academic_session_id') == '1' ? 'selected' : '' }}>2025-2026</option>
                                        <option value="2" {{ old('academic_session_id') == '2' ? 'selected' : '' }}>2026-2027</option>
                                    </select>
                                    @error('academic_session_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="academic_year" class="form-label fw-semibold required-field">
                                        <i class="bi bi-calendar me-2"></i>Year of Admission
                                    </label>
                                    <select class="form-select @error('academic_year') is-invalid @endif" 
                                            id="academic_year" name="academic_year" required>
                                        <option value="">Select Year</option>
                                        <option value="FY" {{ old('academic_year') == 'FY' ? 'selected' : '' }}>First Year</option>
                                        <option value="SY" {{ old('academic_year') == 'SY' ? 'selected' : '' }}>Second Year</option>
                                        <option value="TY" {{ old('academic_year') == 'TY' ? 'selected' : '' }}>Third Year</option>
                                    </select>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Documents -->
                            <div class="section-title">
                                <i class="bi bi-paperclip me-2"></i>Upload Documents
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-camera me-2"></i>Student Photo
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('photo').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload photo</p>
                                        <small class="text-muted">Max 2MB. JPG, PNG</small>
                                        <input type="file" id="photo" name="photo" accept="image/*" onchange="previewFile(this, 'photoPreview')">
                                    </div>
                                    <img id="photoPreview" class="img-thumbnail mt-2" style="display:none; max-width: 150px;">
                                    @error('photo')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-pen me-2"></i>Student Signature
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('signature').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload signature</p>
                                        <small class="text-muted">Max 1MB. JPG, PNG</small>
                                        <input type="file" id="signature" name="signature" accept="image/*" onchange="previewFile(this, 'signaturePreview')">
                                    </div>
                                    <img id="signaturePreview" class="img-thumbnail mt-2" style="display:none; max-width: 200px;">
                                    @error('signature')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-file-earmark-text me-2"></i>12th Marksheet
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('twelfth_marksheet').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload 12th marksheet</p>
                                        <small class="text-muted">Max 5MB. PDF, JPG, PNG</small>
                                        <input type="file" id="twelfth_marksheet" name="twelfth_marksheet" accept=".pdf,image/*">
                                    </div>
                                    <small id="twelfthFileName" class="text-muted"></small>
                                    @error('twelfth_marksheet')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-file-earmark-ruled me-2"></i>Cast Certificate
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('cast_certificate').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload cast certificate</p>
                                        <small class="text-muted">Max 5MB. PDF, JPG, PNG</small>
                                        <input type="file" id="cast_certificate" name="cast_certificate" accept=".pdf,image/*">
                                    </div>
                                    <small id="castFileName" class="text-muted"></small>
                                    @error('cast_certificate')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-apply w-100 mb-3 mt-4">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // REAL-TIME VALIDATION WITH RED ERROR INDICATORS
        // ============================================
        
        // Helper function to show error
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + '_error');
            
            field.classList.remove('valid');
            field.classList.add('error');
            
            if (errorSpan) {
                errorSpan.textContent = message;
                errorSpan.classList.add('show');
            }
        }
        
        // Helper function to clear error
        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + '_error');
            
            field.classList.remove('error');
            
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.remove('show');
            }
        }
        
        // Helper function to show valid
        function showValid(fieldId) {
            const field = document.getElementById(fieldId);
            field.classList.remove('error');
            field.classList.add('valid');
            
            const errorSpan = document.getElementById(fieldId + '_error');
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.remove('show');
            }
        }
        
        // Auto-fill permanent address if same as current
        document.getElementById('current_address').addEventListener('change', function() {
            if (!document.getElementById('permanent_address').value) {
                document.getElementById('permanent_address').value = this.value;
            }
        });

        // Preview uploaded image
        function previewFile(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Show filename for non-image files
        document.getElementById('twelfth_marksheet').addEventListener('change', function() {
            var fileName = this.files[0]?.name;
            document.getElementById('twelfthFileName').textContent = fileName || '';
        });

        document.getElementById('cast_certificate').addEventListener('change', function() {
            var fileName = this.files[0]?.name;
            document.getElementById('castFileName').textContent = fileName || '';
        });

        // ============================================
        // NAME VALIDATIONS - Only Letters
        // ============================================
        document.getElementById('first_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            if (this.value.trim().length === 0) {
                showError('first_name', 'First name is required');
            } else if (this.value.trim().length < 2) {
                showError('first_name', 'First name must be at least 2 characters');
            } else {
                showValid('first_name');
            }
        });
        
        document.getElementById('first_name').addEventListener('blur', function(e) {
            if (this.value.trim().length === 0) {
                showError('first_name', 'First name is required');
            } else if (this.value.trim().length < 2) {
                showError('first_name', 'First name must be at least 2 characters');
            } else {
                clearError('first_name');
            }
        });
        
        document.getElementById('middle_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
        
        document.getElementById('last_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            if (this.value.trim().length === 0) {
                showError('last_name', 'Last name is required');
            } else if (this.value.trim().length < 2) {
                showError('last_name', 'Last name must be at least 2 characters');
            } else {
                showValid('last_name');
            }
        });
        
        document.getElementById('last_name').addEventListener('blur', function(e) {
            if (this.value.trim().length === 0) {
                showError('last_name', 'Last name is required');
            } else if (this.value.trim().length < 2) {
                showError('last_name', 'Last name must be at least 2 characters');
            } else {
                clearError('last_name');
            }
        });

        // ============================================
        // DATE OF BIRTH VALIDATION
        // ============================================
        document.getElementById('date_of_birth').addEventListener('change', function(e) {
            const dob = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const minAge = 5;
            const minDate = new Date(today.getFullYear() - minAge, today.getMonth(), today.getDate());
            
            if (dob > today) {
                showError('date_of_birth', 'Date of birth cannot be in the future');
            } else if (dob > minDate) {
                showError('date_of_birth', 'Student must be at least 5 years old');
            } else {
                showValid('date_of_birth');
            }
        });

        // ============================================
        // GENDER VALIDATION
        // ============================================
        document.getElementById('gender').addEventListener('change', function(e) {
            if (this.value === '') {
                showError('gender', 'Please select a gender');
            } else {
                showValid('gender');
            }
        });

        // ============================================
        // CATEGORY VALIDATION
        // ============================================
        document.getElementById('category').addEventListener('change', function(e) {
            if (this.value === '') {
                showError('category', 'Please select a category');
            } else {
                showValid('category');
            }
        });

        // ============================================
        // EMAIL VALIDATION
        // ============================================
        document.getElementById('email').addEventListener('blur', function(e) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(this.value)) {
                showError('email', 'Please enter a valid email address');
            } else {
                showValid('email');
            }
        });

        // ============================================
        // MOBILE NUMBER VALIDATION - 10 digits, starts with 6-9
        // ============================================
        document.getElementById('mobile_number').addEventListener('input', function(e) {
            // Remove all non-digits
            this.value = this.value.replace(/[^\d]/g, '');
            
            // Limit to 10 digits
            if (this.value.length > 10) {
                this.value = this.value.substring(0, 10);
            }
            
            // Real-time validation
            if (this.value.length > 0 && this.value.length < 10) {
                showError('mobile_number', 'Mobile number must be exactly 10 digits');
            } else if (this.value.length === 10 && !/^[6-9]/.test(this.value)) {
                showError('mobile_number', 'Mobile number must start with 6, 7, 8, or 9');
            } else if (this.value.length === 10) {
                showValid('mobile_number');
            }
        });
        
        document.getElementById('mobile_number').addEventListener('blur', function(e) {
            if (this.value.length === 0) {
                showError('mobile_number', 'Mobile number is required');
            } else if (this.value.length < 10) {
                showError('mobile_number', 'Mobile number must be exactly 10 digits (you entered ' + this.value.length + ')');
            } else if (!/^[6-9]\d{9}$/.test(this.value)) {
                if (!/^[6-9]/.test(this.value)) {
                    showError('mobile_number', 'Mobile number must start with 6, 7, 8, or 9');
                } else {
                    showError('mobile_number', 'Invalid mobile number format');
                }
            } else {
                clearError('mobile_number');
            }
        });

        // ============================================
        // AADHAR VALIDATION - 12 digits
        // ============================================
        document.getElementById('aadhar_number').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^\d]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.substring(0, 12);
            }
        });
        
        document.getElementById('aadhar_number').addEventListener('blur', function(e) {
            if (this.value.length > 0 && this.value.length !== 12) {
                showError('aadhar_number', 'Aadhar number must be exactly 12 digits');
            } else if (this.value.length === 12) {
                showValid('aadhar_number');
            } else {
                clearError('aadhar_number');
            }
        });

        // ============================================
        // ADDRESS VALIDATION
        // ============================================
        document.getElementById('current_address').addEventListener('blur', function(e) {
            if (this.value.trim().length === 0) {
                showError('current_address', 'Current address is required');
            } else if (this.value.trim().length < 10) {
                showError('current_address', 'Address must be at least 10 characters');
            } else {
                showValid('current_address');
            }
        });

        // ============================================
        // FORM SUBMISSION VALIDATION
        // ============================================
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            let firstErrorField = null;
            
            // Validate First Name
            const firstName = document.getElementById('first_name');
            if (firstName.value.trim().length < 2) {
                showError('first_name', 'First name is required (min 2 characters)');
                isValid = false;
                if (!firstErrorField) firstErrorField = firstName;
            } else {
                clearError('first_name');
            }
            
            // Validate Last Name
            const lastName = document.getElementById('last_name');
            if (lastName.value.trim().length < 2) {
                showError('last_name', 'Last name is required (min 2 characters)');
                isValid = false;
                if (!firstErrorField) firstErrorField = lastName;
            } else {
                clearError('last_name');
            }
            
            // Validate Date of Birth
            const dob = document.getElementById('date_of_birth');
            if (!dob.value) {
                showError('date_of_birth', 'Date of birth is required');
                isValid = false;
                if (!firstErrorField) firstErrorField = dob;
            } else {
                clearError('date_of_birth');
            }
            
            // Validate Gender
            const gender = document.getElementById('gender');
            if (!gender.value) {
                showError('gender', 'Please select a gender');
                isValid = false;
                if (!firstErrorField) firstErrorField = gender;
            } else {
                clearError('gender');
            }
            
            // Validate Category
            const category = document.getElementById('category');
            if (!category.value) {
                showError('category', 'Please select a category');
                isValid = false;
                if (!firstErrorField) firstErrorField = category;
            } else {
                clearError('category');
            }
            
            // Validate Email
            const email = document.getElementById('email');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
                if (!firstErrorField) firstErrorField = email;
            } else {
                clearError('email');
            }
            
            // Validate Mobile Number
            const mobile = document.getElementById('mobile_number');
            if (mobile.value.length !== 10 || !/^[6-9]\d{9}$/.test(mobile.value)) {
                showError('mobile_number', 'Mobile number must be 10 digits starting with 6-9');
                isValid = false;
                if (!firstErrorField) firstErrorField = mobile;
            } else {
                clearError('mobile_number');
            }
            
            // Validate Current Address
            const currentAddress = document.getElementById('current_address');
            if (currentAddress.value.trim().length < 10) {
                showError('current_address', 'Address must be at least 10 characters');
                isValid = false;
                if (!firstErrorField) firstErrorField = currentAddress;
            } else {
                clearError('current_address');
            }
            
            // If invalid, prevent submission and scroll to first error
            if (!isValid) {
                e.preventDefault();
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErrorField.focus();
                }
            }
        });

        // Copy credential to clipboard
        function copyCredential(elementId, buttonElement) {
            const element = document.getElementById(elementId);
            if (!element) {
                alert('Element not found!');
                return;
            }
            
            element.select();
            element.setSelectionRange(0, 99999);
            
            navigator.clipboard.writeText(element.value).then(() => {
                // Show success feedback
                if (buttonElement) {
                    const btn = typeof buttonElement === 'string' ? document.querySelector(buttonElement) : buttonElement;
                    if (btn) {
                        const originalHTML = btn.innerHTML;
                        const originalBg = btn.style.background;
                        const originalColor = btn.style.color;
                        
                        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Copied!';
                        btn.style.background = '#10b981';
                        btn.style.color = '#ffffff';
                        
                        setTimeout(() => {
                            btn.innerHTML = originalHTML;
                            btn.style.background = originalBg;
                            btn.style.color = originalColor;
                        }, 2000);
                    }
                }
            }).catch(err => {
                // Fallback for older browsers
                try {
                    document.execCommand('copy');
                    if (buttonElement) {
                        const btn = typeof buttonElement === 'string' ? document.querySelector(buttonElement) : buttonElement;
                        if (btn) {
                            const originalHTML = btn.innerHTML;
                            btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Copied!';
                            btn.style.background = '#10b981';
                            btn.style.color = '#ffffff';
                            setTimeout(() => {
                                btn.innerHTML = originalHTML;
                                btn.style.background = '';
                                btn.style.color = '';
                            }, 2000);
                        }
                    }
                } catch(err) {
                    alert('Failed to copy: ' + err);
                }
            });
        }

        // Toggle password display
        function togglePasswordDisplay() {
            const passwordInput = document.getElementById('loginPassword');
            const eyeIcon = document.getElementById('passwordEyeIcon');
            const toggleButton = eyeIcon.closest('.btn-action-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (eyeIcon) {
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                }
                if (toggleButton) {
                    const btnText = toggleButton.querySelector('.btn-text');
                    if (btnText) btnText.textContent = 'Hide';
                }
            } else {
                passwordInput.type = 'password';
                if (eyeIcon) {
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                }
                if (toggleButton) {
                    const btnText = toggleButton.querySelector('.btn-text');
                    if (btnText) btnText.textContent = 'Show';
                }
            }
        }

        // Load divisions dynamically based on selected program
        function loadDivisions(programId) {
            const divisionSelect = document.getElementById('division_id');
            
            // Clear existing options
            divisionSelect.innerHTML = '<option value="">Select Division (Optional)</option>';
            
            if (!programId) {
                divisionSelect.disabled = true;
                return;
            }
            
            // Show loading state
            divisionSelect.disabled = true;
            divisionSelect.innerHTML = '<option value="">Loading divisions...</option>';
            
            // Fetch divisions from API
            fetch(`/api/divisions/public?program_id=${programId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                divisionSelect.disabled = false;
                
                if (data.data && data.data.length > 0) {
                    // Add divisions to dropdown
                    data.data.forEach(division => {
                        const option = document.createElement('option');
                        option.value = division.id;
                        option.textContent = division.division_name || division.name;
                        divisionSelect.appendChild(option);
                    });
                    
                    // Restore old selection if exists
                    const oldDivision = '{{ old('division_id') }}';
                    if (oldDivision) {
                        divisionSelect.value = oldDivision;
                    }
                } else {
                    divisionSelect.innerHTML = '<option value="">No divisions available for this program</option>';
                }
            })
            .catch(error => {
                console.error('Error loading divisions:', error);
                divisionSelect.disabled = false;
                divisionSelect.innerHTML = '<option value="">Error loading divisions</option>';
            });
        }

        // Load divisions on page load if program was selected
        document.addEventListener('DOMContentLoaded', function() {
            const programSelect = document.getElementById('program_id');
            if (programSelect && programSelect.value) {
                loadDivisions(programSelect.value);
            }
        });
    </script>
</body>
</html>
