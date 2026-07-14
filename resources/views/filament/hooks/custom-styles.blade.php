<style>
    /* Login */
    .fi-simple-layout {
        background:
            radial-gradient(ellipse 80% 60% at 20% 20%, rgba(15, 118, 110, 0.18), transparent 55%),
            radial-gradient(ellipse 70% 50% at 85% 15%, rgba(3, 105, 161, 0.14), transparent 50%),
            radial-gradient(ellipse 60% 45% at 70% 90%, rgba(15, 118, 110, 0.12), transparent 55%),
            linear-gradient(160deg, #0b1220 0%, #111827 45%, #0f172a 100%) !important;
        min-height: 100vh;
    }

    .fi-simple-main {
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45) !important;
        backdrop-filter: blur(8px);
    }

    .fi-simple-layout .fi-logo {
        display: none !important;
    }

    .fi-simple-header {
        margin-bottom: 0.5rem !important;
    }

    /* ========== Premium Topbar ========== */
    .fi-topbar {
        z-index: 30 !important;
    }

    .fi-topbar > nav {
        height: 4.25rem !important;
        gap: 0.75rem !important;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.94)) !important;
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow:
            0 1px 0 rgba(255, 255, 255, 0.7) inset,
            0 10px 30px -18px rgba(15, 23, 42, 0.35) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .dark .fi-topbar > nav {
        background:
            linear-gradient(180deg, rgba(15, 23, 42, 0.92), rgba(2, 6, 23, 0.88)) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        box-shadow:
            0 1px 0 rgba(255, 255, 255, 0.04) inset,
            0 14px 36px -20px rgba(0, 0, 0, 0.7) !important;
    }

    .dk-topbar-brand-mark {
        display: inline-flex;
        height: 2.25rem;
        width: 2.25rem;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        background: linear-gradient(145deg, #0f766e, #0d9488);
        color: white;
        box-shadow: 0 8px 18px -8px rgba(15, 118, 110, 0.8);
        flex-shrink: 0;
    }

    .dk-topbar-divider {
        width: 1px;
        height: 2rem;
        background: rgba(148, 163, 184, 0.35);
        margin-inline: 0.35rem;
    }

    .dk-topbar-actions {
        margin-inline-end: 0.25rem;
    }

    .dk-nav-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 9999px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: rgba(248, 250, 252, 0.85);
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        transition: all 0.18s ease;
        white-space: nowrap;
    }

    .dark .dk-nav-action {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.04);
        color: #e2e8f0;
    }

    .dk-nav-action:hover {
        border-color: rgba(15, 118, 110, 0.45);
        background: rgba(15, 118, 110, 0.08);
        color: #0f766e;
        transform: translateY(-1px);
    }

    .dark .dk-nav-action:hover {
        border-color: rgba(45, 212, 191, 0.45);
        background: rgba(45, 212, 191, 0.1);
        color: #5eead4;
    }

    .dk-nav-action-primary {
        border-color: transparent;
        background: linear-gradient(135deg, #0f766e, #0d9488);
        color: white;
        box-shadow: 0 8px 18px -10px rgba(15, 118, 110, 0.9);
    }

    .dk-nav-action-primary:hover {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        color: white;
        border-color: transparent;
    }

    .dark .dk-nav-action-primary {
        color: white;
    }

    /* Global search polish */
    .fi-global-search-field {
        min-width: 14rem;
    }

    @media (min-width: 1280px) {
        .fi-global-search-field {
            min-width: 18rem;
        }
    }

    /* ========== Secondary stats bar ========== */
    .dk-subheader {
        position: sticky;
        top: 4.25rem;
        z-index: 25;
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        background: rgba(248, 250, 252, 0.88);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .dark .dk-subheader {
        border-bottom-color: rgba(255, 255, 255, 0.06);
        background: rgba(2, 6, 23, 0.72);
    }

    .dk-subheader-inner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem 1rem;
        padding: 0.65rem 1rem;
    }

    @media (min-width: 768px) {
        .dk-subheader-inner {
            padding-inline: 1.5rem;
        }
    }

    @media (min-width: 1024px) {
        .dk-subheader-inner {
            padding-inline: 2rem;
        }
    }

    .dk-subheader-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .dk-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border-radius: 9999px;
        background: rgba(15, 118, 110, 0.1);
        color: #0f766e;
        padding: 0.3rem 0.7rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .dark .dk-meta-pill {
        background: rgba(45, 212, 191, 0.12);
        color: #5eead4;
    }

    .dk-meta-dot {
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 9999px;
        background: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
        animation: dk-pulse 2s ease-in-out infinite;
    }

    @keyframes dk-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.45; }
    }

    .dk-meta-text {
        font-size: 0.8rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .dk-meta-text {
        color: #94a3b8;
    }

    .dk-stat-row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        gap: 0.5rem;
    }

    .dk-stat-chip {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
        min-width: 7.5rem;
        border-radius: 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: rgba(255, 255, 255, 0.9);
        padding: 0.45rem 0.75rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .dark .dk-stat-chip {
        border-color: rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
        box-shadow: none;
    }

    .dk-stat-chip-alert {
        border-color: rgba(245, 158, 11, 0.45);
        background: rgba(245, 158, 11, 0.08);
    }

    .dk-stat-label {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .dk-stat-value {
        font-size: 0.875rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .dark .dk-stat-value {
        color: #f8fafc;
    }

    .dk-stat-hint {
        font-size: 0.68rem;
        color: #64748b;
    }

    /* Sidebar polish */
    .fi-sidebar-header {
        height: 4.25rem !important;
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    }

    .dark .fi-sidebar-header {
        border-bottom-color: rgba(255, 255, 255, 0.06);
    }

    .fi-sidebar-item-button {
        border-radius: 0.75rem !important;
    }

    .fi-sidebar-group-label {
        font-size: 0.7rem !important;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .dk-sidebar-footer {
        padding: 0.85rem;
    }

    .dk-sidebar-footer-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #0f766e, #115e59 55%, #0e7490);
        padding: 0.85rem;
        box-shadow: 0 12px 24px -14px rgba(15, 118, 110, 0.9);
    }

    .dk-sidebar-footer-icon {
        display: inline-flex;
        height: 2.25rem;
        width: 2.25rem;
        align-items: center;
        justify-content: center;
        border-radius: 0.7rem;
        background: rgba(255, 255, 255, 0.16);
        color: white;
        flex-shrink: 0;
    }

    /* Content offset under sticky subheader */
    .fi-main > .fi-main-ctn {
        scroll-margin-top: 7rem;
    }

    /* ========== Reports page ========== */
    .dk-reports {
        width: 100%;
    }

    .dk-report-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    @media (min-width: 768px) {
        .dk-report-meta {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .dk-report-meta-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: rgba(255, 255, 255, 0.85);
        padding: 0.9rem 1rem;
    }

    .dark .dk-report-meta-item {
        border-color: rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
    }

    .dk-report-meta-label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .dk-report-meta-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .dark .dk-report-meta-value {
        color: #f8fafc;
    }

    .dk-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .dark .dk-table-wrap {
        border-color: rgba(255, 255, 255, 0.08);
    }

    .dk-table {
        width: 100%;
        min-width: 560px;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .dk-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
        background: rgba(248, 250, 252, 0.9);
        border-bottom: 1px solid rgba(148, 163, 184, 0.25);
        white-space: nowrap;
    }

    .dark .dk-table th {
        color: #94a3b8;
        background: rgba(15, 23, 42, 0.65);
        border-bottom-color: rgba(255, 255, 255, 0.08);
    }

    .dk-table th.text-right,
    .dk-table td.text-right {
        text-align: right;
    }

    .dk-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        color: #334155;
        vertical-align: middle;
    }

    .dark .dk-table td {
        border-bottom-color: rgba(255, 255, 255, 0.06);
        color: #cbd5e1;
    }

    .dk-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .dk-table tbody tr:hover td {
        background: rgba(15, 118, 110, 0.04);
    }

    .dark .dk-table tbody tr:hover td {
        background: rgba(45, 212, 191, 0.06);
    }

    .dk-cell-title {
        display: block;
        font-weight: 600;
        color: #0f172a;
    }

    .dark .dk-cell-title {
        color: #f8fafc;
    }

    .dk-cell-sub {
        display: block;
        margin-top: 0.15rem;
        font-size: 0.75rem;
        color: #64748b;
    }

    .dk-money-in {
        color: #059669 !important;
        font-weight: 600;
    }

    .dark .dk-money-in {
        color: #34d399 !important;
    }

    .dk-money-profit {
        color: #0f766e !important;
    }

    .dark .dk-money-profit {
        color: #2dd4bf !important;
    }

    .dk-money-loss {
        color: #e11d48 !important;
    }

    .dark .dk-money-loss {
        color: #fb7185 !important;
    }

    .dk-empty {
        padding: 2rem 1rem !important;
        text-align: center;
        color: #94a3b8 !important;
    }

    .dk-cargo-row {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .dk-cargo-row-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .dk-cargo-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.2rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .dk-cargo-count {
        font-size: 0.8rem;
        color: #64748b;
        white-space: nowrap;
    }

    .dark .dk-cargo-count {
        color: #94a3b8;
    }

    .dk-progress {
        height: 0.45rem;
        overflow: hidden;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.25);
    }

    .dark .dk-progress {
        background: rgba(255, 255, 255, 0.08);
    }

    .dk-progress-bar {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.3s ease;
        min-width: 0;
    }

    .dk-badge-gray { background: rgba(100, 116, 139, 0.15); color: #475569; }
    .dk-badge-info { background: rgba(14, 165, 233, 0.15); color: #0284c7; }
    .dk-badge-warning { background: rgba(245, 158, 11, 0.18); color: #d97706; }
    .dk-badge-success { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .dk-badge-danger { background: rgba(244, 63, 94, 0.15); color: #e11d48; }
    .dk-badge-primary { background: rgba(15, 118, 110, 0.15); color: #0f766e; }

    .dark .dk-badge-gray { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; }
    .dark .dk-badge-info { background: rgba(56, 189, 248, 0.15); color: #7dd3fc; }
    .dark .dk-badge-warning { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
    .dark .dk-badge-success { background: rgba(52, 211, 153, 0.15); color: #6ee7b7; }
    .dark .dk-badge-danger { background: rgba(251, 113, 133, 0.15); color: #fda4af; }
    .dark .dk-badge-primary { background: rgba(45, 212, 191, 0.15); color: #5eead4; }

    .dk-bar-gray { background: #64748b; }
    .dk-bar-info { background: #0ea5e9; }
    .dk-bar-warning { background: #f59e0b; }
    .dk-bar-success { background: #10b981; }
    .dk-bar-danger { background: #f43f5e; }
    .dk-bar-primary { background: #0f766e; }

    .dk-expense-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        padding: 0.75rem 1rem;
    }

    .dark .dk-expense-row {
        border-color: rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
    }

    /* Keep report header widgets full width and tidy */
    .fi-page-header-widgets .fi-wi-stats-overview-stats {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
</style>
