/* =============================================================
   MÓDULO SANITARIO — Formulario de Donación
   Archivo único: React 18 + CSS inlineado + Babel standalone
   Subir a: wp-content/themes/[tema-activo]/donacion/donacion.js
   ============================================================= */

(function () {
  const font = document.createElement("link");
  font.rel = "stylesheet";
  font.href = "https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap";
  document.head.appendChild(font);

  const style = document.createElement("style");
  style.textContent = `
:root {
  --primary: #0B6FB8; --primary-dark: #084d80;
  --primary-tint: #E6F4FB; --primary-tint-2: #F4FAFD;
  --accent: #F26B5C; --accent-dark: #d94f3f;
  --ink: #0F1B2D; --ink-2: #4A5A6E; --ink-3: #7A8696;
  --line: #E2EAF1; --line-2: #EDF2F6; --bg: #F6F9FC; --white: #FFFFFF;
  --success: #1F8A5B;
  --shadow-sm: 0 1px 2px rgba(11,27,45,.04), 0 2px 8px rgba(11,27,45,.04);
  --shadow-md: 0 2px 8px rgba(11,27,45,.05), 0 12px 32px rgba(11,27,45,.08);
  --radius: 16px; --radius-lg: 22px;
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--bg); color: var(--ink); font-size: 16px; line-height: 1.5; -webkit-font-smoothing: antialiased; }
img { max-width: 100%; display: block; } button { font: inherit; cursor: pointer; } input, select, textarea { font: inherit; } a { color: var(--primary); text-decoration: none; }
.topbar { background: var(--white); border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 20; }
.topbar-inner { max-width: 1180px; margin: 0 auto; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.topbar-back { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--ink-2); font-weight: 500; padding: 8px 12px; border-radius: 999px; transition: background .15s; background: transparent; border: 0; text-decoration: none; }
.topbar-back:hover { background: var(--primary-tint); color: var(--primary); }
.stepper { max-width: 720px; margin: 0 auto; padding: 10px 20px 18px; display: flex; align-items: center; gap: 8px; }
.step { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.step-num { width: 28px; height: 28px; border-radius: 50%; background: var(--line-2); color: var(--ink-3); display: grid; place-items: center; font-size: 13px; font-weight: 700; transition: all .25s; }
.step.active .step-num { background: var(--primary); color: white; box-shadow: 0 0 0 4px var(--primary-tint); }
.step-label { font-size: 13px; font-weight: 600; color: var(--ink-3); transition: color .25s; }
.step.active .step-label { color: var(--ink); }
.step-line { flex: 1; height: 2px; background: var(--line-2); border-radius: 1px; transition: background .25s; min-width: 24px; }
.step-line.active { background: var(--primary); }
@media (max-width: 640px) { .step-label { display: none; } .stepper { padding: 8px 20px 14px; gap: 12px; } .step-line { min-width: 40px; } }
.content { max-width: 1180px; margin: 0 auto; padding: 28px 20px 60px; }
.eyebrow { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--accent); background: #fdf0ee; padding: 6px 12px; border-radius: 999px; margin-bottom: 14px; }
h1 { font-size: clamp(28px, 4vw, 40px); line-height: 1.1; letter-spacing: -.02em; margin: 0 0 12px; font-weight: 800; color: var(--ink); }
h1 em { font-style: normal; background: linear-gradient(180deg, transparent 60%, #ffd9d4 60%); padding: 0 4px; }
.lede { font-size: 16px; color: var(--ink-2); margin: 0 0 8px; }
.step1-grid { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1.15fr); gap: 32px; align-items: start; }
@media (max-width: 900px) { .step1-grid { grid-template-columns: 1fr; gap: 24px; } }
.hero { position: sticky; top: 120px; display: flex; flex-direction: column; gap: 18px; }
@media (max-width: 900px) { .hero { position: static; } }
.hero-photo { position: relative; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md); aspect-ratio: 4/5; background: var(--line-2); }
.hero-photo img { width: 100%; height: 100%; object-fit: cover; }
.hero-photo-cap { position: absolute; left: 14px; bottom: 14px; background: rgba(15,27,45,.68); backdrop-filter: blur(6px); color: white; font-size: 11px; font-weight: 500; padding: 6px 12px; border-radius: 999px; letter-spacing: .02em; }
@media (max-width: 900px) { .hero-photo { aspect-ratio: 16/9; } }
.hero-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.stat { background: white; border-radius: var(--radius); padding: 16px; box-shadow: var(--shadow-sm); border: 1px solid var(--line); }
.stat-num { font-size: 30px; font-weight: 800; color: var(--primary); line-height: 1; letter-spacing: -.03em; margin-bottom: 6px; }
.stat-label { font-size: 12px; color: var(--ink-2); line-height: 1.35; }
.hero-quote { margin: 0; background: linear-gradient(140deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 22px 22px 18px; border-radius: var(--radius); position: relative; }
.hero-quote::before { content: open-quote; position: absolute; top: -8px; left: 18px; font-family: Georgia, serif; font-size: 80px; line-height: 1; color: rgba(255,255,255,.2); }
.hero-quote p { margin: 0 0 10px; font-size: 15px; line-height: 1.45; font-weight: 500; }
.hero-quote cite { font-style: normal; font-size: 12px; opacity: .8; font-weight: 600; }
.form-card { background: white; border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-md); border: 1px solid var(--line); }
@media (max-width: 600px) { .form-card { padding: 22px 18px; border-radius: var(--radius); } }
.form-head { margin-bottom: 22px; }
.impact-strip { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #fdf3f2; border: 1px solid #f9d4cf; border-radius: var(--radius); margin-bottom: 22px; }
.impact-icon { width: 32px; height: 32px; flex-shrink: 0; background: var(--accent); color: white; border-radius: 50%; display: grid; place-items: center; }
.impact-strip p { margin: 0; font-size: 14px; color: var(--ink); line-height: 1.4; }
.impact-strip strong { color: var(--accent-dark); font-weight: 700; }
.saved-banner { display: flex; align-items: center; gap: 10px; padding: 12px 14px; background: #edf7f2; border: 1px solid #c3e6d4; border-radius: var(--radius); margin-bottom: 18px; color: var(--ink); }
.saved-banner svg { color: var(--success); flex-shrink: 0; }
.saved-banner p { margin: 0; font-size: 13px; line-height: 1.4; }
.fields { display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px; }
.field-row { display: grid; gap: 12px; }
.field-row.two { grid-template-columns: 1fr 1fr; }
@media (max-width: 480px) { .field-row.two { grid-template-columns: 1fr; } }
.field { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 13px; font-weight: 600; color: var(--ink-2); }
.field-label .optional { font-style: normal; color: var(--ink-3); font-weight: 500; font-size: 12px; }
.field input { border: 1.5px solid var(--line); border-radius: 12px; padding: 12px 14px; background: white; outline: 0; transition: border-color .15s, box-shadow .15s; font-size: 15px; color: var(--ink); width: 100%; }
.field input:hover { border-color: var(--ink-3); }
.field input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(11,111,184,.14); }
.field-hint { font-size: 12px; color: var(--ink-3); }
.field-error { font-size: 12px; color: var(--accent-dark); font-weight: 600; }
.field.has-error input { border-color: var(--accent-dark); }
.err { font-size: 12px; color: var(--accent-dark); font-weight: 600; margin: 6px 0 0; }
.cta { width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 16px 24px; background: var(--accent); color: white; border: 0; border-radius: 14px; font-size: 16px; font-weight: 700; letter-spacing: .01em; box-shadow: 0 6px 20px rgba(242,107,92,.35); transition: transform .15s, box-shadow .15s, background .15s; text-decoration: none; }
.cta:hover { background: var(--accent-dark); transform: translateY(-1px); color: white; }
.cta:active { transform: translateY(0); }
.reassure { display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 12px; color: var(--ink-3); margin-top: 14px; }
.reassure svg { color: var(--success); }
.step2-wrap, .step3-wrap { max-width: 720px; margin: 0 auto; }
.back-link { background: transparent; border: 0; padding: 0; display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: var(--ink-2); margin-bottom: 18px; cursor: pointer; }
.back-link:hover { color: var(--primary); }
.step2-head { margin-bottom: 18px; }
.freq { border: 0; padding: 0; margin: 0 0 20px; }
.freq legend, .amount legend { font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--ink-3); padding: 0; margin-bottom: 10px; display: block; }
.freq-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: var(--bg); padding: 4px; border-radius: 14px; }
.freq-opt { position: relative; display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px; cursor: pointer; transition: all .15s; font-size: 14px; font-weight: 600; color: var(--ink-2); }
.freq-opt input { position: absolute; opacity: 0; pointer-events: none; }
.freq-opt.selected { background: white; color: var(--ink); box-shadow: var(--shadow-sm); }
.freq-dot { width: 16px; height: 16px; border-radius: 50%; border: 2px solid var(--line); flex-shrink: 0; transition: all .15s; }
.freq-opt.selected .freq-dot { border-color: var(--primary); background: radial-gradient(circle, var(--primary) 40%, transparent 50%); }
.freq-badge { margin-left: auto; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 999px; background: #fdf3f2; color: var(--accent-dark); letter-spacing: .04em; text-transform: uppercase; }
.amount { border: 0; padding: 0; margin: 0 0 22px; }
.amount-grid { display: grid; gap: 10px; grid-template-columns: repeat(2, 1fr); }
.amount-chip, .amount-custom { position: relative; background: white; border: 1.5px solid var(--line); border-radius: 14px; padding: 14px 16px; display: flex; align-items: baseline; gap: 4px; font-weight: 700; color: var(--ink); transition: all .15s; text-align: left; }
.amount-chip { font-size: 18px; cursor: pointer; }
.amount-chip:hover { border-color: var(--primary); }
.amount-chip.selected, .amount-custom.selected { border-color: var(--primary); background: var(--primary-tint); box-shadow: 0 0 0 3px rgba(11,111,184,.12); }
.amount-grid .amount-custom { grid-column: 1 / -1; }
.amount-currency { color: var(--ink-3); font-size: 14px; font-weight: 600; }
.amount-value { font-size: 18px; }
.amount-iso { font-size: 11px; font-weight: 700; color: var(--ink-3); letter-spacing: .04em; margin-left: 4px; }
.amount-custom { padding: 10px 14px; }
.amount-custom input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; font-size: 16px; font-weight: 700; padding: 4px 0; color: var(--ink); }
.amount-custom input::placeholder { color: var(--ink-3); font-weight: 500; }
.methods-title { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--ink-3); margin: 24px 0 12px; }
.methods { list-style: none; padding: 0; margin: 0 0 28px; display: flex; flex-direction: column; gap: 12px; }
.method-card { width: 100%; display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 16px; background: white; border: 1.5px solid var(--line); border-radius: var(--radius); padding: 18px; text-align: left; transition: all .18s; box-shadow: var(--shadow-sm); cursor: pointer; }
.method-card:hover { border-color: var(--primary); transform: translateY(-2px); box-shadow: var(--shadow-md); }
.method-icon { width: 64px; height: 64px; border-radius: 14px; display: grid; place-items: center; flex-shrink: 0; }
.method-body { min-width: 0; }
.method-title-row { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 4px; }
.method-body h3 { margin: 0; font-size: 16px; font-weight: 700; color: var(--ink); }
.method-body p { margin: 0; font-size: 13px; color: var(--ink-2); line-height: 1.4; }
.method-tags { display: inline-flex; gap: 4px; flex-wrap: wrap; }
.tag { font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; padding: 3px 8px; border-radius: 999px; background: var(--line-2); color: var(--ink-2); }
.tag-rec { background: #e8f5ef; color: var(--success); }
.method-arrow { color: var(--ink-3); transition: transform .18s, color .18s; flex-shrink: 0; }
.method-card:hover .method-arrow { transform: translateX(4px); color: var(--primary); }
@media (max-width: 480px) { .method-icon { width: 52px; height: 52px; } .method-card { padding: 14px; gap: 12px; } }
.trust-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding: 18px; background: white; border: 1px solid var(--line); border-radius: var(--radius); }
@media (max-width: 600px) { .trust-row { grid-template-columns: 1fr; } }
.trust-item { display: flex; align-items: center; gap: 10px; font-size: 12px; color: var(--ink-2); }
.trust-item svg { color: var(--success); flex-shrink: 0; }
.trust-item div { display: flex; flex-direction: column; gap: 2px; }
.trust-item strong { color: var(--ink); font-size: 13px; }
.step3-card { background: white; border-radius: var(--radius-lg); padding: 36px 28px; box-shadow: var(--shadow-md); border: 1px solid var(--line); text-align: center; margin-bottom: 18px; }
.step3-badge { width: 72px; height: 72px; border-radius: 50%; display: grid; place-items: center; margin: 0 auto 18px; }
.step3-card h2 { font-size: 22px; font-weight: 800; margin: 0 0 8px; letter-spacing: -.01em; }
.step3-card .lede { margin-bottom: 14px; }
.step3-loader { display: inline-flex; gap: 6px; margin: 8px 0 22px; }
.dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); opacity: .3; animation: pulse 1.2s infinite ease-in-out; }
.dot:nth-child(2) { animation-delay: .15s; } .dot:nth-child(3) { animation-delay: .3s; }
@keyframes pulse { 0%,100%{opacity:.3;transform:scale(.8);}50%{opacity:1;transform:scale(1.1);} }
.bank-block { margin: 8px 0 22px; padding: 18px; background: var(--primary-tint-2); border: 1px solid var(--primary-tint); border-radius: var(--radius); text-align: left; }
.bank-block h4 { margin: 0 0 12px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--primary); }
.bank-block dl { margin: 0; display: grid; gap: 8px; }
.bank-block dl > div { display: grid; grid-template-columns: 96px 1fr; gap: 8px; padding: 6px 0; border-bottom: 1px dashed var(--line); font-size: 13px; }
.bank-block dl > div:last-of-type { border-bottom: 0; }
.bank-block dt { color: var(--ink-3); font-weight: 600; }
.bank-block dd { margin: 0; color: var(--ink); font-weight: 600; font-family: ui-monospace, 'SF Mono', Menlo, monospace; }
.bank-note { font-size: 12px; color: var(--ink-2); margin: 12px 0 0; }
.step3-actions { display: flex; flex-direction: column; gap: 8px; align-items: center; }
.ghost { font-size: 13px; color: var(--ink-2); font-weight: 600; padding: 8px 12px; background: transparent; border: 0; cursor: pointer; text-decoration: none; }
.site-footer { background: white; border-top: 1px solid var(--line); margin-top: 40px; }
.foot-inner { max-width: 1180px; margin: 0 auto; padding: 24px 20px; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 18px; }
.foot-left { display: flex; flex-direction: column; gap: 6px; }
.foot-left p { margin: 0; font-size: 12px; color: var(--ink-3); }
.foot-seals { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
.seal { display: inline-flex; align-items: center; gap: 4px; padding: 6px 10px; font-size: 11px; font-weight: 600; background: var(--bg); border: 1px solid var(--line); border-radius: 999px; color: var(--ink-2); }
.seal svg { color: var(--success); }
.foot-links { display: flex; gap: 14px; justify-content: flex-end; font-size: 13px; }
.foot-links a { color: var(--ink-2); }
@media (max-width: 700px) { .foot-inner { grid-template-columns: 1fr; text-align: center; } .foot-links { justify-content: center; } .foot-left { align-items: center; } }
.modal-backdrop { position: fixed; inset: 0; background: rgba(15,27,45,.55); backdrop-filter: blur(4px); display: grid; place-items: center; padding: 20px; z-index: 100; animation: fade-in .2s ease-out; }
@keyframes fade-in { from{opacity:0;}to{opacity:1;} }
.modal { background: white; border-radius: var(--radius-lg); max-width: 460px; width: 100%; padding: 32px 28px 24px; box-shadow: 0 24px 64px rgba(11,27,45,.25); position: relative; text-align: center; animation: pop-in .25s cubic-bezier(.16,1,.3,1); }
@keyframes pop-in { from{opacity:0;transform:translateY(12px) scale(.97);}to{opacity:1;transform:none;} }
.modal-close { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background: var(--bg); border: 0; font-size: 22px; line-height: 1; color: var(--ink-3); display: grid; place-items: center; transition: background .15s, color .15s; cursor: pointer; }
.modal-icon { width: 64px; height: 64px; margin: 0 auto 16px; border-radius: 50%; background: #e8f5ef; color: var(--success); display: grid; place-items: center; }
.modal h2 { font-size: 22px; font-weight: 800; margin: 0 0 8px; letter-spacing: -.01em; }
.modal-lede { font-size: 14px; color: var(--ink-2); margin: 0 0 18px; }
.modal-lede strong { color: var(--ink); }
.modal-card { display: flex; align-items: flex-start; gap: 10px; text-align: left; background: #fdf3f2; border: 1px solid #f9d4cf; padding: 14px; border-radius: var(--radius); margin-bottom: 18px; }
.modal-card p { margin: 0; font-size: 13px; color: var(--ink); line-height: 1.45; }
.modal-actions { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
.ghost-btn { background: transparent; border: 1.5px solid var(--line); color: var(--ink-2); padding: 12px 18px; border-radius: 14px; font-size: 14px; font-weight: 600; transition: all .15s; cursor: pointer; }
.modal-foot { margin: 0; font-size: 11px; color: var(--ink-3); display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
.modal-foot svg { color: var(--success); }
`;
  document.head.appendChild(style);
})();

