<style>
  /* ==========================================================================
     RPS WORKBENCH DESIGN SYSTEM (Tailored for Academic Product Workflow)
     Eliminating AI slop: No gratuitous gradients, no neon glows, high contrast,
     compact institutional typography, purposeful interaction states.
     ========================================================================== */

  .rps-workbench {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #1e293b;
    margin-top: 10px;
    margin-bottom: 30px;
  }

  /* --- Top Institutional Header --- */
  .rps-topbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 20px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }

  .rps-topbar-title {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .rps-topbar-meta {
    font-size: 12px;
    color: #64748b;
    margin-top: 2px;
  }

  .rps-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
  }

  .rps-tag.tag-success {
    background: #ecfdf5;
    color: #065f46;
    border-color: #a7f3d0;
  }

  /* --- Two-Pane Layout --- */
  .rps-pane {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
  }

  .rps-sidebar-section {
    padding: 16px 18px;
    border-bottom: 1px solid #f1f5f9;
  }

  .rps-sidebar-section:last-child {
    border-bottom: none;
  }

  .rps-section-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  /* --- Form Controls --- */
  .rps-input, .rps-select, .rps-textarea {
    width: 100%;
    padding: 7px 11px;
    font-size: 13px;
    color: #1e293b;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  .rps-input:focus, .rps-select:focus, .rps-textarea:focus {
    border-color: #1b4332;
    outline: none;
    box-shadow: 0 0 0 2px rgba(27, 67, 50, 0.15);
  }

  /* Select2 Integration for Workbench */
  .rps-workbench .select2-container,
  #s2id_select_mk_diampu {
    width: 100% !important;
    margin-bottom: 4px !important;
  }
  .rps-workbench .select2-container .select2-choice,
  #s2id_select_mk_diampu .select2-choice {
    height: 38px !important;
    line-height: 36px !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    padding: 0 12px !important;
    font-size: 12.5px !important;
    color: #1e293b !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important;
    background-image: none !important;
    display: flex !important;
    align-items: center !important;
    cursor: pointer !important;
  }
  .rps-workbench .select2-container:hover .select2-choice,
  #s2id_select_mk_diampu:hover .select2-choice {
    border-color: #0f766e !important;
  }
  .rps-workbench .select2-container .select2-choice .select2-chosen,
  #s2id_select_mk_diampu .select2-choice .select2-chosen {
    font-weight: 500 !important;
    color: #475569 !important;
    line-height: 36px !important;
    margin-right: 26px !important;
  }
  .rps-workbench .select2-container .select2-choice .select2-arrow,
  #s2id_select_mk_diampu .select2-choice .select2-arrow {
    background: transparent !important;
    border-left: none !important;
    width: 26px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  .rps-workbench .select2-container .select2-choice .select2-arrow b,
  #s2id_select_mk_diampu .select2-choice .select2-arrow b {
    margin-top: 2px !important;
  }
  .rps-workbench .select2-container-active .select2-choice,
  .rps-workbench .select2-dropdown-open.select2-drop-above .select2-choice,
  .rps-workbench .select2-dropdown-open .select2-choice,
  #s2id_select_mk_diampu.select2-dropdown-open .select2-choice {
    border-color: #1b4332 !important;
    box-shadow: 0 0 0 2px rgba(27, 67, 50, 0.15) !important;
    background: #ffffff !important;
  }
  .select2-drop-active {
    border-color: #1b4332 !important;
  }
  .select2-results .select2-highlighted {
    background: #1b4332 !important;
    color: #ffffff !important;
  }
  .select2-results .select2-highlighted strong,
  .select2-results .select2-highlighted div {
    color: #ffffff !important;
  }
  .select2-results .rps-mk-item {
    padding: 4px 2px;
  }
  .select2-results .rps-mk-title {
    color: #0f172a;
    font-weight: 700;
  }
  .select2-results .rps-mk-sks {
    font-size: 10px;
    background: #e2e8f0;
    color: #1e293b !important;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    display: inline-block;
    margin-left: 5px;
    line-height: 1.2;
  }
  .select2-results .rps-mk-diampu {
    font-size: 10px;
    background: #dcfce7;
    color: #166534 !important;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
    border: 1px solid #86efac;
    display: inline-block;
    margin-left: 4px;
    line-height: 1.2;
  }
  .select2-results .rps-mk-prodi {
    font-size: 11px;
    color: #64748b;
    margin-top: 3px;
  }
  .select2-results .select2-highlighted .rps-mk-title {
    color: #ffffff !important;
  }
  .select2-results .select2-highlighted .rps-mk-sks {
    background: rgba(255, 255, 255, 0.22) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.45) !important;
  }
  .select2-results .select2-highlighted .rps-mk-diampu {
    background: #15803d !important;
    color: #ffffff !important;
    border-color: #86efac !important;
  }
  .select2-results .select2-highlighted .rps-mk-prodi {
    color: #cbd5e1 !important;
  }

  .rps-field {
    margin-bottom: 10px;
  }

  .rps-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
  }

  /* --- Document Drop Targets --- */
  .rps-dropzone {
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    border-radius: 6px;
    padding: 12px 14px;
    margin-bottom: 8px;
    position: relative;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .rps-dropzone:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
  }

  .rps-dropzone.has-doc {
    background: #f0fdf4;
    border-color: #86efac;
    border-style: solid;
  }

  .rps-dropzone input[type="file"] {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100%; height: 100%;
    opacity: 0;
    cursor: pointer;
  }

  .rps-dropzone-icon {
    font-size: 18px;
    color: #64748b;
    flex-shrink: 0;
  }

  .rps-dropzone.has-doc .rps-dropzone-icon {
    color: #16a34a;
  }

  .rps-dropzone-body {
    flex-grow: 1;
    min-width: 0;
  }

  .rps-dropzone-title {
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.3;
  }

  .rps-dropzone-desc {
    font-size: 11px;
    color: #64748b;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* --- Action Step Cards (Sleek, Modern & Cohesive) --- */
  .rps-btn-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .rps-btn-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 9px 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
  }

  .rps-btn-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    transform: translateY(-1px);
  }

  .rps-btn-card:hover .rps-card-arrow {
    transform: translateX(3px);
    color: #0f172a;
  }

  .rps-btn-card:active, .rps-btn-card.active {
    background: #f8fafc;
    border-color: #1b4332;
    border-left: 3.5px solid #1b4332;
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }

  .rps-btn-card.active .rps-step-num {
    background: #1b4332;
    color: #fff;
  }

  .rps-btn-card:disabled, .rps-btn-card.disabled {
    opacity: 0.55;
    cursor: not-allowed !important;
    pointer-events: none;
    box-shadow: none !important;
    transform: none !important;
    background: #f8fafc !important;
  }

  .rps-card-content {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .rps-card-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: transform 0.2s ease;
  }

  .rps-btn-card:hover .rps-card-icon {
    transform: scale(1.06);
  }

  /* Color themes per step */
  .rps-icon-roadmap {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
  }

  .rps-icon-rps {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
  }

  .rps-icon-kontrak {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
  }

  .rps-icon-tugas {
    background: #ede9fe;
    color: #6b21a8;
    border: 1px solid #ddd6fe;
  }

  .rps-card-info {
    display: flex;
    flex-direction: column;
    gap: 1px;
  }

  .rps-card-title {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
  }

  .rps-card-desc {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.2;
  }

  .rps-card-arrow {
    color: #94a3b8;
    font-size: 13px;
    transition: transform 0.2s ease, color 0.2s ease;
    margin-left: 6px;
    flex-shrink: 0;
  }

  /* Step Number Badge on Workflow Cards */
  .rps-step-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    font-size: 9px;
    font-weight: 700;
    background: #e2e8f0;
    color: #475569;
    flex-shrink: 0;
    transition: all 0.2s ease;
    margin-right: -2px;
  }

  /* --- Canvas Tab Navigation --- */
  .rps-tab-nav {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0 16px;
    border-radius: 8px 8px 0 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    flex-shrink: 0;
  }

  .rps-tab-nav::-webkit-scrollbar { display: none; }

  .rps-tab-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    border-bottom: 2.5px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
    background: none;
    border-top: none;
    border-left: none;
    border-right: none;
    margin-bottom: -1px;
    position: relative;
  }

  .rps-tab-item:hover {
    color: #334155;
    background: rgba(15, 23, 42, 0.03);
  }

  .rps-tab-item.active {
    color: #0f172a;
    border-bottom-color: #1b4332;
    font-weight: 700;
  }

  .rps-tab-item.active .rps-tab-icon {
    color: #1b4332;
  }

  .rps-tab-item .rps-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #e2e8f0;
    color: #64748b;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    margin-left: 2px;
  }

  .rps-tab-item.active .rps-tab-badge {
    background: #dcfce7;
    color: #15803d;
  }

  .rps-tab-item.tab-pending {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .rps-tab-item.tab-pending:hover {
    background: rgba(15, 23, 42, 0.02);
    color: #64748b;
  }

  .rps-tab-item.tab-ready {
    cursor: pointer;
    opacity: 1;
  }

  .rps-tab-icon {
    font-size: 11px;
    color: #94a3b8;
    transition: color 0.15s;
  }

  @keyframes rpsSpinLoader {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* Canvas header (title + tools) row BELOW tabs */
  .rps-canvas-toolbar {
    padding: 8px 16px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    flex-shrink: 0;
    background: #fff;
  }

  .rps-canvas-doc-label {
    font-size: 12px;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .rps-canvas-doc-mk {
    font-weight: 700;
    color: #0f172a;
  }

  /* Ensure parent containers don't clip or disable sticky */
  .page-container, .page-content-wrapper, .page-content {
    overflow: visible !important;
  }

  @media (min-width: 992px) {
    .rps-grid-row {
      display: flex !important;
      align-items: flex-start !important;
      position: relative !important;
    }

    .rps-sticky-col {
      position: -webkit-sticky !important;
      position: sticky !important;
      top: 85px !important;
      z-index: 20 !important;
    }
  }

  /* --- Right Panel: Document Canvas (Sticky & Scrollable) --- */
  .rps-canvas {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 105px);
    max-height: calc(100vh - 105px);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }

  .rps-canvas-header {
    padding: 12px 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    flex-shrink: 0;
  }

  .rps-canvas-body {
    padding: 24px 30px;
    flex-grow: 1;
    overflow-y: auto !important;
  }

  /* Document Typography (Clean & High Contrast) */
  .rps-doc-view {
    font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, Arial, sans-serif;
    font-size: 13.5px;
    line-height: 1.65;
    color: #1e293b;
  }

  .rps-doc-view h1, .rps-doc-view h2, .rps-doc-view h3, .rps-doc-view h4 {
    color: #0f172a;
    font-weight: 700;
    line-height: 1.3;
    margin-top: 24px;
    margin-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 5px;
  }

  .rps-doc-view h1 { font-size: 18px; }
  .rps-doc-view h2 { font-size: 15px; }
  .rps-doc-view h3 { font-size: 14px; }

  .rps-doc-view table {
    width: 100%;
    min-width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
    font-size: 12.5px;
    table-layout: auto;
  }

  .rps-doc-view th {
    background: #f8fafc;
    color: #0f172a;
    font-weight: 600;
    text-align: left;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    white-space: normal;
  }

  .rps-doc-view td {
    padding: 8px 10px;
    border: 1px solid #e2e8f0;
    vertical-align: top;
  }

  .rps-doc-view tr:nth-child(even) td {
    background-color: #fafbfc;
  }

  .rps-doc-view blockquote {
    margin: 14px 0;
    padding: 10px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    color: #475569;
  }

  /* Subtle In-situ Loader */
  .rps-loader-bar {
    display: none;
    height: 4px;
    background: #e2e8f0;
    overflow: hidden;
    position: relative;
    border-radius: 2px;
    margin-bottom: 0;
  }

  .rps-loader-bar::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 45%;
    background: linear-gradient(90deg, #1b4332, #2d6a4f, #52b788);
    animation: rpsProgress 1.5s infinite ease-in-out;
  }

  @keyframes rpsProgress {
    0% { left: -45%; }
    100% { left: 100%; }
  }

  /* --- Modern AI Loading State & Shimmer Animation --- */
  .rps-loading-box {
    padding: 35px 20px 20px;
    max-width: 580px;
    margin: 0 auto;
    text-align: center;
  }

  .rps-ai-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 16px;
    box-shadow: 0 0 0 0 rgba(27, 67, 50, 0.4);
    animation: rpsPulseRing 2s infinite cubic-bezier(0.66, 0, 0, 1);
  }

  @keyframes rpsPulseRing {
    0% {
      box-shadow: 0 0 0 0 rgba(27, 67, 50, 0.5), 0 4px 14px rgba(27, 67, 50, 0.25);
    }
    70% {
      box-shadow: 0 0 0 16px rgba(27, 67, 50, 0), 0 4px 14px rgba(27, 67, 50, 0.25);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(27, 67, 50, 0), 0 4px 14px rgba(27, 67, 50, 0.25);
    }
  }

  .rps-progress-steps {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin: 20px auto 0;
    text-align: left;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 18px;
  }

  .rps-step-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    color: #64748b;
    transition: all 0.3s ease;
  }

  .rps-step-item.active {
    color: #0f172a;
    font-weight: 700;
  }

  .rps-step-item.completed {
    color: #16a34a;
    font-weight: 600;
  }

  .rps-step-bullet {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #64748b;
    flex-shrink: 0;
    transition: all 0.3s ease;
  }

  .rps-step-item.active .rps-step-bullet {
    background: #1b4332;
    color: #ffffff;
    box-shadow: 0 0 0 3px rgba(27, 67, 50, 0.15);
  }

  .rps-step-item.completed .rps-step-bullet {
    background: #16a34a;
    color: #ffffff;
  }

  /* Skeleton Shimmer */
  .rps-skeleton-shimmer {
    margin-top: 24px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    text-align: left;
    background: #ffffff;
    border: 1px dashed #e2e8f0;
    border-radius: 8px;
    padding: 16px;
  }

  .rps-shimmer-line {
    height: 10px;
    border-radius: 4px;
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: rpsShimmer 1.5s infinite;
  }

  @keyframes rpsShimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  /* Empty state */
  .rps-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
  }

  /* Tab Items — Clean & Always Clickable */
  .rps-tab-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    font-size: 12.5px;
    font-weight: 600;
    color: #64748b;
    border-bottom: 2.5px solid transparent;
    cursor: pointer !important;
    white-space: nowrap;
    transition: all 0.15s ease;
    background: none;
    border-top: none;
    border-left: none;
    border-right: none;
    margin-bottom: -1px;
    position: relative;
    opacity: 1 !important;
  }

  .rps-tab-item:hover {
    color: #0f172a;
    background: rgba(15, 23, 42, 0.04);
  }

  .rps-tab-item.active {
    color: #1b4332;
    border-bottom-color: #1b4332;
    font-weight: 700;
    background: #ffffff;
  }

  .rps-tab-item.active .rps-tab-icon {
    color: #1b4332;
  }

  .rps-tab-icon {
    font-size: 12px;
    color: #94a3b8;
    transition: color 0.15s;
  }

  /* Empty state */
  .rps-empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #64748b;
  }

  .rps-empty-icon {
    font-size: 40px;
    color: #cbd5e1;
    margin-bottom: 12px;
  }

  .rps-empty-title {
    font-weight: 700;
    font-size: 15px;
    color: #1e293b;
    margin-bottom: 6px;
  }

  .rps-empty-desc {
    font-size: 12.5px;
    color: #64748b;
    max-width: 420px;
    margin: 0 auto 18px;
    line-height: 1.6;
  }

  /* ===== AI COMMAND BAR & CHAT HISTORY ===== */
  .rps-command-bar {
    border-top: 1px solid #e2e8f0;
    padding: 10px 16px 12px;
    background: #fafbfc;
    flex-shrink: 0;
    border-radius: 0 0 8px 8px;
  }

  /* History Interaksi Prompt (Chat Stream) */
  .rps-history-panel {
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 8px;
    padding-bottom: 8px;
  }

  .rps-history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2px 4px 6px;
    font-size: 11.5px;
    color: #475569;
    font-weight: 700;
    cursor: pointer;
    user-select: none;
  }

  .rps-history-header:hover {
    color: #1b4332;
  }

  .rps-history-list {
    max-height: 160px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 8px 10px;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    margin-top: 4px;
  }

  .rps-history-item {
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12.5px;
    line-height: 1.5;
    position: relative;
    animation: fadeInMsg 0.2s ease-in-out;
  }

  @keyframes fadeInMsg {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .rps-history-item.user-msg {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    align-self: flex-end;
    max-width: 92%;
    color: #166534;
  }

  .rps-history-item.ai-msg {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    align-self: flex-start;
    max-width: 92%;
    color: #1e293b;
  }

  .rps-history-meta {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 3px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
  }

  .rps-history-item.user-msg .rps-history-meta {
    color: #15803d;
  }

  .rps-history-reuse {
    color: #1b4332;
    cursor: pointer;
    font-size: 10.5px;
    font-weight: 600;
    text-decoration: underline;
  }

  .rps-history-reuse:hover {
    color: #2d6a4f;
  }

  .rps-history-empty {
    padding: 10px;
    text-align: center;
    font-size: 11.5px;
    color: #94a3b8;
    font-style: italic;
  }

  .rps-command-wrap {
    display: flex;
    gap: 10px;
    align-items: stretch;
    margin-bottom: 6px;
  }

  .rps-command-input {
    flex-grow: 1;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 13px;
    resize: none;
    color: #1e293b;
    background: #ffffff;
    min-height: 58px;
    max-height: 160px;
    overflow-y: auto;
    line-height: 1.5;
    font-family: inherit;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  .rps-command-input:focus {
    outline: none;
    border-color: #1b4332;
    box-shadow: 0 0 0 2px rgba(27, 67, 50, 0.12);
  }

  .rps-command-input::placeholder { color: #94a3b8; }

  .btn-generate-ai {
    background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(27, 67, 50, 0.25);
    flex-shrink: 0;
    min-width: 145px;
    min-height: 58px;
  }

  .btn-generate-ai:hover {
    background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
    box-shadow: 0 4px 14px rgba(27, 67, 50, 0.35);
    transform: translateY(-1px);
    color: #ffffff;
  }

  .btn-generate-ai:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
  }

  .rps-command-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    color: #64748b;
    padding: 2px 2px 0;
  }

  /* Context panel (left sidebar) compact improvements */
  .rps-context-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    margin-bottom: 8px;
  }

  .rps-context-badge-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
  }

  .rps-context-badge-info {
    flex-grow: 1;
    min-width: 0;
  }

  .rps-context-badge-label {
    font-size: 10px;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    line-height: 1;
  }

  .rps-context-badge-value {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
  }

  .rps-context-badge-value.empty {
    color: #94a3b8;
    font-weight: 400;
    font-style: italic;
  }

  /* --- CPMK Studio Card Editor Styles --- */
  .cpmk-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
    transition: all 0.15s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }
  .cpmk-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 3px 8px rgba(0,0,0,0.06);
  }
  .cpmk-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    gap: 8px;
  }
  .cpmk-card-title {
    font-weight: 700;
    font-size: 13.5px;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .cpmk-textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 8px 10px;
    font-size: 13px;
    line-height: 1.5;
    color: #1e293b;
    background: #ffffff;
    resize: vertical;
    min-height: 54px;
    font-family: inherit;
    transition: border-color 0.15s ease;
  }
  .cpmk-textarea:focus {
    outline: none;
    border-color: #1b4332;
    box-shadow: 0 0 0 2px rgba(27,67,50,0.12);
  }
  .cpmk-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    flex-wrap: wrap;
    gap: 8px;
  }
  .cpmk-cpl-tags {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    font-size: 11px;
  }
  .cpmk-cpl-section {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px dashed #f1f5f9;
  }
  .cpmk-cpl-label {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .cpmk-cpl-list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
  }
  .cpmk-cpl-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    border-radius: 4px;
    font-weight: 600;
    font-size: 10.5px;
  }
  .cpmk-cpl-remove {
    cursor: pointer;
    color: #94a3b8;
    margin-left: 2px;
    font-weight: bold;
    line-height: 1;
    font-size: 12px;
    transition: color 0.15s ease;
  }
  .cpmk-cpl-remove:hover {
    color: #ef4444;
  }
  .cpmk-bloom-select {
    font-size: 11px;
    font-weight: 700;
    height: 24px;
    padding: 1px 6px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    cursor: pointer;
    outline: none;
  }
  .cpmk-bloom-select:focus {
    border-color: #0f766e;
    background: #ffffff;
  }
  .cpmk-prov-badge {
    font-size: 10.5px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }
  .cpmk-prov-badge.prov-approved { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
  .cpmk-prov-badge.prov-manual { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
  .cpmk-prov-badge.prov-proposal { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
  .cpmk-bloom-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    background: #f3e8ff;
    color: #7e22ce;
    border: 1px solid #e9d5ff;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }
  .cpl-select-item-card {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 10px;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: flex-start;
    gap: 10px;
  }
  .cpl-select-item-card:hover {
    border-color: #0f766e;
    background: #f0fdfa;
  }
  .cpl-select-item-card.selected {
    border-color: #0f766e;
    background: #f0fdfa;
  }
  .cpmk-card-actions {
    display: flex;
    align-items: center;
    gap: 4px;
  }
</style>

<div class="page-content rps-workbench">
  <!-- Top Bar -->
  <div class="rps-topbar">
    <div>
      <h3 class="rps-topbar-title">
        <i class="fa fa-magic" style="color:#1b4332;"></i>
        Studio Dokumen Pembelajaran
        <span class="rps-tag" style="font-size:10px; font-weight:600; background:#f0fdf4; color:#166534; border-color:#bbf7d0; margin-left:4px;">RPS OBE</span>
      </h3>
      <div class="rps-topbar-meta">
        <span style="color:#94a3b8;"><i class="fa fa-home"></i></span>
        <span style="color:#94a3b8;"> / RPS &amp; Pembelajaran</span>
        <span style="color:#94a3b8;"> / </span>
        <span style="color:#475569; font-weight:600;">Generator Studio</span>
        &nbsp;·&nbsp;
        Mengacu <strong>Permendiktisaintek No. 39/2025 jo No. 10/2026</strong>, SPMI 2026, Taksonomi Bloom C4–C6
      </div>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
      <span class="rps-tag tag-success" id="badge_ai_provider">
        <i class="fa fa-server"></i> <span id="badge_ai_provider_name"><?= strtoupper(htmlspecialchars($ai_provider)); ?></span>
      </span>
      <button type="button" class="btn btn-default btn-xs" onclick="bukaModalApiKey()" id="btn_kunci_ai_top" style="border-radius:4px; font-weight:600; cursor:pointer;">
        <i class="fa fa-key"></i> Kunci AI
      </button>
    </div>
  </div>

  <div class="row rps-grid-row">
    <!-- LEFT PANE: CONFIGURATION & DOCUMENTS (COL-MD-4) -->
    <div class="col-md-4">
      <div class="rps-pane">
        <!-- Section 1: Identitas MK -->
        <div class="rps-sidebar-section">
          <div class="rps-section-title">
            <span>Mata Kuliah</span>
            <span class="text-muted" style="font-weight:normal; text-transform:none;">Data Dasar</span>
          </div>

          <div class="rps-field" style="margin-bottom:14px;">
            <label class="rps-label" style="color:#0f172a; display:flex; justify-content:space-between; align-items:center; cursor:pointer; margin-bottom:6px;" onclick="if($.fn.select2){ $('#select_mk_diampu').select2('open'); }">
              <span><i class="fa fa-search text-primary"></i> <strong>Cari / Pilih Mata Kuliah</strong></span>
              <span class="text-muted" style="font-size:11px; font-weight:normal;">min. 3 huruf</span>
            </label>
            <input type="hidden" id="select_mk_diampu" style="width:100%;" placeholder="🔍 Ketik min. 3 huruf nama atau kode MK...">
          </div>

          <div class="rps-field">
            <label class="rps-label">Nama Mata Kuliah</label>
            <input type="text" id="mk_nama" class="rps-input" placeholder="—" readonly style="background:#f1f5f9; cursor:not-allowed; color:#334155; font-weight:600;">
          </div>

          <div class="row" style="margin-left:-4px; margin-right:-4px;">
            <div class="col-xs-6" style="padding-left:4px; padding-right:4px;">
              <div class="rps-field">
                <label class="rps-label">Kode MK</label>
                <input type="text" id="mk_kode" class="rps-input" placeholder="—" readonly style="background:#f1f5f9; cursor:not-allowed; color:#334155; font-weight:600;">
              </div>
            </div>
            <div class="col-xs-6" style="padding-left:4px; padding-right:4px;">
              <div class="rps-field">
                <label class="rps-label">Bobot SKS</label>
                <input type="text" id="mk_sks_display" class="rps-input" value="" placeholder="—" readonly style="background:#f1f5f9; cursor:not-allowed; color:#334155; font-weight:600;">
                <input type="hidden" id="mk_sks" value="">
              </div>
            </div>
          </div>

          <div class="row" style="margin-left:-4px; margin-right:-4px;">
            <div class="col-xs-6" style="padding-left:4px; padding-right:4px;">
              <div class="rps-field">
                <label class="rps-label">Semester</label>
                <select id="mk_semester" class="rps-select">
                  <option value="">— Pilih —</option>
                  <option value="Semester 1">Semester 1 (Ganjil)</option>
                  <option value="Semester 2">Semester 2 (Genap)</option>
                  <option value="Semester 3">Semester 3 (Ganjil)</option>
                  <option value="Semester 4">Semester 4 (Genap)</option>
                  <option value="Semester 5">Semester 5 (Ganjil)</option>
                  <option value="Semester 6">Semester 6 (Genap)</option>
                  <option value="Semester 7">Semester 7 (Ganjil)</option>
                  <option value="Semester 8">Semester 8 (Genap)</option>
                </select>
              </div>
            </div>
            <div class="col-xs-6" style="padding-left:4px; padding-right:4px;">
              <div class="rps-field">
                <label class="rps-label">Dosen Pengampu</label>
                <input type="text" id="mk_dosen" class="rps-input" value="<?= htmlspecialchars($user_name); ?>">
              </div>
            </div>
          </div>

          <div class="rps-field" style="margin-bottom:0;">
            <label class="rps-label">Program Studi</label>
            <input type="text" id="mk_prodi" class="rps-input" placeholder="—" readonly style="background:#f1f5f9; cursor:not-allowed; color:#334155; font-weight:600;">
          </div>
        </div>

        <!-- Section 1.5: Kesiapan Kurikulum (Curriculum Readiness Faktual) -->
        <div class="rps-sidebar-section" id="section_curriculum_readiness" style="background:#fafbfc;">
          <div class="rps-section-title" style="display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fa fa-dashboard text-primary" style="margin-right:3px;"></i> Kesiapan Kurikulum</span>
            <div style="display:flex; align-items:center; gap:5px;">
              <span id="badge_readiness_overall" class="rps-tag" style="font-size:10px; background:#f1f5f9; color:#64748b;">Belum Dicek</span>
              <button type="button" onclick="checkCurriculumReadiness(true)" class="btn btn-link btn-xs" style="padding:0 2px; color:#94a3b8; font-size:11px; text-decoration:none;" title="Periksa ulang kesiapan kurikulum"><i class="fa fa-refresh"></i></button>
            </div>
          </div>

          <div id="readiness_checklist_container" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; font-size:11.5px;">
            <table style="width:100%; border-collapse:collapse; font-size:11.5px;" id="table_readiness_checklist">
              <tr style="border-bottom:1px solid #f1f5f9; height:24px;">
                <td style="color:#64748b; padding:4px 0;"><i class="fa fa-file-text-o"></i> Dokumen Kurikulum:</td>
                <td style="text-align:right; font-weight:600; padding:4px 0;" id="readiness_kurikulum"><span class="text-muted">—</span></td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9; height:24px;">
                <td style="color:#64748b; padding:4px 0;"><i class="fa fa-book"></i> MK di Kurikulum:</td>
                <td style="text-align:right; font-weight:600; padding:4px 0;" id="readiness_mk"><span class="text-muted">—</span></td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9; height:24px;">
                <td style="color:#64748b; padding:4px 0;"><i class="fa fa-list-ul"></i> CPL Prodi:</td>
                <td style="text-align:right; font-weight:600; padding:4px 0;" id="readiness_cpl"><span class="text-muted">—</span></td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9; height:24px;">
                <td style="color:#64748b; padding:4px 0;"><i class="fa fa-map-signs"></i> Pemetaan CPL ↔ MK:</td>
                <td style="text-align:right; font-weight:600; padding:4px 0;" id="readiness_mapping"><span class="text-muted">—</span></td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9; height:24px;">
                <td style="color:#64748b; padding:4px 0;"><i class="fa fa-cubes"></i> Bahan Kajian:</td>
                <td style="text-align:right; font-weight:600; padding:4px 0;" id="readiness_bk"><span class="text-muted">—</span></td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9; height:24px;">
                <td style="color:#64748b; padding:4px 0;"><i class="fa fa-info-circle"></i> Deskripsi MK:</td>
                <td style="text-align:right; font-weight:600; padding:4px 0;" id="readiness_desc"><span class="text-muted">—</span></td>
              </tr>
              <tr style="height:24px;">
                <td style="color:#64748b; padding:4px 0;"><i class="fa fa-bullseye"></i> CPMK Mata Kuliah:</td>
                <td style="text-align:right; font-weight:600; padding:4px 0;" id="readiness_cpmk"><span class="text-muted">—</span></td>
              </tr>
            </table>

            <div id="readiness_action_box" style="margin-top:10px; padding-top:8px; border-top:1px solid #f1f5f9; display:none;">
              <button type="button" class="btn btn-xs btn-primary btn-block" onclick="switchTab('susun_cpmk'); jalankanGenerateCpmk();" style="font-weight:700; background:#1b4332; border-color:#1b4332; padding:6px 10px; border-radius:5px; color:#ffffff;">
                <i class="fa fa-magic"></i> Susun Draft CPMK dengan AI
              </button>
            </div>
          </div>
        </div>

        <!-- Section 2: Dokumen Acuan (Source-First) -->
        <div class="rps-sidebar-section">
          <div class="rps-section-title">
            <span>Dokumen Acuan (Source-First)</span>
            <span class="rps-tag" style="font-size:10px;">Faktual</span>
          </div>

          <!-- Pustaka & Kurikulum CPL Prodi (Smart Shared Library) -->
          <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <span style="font-size:12px; font-weight:700; color:#0f172a;">
                <i class="fa fa-graduation-cap text-primary" style="margin-right:4px;"></i> Kurikulum &amp; CPL Prodi
              </span>
              <span id="badge_status_pustaka" class="rps-tag" style="background:#f1f5f9; color:#64748b; font-size:10px;">
                Belum Terpilih
              </span>
            </div>

            <!-- Pustaka Dropdown Selector -->
            <div class="rps-field" style="margin-bottom:8px;">
              <select id="select_pustaka_kurikulum" class="rps-select" onchange="pilihPustakaManual(this)" style="font-size:12px; padding:5px 8px; height:32px; background:#f8fafc; border-color:#cbd5e1; font-weight:600;">
                <option value="">— Pilih dari Pustaka Kurikulum —</option>
                <?php if (!empty($pustaka_list)): ?>
                  <?php foreach ($pustaka_list as $pk): ?>
                    <option value="<?= $pk->id; ?>" data-prodi="<?= htmlspecialchars($pk->nama_prodi); ?>">
                      📚 <?= htmlspecialchars($pk->nama_prodi); ?> (<?= htmlspecialchars($pk->file_name); ?>)
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

            <!-- Detail Info Pustaka Aktif -->
            <div id="info_pustaka_aktif" style="display:none; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:7px 10px; margin-bottom:8px; font-size:11.5px; color:#166534;">
              <strong id="pustaka_nama_doc" style="color:#0f766e; font-size:11.5px; line-height:1.4; display:block;">Kurikulum</strong>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px; padding-top:6px; border-top:1px solid #f1f5f9;">
              <span style="font-size:10.5px; color:#64748b;">
                <i class="fa fa-check-circle text-success"></i> Otomatis terpilih via MK
              </span>
              <a href="<?= site_url('C_kurikulum'); ?>" target="_blank" style="font-size:11px; color:#0f766e; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:3px;">
                <i class="fa fa-layers"></i> Kelola / Audit Kurikulum <i class="fa fa-external-link" style="font-size:9px;"></i>
              </a>
            </div>
          </div>

          <!-- Dropzone: Matriks 16 Pertemuan Excel 13 Kolom (Opsional) -->
          <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <span style="font-size:12px; font-weight:700; color:#0f172a;">
                <i class="fa fa-file-excel-o text-success" style="margin-right:4px;"></i> 16 Pertemuan (Excel 13 Kolom)
              </span>
              <a href="<?= site_url('C_rps_generator/download_template_16_pertemuan'); ?>" target="_blank" style="font-size:11px; color:#166534; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:3px;" title="Unduh Template Excel 13 Kolom Format Resmi WBI">
                <i class="fa fa-download"></i> Template <i class="fa fa-file-excel-o" style="font-size:9px;"></i>
              </a>
            </div>

            <div class="rps-dropzone" id="box_excel_pertemuan" style="margin-bottom:0; background:#f8fafc; border:1px dashed #cbd5e1; padding:10px 12px; position:relative;">
              <input type="file" id="file_excel_pertemuan" accept=".xlsx,.xls" onchange="handleExcelPertemuanUpload(this)">
              <i class="fa fa-table rps-dropzone-icon" style="color:#16a34a; font-size:16px;"></i>
              <div class="rps-dropzone-body" style="flex:1; min-width:0;">
                <div class="rps-dropzone-title" id="title_excel_pertemuan" style="font-size:11.5px; font-weight:600; color:#334155;">Upload File Excel 16 Pertemuan</div>
                <div class="rps-dropzone-desc" id="desc_excel_pertemuan" style="font-size:10.5px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Pilih file spreadsheet (.xlsx/.xls) jika sudah ada</div>
              </div>
              <button type="button" id="btn_hapus_excel_pertemuan" onclick="hapusExcelPertemuan(event)" class="btn btn-xs btn-default" style="display:none; color:#dc2626; border-color:#fca5a5; padding:2px 6px; z-index:10; position:relative;" title="Batalkan / Hapus Excel (Kembali ke Generate AI)">
                <i class="fa fa-trash"></i>
              </button>
            </div>

            <div id="info_excel_aktif" style="display:none; margin-top:6px; font-size:10.5px; color:#166534; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; padding:4px 8px; line-height:1.4;">
              <i class="fa fa-check-circle"></i> <strong id="info_excel_summary">16 Pertemuan Terverifikasi</strong><br>
              <span style="color:#15803d; font-size:10px;">AI tidak perlu generate 16 pertemuan, langsung pakai data Excel ini.</span>
            </div>
          </div>

          <!-- Dropzone 2: Penelitian Dosen (Opsional) -->
          <div class="rps-dropzone" id="box_penelitian">
            <input type="file" id="file_penelitian" accept=".pdf,.docx,.txt,.md" onchange="handleFileUpload(this, 'penelitian')">
            <i class="fa fa-flask rps-dropzone-icon"></i>
            <div class="rps-dropzone-body">
              <div class="rps-dropzone-title">Penelitian Dosen <span class="text-muted" style="font-weight:normal; font-size:11px;">(Opsional)</span></div>
              <div class="rps-dropzone-desc" id="desc_penelitian">Pilih file PDF / DOCX (bisa dilewati)</div>
            </div>
          </div>

          <!-- Dropzone 3: RPS Lama (Opsional) -->
          <div class="rps-dropzone" id="box_rps_lama" style="margin-bottom:0;">
            <input type="file" id="file_rps_lama" accept=".pdf,.docx,.txt,.md" onchange="handleFileUpload(this, 'rps_lama')">
            <i class="fa fa-file-text-o rps-dropzone-icon"></i>
            <div class="rps-dropzone-body">
              <div class="rps-dropzone-title">Draft RPS Lama / Bahan Kajian</div>
              <div class="rps-dropzone-desc" id="desc_rps_lama">Opsional sebagai referensi materi</div>
            </div>
          </div>
        </div>

        <!-- Section 3: AI Info Footer -->
        <div class="rps-sidebar-section" style="padding:12px 18px;">
          <div style="display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:11px; color:#475569; font-weight:600;">
              <i class="fa fa-server" style="color:#1b4332; margin-right:4px;"></i>
              AI: <span id="badge_ai_provider_sidebar"><?= strtoupper(htmlspecialchars($ai_provider)); ?></span>
            </span>
            <button type="button" class="btn btn-default btn-xs" onclick="bukaModalApiKey()" style="border-radius:4px; font-size:11px; cursor:pointer;">
              <i class="fa fa-key"></i> API Key
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT PANE: DOCUMENT CANVAS (COL-MD-8 STICKY) -->
    <div class="col-md-8 rps-sticky-col">
      <div class="rps-canvas">

        <!-- Tab Navigation (Clean Unified Single Bar) -->
        <div class="rps-tab-nav" id="canvas_tab_nav">
          <button type="button" class="rps-tab-item active" id="tab_studio_cpl" onclick="switchTab('studio_cpl')">
            <i class="fa fa-list-check rps-tab-icon fa-th-list"></i>
            Deskripsi dan CPL
          </button>
          <button type="button" class="rps-tab-item" id="tab_susun_cpmk" onclick="switchTab('susun_cpmk')">
            <i class="fa fa-bullseye rps-tab-icon"></i>
            CPMK
            <i class="fa fa-lock ml-1 text-muted" id="lock_icon_cpmk" style="font-size:10px; margin-left:3px;"></i>
          </button>
          <button type="button" class="rps-tab-item disabled" id="tab_susun_subcpmk" onclick="switchTab('susun_subcpmk')" style="opacity:0.6; cursor:not-allowed;" title="Sub-CPMK Studio akan hadir pada Phase 2">
            <i class="fa fa-cubes rps-tab-icon"></i>
            sub-cpmk
            <i class="fa fa-lock ml-1 text-muted" style="font-size:10px; margin-left:3px;"></i>
          </button>
          <button type="button" class="rps-tab-item" id="tab_susun_rps" onclick="switchTab('susun_rps')">
            <i class="fa fa-book rps-tab-icon"></i>
            RPS
            <i class="fa fa-lock ml-1 text-muted" id="lock_icon_rps" style="font-size:10px; margin-left:3px;"></i>
          </button>
          <button type="button" class="rps-tab-item" id="tab_kontrak_kuliah" onclick="switchTab('kontrak_kuliah')">
            <i class="fa fa-handshake-o rps-tab-icon"></i>
            kontrak kuliah
          </button>
          <button type="button" class="rps-tab-item" id="tab_rancangan_tugas" onclick="switchTab('rancangan_tugas')">
            <i class="fa fa-tasks rps-tab-icon"></i>
            rancangan tugas
          </button>
          <button type="button" class="rps-tab-item" id="tab_roadmap_wbi" onclick="switchTab('roadmap_wbi')">
            <i class="fa fa-sitemap rps-tab-icon"></i>
            roadmap
          </button>
          <button type="button" class="rps-tab-item" id="tab_skenario_pembelajaran" onclick="switchTab('skenario_pembelajaran')">
            <i class="fa fa-calendar-check-o rps-tab-icon"></i>
            Strategi Pertemuan
          </button>
        </div>

        <!-- Canvas Toolbar (doc info + actions) -->
        <div class="rps-canvas-toolbar">
          <div class="rps-canvas-doc-label">
            <i class="fa fa-file-text-o text-muted"></i>
            <span id="canvas_title" style="font-weight:700; font-size:13px; color:#0f172a;">Studio CPL &amp; Deskripsi Mata Kuliah</span>
            <span id="canvas_badge" class="rps-tag" style="margin-left:4px;">Menunggu Perintah</span>
          </div>

          <!-- Utility Toolbar -->
          <div id="canvas_tools" style="display:none; gap:5px; align-items:center;">
            <input type="hidden" id="id_rps_loaded" value="<?= isset($_GET['load_rps_obe']) ? (int)$_GET['load_rps_obe'] : ''; ?>">
            <button type="button" class="btn btn-default btn-xs" onclick="salinHasil()" title="Salin Naskah Markdown">
              <i class="fa fa-copy"></i> Salin
            </button>
            <button type="button" class="btn btn-default btn-xs" onclick="unduhMarkdown()" title="Unduh File Markdown">
              <i class="fa fa-download"></i> .md
            </button>
            <button type="button" class="btn btn-default btn-xs" onclick="cetakHasil()" title="Cetak Dokumen">
              <i class="fa fa-print"></i> Cetak
            </button>
            <button type="button" id="btn_unduh_roadmap" class="btn btn-warning btn-xs" style="display:none; color:#78350f; font-weight:600;" onclick="unduhRoadmapExcel()" title="Unduh File Excel Roadmap Format WBI">
              <i class="fa fa-file-excel-o"></i> .xlsx
            </button>
            <button type="button" id="btn_sync_jadwal_kelas" class="btn btn-primary btn-xs" style="display:none; font-weight:700; background-color:#0284c7; border-color:#0369a1; color:#ffffff; padding:4px 10px;" onclick="bukaModalSyncJadwal()" title="Terapkan Skenario Pembelajaran & Draf BAP 16 Pertemuan ke Jadwal Perkuliahan Kelas">
              <i class="fa fa-calendar-check-o"></i> Terapkan ke Jadwal &amp; BAP
            </button>
            <button type="button" id="btn_save_db_rps_obe" class="btn btn-success btn-xs" style="font-weight:700; background-color:#16a34a; border-color:#15803d; color:#ffffff; padding:4px 10px;" onclick="simpanKeDatabaseRpsObe()" title="Simpan Dokumen ini ke Database RPS OBE">
              <i class="fa fa-save"></i> Simpan ke RPS OBE
            </button>
          </div>
        </div>

        <!-- In-situ Loader Bar -->
        <div id="canvas_loader_bar" class="rps-loader-bar"></div>

        <!-- Canvas Body -->
        <div class="rps-canvas-body">
          <!-- Empty State (Interactive per Tab) -->
          <div id="canvas_placeholder" class="rps-empty-state">
            <div class="rps-empty-icon" id="empty_icon"><i class="fa fa-th-list"></i></div>
            <div class="rps-empty-title" id="empty_title">Studio CPL &amp; Deskripsi Mata Kuliah</div>
            <div class="rps-empty-desc" id="empty_desc">
              Silakan pilih Mata Kuliah di panel kiri untuk memuat CPL resmi program studi, menyelaraskan peran CPL, dan menyempurnakan Deskripsi Mata Kuliah.
            </div>
            <div>
              <button type="button" class="btn-generate-ai" id="btn_empty_generate" onclick="jalankanAksi(activeMode)" style="margin: 0 auto; display:inline-flex;">
                <i class="fa fa-magic"></i> Muat &amp; Susun Studio CPL
              </button>
            </div>
          </div>

          <!-- ================================================================= -->
          <!-- PHASE 1.5: STUDIO CPL & DESKRIPSI MATA KULIAH CANVAS VIEW -->
          <!-- ================================================================= -->
          <div id="canvas_cpl_studio_view" style="display:none;">
            <!-- Header Banner Status CPL-MK -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
              <div>
                <strong style="color:#0f172a; font-size:13px;"><i class="fa fa-graduation-cap text-primary"></i> Kurikulum &amp; Pemetaan CPL Mata Kuliah</strong>
                <div style="font-size:11.5px; color:#64748b; margin-top:2px;" id="cpl_studio_meta_subtitle">
                  Pilih CPL resmi yang dibebankan dan tentukan peran CPL (Primary / Supporting).
                </div>
              </div>
              <div>
                <span class="rps-tag" id="cpl_studio_status_badge" style="font-size:11px; font-weight:700; background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                  DRAFT
                </span>
              </div>
            </div>

            <!-- Alert Banner Stale Curriculum -->
            <div id="cpl_stale_banner" style="display:none; background:#fffbeb; border:1px solid #fef3c7; border-left:4px solid #d97706; padding:12px 16px; border-radius:6px; margin-bottom:14px; font-size:12px; color:#92400e;">
              <div style="display:flex; align-items:center; gap:10px;">
                <i class="fa fa-exclamation-triangle" style="font-size:18px; color:#d97706; flex-shrink:0;"></i>
                <div>
                  <strong style="color:#b45309; display:block; margin-bottom:2px;">Perhatian Dokumen Kurikulum:</strong>
                  <span id="cpl_stale_message">Dokumen kurikulum prodi ini belum berstatus LOCKED atau mengalami pembaruan. Disarankan meninjau ulang pemetaan CPL.</span>
                </div>
              </div>
            </div>

            <!-- Alert Banner Special Matrix Source Status -->
            <div id="cpl_special_status_banner" style="display:none; background:#eff6ff; border:1px solid #dbeafe; border-left:4px solid #3b82f6; padding:12px 16px; border-radius:6px; margin-bottom:14px; font-size:12px; color:#1e40af;">
              <div style="display:flex; align-items:center; gap:10px;">
                <i class="fa fa-info-circle" style="font-size:18px; color:#2563eb; flex-shrink:0;"></i>
                <div>
                  <strong id="cpl_special_status_title" style="color:#1d4ed8; display:block; margin-bottom:2px;">Status Pemetaan Kurikulum:</strong>
                  <span id="cpl_special_status_text">Informasi khusus pemetaan kurikulum mata kuliah ini.</span>
                </div>
              </div>
            </div>

            <!-- Alert Banner Trust & Governance Guard for Unparsed Matrix -->
            <div id="cpl_unparsed_banner" style="display:none; background:#fffbeb; border:1px solid #fef3c7; border-left:4px solid #f59e0b; padding:12px 16px; border-radius:6px; margin-bottom:14px; font-size:12px; color:#92400e;">
              <div style="display:flex; align-items:center; gap:10px;">
                <i class="fa fa-exclamation-triangle" style="font-size:18px; color:#d97706; flex-shrink:0;"></i>
                <div>
                  <strong style="color:#b45309; display:block; margin-bottom:2px;">Perhatian Tata Kelola Kurikulum:</strong>
                  <span>Dokumen kurikulum memiliki pemetaan resmi yang belum seluruhnya berhasil dibaca sistem. Pilihan CPL perlu ditinjau.</span>
                </div>
              </div>
            </div>

            <!-- SUBSECTION 1: DESKRIPSI MATA KULIAH -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px; margin-bottom:14px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <label style="font-weight:700; font-size:12.5px; color:#0f172a; margin:0;">
                  <i class="fa fa-align-left text-primary mr-1"></i> Deskripsi Mata Kuliah
                </label>
                <span id="badge_desc_source" style="font-size:10px; font-weight:700; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:4px; border:1px solid #bae6fd;">
                  Sumber: Kurikulum
                </span>
              </div>
              <textarea id="studio_deskripsi_mk" rows="3" class="form-control" style="font-size:12px; line-height:1.6; resize:vertical; border-radius:6px; margin-bottom:8px;" placeholder="Tuliskan atau susun deskripsi ringkas mata kuliah ini (3-5 kalimat)..."></textarea>
              <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px;">
                <span style="font-size:11px; color:#64748b;">
                  <i class="fa fa-info-circle"></i> Deskripsi digunakan sebagai grounding kontekstual perumusan CPMK.
                </span>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                  <button type="button" class="btn btn-default btn-xs" id="btn_ambil_desc_rps_lama" onclick="ambilDeskripsiDariRpsLama()" style="font-weight:600; font-size:11px; color:#1e40af;" title="Ambil deskripsi & referensi dari arsip database siakad_rps_online">
                    <i class="fa fa-database"></i> Ambil dari RPS Lama
                  </button>
                  <button type="button" class="btn btn-default btn-xs" id="btn_ambil_desc_kurikulum" onclick="ambilDeskripsiDariKurikulum()" style="font-weight:600; font-size:11px;">
                    <i class="fa fa-book"></i> Ambil dari Kurikulum
                  </button>
                  <button type="button" class="btn btn-default btn-xs" id="btn_draft_desc_ai" onclick="draftDeskripsiDenganAi()" style="font-weight:600; font-size:11px; color:#0f766e;">
                    <i class="fa fa-magic"></i> Susun Draft dengan AI
                  </button>
                </div>
              </div>
            </div>

            <!-- SUBSECTION 2: DAFTAR REFERENSI PERKULIAHAN (MANDATORY) -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px; margin-bottom:14px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <label style="font-weight:700; font-size:12.5px; color:#0f172a; margin:0;">
                  <i class="fa fa-bookmark text-primary mr-1"></i> Referensi &amp; Pustaka Perkuliahan <span class="text-danger">*</span>
                </label>
                <span style="font-size:10.5px; color:#64748b;">Buku teks utama &amp; pendukung</span>
              </div>
              <textarea id="studio_referensi_mk" rows="3" class="form-control" style="font-size:12px; line-height:1.5; resize:vertical; border-radius:6px; margin-bottom:4px;" placeholder="Cantumkan daftar buku/artikel rujukan perkuliahan (Contoh: 1. Nama Penulis (Tahun). Judul Buku. Penerbit...)"></textarea>
              <span style="font-size:11px; color:#64748b;">
                <i class="fa fa-shield"></i> Referensi faktual ini akan otomatis digunakan pada RPS Lengkap dan Kontrak Kuliah.
              </span>
            </div>

            <!-- SUBSECTION 3: DAFTAR CPL RESMI PRODI & PEMILIHAN CPL-MK -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px; margin-bottom:14px;">
              <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
                <div>
                  <label style="font-weight:700; font-size:12.5px; color:#0f172a; margin:0;">
                    <i class="fa fa-check-square-o text-primary mr-1"></i> Butir CPL Resmi Program Studi
                  </label>
                  <div style="font-size:11px; color:#64748b;">
                    Pilih CPL yang difokuskan dalam RPS dan tentukan perannya (Utama / Pendukung). CPL resmi yang tidak dipilih tetap tercatat sebagai bukti kurikulum.
                  </div>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                  <div class="btn-group btn-group-xs" id="cpl_filter_buttons">
                    <button type="button" class="btn btn-default active" onclick="filterCplStudioList('all')">Semua (<span id="count_cpl_all">0</span>)</button>
                    <button type="button" class="btn btn-default" onclick="filterCplStudioList('selected')">Fokus RPS (<span id="count_cpl_selected">0</span>)</button>
                    <button type="button" class="btn btn-default" onclick="filterCplStudioList('recommended')">Rekomendasi / Terpetakan</button>
                  </div>
                  <button type="button" class="btn btn-default btn-xs" id="btn_cpl_rps_lama" onclick="bukaModalCplRpsLama()" style="font-weight:600; font-size:11px; color:#1e40af; border-color:#93c5fd; background:#eff6ff;" title="Bandingkan dan pilih CPL dari arsip RPS Lama (siakad_rps_online)">
                    <i class="fa fa-history text-primary"></i> RPS Lama
                  </button>
                  <button type="button" class="btn btn-xs btn-primary" id="btn_rekomendasi_cpl_ai" onclick="mintaRekomendasiCplAi()" style="background:#0f766e; border-color:#0f766e; font-weight:600; font-size:11px;" title="Minta saran klasifikasi peran CPL (Primary/Supporting) berbasis AI">
                    <i class="fa fa-magic"></i> Rekomendasi Peran CPL (AI)
                  </button>
                </div>
              </div>

              <!-- Container List CPL Items -->
              <div id="cpl_studio_list_container" style="display:flex; flex-direction:column; gap:8px; max-height:420px; overflow-y:auto; padding-right:4px;">
                <div style="text-align:center; padding:20px; color:#94a3b8; font-size:12px;">
                  <i class="fa fa-spinner fa-spin"></i> Memuat butir CPL resmi...
                </div>
              </div>
            </div>

            <!-- BOTTOM ACTION BAR STUDIO CPL -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; padding-top:12px; border-top:1px solid #e2e8f0;">
              <div style="font-size:12px; color:#334155;">
                <strong>Ringkasan:</strong> <span id="summary_cpl_selection">0 CPL Terpilih (0 Primary, 0 Supporting)</span>
              </div>
              <div style="display:flex; gap:6px;">
                <button type="button" class="btn btn-default btn-sm" id="btn_simpan_draft_cpl" onclick="simpanDraftCplStudio()" style="font-weight:700; border-radius:6px; color:#334155;">
                  <i class="fa fa-save"></i> Simpan Draf CPL-MK
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btn_setujui_cpl" onclick="setujuiCplStudio()" style="font-weight:700; background:#15803d; border-color:#166534; color:#ffffff; border-radius:6px; padding:6px 16px; box-shadow:0 2px 6px rgba(21,128,61,0.25);">
                  <i class="fa fa-check-circle"></i> Setujui CPL &amp; Deskripsi (APPROVED)
                </button>
                <button type="button" class="btn btn-warning btn-sm" id="btn_revisi_cpl" onclick="revisiCplStudio()" style="display:none; font-weight:700; border-radius:6px; padding:6px 16px;">
                  <i class="fa fa-pencil"></i> Revisi CPL &amp; Deskripsi
                </button>
              </div>
            </div>

            <!-- Notice Sukses Approval & Akses ke CPMK Studio -->
            <div id="cpl_approved_notice" style="display:none; margin-top:12px; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:8px; padding:12px 16px; font-size:12px; color:#065f46; line-height:1.5;">
              <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <div>
                  <i class="fa fa-check-circle" style="font-size:15px; color:#10b981; margin-right:4px;"></i>
                  <strong>CPL-MK &amp; Deskripsi telah disetujui (APPROVED).</strong> Silakan lanjutkan ke <strong>Studio CPMK</strong> untuk merumuskan CPMK berbasis CPL terpilih.
                </div>
                <button type="button" class="btn btn-success btn-sm" style="background:#15803d; border-color:#166534; font-weight:700; border-radius:6px; padding:5px 14px; font-size:12px; white-space:nowrap;" onclick="switchTab('susun_cpmk')">
                  <i class="fa fa-bullseye"></i> Buka Studio CPMK &rarr;
                </button>
              </div>
            </div>
          </div>

          <!-- CPMK Studio Card Editor View (Phase 2A) -->
          <div id="canvas_cpmk_view" style="display:none;">
            <!-- Header Studio CPMK -->
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid #e2e8f0; flex-wrap:wrap; gap:8px;">
              <div>
                <h4 style="margin:0; font-size:16px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                  <i class="fa fa-bullseye" style="color:#0f766e;"></i> Studio Capaian Pembelajaran Mata Kuliah (CPMK)
                </h4>
                <div style="font-size:11.5px; color:#64748b; margin-top:2px;" id="cpmk_studio_meta_subtitle">
                  Perumusan CPMK berbasis kurikulum resmi &amp; CPL yang telah disetujui
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:6px;">
                <span class="rps-tag" id="cpmk_global_status_badge" style="font-size:11px; font-weight:700; background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                  DRAFT
                </span>
              </div>
            </div>

            <!-- 1. Official CPMK Source Status Banner -->
            <div id="cpmk_official_source_banner" style="display:none; margin-bottom:12px; border-radius:8px; padding:12px 16px; font-size:12px; line-height:1.5;">
              <!-- Injected by JS -->
            </div>

            <!-- 2. Approved CPL Grounding Summary Header -->
            <div id="cpmk_grounding_banner" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
              <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <strong style="color:#334155; font-size:12px;"><i class="fa fa-shield text-teal"></i> CPL Pendorong (Primary):</strong>
                <span id="cpmk_primary_cpl_badges"></span>
                <span style="color:#cbd5e1;">|</span>
                <strong style="color:#64748b; font-size:12px;"><i class="fa fa-info-circle text-muted"></i> Kontekstual (Supporting):</strong>
                <span id="cpmk_supporting_cpl_badges"></span>
              </div>
              <div style="font-size:11.5px; color:#64748b;" id="cpmk_cpl_summary_counts">
                <!-- Injected by JS -->
              </div>
            </div>

            <!-- 3. Primary CPL Coverage Matrix (Collapsible Table) -->
            <div id="cpmk_matrix_wrapper" style="margin-bottom:14px; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
              <div style="padding:8px 12px; background:#f1f5f9; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; cursor:pointer;" onclick="$('#cpmk_matrix_body').slideToggle(150);">
                <div style="font-size:12px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:6px;">
                  <i class="fa fa-table" style="color:#0f766e;"></i> Matriks Ketercapaian CPL &times; CPMK (Coverage Matrix)
                </div>
                <div style="font-size:11px; color:#0f766e; font-weight:600;">
                  <span id="matrix_coverage_status_badge"></span> <i class="fa fa-chevron-down" style="margin-left:4px;"></i>
                </div>
              </div>
              <div id="cpmk_matrix_body" style="padding:10px; overflow-x:auto;">
                <table class="table table-bordered table-condensed" style="margin:0; font-size:11.5px;" id="table_cpmk_coverage_matrix">
                  <thead>
                    <tr style="background:#f8fafc; color:#334155;">
                      <th style="width:70px; text-align:center;">Kode CPL</th>
                      <th style="width:85px; text-align:center;">Peran</th>
                      <th>Deskripsi CPL</th>
                      <th id="th_cpmk_matrix_cols" style="text-align:center;">CPMK Mapping</th>
                      <th style="width:90px; text-align:center;">Status</th>
                    </tr>
                  </thead>
                  <tbody id="tbody_cpmk_coverage_matrix">
                    <!-- Injected by JS -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- 4. Validation Alert Box (if any errors exist) -->
            <div id="cpmk_validation_box" style="display:none; margin-bottom:14px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 14px; font-size:12px; color:#991b1b;">
              <!-- Injected by JS -->
            </div>

            <!-- 5. List of CPMK Cards -->
            <div id="cpmk_cards_container" style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
              <!-- Cards rendered dynamically via JS -->
            </div>

            <!-- 6. Bottom Action Toolbar for CPMK Studio -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; padding-top:12px; border-top:1px solid #e2e8f0;">
              <div style="display:flex; gap:6px;">
                <button type="button" class="btn btn-default btn-sm" id="btn_tambah_cpmk" onclick="tambahCpmkManual()" style="font-weight:600; border-radius:6px;">
                  <i class="fa fa-plus"></i> Tambah CPMK
                </button>
                <button type="button" class="btn btn-default btn-sm" id="btn_susun_ulang_cpmk" onclick="jalankanGenerateCpmk()" style="font-weight:600; border-radius:6px;" title="Susun Usulan CPMK dengan AI (Fallback)">
                  <i class="fa fa-magic text-primary"></i> Usulan AI (Fallback)
                </button>
              </div>
              <div style="display:flex; gap:6px;">
                <button type="button" class="btn btn-default btn-sm" id="btn_simpan_draft_cpmk" onclick="simpanDraftCpmk()" style="font-weight:700; border-radius:6px; color:#334155;">
                  <i class="fa fa-save"></i> Simpan Draf CPMK
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btn_setujui_cpmk" onclick="setujuiCpmk()" style="font-weight:700; background:#15803d; border-color:#166534; color:#ffffff; border-radius:6px; padding:6px 16px; box-shadow:0 2px 6px rgba(21,128,61,0.25);">
                  <i class="fa fa-check-circle"></i> Setujui CPMK (APPROVED)
                </button>
                <button type="button" class="btn btn-warning btn-sm" id="btn_revisi_cpmk" onclick="revisiCpmk()" style="display:none; font-weight:700; border-radius:6px; padding:6px 16px;">
                  <i class="fa fa-pencil"></i> Revisi CPMK
                </button>
              </div>
            </div>

            <div id="cpmk_approved_notice" style="display:none; margin-top:12px; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:8px; padding:10px 14px; font-size:12px; color:#065f46; line-height:1.5;">
              <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <div>
                  <i class="fa fa-check-circle" style="font-size:14px; color:#10b981; margin-right:4px;"></i>
                  <strong>CPMK telah disetujui (APPROVED).</strong> CPMK kini menjadi acuan resmi penyusunan dokumen turunan.
                </div>
                <button type="button" class="btn btn-success btn-sm" style="background:#15803d; border-color:#166534; font-weight:700; border-radius:6px; padding:5px 14px; font-size:12px; white-space:nowrap;" onclick="switchTab('susun_rps'); jalankanAksi('susun_rps');">
                  <i class="fa fa-book"></i> Generate RPS Lengkap &rarr;
                </button>
              </div>
            </div>
          </div>

          <!-- Loading State (Rich Interactive AI Progress) -->
          <div id="canvas_loading" style="display:none;">
            <div class="rps-loading-box">
              <div class="rps-ai-avatar">
                <i class="fa fa-magic"></i>
              </div>
              <div style="font-size:15px; font-weight:700; color:#0f172a; margin-bottom:4px;" id="loading_label">
                AI sedang menyusun RPS lengkap 16 pertemuan dan Kontrak Kuliah...
              </div>
              <div style="font-size:12px; color:#64748b;" id="loading_subtext">
                AI sedang menyelaraskan dokumen acuan dengan regulasi SN-Dikti 2026 & SPMI WBI.
              </div>

              <!-- Live Progress Stepper (content injected per mode by JS) -->
              <div class="rps-progress-steps" id="rps_progress_steps_container">
                <!-- Steps are rendered dynamically -->
              </div>

              <!-- Skeleton Shimmer Mockup -->
              <div class="rps-skeleton-shimmer">
                <div class="rps-shimmer-line" style="width: 45%;"></div>
                <div class="rps-shimmer-line" style="width: 85%;"></div>
                <div class="rps-shimmer-line" style="width: 70%;"></div>
                <div class="rps-shimmer-line" style="width: 95%;"></div>
                <div class="rps-shimmer-line" style="width: 60%;"></div>
              </div>
            </div>
          </div>

          <!-- Content Output -->
          <div id="canvas_output" style="display:none;">
            <div id="rendered_markdown" class="rps-doc-view"></div>

            <!-- Interactive Continuation Warning Banner -->
            <div id="rps_continue_banner" style="display:none; margin:20px 0 10px 0; padding:16px 20px; background:linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border:1.5px solid #f59e0b; border-radius:10px; box-shadow:0 4px 12px rgba(245, 158, 11, 0.15);">
              <div style="display:flex; align-items:flex-start; gap:14px;">
                <div style="width:38px; height:38px; border-radius:50%; background:#fef3c7; border:2px solid #f59e0b; display:flex; align-items:center; justify-content:center; color:#b45309; font-size:17px; flex-shrink:0;">
                  <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div style="flex:1;">
                  <div style="font-size:14px; font-weight:700; color:#92400e; margin-bottom:4px;">
                    Penyusunan Terhenti di Batas Output Token AI
                  </div>
                  <div style="font-size:12.5px; color:#78350f; line-height:1.5; margin-bottom:12px;">
                    Dokumen ini sangat lengkap dan komprehensif sehingga panjangnya mencapai batas maksimal 1 respons AI. Anda dapat langsung melanjutkan bagian berikutnya tanpa perlu mengulang dari awal.
                  </div>
                  <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="btn btn-warning btn-sm" id="btn_lanjutkan_generate" onclick="lanjutkanDokumen()" style="background:#d97706; border-color:#b45309; color:#ffffff; font-weight:700; padding:8px 18px; border-radius:6px; box-shadow:0 2px 6px rgba(217, 119, 6, 0.3); font-size:13px; cursor:pointer;">
                      <i class="fa fa-play-circle"></i> Lanjutkan Penyusunan Dokumen
                    </button>
                    <span style="font-size:11.5px; color:#92400e; font-style:italic;">
                      <i class="fa fa-info-circle"></i> AI akan menyambung persis dari baris terakhir.
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== AI COMMAND BAR ===== -->
        <div class="rps-command-bar" id="rps_command_bar" style="display:none;">
          <!-- Chat / Prompt Interaction Stream (Expandable) -->
          <div class="rps-history-panel" id="rps_history_panel" style="display:none;">
            <div class="rps-history-header" onclick="togglePromptHistory()">
              <span>
                <i class="fa fa-comments-o text-primary"></i> 
                <strong>Riwayat Instruksi &amp; Prompt AI</strong> 
                <span id="prompt_history_count" style="font-size:10px; font-weight:600; color:#64748b; margin-left:4px;">(0 riwayat)</span>
              </span>
              <span style="font-size:11px; color:#64748b;">
                <span id="label_toggle_history">Tutup</span> <i id="icon_toggle_history" class="fa fa-chevron-up" style="font-size:10px; margin-left:2px;"></i>
              </span>
            </div>
            <div class="rps-history-list" id="prompt_history_list">
              <div class="rps-history-empty" id="history_empty_state">
                <i class="fa fa-commenting-o"></i> Belum ada instruksi tambahan. Ketik prompt revisi di bawah untuk menyesuaikan naskah dokumen ini.
              </div>
            </div>
          </div>

          <div class="rps-command-wrap">
            <textarea
              id="mk_catatan"
              class="rps-command-input"
              rows="2"
              placeholder="Ketik instruksi atau revisi untuk AI... (misal: 'Ubah bobot UAS jadi 25% dan tambahkan evaluasi portofolio')"
              oninput="autoResizeTextarea(this)"
              onkeydown="if(event.ctrlKey && event.keyCode == 13){ jalankanAksi(activeMode); return false; }"
            ></textarea>
            <button type="button" class="btn-generate-ai" id="btn_submit_generate" onclick="jalankanAksi(activeMode)">
              <i class="fa fa-magic" style="font-size:15px;"></i>
              <span style="line-height:1.2;">Generate <strong id="label_generate_mode">RPS Lengkap</strong></span>
            </button>
          </div>
          <div class="rps-command-footer">
            <span>
              <i class="fa fa-info-circle text-primary"></i> Dokumen aktif: <strong id="footer_active_doc_name" style="color:#0f172a;">RPS Lengkap 16 Pertemuan</strong>
            </span>
            <span style="font-size:10.5px; color:#94a3b8;">
              <i class="fa fa-keyboard-o"></i> Tekan <strong>Ctrl + Enter</strong> untuk kirim prompt
            </span>
          </div>
        </div>
        <!-- ===== END COMMAND BAR ===== -->

      </div>
    </div>
  </div>
</div>

<!-- Hidden Form for Exporting Excel -->
<form id="formExportExcel" action="<?= site_url('C_rps_generator/export_excel_wbi'); ?>" method="POST" target="_blank" style="display:none;">
  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <input type="hidden" name="kode_mk" id="export_kode_mk">
  <input type="hidden" name="nama_mk" id="export_nama_mk">
  <input type="hidden" name="matrix_json" id="export_matrix_json">
  <input type="hidden" name="referensi" id="export_referensi">
</form>

<!-- Hidden Form for Exporting Roadmap Excel -->
<form id="formExportRoadmap" action="<?= site_url('C_rps_generator/export_roadmap_excel'); ?>" method="POST" target="_blank" style="display:none;">
  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
  <input type="hidden" name="kode_mk" id="export_roadmap_kode_mk">
  <input type="hidden" name="nama_mk" id="export_roadmap_nama_mk">
  <input type="hidden" name="roadmap_json" id="export_roadmap_json">
</form>

<!-- Modal: Sinkronisasi Skenario Pertemuan & Draf BAP ke Jadwal Kelas -->
<div class="modal fade" id="modalSyncJadwalKelas" tabindex="-1" role="dialog" aria-labelledby="modalSyncJadwalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 680px; margin: 30px auto;">
    <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 12px 35px rgba(0,0,0,0.2); overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color:#ffffff; padding:16px 22px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.85; font-size:22px; text-shadow:none;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalSyncJadwalLabel" style="font-weight:700; font-size:16px; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="fa fa-calendar-check-o"></i> Terapkan Skenario &amp; Draf BAP ke Jadwal Kelas
        </h4>
        <p style="margin:4px 0 0 0; font-size:12px; color:#e0f2fe;">
          Sinkronisasi 16 rencana pertemuan SCL &amp; narasi draf BAP ke Virtual Class SI-Akademik
        </p>
      </div>

      <div class="modal-body" style="padding:22px 24px; background:#f8fafc;">
        
        <!-- Banner Informasi -->
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:12px 14px; margin-bottom:18px; font-size:12px; color:#166534; line-height:1.5;">
          <i class="fa fa-info-circle" style="color:#16a34a; font-size:14px; margin-right:4px;"></i>
          <strong>Sinkronisasi Terarah:</strong>
          Pilih satu atau beberapa kelas perkuliahan di bawah. Sistem akan menyiapkan 16 pertemuan lengkap beserta draf BAP faktual sesuai jadwal hari masing-masing kelas.
        </div>

        <form id="formSyncJadwal">
          
          <!-- Section 1: Daftar Kelas Perkuliahan Sesuai Mata Kuliah Ini -->
          <div style="margin-bottom:18px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <label style="font-size:12.5px; font-weight:700; color:#1e293b; margin:0;">
                <i class="fa fa-graduation-cap text-primary"></i> Pilih Kelas Target (<span id="label_mk_sync">Mata Kuliah</span>):
              </label>
              <div id="btn_toggle_select_all" style="font-size:11.5px; display:flex; gap:8px;">
                <a href="javascript:void(0)" onclick="selectAllSyncClasses(true)" style="color:#0284c7; font-weight:600;">Pilih Semua</a>
                <span style="color:#cbd5e1;">|</span>
                <a href="javascript:void(0)" onclick="selectAllSyncClasses(false)" style="color:#64748b; font-weight:600;">Hapus Pilihan</a>
              </div>
            </div>

            <div id="loading_schedules" style="display:none; font-size:12px; color:#64748b; padding:14px; text-align:center; background:#ffffff; border-radius:8px; border:1px solid #e2e8f0;">
              <i class="fa fa-spinner fa-spin fa-2x text-primary" style="margin-bottom:6px; display:block;"></i>
              Memuat jadwal mengajar aktif Anda...
            </div>

            <!-- Container Card Kelas yang Sesuai -->
            <div id="container_matching_classes" style="display:flex; flex-direction:column; gap:8px;">
              <!-- Dynamic class cards inserted here -->
            </div>

            <!-- Accordion Jadwal Lain jika perlu -->
            <div id="box_other_schedules_wrapper" style="margin-top:10px; display:none;">
              <a href="#collapseOtherSchedules" data-toggle="collapse" style="font-size:11.5px; color:#64748b; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600;">
                <i class="fa fa-chevron-down" style="font-size:10px;"></i> Tampilkan jadwal kelas mata kuliah lain (Cross-listing / Penggabungan)
              </a>
              <div id="collapseOtherSchedules" class="collapse" style="margin-top:8px;">
                <div id="container_other_classes" style="display:flex; flex-direction:column; gap:8px; max-height:160px; overflow-y:auto; padding-right:4px;">
                  <!-- Dynamic other class cards -->
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Pengaturan Tanggal Mulai Perkuliahan Semester -->
          <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; margin-bottom:16px;">
            <div class="form-group" style="margin-bottom:10px;">
              <label style="font-size:12px; font-weight:700; color:#334155; margin-bottom:4px; display:block;">
                <i class="fa fa-calendar text-primary"></i> Tanggal Mulai Perkuliahan Semester:
              </label>
              <input type="date" class="form-control" id="input_sync_tanggal_mulai" name="tanggal_mulai" value="<?= date('Y-m-d'); ?>" onchange="updateSyncSchedulePreview()" style="border-radius:6px; font-size:13px; height:38px; border-color:#cbd5e1; max-width:240px;">
            </div>
            
            <!-- Dynamic Projection Preview Box -->
            <div id="sync_date_projection_box" style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:6px; padding:10px 12px; font-size:11.5px; color:#0369a1; line-height:1.5;">
              <i class="fa fa-lightbulb-o text-info" style="margin-right:4px;"></i>
              <span id="sync_date_projection_text">Pilih kelas di atas untuk melihat proyeksi tanggal pertemuan pertama.</span>
            </div>
          </div>

          <!-- Section 3: Opsi Overwrite -->
          <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
              <label style="font-size:12px; color:#334155; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; margin:0;">
                <input type="checkbox" id="check_sync_overwrite" name="overwrite" value="1" checked style="width:16px; height:16px; margin:0; cursor:pointer; accent-color:#0284c7;">
                <span>Perbarui rencana pertemuan yang <strong>belum direalisasikan</strong></span>
              </label>
            </div>
          </div>

        </form>
      </div>
      <div class="modal-footer" style="background:#ffffff; border-top:1px solid #e2e8f0; padding:12px 24px; display:flex; justify-content:space-between; align-items:center;">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Batal</button>
        <button type="button" class="btn btn-primary" id="btn_execute_sync_jadwal" onclick="eksekusiSyncJadwalKelas()" style="background:#0284c7; border:1px solid #0284c7; font-weight:700; border-radius:6px; padding:7px 20px;">
          <i class="fa fa-refresh"></i> Terapkan ke Jadwal Perkuliahan
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Reprompt Single CPMK dengan AI -->
<div class="modal fade" id="modalRepromptCpmk" tabindex="-1" role="dialog" aria-labelledby="modalRepromptCpmkLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 520px; margin: 40px auto;">
    <div class="modal-content" style="border-radius:10px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.2); overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%); color:#ffffff; padding:14px 18px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.85; text-shadow:none;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalRepromptCpmkLabel" style="font-weight:700; font-size:14px; margin:0; display:flex; align-items:center; gap:6px;">
          <i class="fa fa-magic"></i> Perbaiki <span id="reprompt_cpmk_kode">CPMK-1</span> dengan AI
        </h4>
      </div>
      <div class="modal-body" style="padding:16px 18px; background:#f8fafc;">
        <input type="hidden" id="reprompt_cpmk_index" value="">
        <div class="form-group" style="margin-bottom:12px;">
          <label style="font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px; display:block;">Teks CPMK Saat Ini:</label>
          <div id="reprompt_cpmk_current_text" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:8px 12px; font-size:12px; color:#475569; line-height:1.5;"></div>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label style="font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px; display:block;">Instruksi Revisi Khusus untuk AI:</label>
          <textarea id="reprompt_cpmk_instruction" class="form-control" rows="3" placeholder="Contoh: 'Fokuskan pada perancangan arsitektur RESTful API modern dan keamanan autentikasi JWT...'" style="border-radius:6px; font-size:12.5px; border-color:#cbd5e1;"></textarea>
          <span style="font-size:10.5px; color:#64748b; margin-top:4px; display:block;">
            <i class="fa fa-info-circle"></i> AI hanya akan memperbarui rumusan butir CPMK ini dan mempertahankan butir lainnya.
          </span>
        </div>
      </div>
      <div class="modal-footer" style="background:#ffffff; border-top:1px solid #e2e8f0; padding:10px 18px; display:flex; justify-content:space-between; align-items:center;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Batal</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn_execute_reprompt_cpmk" onclick="kirimRepromptCpmk()" style="background:#1b4332; border:1px solid #1b4332; font-weight:700; border-radius:6px; padding:6px 16px;">
          <i class="fa fa-paper-plane"></i> Terapkan Revisi AI
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Pilih / Edit CPL Terkait untuk Butir CPMK -->
<div class="modal fade" id="modalPilihCplCpmk" tabindex="-1" role="dialog" aria-labelledby="modalPilihCplCpmkLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document" style="max-width: 620px; margin: 35px auto;">
    <div class="modal-content" style="border-radius:10px; border:none; box-shadow:0 12px 35px rgba(0,0,0,0.25); overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color:#ffffff; padding:14px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.85; text-shadow:none;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalPilihCplCpmkLabel" style="font-weight:700; font-size:15px; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="fa fa-crosshairs"></i> <span id="modal_pilih_cpl_cpmk_title">Pilih CPL Terkait untuk CPMK</span>
        </h4>
      </div>
      <div class="modal-body" style="padding:16px 20px; background:#f8fafc; max-height:480px; overflow-y:auto;">
        <input type="hidden" id="pilih_cpl_cpmk_target_index" value="">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; margin-bottom:12px;">
          <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:4px;">Rumusan Butir CPMK:</div>
          <div id="modal_pilih_cpl_cpmk_text_preview" style="font-size:12.5px; color:#1e293b; font-weight:500; line-height:1.4;"></div>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <span style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.04em;">
            <i class="fa fa-check-circle text-teal"></i> CPL Tersedia untuk Mata Kuliah Ini:
          </span>
          <div style="display:flex; gap:6px;">
            <button type="button" class="btn btn-default btn-xs" onclick="selectAllCplForCpmk(true)" style="font-size:10.5px; border-radius:4px; font-weight:600;"><i class="fa fa-check-square-o text-success"></i> Pilih Semua</button>
            <button type="button" class="btn btn-default btn-xs" onclick="selectAllCplForCpmk(false)" style="font-size:10.5px; border-radius:4px; font-weight:600;"><i class="fa fa-square-o text-muted"></i> Kosongkan</button>
          </div>
        </div>
        <div id="pilih_cpl_cpmk_list" style="display:flex; flex-direction:column; gap:8px;">
          <!-- Rendered dynamically by JavaScript -->
        </div>
      </div>
      <div class="modal-footer" style="background:#ffffff; border-top:1px solid #e2e8f0; padding:10px 18px; display:flex; justify-content:space-between; align-items:center;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Batal</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="simpanPilihanCplCpmk()" style="background:#0f766e; border:1px solid #0f766e; font-weight:700; border-radius:6px; padding:6px 18px;">
          <i class="fa fa-save"></i> Terapkan Pilihan CPL
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Ambil Deskripsi & Referensi dari Database RPS Lama (siakad_rps_online) -->
<div class="modal fade" id="modal_ambil_rps_lama" tabindex="-1" role="dialog" aria-labelledby="modalAmbilRpsLamaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 880px; margin: 30px auto;">
    <div class="modal-content" style="border-radius:10px; border:none; box-shadow:0 12px 35px rgba(0,0,0,0.25); overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color:#ffffff; padding:14px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.85; text-shadow:none;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalAmbilRpsLamaLabel" style="font-weight:700; font-size:15px; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="fa fa-database"></i> Pilih Deskripsi &amp; Referensi dari Arsip RPS Online
        </h4>
      </div>
      <div class="modal-body" style="padding:18px 20px; background:#f8fafc;">
        
        <!-- Search & Info Bar -->
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; margin-bottom:14px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="flex:1; min-width:240px;">
              <label style="font-size:11px; font-weight:700; color:#475569; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.04em;">
                <i class="fa fa-search text-primary"></i> Pencarian Arsip RPS di Database:
              </label>
              <div class="input-group input-group-sm">
                <input type="text" id="input_modal_search_rps_lama" class="form-control" placeholder="Cari berdasarkan Kode MK, Nama MK, atau Nama Dosen..." onkeydown="if(event.keyCode===13){ cariArsipRpsLamaModal(); }">
                <span class="input-group-btn">
                  <button class="btn btn-primary" type="button" onclick="cariArsipRpsLamaModal()" style="font-weight:600; background:#2563eb; border-color:#2563eb;">
                    <i class="fa fa-search"></i> Cari
                  </button>
                </span>
              </div>
            </div>
            <div style="font-size:11.5px; color:#64748b; line-height:1.4;">
              <div>MK Target: <strong id="modal_rps_lama_target_mk" style="color:#0f172a;">—</strong></div>
              <div>Kode MK: <strong id="modal_rps_lama_target_kode" style="color:#0f172a;">—</strong></div>
            </div>
          </div>
        </div>

        <!-- Candidate Cards Container -->
        <div id="modal_rps_lama_candidates_container" style="display:flex; flex-direction:column; gap:10px; max-height:460px; overflow-y:auto; padding-right:4px;">
          <!-- Rendered via JS -->
        </div>

      </div>
      <div class="modal-footer" style="background:#ffffff; border-top:1px solid #e2e8f0; padding:10px 20px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:11.5px; color:#64748b;" id="modal_rps_lama_footer_summary">
          <i class="fa fa-info-circle"></i> Memilih salah satu arsip akan otomatis mengisi Deskripsi MK dan Referensi pada form Studio CPL.
        </span>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Bandingkan & Pilih CPL dari Arsip RPS Online (siakad_rps_online) -->
<div class="modal fade" id="modal_cpl_rps_lama" tabindex="-1" role="dialog" aria-labelledby="modalCplRpsLamaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 1040px; margin: 25px auto;">
    <div class="modal-content" style="border-radius:10px; border:none; box-shadow:0 15px 40px rgba(0,0,0,0.3); overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color:#ffffff; padding:14px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.85; text-shadow:none;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalCplRpsLamaLabel" style="font-weight:700; font-size:15px; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="fa fa-balance-scale text-warning"></i> Pembanding &amp; Pemilihan CPL dari Arsip RPS Online (Lama)
        </h4>
      </div>
      <div class="modal-body" style="padding:16px 20px; background:#f8fafc;">
        
        <!-- Header Info & Search Switcher -->
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div style="font-size:12px; color:#334155;">
            <div>Arsip Terhubung: <strong id="modal_cpl_rps_lama_info_mk" style="color:#0f172a;">—</strong> (<span id="modal_cpl_rps_lama_info_penyusun">—</span>, <span id="modal_cpl_rps_lama_info_tahun">—</span>)</div>
          </div>
          <div style="display:flex; gap:6px; align-items:center;">
            <button type="button" class="btn btn-default btn-xs" onclick="cariLainArsipCplRpsLama()" style="font-weight:600; font-size:11px;">
              <i class="fa fa-search"></i> Ganti Arsip RPS Pembanding
            </button>
          </div>
        </div>

        <!-- Relevance Alert Notification Box -->
        <div id="modal_cpl_rps_lama_alert" style="margin-bottom:12px;">
          <!-- Rendered via JS -->
        </div>

        <!-- 2-Column Comparison Layout -->
        <div class="row" style="margin:0 -6px;">
          
          <!-- Column 1: Teks Asli CPL RPS Lama -->
          <div class="col-md-5" style="padding:0 6px;">
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; height:460px; display:flex; flex-direction:column;">
              <div style="background:#f1f5f9; border-bottom:1px solid #e2e8f0; padding:8px 12px; font-weight:700; font-size:12px; color:#1e293b; display:flex; justify-content:space-between; align-items:center;">
                <span><i class="fa fa-archive text-primary mr-1"></i> Teks CPL RPS Lama (Arsip)</span>
                <span class="badge" style="background:#64748b; font-size:10px;" id="badge_total_old_cpl_sections">4 Aspek</span>
              </div>
              <div style="padding:12px; overflow-y:auto; flex:1; font-size:11.5px; line-height:1.6;" id="container_old_cpl_raw_text">
                <!-- Rendered via JS -->
              </div>
            </div>
          </div>

          <!-- Column 2: Pemetaan ke CPL Resmi Kurikulum 2026 -->
          <div class="col-md-7" style="padding:0 6px;">
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; height:460px; display:flex; flex-direction:column;">
              <div style="background:#f1f5f9; border-bottom:1px solid #e2e8f0; padding:8px 12px; font-weight:700; font-size:12px; color:#1e293b; display:flex; justify-content:space-between; align-items:center;">
                <span><i class="fa fa-check-square-o text-success mr-1"></i> Padanan CPL Resmi Prodi 2026</span>
                <div style="display:flex; gap:4px;">
                  <button type="button" class="btn btn-default btn-xs" onclick="centangSemuaCplRpsLamaModal(true)" style="font-size:10.5px; padding:1px 6px;">Pilih Rekomendasi</button>
                  <button type="button" class="btn btn-default btn-xs" onclick="centangSemuaCplRpsLamaModal(false)" style="font-size:10.5px; padding:1px 6px;">Batal Semua</button>
                </div>
              </div>
              <div style="padding:10px; overflow-y:auto; flex:1;" id="container_cpl_rps_lama_mapping_cards">
                <!-- Rendered via JS -->
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="modal-footer" style="background:#ffffff; border-top:1px solid #e2e8f0; padding:10px 20px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:11.5px; color:#64748b;" id="modal_cpl_rps_lama_footer_summary">
          Terpilih: <strong id="modal_cpl_rps_lama_count_selected" style="color:#0f766e;">0 butir</strong>
        </span>
        <div style="display:flex; gap:8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Batal</button>
          <button type="button" class="btn btn-primary btn-sm" id="btn_terapkan_cpl_rps_lama" onclick="terapkanCplRpsLamaKeStudio()" style="font-weight:700; background:#2563eb; border-color:#2563eb; border-radius:6px; padding:6px 16px;">
            <i class="fa fa-check"></i> Terapkan CPL Terpilih ke Studio
          </button>
        </div>
      </div>
    </div>
  </div>
</div>



