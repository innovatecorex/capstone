<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Admission Application — Philippine Academy of Sakya</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  font-family: 'Inter', sans-serif;
  background: #f4f7fb;
  color: #1e293b;
  min-height: 100vh;
}

/* ══════════════════════════════════════
   SPLIT LAYOUT  (dark LEFT · form RIGHT)
══════════════════════════════════════ */
.ap-wrap {
  display: grid;
  grid-template-columns: 260px 1fr;
  min-height: 100vh;
}

/* ══════════════════════════════════════
   RIGHT  — scrollable form area
══════════════════════════════════════ */
.ap-left {
  padding: 2rem 2.25rem 5rem;
  background: #f4f7fb;
  position: relative;
}

/* subtle corner gradient accent */
.ap-left::before {
  content: '';
  position: fixed;
  top: 0; right: 0;
  width: 500px; height: 360px;
  background: radial-gradient(circle at top right, rgba(37,99,235,.05), transparent 65%);
  pointer-events: none;
  z-index: 0;
}
.ap-left > * { position: relative; z-index: 1; }

/* top bar */
.ap-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.75rem;
}
.ap-topbar-back {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: .75rem;
  font-weight: 600;
  color: #64748b;
  text-decoration: none;
  transition: color .15s;
}
.ap-topbar-back:hover { color: #1d4ed8; }
.ap-topbar-back svg { width: 14px; height: 14px; }
.ap-topbar-hint {
  font-size: .72rem;
  color: #94a3b8;
  font-weight: 600;
}

/* error banner */
.ap-error-banner {
  background: #fef2f2;
  border: 1px solid #fca5a5;
  border-left: 4px solid #dc2626;
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.25rem;
  font-size: .84rem;
  color: #991b1b;
}
.ap-error-banner strong { display: block; margin-bottom: .4rem; font-size: .88rem; }
.ap-error-banner ul { margin-left: 1.1rem; display: flex; flex-direction: column; gap: 3px; }

/* ══════════════════════════════════════
   SECTION CARDS
══════════════════════════════════════ */
.ap-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 2px 16px rgba(15,23,42,.07), 0 1px 3px rgba(15,23,42,.04);
  margin-bottom: 1.25rem;
  overflow: hidden;
  border: 1px solid rgba(226,232,240,.7);
  scroll-margin-top: 24px;
}