const _app = document.createElement("script");
_app.type = "text/babel";
_app.setAttribute("data-presets", "react");
_app.textContent = `
const { useState, useEffect } = React;

const FOTO_URL = "https://modulosanitario.org/wp-content/uploads/2025/08/banos-portadad-_0003_IMG-20250209-WA0023-1-768x768.jpg";

const Ic = {
  Lock:   (p) => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>,
  Shield: (p) => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/><path d="m9 12 2 2 4-4"/></svg>,
  Check:  (p) => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" {...p}><path d="m5 12 5 5L20 7"/></svg>,
  Arrow:  (p) => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>,
  Back:   (p) => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><path d="M19 12H5"/><path d="m11 19-7-7 7-7"/></svg>,
  Heart:  (p) => <svg viewBox="0 0 24 24" fill="currentColor" {...p}><path d="M12 21s-7.5-4.7-9.5-9.3C1 7.8 3.6 4 7.4 4c2 0 3.5 1.1 4.6 2.7C13.1 5.1 14.6 4 16.6 4 20.4 4 23 7.8 21.5 11.7 19.5 16.3 12 21 12 21z"/></svg>,
  Spark:  (p) => <svg viewBox="0 0 24 24" fill="currentColor" {...p}><path d="M12 2 13.8 9 21 11l-7.2 2L12 20l-1.8-7L3 11l7.2-2L12 2z"/></svg>,
  MP:     (p) => <svg viewBox="0 0 48 48" fill="none" {...p}><rect x="4" y="12" width="40" height="24" rx="6" fill="#00B1EA"/><path d="M14 24c4-6 12-6 16 0" stroke="#fff" strokeWidth="2.5" strokeLinecap="round"/><circle cx="22" cy="24" r="2.5" fill="#fff"/></svg>,
  CardL:  (p) => <svg viewBox="0 0 48 48" fill="none" {...p}><rect x="4" y="10" width="40" height="28" rx="5" fill="#0B6FB8"/><rect x="4" y="16" width="40" height="5" fill="#08527d"/><rect x="9" y="27" width="10" height="3" rx="1" fill="#fff" opacity=".9"/><circle cx="36" cy="31" r="3.5" fill="#F26B5C"/><circle cx="40" cy="31" r="3.5" fill="#FFC857" opacity=".8"/></svg>,
  CardI:  (p) => <svg viewBox="0 0 48 48" fill="none" {...p}><circle cx="24" cy="24" r="18" fill="#1F3A5F"/><ellipse cx="24" cy="24" rx="8" ry="18" stroke="#fff" strokeWidth="1.5" fill="none"/><path d="M6 24h36M9 16h30M9 32h30" stroke="#fff" strokeWidth="1.5"/></svg>,
  Bank:   (p) => <svg viewBox="0 0 48 48" fill="none" {...p}><path d="M24 6 6 16h36L24 6z" fill="#0B6FB8"/><rect x="9" y="18" width="4" height="16" fill="#0B6FB8"/><rect x="17" y="18" width="4" height="16" fill="#0B6FB8"/><rect x="27" y="18" width="4" height="16" fill="#0B6FB8"/><rect x="35" y="18" width="4" height="16" fill="#0B6FB8"/><rect x="6" y="36" width="36" height="4" rx="1" fill="#0B6FB8"/></svg>,
};

function Logo({ size = 40 }) {
  return (
    <div style={{ display:"flex", alignItems:"center", gap:10 }}>
      <svg width={size} height={size} viewBox="0 0 40 40" fill="none">
        <rect x="2" y="2" width="16" height="16" rx="3" fill="var(--primary)"/>
        <rect x="22" y="2" width="16" height="16" rx="3" fill="var(--primary)" opacity=".55"/>
        <rect x="2" y="22" width="16" height="16" rx="3" fill="var(--primary)" opacity=".75"/>
        <rect x="22" y="22" width="16" height="16" rx="3" fill="var(--accent)"/>
      </svg>
      <div style={{ lineHeight:1, display:"flex", flexDirection:"column", gap:2 }}>
        <span style={{ fontWeight:800, fontSize:14, color:"var(--primary)", letterSpacing:".02em" }}>MÓDULO</span>
        <span style={{ fontWeight:600, fontSize:12, color:"var(--ink-2)", letterSpacing:".18em" }}>SANITARIO</span>
      </div>
    </div>
  );
}

function TopBar({ step }) {
  return (
    <header className="topbar">
      <div className="topbar-inner">
        <Logo />
        <a href="/inicio" className="topbar-back">
          <Ic.Back width="16" height="16" /> Volver al sitio
        </a>
      </div>
      <div className="stepper">
        {[["Tus datos",1],["Método de pago",2],["Confirmar",3]].map(([lbl,n],i) => (
          <React.Fragment key={n}>
            {i > 0 && <div className={"step-line "+(step>=n?"active":"")} />}
            <div className={"step "+(step>=n?"active":"")}>
              <span className="step-num">{n}</span>
              <span className="step-label">{lbl}</span>
            </div>
          </React.Fragment>
        ))}
      </div>
    </header>
  );
}

function ImpactStrip({ amount }) {
  const tiers = [
    { min:0,      max:1499,     msg:"una familia accede a productos de higiene por un mes" },
    { min:1500,   max:4999,     msg:"ayudás a financiar materiales para construir un baño digno" },
    { min:5000,   max:14999,    msg:"cubrís el inodoro y la ducha de un módulo sanitario" },
    { min:15000,  max:49999,    msg:"una familia accede a un baño digno por primera vez" },
    { min:50000,  max:Infinity, msg:"construís un módulo sanitario completo para una familia" },
  ];
  const tier = tiers.find(t => amount >= t.min && amount <= t.max) || tiers[0];
  return (
    <div className="impact-strip">
      <div className="impact-icon"><Ic.Spark width="16" height="16" /></div>
      <p>Con <strong>\${amount.toLocaleString("es-AR")} ARS</strong>, {tier.msg}.</p>
    </div>
  );
}

function Field({ label, error, hint, optional, ...props }) {
  return (
    <label className={"field "+(error?"has-error":"")}>
      <span className="field-label">{label} {optional && <em className="optional">(opcional)</em>}</span>
      <input {...props} />
      {hint && !error && <span className="field-hint">{hint}</span>}
      {error && <span className="field-error">{error}</span>}
    </label>
  );
}

function TrustRow() {
  return (
    <div className="trust-row">
      <div className="trust-item"><Ic.Lock width="16" height="16"/><div><strong>Pago seguro</strong><span>SSL 256-bit</span></div></div>
      <div className="trust-item"><Ic.Shield width="16" height="16"/><div><strong>Sitio verificado</strong><span>PCI-DSS · MP</span></div></div>
      <div className="trust-item"><Ic.Check width="16" height="16"/><div><strong>ONG inscripta</strong><span>IGJ · Ley 27.260</span></div></div>
    </div>
  );
}

/* ── STEP 1 ── */
function Step1({ data, setData, onNext, savedForLater }) {
  const [errors, setErrors] = useState({});
  const [touched, setTouched] = useState({});

  const validate = () => {
    const e = {};
    if (!data.nombre?.trim())   e.nombre   = "Ingresá tu nombre";
    if (!data.apellido?.trim()) e.apellido  = "Ingresá tu apellido";
    if (!data.email?.trim())    e.email     = "Ingresá tu email";
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) e.email = "Email inválido";
    if (!data.dni?.trim())      e.dni       = "Ingresá tu DNI";
    if (data.telefono && data.telefono.replace(/\D/g,"").length < 8) e.telefono = "Teléfono inválido";
    return e;
  };

  const ch = (k) => (ev) => setData({ ...data, [k]: ev.target.value });
  const bl = (k) => () => setTouched({ ...touched, [k]: true });

  const submit = (ev) => {
    ev.preventDefault();
    const e = validate();
    setErrors(e);
    setTouched({ nombre:true, apellido:true, email:true, dni:true, telefono:true });
    if (Object.keys(e).length === 0) onNext();
  };

  useEffect(() => { if (Object.keys(touched).length) setErrors(validate()); }, [data]);
  const showErr = (k) => touched[k] && errors[k];

  return (
    <div className="step1-grid">
      <aside className="hero">
        <div className="hero-photo">
          <img src={FOTO_URL} alt="Familia con su nuevo baño digno" />
          <div className="hero-photo-cap">Familia Pereyra · Florencio Varela · 2025</div>
        </div>
        <div className="hero-stats">
          <div className="stat"><div className="stat-num">6M</div><div className="stat-label">de personas en Argentina viven sin baño</div></div>
          <div className="stat"><div className="stat-num">+1.200</div><div className="stat-label">módulos sanitarios construidos desde 2014</div></div>
        </div>
        <blockquote className="hero-quote">
          <p>"Antes mis hijos hacían sus necesidades en una letrina afuera. Ahora tienen un baño que les da dignidad."</p>
          <cite>— Carolina, beneficiaria, Quilmes</cite>
        </blockquote>
      </aside>
      <main className="form-card">
        <div className="form-head">
          <span className="eyebrow">Doná en 2 pasos</span>
          <h1>Construyamos juntos un <em>baño digno</em>.</h1>
          <p className="lede">Cada donación es una familia que deja de defecar al aire libre.</p>
        </div>
        {savedForLater && (
          <div className="saved-banner"><Ic.Check width="16" height="16"/><p>Tus datos están guardados. Cuando quieras, completá tu donación.</p></div>
        )}
        <div className="impact-strip">
          <div className="impact-icon"><Ic.Spark width="16" height="16"/></div>
          <p>Con tu donación, una familia accede a un <strong>baño digno</strong> por primera vez.</p>
        </div>
        <form onSubmit={submit} noValidate>
          <div className="fields">
            <div className="field-row two">
              <Field label="Nombre" value={data.nombre} onChange={ch("nombre")} onBlur={bl("nombre")} error={showErr("nombre")&&errors.nombre} autoComplete="given-name" required />
              <Field label="Apellido" value={data.apellido} onChange={ch("apellido")} onBlur={bl("apellido")} error={showErr("apellido")&&errors.apellido} autoComplete="family-name" required />
            </div>
            <Field label="Email" type="email" value={data.email} onChange={ch("email")} onBlur={bl("email")} error={showErr("email")&&errors.email} autoComplete="email" required hint="Te enviaremos el comprobante." />
            <Field label="DNI" type="text" inputMode="numeric" value={data.dni} onChange={ch("dni")} onBlur={bl("dni")} error={showErr("dni")&&errors.dni} required hint="Requerido por Mercado Pago para identificar el pago." />
            <Field label="Teléfono" type="tel" value={data.telefono} onChange={ch("telefono")} onBlur={bl("telefono")} error={showErr("telefono")&&errors.telefono} autoComplete="tel" optional hint="Solo si querés que te contactemos." />
          </div>
          <button type="submit" className="cta"><span>Continuar</span><Ic.Arrow width="20" height="20"/></button>
          <div className="reassure"><Ic.Lock width="14" height="14"/> Tus datos están protegidos. No los compartimos con terceros.</div>
        </form>
      </main>
    </div>
  );
}

/* ── STEP 2 ── */
function Step2({ data, amount, setAmount, frequency, setFrequency, onBack, onSelect }) {
  const [customAmount, setCustomAmount] = useState(false);
  const [amountError, setAmountError] = useState(null);
  const presets = [1500, 5000, 15000, 50000];

  const handleSelect = (id) => {
    if (!amount || amount < 100) { setAmountError("Elegí un monto válido (mínimo \$100)"); return; }
    setAmountError(null);
    onSelect(id);
  };

  const methods = [
    { id:"mp",    name:"Mercado Pago",             desc:"Tarjeta, dinero en cuenta o efectivo en Pago Fácil/Rapipago.", tags:["Recomendado","Sin comisión extra"], icon:<Ic.MP    width="48" height="48"/>, color:"#00B1EA" },
    { id:"local", name:"Tarjeta local (Argentina)", desc:"Crédito o débito emitida en Argentina. Hasta 3 cuotas sin interés.", tags:["Crédito y débito"],          icon:<Ic.CardL width="48" height="48"/>, color:"#0B6FB8" },
    { id:"intl",  name:"Tarjeta internacional",     desc:"Para donantes desde el exterior. Procesado en USD.",          tags:["USD","Visa · Master · Amex"],       icon:<Ic.CardI width="48" height="48"/>, color:"#1F3A5F" },
    { id:"bank",  name:"Transferencia bancaria",    desc:"Te mostramos los datos de la cuenta para hacer la transferencia.", tags:["CBU/Alias"],                    icon:<Ic.Bank  width="48" height="48"/>, color:"#0B6FB8" },
  ];

  return (
    <div className="step2-wrap">
      <button className="back-link" onClick={onBack} type="button"><Ic.Back width="16" height="16"/> Volver</button>
      <div className="step2-head">
        <span className="eyebrow">Paso 2 de 2</span>
        <h1>Elegí tu monto y cómo donar</h1>
        <p className="lede">¡Gracias <strong>{data.nombre||"donante"}</strong>! Definí cuánto querés aportar y elegí el método de pago.</p>
      </div>
      <fieldset className="freq">
        <legend>Frecuencia</legend>
        <div className="freq-row">
          {[{id:"unico",label:"Donación única"},{id:"mensual",label:"Mensual",badge:"+ impacto"}].map(f => (
            <label key={f.id} className={"freq-opt "+(frequency===f.id?"selected":"")}>
              <input type="radio" name="freq" value={f.id} checked={frequency===f.id} onChange={()=>setFrequency(f.id)}/>
              <span className="freq-dot"/>
              <span>{f.label}</span>
              {f.badge && <span className="freq-badge">{f.badge}</span>}
            </label>
          ))}
        </div>
      </fieldset>
      <fieldset className="amount">
        <legend>Elegí un monto {frequency==="mensual"&&<small>por mes</small>}</legend>
        <div className="amount-grid">
          {presets.map(p => (
            <button key={p} type="button" className={"amount-chip "+(!customAmount&&amount===p?"selected":"")} onClick={()=>{setAmount(p);setCustomAmount(false);setAmountError(null);}}>
              <span className="amount-currency">\$</span><span className="amount-value">{p.toLocaleString("es-AR")}</span><span className="amount-iso">ARS</span>
            </button>
          ))}
          <div className={"amount-custom "+(customAmount?"selected":"")}>
            <span className="amount-currency">\$</span>
            <input type="number" inputMode="numeric" placeholder="Otro monto" onFocus={()=>setCustomAmount(true)} onChange={e=>{setAmount(parseInt(e.target.value||"0",10));setAmountError(null);}} aria-label="Otro monto"/>
          </div>
        </div>
        {amountError && <p className="err">{amountError}</p>}
      </fieldset>
      <ImpactStrip amount={amount||0}/>
      <h2 className="methods-title">Método de pago</h2>
      <ul className="methods">
        {methods.map(m => (
          <li key={m.id}>
            <button type="button" className="method-card" onClick={()=>handleSelect(m.id)}>
              <div className="method-icon" style={{background:m.color+"12"}}>{m.icon}</div>
              <div className="method-body">
                <div className="method-title-row">
                  <h3>{m.name}</h3>
                  <div className="method-tags">{m.tags.map((t,i)=><span key={i} className={"tag "+(t==="Recomendado"?"tag-rec":"")}>{t}</span>)}</div>
                </div>
                <p>{m.desc}</p>
              </div>
              <div className="method-arrow"><Ic.Arrow width="20" height="20"/></div>
            </button>
          </li>
        ))}
      </ul>
      <TrustRow/>
    </div>
  );
}

/* ── STEP 3 ── */
function Step3({ data, amount, frequency, method, onBack, onRestart, guardarEnFormidable }) {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const map = {
    mp:    { name:"Mercado Pago",           color:"#00B1EA", icon:<Ic.MP    width="40" height="40"/> },
    local: { name:"Tarjeta local",          color:"#0B6FB8", icon:<Ic.CardL width="40" height="40"/> },
    intl:  { name:"Tarjeta internacional",  color:"#1F3A5F", icon:<Ic.CardI width="40" height="40"/> },
    bank:  { name:"Transferencia bancaria", color:"#0B6FB8", icon:<Ic.Bank  width="40" height="40"/> },
  };
  const m = map[method] || map.mp;

  useEffect(() => {
    if (method === 'bank') { setLoading(false); return; }
    fetch('/wp-json/donacion/v1/crear-preferencia', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        monto:    amount,
        nombre:   data.nombre,
        apellido: data.apellido,
        email:    data.email,
        dni:      data.dni,
      })
    })
    .then(r => r.json())
    .then(res => {
      if (res.success && res.init_point) {
        guardarEnFormidable(data, {
          monto:         amount,
          metodo:        method,
          preference_id: res.id,
        });
        window.location.href = res.init_point;
      } else {
        setError('No pudimos conectar con Mercado Pago. Intentá de nuevo.');
        setLoading(false);
      }
    })
    .catch(() => {
      setError('Error de conexión. Intentá de nuevo.');
      setLoading(false);
    });
  }, []);

  return (
    <div className="step3-wrap">
      <button className="back-link" onClick={onBack} type="button">
        <Ic.Back width="16" height="16"/> Cambiar método de pago
      </button>
      <div className="step3-card">
        <div className="step3-badge" style={{background:m.color+"18", color:m.color}}>{m.icon}</div>

        {loading && method !== 'bank' && (
          <>
            <h2>Preparando tu donación…</h2>
            <p className="lede">Conectando con {m.name}. Un segundo.</p>
            <div className="step3-loader">
              <div className="dot"/><div className="dot"/><div className="dot"/>
            </div>
          </>
        )}

        {error && (
          <>
            <h2>Algo salió mal</h2>
            <p className="lede" style={{color:"var(--accent-dark)"}}>{error}</p>
            <div className="step3-actions" style={{marginTop:20}}>
              <button type="button" className="cta" onClick={onBack}>Volver a intentar</button>
            </div>
          </>
        )}

        {method === 'bank' && (
          <>
            <h2>Datos para transferencia</h2>
            <p className="lede">Donación de <strong>\${amount.toLocaleString("es-AR")} ARS</strong> a nombre de <strong>{data.nombre} {data.apellido}</strong>.</p>
            <div className="bank-block">
              <h4>Datos de la cuenta</h4>
              <dl>
                <div><dt>Titular</dt><dd>Asoc. Civil Módulo Sanitario</dd></div>
                <div><dt>CUIT</dt><dd>30-71234567-8</dd></div>
                <div><dt>Banco</dt><dd>Banco Galicia</dd></div>
                <div><dt>CBU</dt><dd>0070123456789012345678</dd></div>
                <div><dt>Alias</dt><dd>MODULO.SANITARIO.AR</dd></div>
              </dl>
              <p className="bank-note">Enviá el comprobante a <a href="mailto:donaciones@modulosanitario.org">donaciones@modulosanitario.org</a></p>
            </div>
            <div className="step3-actions">
              <button type="button" className="cta" onClick={onRestart}>Hacer otra donación</button>
              <a href="/inicio" className="ghost">Volver al sitio</a>
            </div>
          </>
        )}
      </div>
      <TrustRow/>
    </div>
  );
}

/* ── MODAL ── */
function SavedDataModal({ data, onDonateNow, onDonateLater, onClose }) {
  useEffect(() => {
    const onKey = e => { if (e.key==="Escape") onClose(); };
    document.addEventListener("keydown", onKey);
    document.body.style.overflow = "hidden";
    return () => { document.removeEventListener("keydown", onKey); document.body.style.overflow = ""; };
  }, [onClose]);
  return (
    <div className="modal-backdrop" onClick={onClose}>
      <div className="modal" onClick={e=>e.stopPropagation()}>
        <button type="button" className="modal-close" onClick={onClose}>×</button>
        <div className="modal-icon"><Ic.Check width="28" height="28"/></div>
        <h2>¡Listo, {data.nombre||"donante"}! Guardamos tus datos.</h2>
        <p className="modal-lede">Te enviamos un correo a <strong>{data.email}</strong> para que puedas retomar tu donación cuando quieras.</p>
        <div className="modal-card"><Ic.Heart width="18" height="18" style={{color:"var(--accent)",flexShrink:0}}/><p><strong>¿Querés donar ahora?</strong> Te lleva 1 minuto y tu aporte se convierte hoy mismo en materiales para construir un baño digno.</p></div>
        <div className="modal-actions">
          <button type="button" className="cta" onClick={onDonateNow}><span>Sí, donar ahora</span><Ic.Arrow width="20" height="20"/></button>
          <button type="button" className="ghost-btn" onClick={onDonateLater}>Donar más tarde</button>
        </div>
        <p className="modal-foot"><Ic.Lock width="12" height="12"/> Tus datos están protegidos.</p>
      </div>
    </div>
  );
}

/* ── FOOTER ── */
function Footer() {
  return (
    <footer className="site-footer">
      <div className="foot-inner">
        <div className="foot-left"><Logo size={32}/><p>Asoc. Civil sin fines de lucro · Buenos Aires, Argentina</p></div>
        <div className="foot-seals">
          <span className="seal"><Ic.Lock width="12" height="12"/> SSL Seguro</span>
          <span className="seal"><Ic.Shield width="12" height="12"/> PCI-DSS</span>
          <span className="seal"><Ic.Check width="12" height="12"/> ONG Verificada</span>
        </div>
        <div className="foot-links"><a href="#">Términos</a><a href="#">Privacidad</a><a href="#">Contacto</a></div>
      </div>
    </footer>
  );
}

/* ── APP ── */
function App() {
  const [step, setStep]           = useState(1);
  const [data, setData]           = useState({ nombre:"", apellido:"", email:"", dni:"", telefono:"" });
  const [amount, setAmount]       = useState(5000);
  const [frequency, setFrequency] = useState("unico");
  const [method, setMethod]       = useState(null);
  const [showModal, setShowModal] = useState(false);
  const [savedForLater, setSavedForLater] = useState(false);

  const goStep = (s) => { setStep(s); window.scrollTo({ top:0, behavior:"smooth" }); };

  const guardarEnFormidable = async (datos, extra = {}) => {
    try {
      await fetch('/wp-json/donacion/v1/guardar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...datos, ...extra })
      });
    } catch(e) {
      console.log('Formidable save error:', e);
    }
  };

  return (
    <div>
      <TopBar step={step}/>
      <div className="content">
        {step===1 && <Step1 data={data} setData={setData} onNext={()=>{ guardarEnFormidable(data); setShowModal(true); }} savedForLater={savedForLater}/>}
        {step===2 && <Step2 data={data} amount={amount} setAmount={setAmount} frequency={frequency} setFrequency={setFrequency} onBack={()=>goStep(1)} onSelect={id=>{setMethod(id);goStep(3);}}/>}
        {step===3 && <Step3 data={data} amount={amount} frequency={frequency} method={method} onBack={()=>goStep(2)} onRestart={()=>{setStep(1);setMethod(null);}} guardarEnFormidable={guardarEnFormidable}/>}
      </div>
      <Footer/>
      {showModal && (
        <SavedDataModal data={data}
          onDonateNow={()=>{ setShowModal(false); goStep(2); }}
          onDonateLater={()=>{ setShowModal(false); setSavedForLater(true); }}
          onClose={()=>setShowModal(false)}
        />
      )}
    </div>
  );
}

ReactDOM.createRoot(document.getElementById("root")).render(<App/>);
`;
document.currentScript.after(_app);