/* 3-px gradient accent bar — same as login card */
.ap-card-bar {
  height: 3px;
  background: linear-gradient(90deg, #1a3a6b, #2563eb, #3ecfa0);
}

.ap-card-head {
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 12px;
  border-bottom: 1px solid #f1f5f9;
  background: #fafbfd;
}
.ap-card-icon {
  width: 36px; height: 36px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.ap-card-icon svg { width: 17px; height: 17px; }
.ap-card-section-num {
  font-size: .62rem;
  font-weight: 800;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: .1em;
  margin-bottom: 1px;
}
.ap-card-title {
  font-size: .92rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -.01em;
}

.ap-card-body {
  padding: 1.4rem 1.5rem;
  display: grid;
  gap: 1.1rem;
}
.grid-2 { grid-template-columns: 1fr 1fr; }
.grid-3 { grid-template-columns: 1fr 1fr 1fr; }
.span-2 { grid-column: span 2; }
.span-3 { grid-column: span 3; }

/* Fields */
.field { display: flex; flex-direction: column; }
.field-label {
  font-size: .75rem;
  font-weight: 600;
  color: #374151;
  text-transform: none;
  letter-spacing: 0;
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.field-label .req {
  color: #dc2626;
  font-size: .65rem;
  margin-left: 1px;
}
.field-label .opt {
  font-size: .7rem;
  font-weight: 400;
  color: #9ca3af;
  text-transform: none;
  letter-spacing: 0;
  margin-left: 2px;
}

/* ── Input base ────────────────────────────────────── */
.field input,
.field select,
.field textarea {
  width: 100%;
  height: 46px;
  padding: 0 14px;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: .9rem;
  color: #111827;
  background: #ffffff;
  font-family: inherit;
  outline: none;
  box-shadow: 0 1px 2px rgba(0,0,0,.05), inset 0 1px 2px rgba(0,0,0,.03);
  transition: border-color .15s, box-shadow .15s;
  -webkit-appearance: none;
  appearance: none;
}
.field select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 15px;
  padding-right: 38px;
  cursor: pointer;
  color: #374151;
}
.field select option[value=""] { color: #9ca3af; }
.field textarea {
  height: auto;
  padding: 11px 14px;
  resize: vertical;
  min-height: 72px;
  line-height: 1.6;
}
.field input:hover, .field select:hover, .field textarea:hover {
  border-color: #9ca3af;
}
.field input:focus,
.field select:focus,
.field textarea:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37,99,235,.12), 0 1px 2px rgba(0,0,0,.05);
}
.field input.is-err,
.field select.is-err,
.field textarea.is-err {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239,68,68,.1);
  background: #fff9f9;
}
.field input::placeholder,
.field textarea::placeholder { color: #9ca3af; font-size: .86rem; }

/* ── Input with left icon ──────────────────────────── */
.fi-wrap { position: relative; }
.fi-wrap .fi-icon {
  position: absolute;
  left: 13px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px; height: 16px;
  color: #9ca3af;
  pointer-events: none;
  transition: color .15s;
  flex-shrink: 0;
}
.fi-wrap input { padding-left: 40px; }
.fi-wrap select { padding-left: 40px; padding-right: 38px; }
.fi-wrap:focus-within .fi-icon { color: #2563eb; }
.fi-wrap.fi-err .fi-icon { color: #ef4444; }

/* ── Date-of-birth three-select row ───────────────── */
.dob-row {
  display: flex;
  gap: 8px;
}
.dob-sel {
  flex: 1;
  height: 46px;
  padding: 0 32px 0 14px;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: .9rem;
  color: #374151;
  background: #ffffff;
  font-family: inherit;
  outline: none;
  box-shadow: 0 1px 2px rgba(0,0,0,.05), inset 0 1px 2px rgba(0,0,0,.03);
  transition: border-color .15s, box-shadow .15s;
  -webkit-appearance: none;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 14px;
  cursor: pointer;
}
.dob-sel:hover { border-color: #9ca3af; }
.dob-sel:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37,99,235,.12), 0 1px 2px rgba(0,0,0,.05);
}
.dob-sel.is-err {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239,68,68,.1);
  background-color: #fff9f9;
}
/* Month gets the icon prefix, needs extra left padding */
.dob-row .fi-wrap { flex: 5; }
.dob-row .fi-wrap select { padding-left: 40px; }
/* Day narrower, Year wider */
.dob-sel.dob-day  { flex: 3; }
.dob-sel.dob-year { flex: 4; }

.field-hint {
  font-size: .72rem;
  color: #9ca3af;
  margin-top: 5px;
  line-height: 1.45;
}
.field-err {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: .72rem;
  color: #dc2626;
  font-weight: 600;
  margin-top: 5px;
}
.field-err svg { width: 13px; height: 13px; flex-shrink: 0; }

/* ── Section card header upgrade ──────────────────── */
.ap-card-head {
  padding: 1.1rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 14px;
  border-bottom: 1px solid #f1f5f9;
  background: #fafbfd;
}
.ap-card-section-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: .62rem;
  font-weight: 700;
  color: #2563eb;
  background: #dbeafe;
  padding: 2px 8px;
  border-radius: 99px;
  letter-spacing: .04em;
  text-transform: uppercase;
  margin-bottom: 3px;
}
.ap-card-title {
  font-size: .95rem;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -.015em;
}
.ap-card-icon {
  width: 40px; height: 40px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.ap-card-icon svg { width: 19px; height: 19px; }

/* ══════════════════════════════════════
   UPLOAD ZONES
══════════════════════════════════════ */
.upload-zone {
  border: 1.5px dashed #cbd5e1;
  border-radius: 10px;
  padding: 0 12px;
  height: 44px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: border-color .15s, background .15s, box-shadow .15s;
  background: #f8fafc;
  user-select: none;
  margin-top: 6px;
  overflow: hidden;
}
.upload-zone:hover, .upload-zone.uz-drag {
  border-color: #2563eb;
  background: #eff6ff;
  box-shadow: 0 0 0 3px rgba(37,99,235,.08);
}
.upload-zone.uz-ok {
  border: 1.5px solid #86efac;
  background: #f0fdf4;
}
.upload-zone.uz-err {
  border-color: #fca5a5;
  background: #fef2f2;
}
.upload-zone svg.uz-icon {
  width: 15px; height: 15px;
  color: #94a3b8;
  flex-shrink: 0;
  transition: color .15s;
}
.upload-zone:hover svg.uz-icon, .upload-zone.uz-drag svg.uz-icon { color: #2563eb; }
.upload-zone.uz-err svg.uz-icon { color: #fca5a5; }
.uz-body { flex: 1; min-width: 0; display: flex; align-items: center; gap: 6px; }
.uz-title { font-size: .78rem; font-weight: 500; color: #475569; white-space: nowrap; }
.uz-title strong { color: #2563eb; font-weight: 600; }
.uz-sub { font-size: .67rem; color: #94a3b8; }
.uz-preview { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; }
.uz-preview img { width: 28px; height: 28px; object-fit: cover; border-radius: 4px; border: 1px solid #bbf7d0; flex-shrink: 0; }
.uz-file-icon { width: 24px; height: 24px; background: #dcfce7; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.uz-file-icon svg { width: 12px; height: 12px; color: #16a34a; }
.uz-file-text { min-width: 0; flex: 1; }
.uz-file-name { font-size: .75rem; font-weight: 600; color: #15803d; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; line-height: 1.2; }
.uz-file-size { font-size: .64rem; color: #6b928e; line-height: 1.2; }
.uz-change { font-size: .67rem; color: #2563eb; font-weight: 600; cursor: pointer; white-space: nowrap; flex-shrink: 0; margin-left: 4px; }
.uz-change:hover { text-decoration: underline; }

/* Submit card */
.ap-submit-card {
  background: #fff;
  border-radius: 18px;
  border: 1px solid rgba(226,232,240,.7);
  overflow: hidden;
  box-shadow: 0 2px 16px rgba(15,23,42,.07);
}
.ap-disclaimer {
  padding: 1.1rem 1.5rem;
  background: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
  border-left: 3px solid #2563eb;
  font-size: .77rem;
  color: #475569;
  line-height: 1.65;
}
.ap-disclaimer p + p { margin-top: .4rem; }
.ap-privacy-check {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 1.1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  cursor: pointer;
}
.ap-privacy-check input { width: 16px; height: 16px; accent-color: #2563eb; margin-top: 1px; flex-shrink: 0; cursor: pointer; }
.ap-privacy-check span { font-size: .78rem; color: #475569; line-height: 1.55; }
.ap-privacy-check strong { color: #0f172a; }
.ap-submit-btn-wrap { padding: 1.1rem 1.5rem; }
.ap-submit-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  width: 100%;
  padding: .85rem 1.5rem;
  background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #2563eb 100%);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: .92rem;
  font-weight: 800;
  font-family: inherit;
  cursor: pointer;
  letter-spacing: .01em;
  box-shadow: 0 4px 20px rgba(29,78,216,.35);
  transition: box-shadow .2s, transform .15s;
}
.ap-submit-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(29,78,216,.45); }
.ap-submit-btn:active { transform: translateY(0); }
.ap-submit-btn svg { width: 17px; height: 17px; }

/* ══════════════════════════════════════
   LEFT  — sticky dark info panel
══════════════════════════════════════ */
.ap-right {
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  padding: 2rem 1.5rem;

  background: linear-gradient(175deg, #0f2347 0%, #091830 45%, #060f1e 100%);
  border-right: 1px solid rgba(255,255,255,.06);
  scrollbar-width: none;
}
.ap-right::-webkit-scrollbar { display: none; }

/* vertical accent strip on the right edge of left panel */
.ap-right::before {
  content: '';
  position: fixed;
  top: 0; left: 260px;
  width: 2px; height: 100vh;
  background: linear-gradient(180deg,
    transparent 0%,
    rgba(62,207,160,.5) 25%,
    rgba(46,106,230,.6) 60%,
    transparent 100%);
  pointer-events: none;
  z-index: 2;
}

/* blueprint grid texture */
.ap-right::after {
  content: '';
  position: fixed;
  top: 0; left: 0;
  width: 260px; height: 100vh;
  background-image:
    linear-gradient(rgba(255,255,255,.022) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.022) 1px, transparent 1px);
  background-size: 26px 26px;
  pointer-events: none;
  z-index: 0;
}
.ap-right > * { position: relative; z-index: 1; }

/* brand block */
.ap-brand {
  display: flex;
  align-items: center;
  gap: .75rem;
  margin-bottom: 2rem;
}
.ap-brand-emblem {
  width: 44px; height: 44px;
  border-radius: 13px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 0 0 1px rgba(255,255,255,.1), 0 4px 14px rgba(37,99,235,.4);
}
.ap-brand-emblem img {
  width: 26px; height: 26px;
  object-fit: contain;
  filter: brightness(0) invert(1);
}
.ap-brand-name {
  font-size: .8rem;
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
  letter-spacing: -.01em;
}
.ap-brand-sub {
  font-size: .6rem;
  color: rgba(255,255,255,.3);
  letter-spacing: .04em;
  margin-top: 1px;
}

.ap-portal-tag {
  font-size: .6rem;
  font-weight: 800;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: rgba(255,255,255,.28);
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: .5rem;
}
.ap-portal-tag::before {
  content: '';
  display: inline-block;
  width: 16px; height: 1.5px;
  background: rgba(255,255,255,.2);
}

.ap-headline {
  font-family: 'Merriweather', Georgia, serif;
  font-size: 1.3rem;
  font-weight: 900;
  color: #fff;
  line-height: 1.3;
  letter-spacing: -.02em;
  margin-bottom: .5rem;
}

.ap-subtext {
  font-size: .72rem;
  color: rgba(255,255,255,.38);
  line-height: 1.65;
  margin-bottom: 1.75rem;
}
.ap-subtext strong { color: rgba(255,255,255,.55); }

.ap-divider {
  height: 1px;
  background: rgba(255,255,255,.07);
  margin-bottom: 1.5rem;
}

/* section nav */
.ap-nav-label {
  font-size: .58rem;
  font-weight: 800;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: rgba(255,255,255,.22);
  margin-bottom: .75rem;
}

.ap-nav { list-style: none; display: flex; flex-direction: column; gap: 2px; }

.ap-nav a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 10px;
  text-decoration: none;
  font-size: .77rem;
  font-weight: 600;
  color: rgba(255,255,255,.38);
  transition: all .15s;
}
.ap-nav a:hover {
  background: rgba(255,255,255,.06);
  color: rgba(255,255,255,.75);
}
.ap-nav a.active {
  background: rgba(37,99,235,.28);
  color: #93c5fd;
}
.ap-nav-num {
  width: 24px; height: 24px;
  border-radius: 50%;
  background: rgba(255,255,255,.07);
  font-size: .67rem;
  font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  color: rgba(255,255,255,.28);
  transition: all .15s;
}
.ap-nav a.active .ap-nav-num {
  background: #2563eb;
  color: #fff;
}
.ap-nav a:hover .ap-nav-num {
  background: rgba(255,255,255,.14);
  color: #fff;
}

/* footer of right panel */
.ap-right-footer {
  margin-top: auto;
  padding-top: 1.5rem;
}
.ap-req-note {
  background: rgba(245,158,11,.07);
  border: 1px solid rgba(245,158,11,.18);
  border-left: 3px solid rgba(245,158,11,.6);
  border-radius: 8px;
  padding: 10px 12px;
  font-size: .69rem;
  color: rgba(255,255,255,.38);
  line-height: 1.55;
  margin-bottom: 1rem;
}
.ap-req-note strong { color: #fca5a5; }

.ap-security-badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.ap-sec-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: .22rem .55rem;
  border-radius: 5px;
  font-size: .59rem;
  font-weight: 700;
  background: rgba(255,255,255,.05);
  color: rgba(255,255,255,.28);
  border: 1px solid rgba(255,255,255,.07);
}
.ap-sec-badge svg { width: 9px; height: 9px; }

/* ── Colored badge variants (one per section) ─────── */
.badge-green  { color: #15803d; background: #dcfce7; }
.badge-purple { color: #7e22ce; background: #f3e8ff; }
.badge-orange { color: #c2410c; background: #ffedd5; }
.badge-rose   { color: #be123c; background: #ffe4e6; }
.badge-teal   { color: #0f766e; background: #ccfbf1; }

/* ── Form progress strip ─────────────────────────── */
.ap-progress-strip {
  background: #fff;
  border-radius: 14px;
  padding: 10px 16px;
  margin-bottom: 1.1rem;
  border: 1px solid rgba(226,232,240,.8);
  box-shadow: 0 1px 4px rgba(15,23,42,.04);
  display: flex;
  align-items: center;
  gap: 12px;
}
.ap-progress-icon {
  width: 30px; height: 30px;
  border-radius: 8px;
  background: linear-gradient(135deg, #dbeafe, #eff6ff);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.ap-progress-icon svg { width: 14px; height: 14px; color: #2563eb; }
.ap-progress-info { flex: 1; min-width: 0; }
.ap-progress-label {
  font-size: .68rem; font-weight: 700; color: #475569;
  margin-bottom: 5px;
}
.ap-progress-track {
  height: 5px; background: #f1f5f9;
  border-radius: 99px; overflow: hidden;
}
.ap-progress-fill {
  height: 100%; width: 0%;
  background: linear-gradient(90deg, #1e3a8a, #2563eb, #3ecfa0);
  border-radius: 99px; transition: width .45s ease;
}
.ap-progress-pct {
  font-size: .82rem; font-weight: 800; color: #2563eb; white-space: nowrap;
}

/* ── Document card grid ──────────────────────────── */
.doc-notice {
  margin: 1rem 1.5rem .8rem;
  background: linear-gradient(135deg, #fffbeb, #fefce8);
  border: 1px solid #fde68a;
  border-left: 3px solid #f59e0b;
  border-radius: 11px;
  padding: 10px 14px;
  font-size: .8rem; color: #92400e; line-height: 1.6;
}
.doc-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: .85rem;
  padding: 0 1.5rem 1.5rem;
}
.doc-card {
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 1rem;
  background: #fafbfd;
  transition: border-color .15s, box-shadow .15s;
}
.doc-card:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(15,23,42,.07);
}
.doc-card-head {
  display: flex; align-items: flex-start; gap: 10px;
  margin-bottom: 10px;
}
.doc-card-icon {
  width: 34px; height: 34px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.doc-card-icon svg { width: 17px; height: 17px; }
.doc-card-meta { flex: 1; min-width: 0; }
.doc-card-title {
  font-size: .8rem; font-weight: 700; color: #0f172a;
  line-height: 1.3; margin-bottom: 4px;
}
.doc-badge-req {
  display: inline-block; font-size: .59rem; font-weight: 700;
  padding: 1px 7px; border-radius: 99px;
  background: #fee2e2; color: #b91c1c;
}
.doc-badge-opt {
  display: inline-block; font-size: .59rem; font-weight: 700;
  padding: 1px 7px; border-radius: 99px;
  background: #f1f5f9; color: #64748b;
}

/* ── Nav completion dot ──────────────────────────── */
.ap-nav-dot {
  width: 7px; height: 7px; border-radius: 50%;
  margin-left: auto; flex-shrink: 0;
  background: rgba(255,255,255,.1);
  transition: background .25s, box-shadow .25s;
}
.ap-nav a.done .ap-nav-dot {
  background: #34d399;
  box-shadow: 0 0 0 2px rgba(52,211,153,.22);
}

/* ── Left panel completion mini-widget ───────────── */
.ap-comp-widget {
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 10px;
  padding: 10px 13px;
  margin-bottom: .9rem;
}
.ap-comp-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 7px;
}
.ap-comp-title {
  font-size: .6rem; font-weight: 700;
  color: rgba(255,255,255,.28);
  text-transform: uppercase; letter-spacing: .08em;
}
.ap-comp-count { font-size: .68rem; font-weight: 800; color: #34d399; }
.ap-comp-track {
  height: 3px; background: rgba(255,255,255,.07);
  border-radius: 99px; overflow: hidden;
}
.ap-comp-fill {
  height: 100%; width: 0%;
  background: linear-gradient(90deg, #2563eb, #34d399);
  border-radius: 99px; transition: width .45s ease;
}

/* ── Responsive ── */
@media (max-width: 860px) {
  .ap-wrap { grid-template-columns: 1fr; }
  .ap-right { position: relative; height: auto; }
  .ap-right::before, .ap-right::after { display: none; }
  .ap-left::before { display: none; }
  .grid-2, .grid-3 { grid-template-columns: 1fr; }
  .span-2, .span-3 { grid-column: span 1; }
  .doc-grid { grid-template-columns: 1fr; }
  .doc-card[style*="grid-column:span 2"] { grid-column: span 1; }
}
</style>
</head>
<body>

<div class="ap-wrap">

  {{-- ══════════════════════════════════════ --}}
  {{-- LEFT — dark info panel                --}}
  {{-- ══════════════════════════════════════ --}}
  <aside class="ap-right">

    <div class="ap-brand">
      <div class="ap-brand-emblem">
        <img src="/images/logo.png" alt="PAS Logo">
      </div>
      <div>
        <a href="{{ route('login') }}" class="ap-brand-name" style="text-decoration:none;color:inherit;display:inline-block;">Philippine Academy of Sakya</a>
      </div>
    </div>

    <div class="ap-portal-tag">Online Application</div>
    <h2 class="ap-headline">Application<br>Form</h2>

    <div class="ap-divider"></div>

    <div class="ap-nav-label">Sections</div>
    <ul class="ap-nav">
      <li><a href="#sec-personal" class="active"><span class="ap-nav-num">1</span> Personal Info <span class="ap-nav-dot"></span></a></li>
      <li><a href="#sec-address"><span class="ap-nav-num">2</span> Home Address <span class="ap-nav-dot"></span></a></li>
      <li><a href="#sec-school"><span class="ap-nav-num">3</span> Previous School <span class="ap-nav-dot"></span></a></li>
      <li><a href="#sec-applying"><span class="ap-nav-num">4</span> Applying For <span class="ap-nav-dot"></span></a></li>
      <li><a href="#sec-parent"><span class="ap-nav-num">5</span> Parent / Guardian <span class="ap-nav-dot"></span></a></li>
      <li><a href="#sec-documents"><span class="ap-nav-num">6</span> Documents <span class="ap-nav-dot"></span></a></li>
    </ul>

    <div class="ap-right-footer">
      <div class="ap-comp-widget">
        <div class="ap-comp-header">
          <span class="ap-comp-title">Progress</span>
          <span class="ap-comp-count" id="ap-comp-count">—</span>
        </div>
        <div class="ap-comp-track">
          <div class="ap-comp-fill" id="ap-comp-fill"></div>
        </div>
      </div>
      <div class="ap-req-note">
        Fields marked <strong>*</strong> are required.
      </div>
    </div>

  </aside>

  {{-- ══════════════════════════════════════ --}}
  {{-- RIGHT — form                          --}}
  {{-- ══════════════════════════════════════ --}}
  <div class="ap-left">

    <div class="ap-topbar">
      <a href="{{ route('login') }}" class="ap-topbar-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Login
      </a>
      <span class="ap-topbar-hint">6 sections · All required fields must be filled</span>
    </div>

    <div class="ap-progress-strip">
      <div class="ap-progress-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div class="ap-progress-info">
        <div class="ap-progress-label">Required fields completed</div>
        <div class="ap-progress-track"><div class="ap-progress-fill" id="ap-prog-fill"></div></div>
      </div>
      <div class="ap-progress-pct" id="ap-prog-pct">0%</div>
    </div>

    @if($errors->any())
    <div class="ap-error-banner">
      <strong>Please correct the following errors:</strong>
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('apply.store') }}" enctype="multipart/form-data">
      @csrf

      {{-- 1. Personal Information --}}
      <div class="ap-card" id="sec-personal">
        <div class="ap-card-bar"></div>
        <div class="ap-card-head">
          <div class="ap-card-icon" style="background:linear-gradient(135deg,#dbeafe,#eff6ff);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
          </div>
          <div>
            <div class="ap-card-section-badge">Section 1</div>
            <div class="ap-card-title">Personal Information</div>
          </div>
        </div>

        <div class="ap-card-body grid-2">

          {{-- First Name --}}
          <div class="field">
            <label class="field-label">First Name <span class="req">*</span></label>
            <div class="fi-wrap {{ $errors->has('first_name') ? 'fi-err' : '' }}">
              <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
              <input type="text" name="first_name" value="{{ old('first_name') }}" required maxlength="100"
                class="{{ $errors->has('first_name') ? 'is-err' : '' }}" placeholder="e.g. Juan">
            </div>
            @error('first_name')<div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>@enderror
          </div>

          {{-- Last Name --}}
          <div class="field">
            <label class="field-label">Last Name <span class="req">*</span></label>
            <div class="fi-wrap {{ $errors->has('last_name') ? 'fi-err' : '' }}">
              <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
              <input type="text" name="last_name" value="{{ old('last_name') }}" required maxlength="100"
                class="{{ $errors->has('last_name') ? 'is-err' : '' }}" placeholder="e.g. dela Cruz">
            </div>
            @error('last_name')<div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>@enderror
          </div>

          {{-- Middle Name --}}
          <div class="field">
            <label class="field-label">Middle Name <span class="opt">(optional)</span></label>
            <div class="fi-wrap">
              <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
              <input type="text" name="middle_name" value="{{ old('middle_name') }}" maxlength="100" placeholder="e.g. Santos">
            </div>
          </div>

          {{-- Suffix --}}
          <div class="field">
            <label class="field-label">Suffix <span class="opt">Jr., Sr., III…</span></label>
            <input type="text" name="suffix" value="{{ old('suffix') }}" maxlength="20" placeholder="e.g. Jr.">
          </div>

          {{-- Date of Birth --}}
          <div class="field">
            <label class="field-label">Date of birth <span class="req">*</span></label>
            @php
              $dobOld = old('date_of_birth', '');
              $dobY   = $dobOld ? substr($dobOld, 0, 4) : '';
              $dobM   = $dobOld ? substr($dobOld, 5, 2) : '';
              $dobD   = $dobOld ? substr($dobOld, 8, 2) : '';
            @endphp
            {{-- Hidden field carries the combined YYYY-MM-DD value for server validation --}}
            <input type="hidden" id="dob-hidden" name="date_of_birth" value="{{ $dobOld }}">
            <div class="dob-row">
              {{-- Month (with calendar icon) --}}
              <div class="fi-wrap {{ $errors->has('date_of_birth') ? 'fi-err' : '' }}">
                <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                <select id="dob-month" class="{{ $errors->has('date_of_birth') ? 'is-err' : '' }}" onchange="dobCombine()">
                  <option value="">Month</option>
                  @foreach(['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'] as $mv => $mn)
                    <option value="{{ $mv }}" {{ $dobM === $mv ? 'selected' : '' }}>{{ $mn }}</option>
                  @endforeach
                </select>
              </div>
              {{-- Day --}}
              <select id="dob-day" class="dob-sel dob-day {{ $errors->has('date_of_birth') ? 'is-err' : '' }}" onchange="dobCombine()">
                <option value="">Day</option>
                @for($d = 1; $d <= 31; $d++)
                  @php $dv = str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                  <option value="{{ $dv }}" {{ $dobD === $dv ? 'selected' : '' }}>{{ $d }}</option>
                @endfor
              </select>
              {{-- Year --}}
              <select id="dob-year" class="dob-sel dob-year {{ $errors->has('date_of_birth') ? 'is-err' : '' }}" onchange="dobCombine()">
                <option value="">Year</option>
                @php $curYr = (int) date('Y'); @endphp
                @for($y = $curYr - 3; $y >= $curYr - 30; $y--)
                  <option value="{{ $y }}" {{ $dobY === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>
            @error('date_of_birth')<div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>@enderror
          </div>

          {{-- Sex --}}
          <div class="field">
            <label class="field-label">Sex <span class="req">*</span></label>
            <div class="fi-wrap {{ $errors->has('sex') ? 'fi-err' : '' }}">
              <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
              <select name="sex" required class="{{ $errors->has('sex') ? 'is-err' : '' }}">
                <option value="">— Select —</option>
                <option value="Male"   {{ old('sex') === 'Male'   ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
              </select>
            </div>
            @error('sex')<div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>@enderror
          </div>

          {{-- LRN --}}
          <div class="field">
            <label class="field-label">Learner Reference Number <span class="opt">(LRN)</span></label>
            <div class="fi-wrap {{ $errors->has('lrn') ? 'fi-err' : '' }}">
              <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/></svg>
              <input type="text" name="lrn" value="{{ old('lrn') }}" maxlength="12" pattern="\d{12}"
                placeholder="12-digit LRN" class="{{ $errors->has('lrn') ? 'is-err' : '' }}">
            </div>
            <div class="field-hint">Leave blank if not yet assigned.</div>
            @error('lrn')<div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>@enderror
          </div>

          {{-- Nationality --}}
          <div class="field">
            <label class="field-label">Nationality</label>
            <div class="fi-wrap">
              <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.893 13.393l-1.135-1.135a2.252 2.252 0 01-.421-.585l-1.08-2.16a.414.414 0 00-.663-.107.827.827 0 01-.812.21l-1.273-.363a.89.89 0 00-.738 1.595l.587.39c.59.395.674 1.23.172 1.732l-.2.2c-.212.212-.33.498-.33.796v.41c0 .409-.11.809-.32 1.158l-1.315 2.191a2.11 2.11 0 01-1.81 1.025 1.055 1.055 0 01-1.055-1.055v-1.172c0-.92-.56-1.747-1.414-2.089l-.655-.261a2.25 2.25 0 01-1.383-2.46l.007-.042a2.25 2.25 0 01.29-.787l.09-.15a2.25 2.25 0 012.37-1.048l1.178.236a1.125 1.125 0 001.302-.795l.208-.73a1.125 1.125 0 00-.578-1.315l-.665-.332-.091.091a2.25 2.25 0 01-1.591.659h-.18c-.249 0-.487.1-.662.274a.931.931 0 01-1.458-1.137l1.279-2.132"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
              <input type="text" name="nationality" value="{{ old('nationality', 'Filipino') }}" maxlength="80">
            </div>
          </div>

        </div>
      </div>

      {{-- 2. Home Address --}}
      <div class="ap-card" id="sec-address">
        <div class="ap-card-bar"></div>
        <div class="ap-card-head">
          <div class="ap-card-icon" style="background:#f0fdf4;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
          </div>
          <div>
            <div class="ap-card-section-badge badge-green">Section 2</div>
            <div class="ap-card-title">Home Address</div>
          </div>
        </div>

        <div class="ap-card-body">

          <div class="field">
            <label class="field-label">Street / House No. / Purok <span class="req">*</span></label>
            <input type="text" name="address" value="{{ old('address') }}" required maxlength="300"
              placeholder="e.g. 123 Rizal St., Purok 2"
              class="{{ $errors->has('address') ? 'is-err' : '' }}">
            @error('address')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.1rem;">
            <div class="field">
              <label class="field-label">Province <span class="req">*</span></label>
              <select id="addr-province" name="province" required onchange="addrOnProvince()" class="{{ $errors->has('province') ? 'is-err' : '' }}">
                <option value="">Loading…</option>
              </select>
              @error('province')
              <div class="field-err">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
                {{ $message }}
              </div>
              @enderror
            </div>
            <div class="field">
              <label class="field-label">Municipality / City <span class="req">*</span></label>
              <select id="addr-city" name="municipality" required disabled onchange="addrOnCity()" class="{{ $errors->has('municipality') ? 'is-err' : '' }}">
                <option value="">Select province first</option>
              </select>
              @error('municipality')
              <div class="field-err">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
                {{ $message }}
              </div>
              @enderror
            </div>
            <div class="field">
              <label class="field-label">Barangay <span class="req">*</span></label>
              <select id="addr-barangay" name="barangay" required disabled class="{{ $errors->has('barangay') ? 'is-err' : '' }}">
                <option value="">Select city first</option>
              </select>
              @error('barangay')
              <div class="field-err">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
                {{ $message }}
              </div>
              @enderror
            </div>
          </div>

        </div>
      </div>

      {{-- 3. Previous School --}}
      <div class="ap-card" id="sec-school">
        <div class="ap-card-bar"></div>
        <div class="ap-card-head">
          <div class="ap-card-icon" style="background:#fdf4ff;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#9333ea" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
            </svg>
          </div>
          <div>
            <div class="ap-card-section-badge badge-purple">Section 3</div>
            <div class="ap-card-title">Previous School</div>
          </div>
        </div>

        <div class="ap-card-body" style="grid-template-columns:1fr 1fr 1fr;">

          <div class="field span-2">
            <label class="field-label">School Name <span class="req">*</span></label>
            <input type="text" name="previous_school" value="{{ old('previous_school') }}" required maxlength="200"
              placeholder="Full name of last school attended"
              class="{{ $errors->has('previous_school') ? 'is-err' : '' }}">
            @error('previous_school')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Grade Level Completed <span class="req">*</span></label>
            <select name="previous_grade_level" required class="{{ $errors->has('previous_grade_level') ? 'is-err' : '' }}">
              <option value="">— Select —</option>
              @foreach($previousGradeLevels as $lvl)
              <option value="{{ $lvl }}" {{ old('previous_grade_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
              @endforeach
            </select>
            @error('previous_grade_level')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">School Year Completed <span class="req">*</span></label>
            <select name="school_year_completed" required class="{{ $errors->has('school_year_completed') ? 'is-err' : '' }}">
              <option value="">— Select school year —</option>
              @php $curYear = (int) date('Y'); @endphp
              @for($y = $curYear; $y >= $curYear - 15; $y--)
                @php $sy = $y . '–' . ($y + 1); @endphp
                <option value="{{ $sy }}" {{ old('school_year_completed') === $sy ? 'selected' : '' }}>{{ $sy }}</option>
              @endfor
            </select>
            @error('school_year_completed')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

        </div>
      </div>

      {{-- 4. Applying For --}}
      <div class="ap-card" id="sec-applying">
        <div class="ap-card-bar"></div>
        <div class="ap-card-head">
          <div class="ap-card-icon" style="background:#fff7ed;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#ea580c" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
          </div>
          <div>
            <div class="ap-card-section-badge badge-orange">Section 4</div>
            <div class="ap-card-title">Applying For</div>
          </div>
        </div>

        <div class="ap-card-body grid-2">

          <div class="field">
            <label class="field-label">Grade Level <span class="req">*</span></label>
            <select name="applying_for_grade" required class="{{ $errors->has('applying_for_grade') ? 'is-err' : '' }}">
              <option value="">— Select Grade —</option>
              @foreach($gradeLevels as $lvl)
              <option value="{{ $lvl }}" {{ old('applying_for_grade') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
              @endforeach
            </select>
            @error('applying_for_grade')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">School Year <span class="req">*</span></label>
            <select name="applying_for_year" required class="{{ $errors->has('applying_for_year') ? 'is-err' : '' }}">
              <option value="">— Select school year —</option>
              @if(!empty($activeYearLabel))
                <option value="{{ $activeYearLabel }}" {{ old('applying_for_year', $activeYearLabel) === $activeYearLabel ? 'selected' : '' }}>{{ $activeYearLabel }}</option>
              @endif
            </select>
            @error('applying_for_year')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

        </div>
      </div>

      {{-- 5. Parent / Guardian --}}
      <div class="ap-card" id="sec-parent">
        <div class="ap-card-bar"></div>
        <div class="ap-card-head">
          <div class="ap-card-icon" style="background:#fff1f2;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#e11d48" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
            </svg>
          </div>
          <div>
            <div class="ap-card-section-badge badge-rose">Section 5</div>
            <div class="ap-card-title">Parent / Guardian Information</div>
          </div>
        </div>

        <div class="ap-card-body grid-2">

          <div class="field span-2">
            <label class="field-label">Full Name <span class="req">*</span></label>
            <input type="text" name="parent_guardian_name" value="{{ old('parent_guardian_name') }}" required maxlength="200"
              placeholder="Complete name of parent or guardian"
              class="{{ $errors->has('parent_guardian_name') ? 'is-err' : '' }}">
            @error('parent_guardian_name')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Relationship <span class="req">*</span></label>
            <select name="relationship" required class="{{ $errors->has('relationship') ? 'is-err' : '' }}">
              <option value="">— Select —</option>
              @foreach(['Mother','Father','Guardian','Grandparent','Sibling','Other'] as $rel)
              <option value="{{ $rel }}" {{ old('relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
              @endforeach
            </select>
            @error('relationship')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Contact Number <span class="req">*</span></label>
            <input type="text" name="parent_contact" value="{{ old('parent_contact') }}" required maxlength="20"
              placeholder="e.g. 09XX-XXX-XXXX"
              class="{{ $errors->has('parent_contact') ? 'is-err' : '' }}">
            @error('parent_contact')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field span-2">
            <label class="field-label">Email Address <span class="req">*</span></label>
            <input type="email" name="parent_email" value="{{ old('parent_email') }}" required maxlength="180"
              placeholder="yourname@email.com"
              class="{{ $errors->has('parent_email') ? 'is-err' : '' }}">
            <div class="field-hint">Status updates and login credentials will be sent here once your application is reviewed.</div>
            @error('parent_email')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

        </div>
      </div>

      {{-- 6. Documents --}}
      <div class="ap-card" id="sec-documents">
        <div class="ap-card-bar"></div>
        <div class="ap-card-head">
          <div class="ap-card-icon" style="background:#f0fdf4;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#15803d" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>
            </svg>
          </div>
          <div>
            <div class="ap-card-section-badge badge-teal">Section 6</div>
            <h2 class="ap-card-title">Required Documents</h2>
          </div>
        </div>

        <div class="doc-notice">
          <strong>Upload digital copies of the following documents.</strong><br>
          Accepted formats: PDF, JPG, JPEG, PNG &mdash; max 5 MB per file. Original copies will be verified upon enrollment.
        </div>

        <div class="doc-grid">

          {{-- Birth Certificate --}}
          <div class="doc-card">
            <div class="doc-card-head">
              <div class="doc-card-icon" style="background:#dbeafe;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
              </div>
              <div class="doc-card-meta">
                <div class="doc-card-title">Birth Certificate (PSA)</div>
                <span class="doc-badge-req">Required</span>
              </div>
            </div>
            <div class="upload-zone{{ $errors->has('docs.birth_certificate') ? ' uz-err' : '' }}"
                 id="zone-birth"
                 onclick="triggerUpload('file-birth',event)"
                 ondragover="event.preventDefault();uzDragOn('zone-birth')"
                 ondragleave="uzDragOff('zone-birth')"
                 ondrop="uzDrop(event,'file-birth','prev-birth','zone-birth')">
              <input type="file" id="file-birth" name="docs[birth_certificate]"
                     accept=".pdf,.jpg,.jpeg,.png" style="display:none"
                     onchange="uzPreview(this,'prev-birth','zone-birth')">
              <svg class="uz-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.344 11.095"/>
              </svg>
              <div class="uz-body">
                <p class="uz-title"><strong>Click to upload</strong> or drag &amp; drop</p>
                <p class="uz-sub">PDF, JPG, JPEG, PNG &mdash; max 5 MB</p>
              </div>
              <div id="prev-birth" class="uz-preview" style="display:none"></div>
            </div>
            <div class="field-hint" style="margin-top:6px;">PSA-authenticated Birth Certificate.</div>
            @error('docs.birth_certificate')
            <div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>
            @enderror
          </div>

          {{-- Form 137 --}}
          <div class="doc-card">
            <div class="doc-card-head">
              <div class="doc-card-icon" style="background:#f3e8ff;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#7e22ce" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                </svg>
              </div>
              <div class="doc-card-meta">
                <div class="doc-card-title">Form 137 (Permanent Record)</div>
                <span class="doc-badge-req" style="background:#f1f5f9;color:#64748b;">Optional</span>
              </div>
            </div>
            <div class="upload-zone{{ $errors->has('docs.form_137') ? ' uz-err' : '' }}"
                 id="zone-form137"
                 onclick="triggerUpload('file-form137',event)"
                 ondragover="event.preventDefault();uzDragOn('zone-form137')"
                 ondragleave="uzDragOff('zone-form137')"
                 ondrop="uzDrop(event,'file-form137','prev-form137','zone-form137')">
              <input type="file" id="file-form137" name="docs[form_137]"
                     accept=".pdf,.jpg,.jpeg,.png" style="display:none"
                     onchange="uzPreview(this,'prev-form137','zone-form137')">
              <svg class="uz-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.344 11.095"/>
              </svg>
              <div class="uz-body">
                <p class="uz-title"><strong>Click to upload</strong> or drag &amp; drop</p>
                <p class="uz-sub">PDF, JPG, JPEG, PNG &mdash; max 5 MB</p>
              </div>
              <div id="prev-form137" class="uz-preview" style="display:none"></div>
            </div>
            <div class="field-hint" style="margin-top:6px;">Issued and signed by your previous school.</div>
            @error('docs.form_137')
            <div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>
            @enderror
          </div>

          {{-- Report Card --}}
          <div class="doc-card">
            <div class="doc-card-head">
              <div class="doc-card-icon" style="background:#ffedd5;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#c2410c" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                </svg>
              </div>
              <div class="doc-card-meta">
                <div class="doc-card-title">Form 138 (Report Card)</div>
                <span class="doc-badge-req">Required</span>
              </div>
            </div>
            <div class="upload-zone{{ $errors->has('docs.report_card') ? ' uz-err' : '' }}"
                 id="zone-report"
                 onclick="triggerUpload('file-report',event)"
                 ondragover="event.preventDefault();uzDragOn('zone-report')"
                 ondragleave="uzDragOff('zone-report')"
                 ondrop="uzDrop(event,'file-report','prev-report','zone-report')">
              <input type="file" id="file-report" name="docs[report_card]"
                     accept=".pdf,.jpg,.jpeg,.png" style="display:none"
                     onchange="uzPreview(this,'prev-report','zone-report')">
              <svg class="uz-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.344 11.095"/>
              </svg>
              <div class="uz-body">
                <p class="uz-title"><strong>Click to upload</strong> or drag &amp; drop</p>
                <p class="uz-sub">PDF, JPG, JPEG, PNG &mdash; max 5 MB</p>
              </div>
              <div id="prev-report" class="uz-preview" style="display:none"></div>
            </div>
            <div class="field-hint" style="margin-top:6px;">Most recent report card or Form 138.</div>
            @error('docs.report_card')
            <div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>
            @enderror
          </div>

          {{-- Good Moral --}}
          <div class="doc-card">
            <div class="doc-card-head">
              <div class="doc-card-icon" style="background:#d1fae5;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#065f46" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
              </div>
              <div class="doc-card-meta">
                <div class="doc-card-title">Certificate of Good Moral Character</div>
                <span class="doc-badge-req" style="background:#f1f5f9;color:#64748b;">Optional</span>
              </div>
            </div>
            <div class="upload-zone{{ $errors->has('docs.good_moral') ? ' uz-err' : '' }}"
                 id="zone-moral"
                 onclick="triggerUpload('file-moral',event)"
                 ondragover="event.preventDefault();uzDragOn('zone-moral')"
                 ondragleave="uzDragOff('zone-moral')"
                 ondrop="uzDrop(event,'file-moral','prev-moral','zone-moral')">
              <input type="file" id="file-moral" name="docs[good_moral]"
                     accept=".pdf,.jpg,.jpeg,.png" style="display:none"
                     onchange="uzPreview(this,'prev-moral','zone-moral')">
              <svg class="uz-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.344 11.095"/>
              </svg>
              <div class="uz-body">
                <p class="uz-title"><strong>Click to upload</strong> or drag &amp; drop</p>
                <p class="uz-sub">PDF, JPG, JPEG, PNG &mdash; max 5 MB</p>
              </div>
              <div id="prev-moral" class="uz-preview" style="display:none"></div>
            </div>
            <div class="field-hint" style="margin-top:6px;">Issued by your previous school.</div>
            @error('docs.good_moral')
            <div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>
            @enderror
          </div>

          {{-- 2x2 Picture — spans both columns --}}
          <div class="doc-card" style="grid-column:span 2;">
            <div class="doc-card-head">
              <div class="doc-card-icon" style="background:#f1f5f9;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#475569" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                </svg>
              </div>
              <div class="doc-card-meta">
                <div class="doc-card-title">2&times;2 ID Picture</div>
                <span class="doc-badge-opt">Optional</span>
              </div>
            </div>
            <div class="upload-zone{{ $errors->has('docs.picture_2x2') ? ' uz-err' : '' }}"
                 id="zone-pic2x2"
                 onclick="triggerUpload('file-pic2x2',event)"
                 ondragover="event.preventDefault();uzDragOn('zone-pic2x2')"
                 ondragleave="uzDragOff('zone-pic2x2')"
                 ondrop="uzDrop(event,'file-pic2x2','prev-pic2x2','zone-pic2x2')">
              <input type="file" id="file-pic2x2" name="docs[picture_2x2]"
                     accept=".jpg,.jpeg,.png" style="display:none"
                     onchange="uzPreview(this,'prev-pic2x2','zone-pic2x2')">
              <svg class="uz-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
              </svg>
              <div class="uz-body">
                <p class="uz-title"><strong>Click to upload</strong> or drag &amp; drop</p>
                <p class="uz-sub">JPG, JPEG, PNG &mdash; max 5 MB</p>
              </div>
              <div id="prev-pic2x2" class="uz-preview" style="display:none"></div>
            </div>
            <div class="field-hint" style="margin-top:6px;">Recent 2&times;2 photo, white background, plain clothes.</div>
            @error('docs.picture_2x2')
            <div class="field-err"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>{{ $message }}</div>
            @enderror
          </div>

        </div>
      </div>

      {{-- Submit --}}
      <div class="ap-submit-card">
        <div class="ap-card-bar"></div>
        <div class="ap-disclaimer">
          <p>By submitting this form, I certify that all information provided is <strong>true, correct, and complete</strong> to the best of my knowledge.</p>
          <p>Submission of false or misleading information is grounds for disqualification from admission or enrollment, in accordance with DepEd regulations.</p>
        </div>
        <label class="ap-privacy-check{{ $errors->has('data_privacy_consent') ? ' ap-privacy-check--err' : '' }}">
          <input type="checkbox" id="data-privacy-consent" name="data_privacy_consent" value="yes"{{ old('data_privacy_consent') ? ' checked' : '' }}>
          <span>I have read and agree to the school's <a href="#" onclick="document.getElementById('privacyPolicyModal').style.display='flex';return false;" style="color:#1d4ed8;font-weight:700;text-decoration:underline;">data privacy policy</a>. I consent to the collection and processing of the information above in accordance with <strong>RA 10173 (Data Privacy Act of 2012)</strong>.</span>
        </label>
        @error('data_privacy_consent')
        <div class="field-err" style="margin-top:6px;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
          {{ $message }}
        </div>
        @enderror
        <div class="field-err" id="privacy-client-err" style="display:none;margin-top:6px;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
          Please agree to the data privacy policy to continue.
        </div>
        <div class="ap-submit-btn-wrap">
          <button type="submit" class="ap-submit-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
            </svg>
            Submit Application
          </button>
        </div>
      </div>

    </form>
  </div>

</div>

<script>
  const sections = document.querySelectorAll('.ap-card[id]');
  const navLinks  = document.querySelectorAll('.ap-nav a');

  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        navLinks.forEach(l => l.classList.remove('active'));
        const active = document.querySelector(`.ap-nav a[href="#${e.target.id}"]`);
        if (active) active.classList.add('active');
      }
    });
  }, { rootMargin: '-20% 0px -70% 0px' });

  sections.forEach(s => observer.observe(s));

  /* ── Upload zone helpers ─────────────────────────────── */
  function triggerUpload(inputId, e) {
    if (e && e.target.closest('.uz-change')) return;
    document.getElementById(inputId).click();
  }
  function uzDragOn(zoneId)  { document.getElementById(zoneId).classList.add('uz-drag'); }
  function uzDragOff(zoneId) { document.getElementById(zoneId).classList.remove('uz-drag'); }
  function uzDrop(e, inputId, previewId, zoneId) {
    e.preventDefault();
    uzDragOff(zoneId);
    const dt = e.dataTransfer;
    if (!dt || !dt.files.length) return;
    const inp = document.getElementById(inputId);
    try {
      const transfer = new DataTransfer();
      transfer.items.add(dt.files[0]);
      inp.files = transfer.files;
    } catch (_) {}
    uzPreview(inp, previewId, zoneId);
  }
  function uzPreview(input, previewId, zoneId) {
    const zone = document.getElementById(zoneId);
    const box  = document.getElementById(previewId);
    if (!input.files || !input.files[0]) {
      box.style.display = 'none';
      zone.classList.remove('uz-ok');
      return;
    }
    const file   = input.files[0];
    const sizeMB = (file.size / 1048576).toFixed(2);
    zone.classList.remove('uz-err', 'uz-drag');
    zone.classList.add('uz-ok');
    zone.querySelector('.uz-icon').style.display = 'none';
    zone.querySelector('.uz-body').style.display = 'none';

    const pdfIconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>';
    const changeBtn = '<span class="uz-change" onclick="event.stopPropagation();uzChange(\''+input.id+'\',\''+previewId+'\',\''+zoneId+'\')">Change</span>';

    const buildPreview = (thumbHtml) =>
      (thumbHtml ? thumbHtml : '<div class="uz-file-icon">'+pdfIconSvg+'</div>')
      + '<div class="uz-file-text"><div class="uz-file-name">'+file.name+'</div><div class="uz-file-size">'+sizeMB+' MB</div></div>'
      + changeBtn;

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = ev => {
        box.innerHTML = buildPreview('<img src="'+ev.target.result+'">');
        box.style.display = 'flex';
      };
      reader.readAsDataURL(file);
    } else {
      box.innerHTML = buildPreview(null);
      box.style.display = 'flex';
    }
  }
  function uzChange(inputId, previewId, zoneId) {
    const zone = document.getElementById(zoneId);
    const box  = document.getElementById(previewId);
    box.style.display = 'none';
    box.innerHTML = '';
    zone.classList.remove('uz-ok');
    zone.querySelector('.uz-icon').style.display = '';
    zone.querySelector('.uz-body').style.display = '';
    document.getElementById(inputId).click();
  }

  /* ── Philippine Address Cascade (local cached proxy) ───────────── */
  const ADDR_OLD = {
    province:     @json(old('province', '')),
    municipality: @json(old('municipality', '')),
    barangay:     @json(old('barangay', '')),
  };

  async function addrGet(url) {
    const r = await fetch(url);
    if (!r.ok) throw new Error(r.status);
    return r.json();
  }

  function addrFill(sel, items, placeholder) {
    sel.innerHTML = '<option value="">' + placeholder + '</option>';
    items.forEach(function(item) {
      const o = document.createElement('option');
      o.value = item.name;
      o.dataset.code = item.code;
      if (item.type) o.dataset.type = item.type;
      o.textContent = item.name;
      sel.appendChild(o);
    });
    sel.disabled = false;
  }

  function addrLock(sel, msg) {
    sel.innerHTML = '<option value="">' + msg + '</option>';
    sel.disabled = true;
  }

  function addrFallback() {
    var rows = [['addr-province','province','Province'],
                ['addr-city','municipality','City / Municipality'],
                ['addr-barangay','barangay','Barangay']];
    rows.forEach(function(row) {
      var sel = document.getElementById(row[0]);
      if (!sel) return;
      var inp = document.createElement('input');
      inp.type = 'text'; inp.name = row[1]; inp.maxLength = 100; inp.placeholder = row[2];
      inp.required = true;
      sel.parentNode.replaceChild(inp, sel);
    });
  }

  async function addrInit() {
    var pSel = document.getElementById('addr-province');
    try {
      var provinces = await addrGet('/address/provinces');
      addrFill(pSel, provinces, '— Select Province —');
      if (ADDR_OLD.province) {
        pSel.value = ADDR_OLD.province;
        if (pSel.value) await addrOnProvince(true);
      }
    } catch(e) {
      addrFallback();
    }
  }

  async function addrOnProvince(restoring) {
    var pSel = document.getElementById('addr-province');
    var cSel = document.getElementById('addr-city');
    var bSel = document.getElementById('addr-barangay');
    var opt  = pSel.options[pSel.selectedIndex];
    var code = opt && opt.dataset.code;

    addrLock(cSel, '— Loading cities… —');
    addrLock(bSel, '— Select city first —');

    if (!code) { addrLock(cSel, '— Select province first —'); return; }

    try {
      var cities = await addrGet('/address/cities/' + code);
      addrFill(cSel, cities, '— Select City / Municipality —');
      if (restoring && ADDR_OLD.municipality) {
        cSel.value = ADDR_OLD.municipality;
        if (cSel.value) await addrOnCity(true);
      }
    } catch(e) {
      addrLock(cSel, '— Failed to load —');
    }
  }

  async function addrOnCity(restoring) {
    var cSel = document.getElementById('addr-city');
    var bSel = document.getElementById('addr-barangay');
    var opt  = cSel.options[cSel.selectedIndex];
    var code = opt && opt.dataset.code;

    addrLock(bSel, '— Loading barangays… —');

    if (!code) { addrLock(bSel, '— Select city first —'); return; }

    try {
      var barangays = await addrGet('/address/barangays/' + code);
      addrFill(bSel, barangays, '— Select Barangay —');
      if (restoring && ADDR_OLD.barangay) bSel.value = ADDR_OLD.barangay;
    } catch(e) {
      addrLock(bSel, '— Failed to load —');
    }
  }

  addrInit();

  /* ── Date of birth three-select combine ─────────────────────────── */
  function dobCombine() {
    var m = document.getElementById('dob-month').value;
    var d = document.getElementById('dob-day').value;
    var y = document.getElementById('dob-year').value;
    document.getElementById('dob-hidden').value = (m && d && y) ? (y + '-' + m + '-' + d) : '';
  }
  dobCombine(); // sync on page load (handles old() pre-fill)

  /* ── Form progress tracker ──────────────────────────────────────── */
  function updateProgress() {
    const req    = Array.from(document.querySelectorAll(
      'form input[required]:not([type=file]):not([type=checkbox]), form select[required]'
    ));
    const filled = req.filter(function(el) { return el.value && el.value.trim() !== ''; });
    const pct    = req.length ? Math.round(filled.length / req.length * 100) : 0;

    var fill  = document.getElementById('ap-prog-fill');
    var lbl   = document.getElementById('ap-prog-pct');
    var cfill = document.getElementById('ap-comp-fill');
    var clbl  = document.getElementById('ap-comp-count');

    if (fill)  fill.style.width  = pct + '%';
    if (lbl)   lbl.textContent   = pct + '%';
    if (cfill) cfill.style.width = pct + '%';
    if (clbl)  clbl.textContent  = filled.length + ' / ' + req.length;
  }

  document.querySelectorAll('form input, form select').forEach(function(el) {
    el.addEventListener('change', updateProgress);
    el.addEventListener('input',  updateProgress);
  });

  updateProgress();

  /* ── Consent checkbox gating (robust) ───────────────────────────────── */
  (function () {
    var form = document.querySelector('form');
    var err  = document.getElementById('privacy-client-err');

    form.addEventListener('submit', function (e) {
      // Re-query the checkbox live at submit time (avoids stale references).
      var cb = document.getElementById('data-privacy-consent');
      if (cb && !cb.checked) {
        e.preventDefault();
        if (err) err.style.display = 'flex';
        cb.closest('label').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
      }
      // checked (or not found) → allow normal submission; server validates too.
    });

    var cbInit = document.getElementById('data-privacy-consent');
    if (cbInit) {
      cbInit.addEventListener('change', function () {
        if (this.checked && err) err.style.display = 'none';
      });
    }
  }());
</script>

<div id="privacyPolicyModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:2000;overflow-y:auto;padding:20px;">
  <div style="background:#fff;border-radius:14px;max-width:620px;width:100%;margin:40px auto;max-height:calc(100vh - 80px);overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);">
    <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;">
      <h3 style="margin:0;font-size:1.1rem;font-weight:800;color:#0f172a;">Data Privacy Policy</h3>
      <button type="button" onclick="document.getElementById('privacyPolicyModal').style.display='none'"
              style="background:none;border:none;font-size:1.5rem;line-height:1;color:#94a3b8;cursor:pointer;">&times;</button>
    </div>
    <div style="padding:24px;font-size:.9rem;line-height:1.65;color:#334155;">
      <p style="margin:0 0 14px;"><strong>Philippine Academy of Sakya</strong> is committed to protecting your personal data in accordance with <strong>Republic Act No. 10173 (Data Privacy Act of 2012)</strong> and its Implementing Rules and Regulations.</p>

      <p style="margin:0 0 6px;font-weight:700;color:#0f172a;">1. Information We Collect</p>
      <p style="margin:0 0 14px;">We collect the applicant's personal information (name, date of birth, sex, address), academic records (previous school, grade level, report cards), parent/guardian contact details, and supporting documents you upload as part of the admission process.</p>

      <p style="margin:0 0 6px;font-weight:700;color:#0f172a;">2. How We Use It</p>
      <p style="margin:0 0 14px;">Your information is used solely to evaluate and process this application for admission, to communicate with you regarding your application status, and to maintain official academic and enrollment records if admitted.</p>

      <p style="margin:0 0 6px;font-weight:700;color:#0f172a;">3. How We Protect It</p>
      <p style="margin:0 0 14px;">Sensitive personal information is encrypted at rest using AES-256 encryption. Access is restricted to authorized school personnel (registrar and administration) on a need-to-know basis, and all access is logged in a tamper-evident audit trail.</p>

      <p style="margin:0 0 6px;font-weight:700;color:#0f172a;">4. Data Retention & Sharing</p>
      <p style="margin:0 0 14px;">We retain your information only as long as necessary for admission and enrollment purposes or as required by law. We do not sell or share your personal data with third parties except where required by the Department of Education or by law.</p>

      <p style="margin:0 0 6px;font-weight:700;color:#0f172a;">5. Your Rights</p>
      <p style="margin:0 0 14px;">Under the Data Privacy Act, you have the right to be informed, to access, to correct, and to object to the processing of your personal data, and to file a complaint with the National Privacy Commission. To exercise these rights, contact the school registrar.</p>

      <p style="margin:0;color:#64748b;font-size:.85rem;">By checking the consent box, you confirm that you have read and understood this policy and consent to the collection and processing of the information provided.</p>
    </div>
    <div style="padding:16px 24px;border-top:1px solid #e2e8f0;text-align:right;">
      <button type="button" onclick="document.getElementById('privacyPolicyModal').style.display='none'"
              style="padding:.55rem 1.4rem;background:#1e293b;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:.88rem;">Close</button>
    </div>
  </div>
</div>

</body>
</html>
