<style>
/* ══════════════════════════════════════════
   MEMORABLE DAYS  –  Dashboard Styles
   ══════════════════════════════════════════ */

/* ── Controls bar ── */
.md-controls {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: .85rem;
  margin-bottom: 1.75rem;
}

.md-search-wrap {
  position: relative;
  flex: 1;
  min-width: 200px;
}

.md-search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
  pointer-events: none;
  font-size: .85rem;
}

.md-search {
  width: 100%;
  padding: .6rem 1rem .6rem 2.6rem;
  border: none;
  border-radius: 2rem;
  background: rgba(255,255,255,.82);
  box-shadow: 0 2px 14px rgba(0,0,0,.08);
  font-size: .93rem;
  backdrop-filter: blur(8px);
  transition: box-shadow .2s;
}

.md-search:focus {
  outline: none;
  box-shadow: 0 4px 22px rgba(102,126,234,.22);
}

/* ── Category filter chips ── */
.md-filter-chips {
  display: flex;
  flex-wrap: wrap;
  gap: .45rem;
}

.md-chip {
  padding: .3rem .85rem;
  border-radius: 999px;
  font-size: .78rem;
  font-weight: 500;
  border: 1.5px solid transparent;
  cursor: pointer;
  background: rgba(255,255,255,.78);
  color: #666;
  box-shadow: 0 1px 6px rgba(0,0,0,.07);
  transition: all .2s;
}

.md-chip:hover,
.md-chip.active {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 3px 14px rgba(102,126,234,.35);
}

/* ── Card grid ── */
.md-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 1.2rem;
  padding-bottom: 2rem;
}

/* ── 3-D flip card wrapper ── */
.md-card-wrap {
  height: 295px;
  perspective: 1200px;
  cursor: default;
}

.md-card {
  position: relative;
  width: 100%;
  height: 100%;
  transform-style: preserve-3d;
  transition: transform .72s cubic-bezier(.4,0,.2,1);
}

.md-card-wrap:hover .md-card,
.md-card-wrap.flipped .md-card {
  transform: rotateY(180deg);
}

/* ── Shared face styles ── */
.md-card-front,
.md-card-back {
  position: absolute;
  inset: 0;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 18px rgba(0,0,0,.1), 0 1px 4px rgba(0,0,0,.06);
  transition: box-shadow .3s;
}

.md-card-wrap:hover .md-card-front,
.md-card-wrap:hover .md-card-back {
  box-shadow: 0 14px 42px rgba(0,0,0,.17), 0 2px 8px rgba(0,0,0,.09);
}

/* ════ FRONT ════ */
.md-card-front {
  background: #f5edd8;   /* old parchment paper */
  display: flex;
  flex-direction: column;
}

.md-img-wrap {
  position: relative;
  height: 155px;
  flex-shrink: 0;
}

.md-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* gradient placeholder */
.md-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
}

/* photo-to-body gradient overlay */
.md-img-overlay {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 55%;
  background: linear-gradient(transparent, rgba(0,0,0,.3));
  pointer-events: none;
}

/* color accent bar */
.md-color-bar {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 4px;
}

/* edit button — lives on the back face */
.md-edit-btn {
  background: rgba(255,255,255,.15);
  border: 1px solid rgba(255,255,255,.25);
  border-radius: 50%;
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  color: rgba(255,255,255,.9);
  font-size: .75rem;
  backdrop-filter: blur(6px);
  transition: all .2s;
  flex-shrink: 0;
}

.md-edit-btn:hover {
  background: rgba(255,255,255,.28);
  color: #fff;
  transform: scale(1.12);
  box-shadow: 0 3px 12px rgba(0,0,0,.25);
}

/* private lock badge */
.md-lock {
  position: absolute;
  top: .65rem; left: .65rem;
  z-index: 10;
  background: rgba(0,0,0,.5);
  border-radius: 50%;
  width: 26px; height: 26px;
  display: flex; align-items: center; justify-content: center;
  color: rgba(255,255,255,.9);
  font-size: .65rem;
  backdrop-filter: blur(4px);
}

/* repeat yearly ribbon */
.md-repeat {
  position: absolute;
  top: .65rem; left: .65rem;
  z-index: 10;
  background: rgba(255,255,255,.88);
  border-radius: 999px;
  padding: .15rem .55rem;
  font-size: .65rem;
  font-weight: 700;
  color: #764ba2;
  box-shadow: 0 1px 6px rgba(0,0,0,.12);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; gap: .25rem;
}

.md-card-body {
  padding: .7rem 1rem .75rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  position: relative; /* needed for folded-corner pseudo-element */
}

/* ── Folded bottom-right corner ── */
.md-card-body::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: 0;
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 0 0 28px 28px;
  border-color: transparent transparent #c9a87a transparent;
  filter: drop-shadow(-2px -2px 3px rgba(0,0,0,.15));
}

.md-card-title {
  font-weight: 700;
  font-size: .95rem;
  color: #3a2410;   /* warm dark brown */
  margin: 0 0 .22rem;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  line-height: 1.35;
}

.md-card-date {
  font-size: .78rem;
  color: #8a6e4b;   /* medium brown */
  display: flex;
  align-items: center;
  gap: .3rem;
  margin-bottom: .4rem;
}

/* proximity badge */
.md-proximity {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  padding: .22rem .7rem;
  border-radius: 999px;
  font-size: .71rem;
  font-weight: 600;
  letter-spacing: .3px;
  margin-top: auto;
  align-self: flex-start;
}

.prox-today    { background: #fff3cd; color: #856404; }
.prox-soon     { background: #d1fae5; color: #065f46; }
.prox-upcoming { background: #dbeafe; color: #1e40af; }
.prox-future   { background: #ede9fe; color: #4c1d95; }
.prox-past     { background: #f3f4f6; color: #6b7280; }

/* ════ BACK ════ */
.md-card-back {
  transform: rotateY(180deg);
  background: linear-gradient(145deg, #1a1a2e 0%, #16213e 52%, #0f3460 100%);
  color: #fff;
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: .6rem;
  overflow-y: auto;
}

/* top row: category chip + edit button */
.md-back-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .5rem;
}

.md-back-cat {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  padding: .28rem .8rem;
  border-radius: 999px;
  font-size: .72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .5px;
  background: rgba(255,255,255,.12);
  color: rgba(255,255,255,.9);
  align-self: flex-start;
  border: 1px solid rgba(255,255,255,.14);
  backdrop-filter: blur(4px);
}

.md-back-title {
  font-size: 1.02rem;
  font-weight: 700;
  color: #fff;
  line-height: 1.3;
  margin: 0;
}

.md-back-desc {
  font-size: .84rem;
  color: rgba(255,255,255,.72);
  line-height: 1.55;
  flex: 1;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,.2) transparent;
}

.md-back-meta {
  display: flex;
  align-items: center;
  gap: .5rem;
  font-size: .79rem;
  color: rgba(255,255,255,.65);
}

.md-back-meta i {
  width: 14px;
  color: rgba(255,255,255,.45);
  flex-shrink: 0;
}

.md-back-stars {
  display: flex;
  gap: 1px;
}

.md-star-filled { color: #fbbf24; font-size: .75rem; }
.md-star-empty  { color: rgba(255,255,255,.18); font-size: .75rem; }

.md-back-tags {
  display: flex;
  flex-wrap: wrap;
  gap: .38rem;
  margin-top: auto;
}

.md-back-tag {
  padding: .14rem .52rem;
  border-radius: 999px;
  font-size: .68rem;
  background: rgba(255,255,255,.1);
  color: rgba(255,255,255,.78);
  border: 1px solid rgba(255,255,255,.16);
}

/* ── Empty state ── */
.md-empty {
  grid-column: 1 / -1;
  text-align: center;
  padding: 4.5rem 2rem;
  color: #bbb;
}

.md-empty i {
  font-size: 3.2rem;
  margin-bottom: .9rem;
  display: block;
  opacity: .35;
}

.md-empty p {
  font-size: 1rem;
  margin: 0;
}

/* ── Entrance animation ── */
@keyframes mdCardIn {
  from { opacity: 0; transform: translateY(18px) scale(.97); }
  to   { opacity: 1; transform: translateY(0)   scale(1);    }
}

.md-card-wrap {
  animation: mdCardIn .4s ease both;
}

/* stagger via nth-child (max 24 cards visible) */
.md-card-wrap:nth-child(1)  { animation-delay: .04s; }
.md-card-wrap:nth-child(2)  { animation-delay: .08s; }
.md-card-wrap:nth-child(3)  { animation-delay: .12s; }
.md-card-wrap:nth-child(4)  { animation-delay: .16s; }
.md-card-wrap:nth-child(5)  { animation-delay: .20s; }
.md-card-wrap:nth-child(6)  { animation-delay: .24s; }
.md-card-wrap:nth-child(7)  { animation-delay: .28s; }
.md-card-wrap:nth-child(8)  { animation-delay: .32s; }
.md-card-wrap:nth-child(9)  { animation-delay: .36s; }
.md-card-wrap:nth-child(10) { animation-delay: .40s; }
.md-card-wrap:nth-child(11) { animation-delay: .44s; }
.md-card-wrap:nth-child(12) { animation-delay: .48s; }

/* ════ MODAL ════ */
.md-modal-content {
  border: none;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,.22);
}

.md-modal-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border-bottom: none;
  padding: 1.1rem 1.5rem;
}

.md-modal-header .modal-title { font-weight: 700; font-size: 1.1rem; }

.md-modal-body {
  padding: 1.5rem;
  background: #f8f9fc;
}

.md-modal-footer {
  background: #f8f9fc;
  border-top: 1px solid rgba(0,0,0,.06);
  padding: .9rem 1.5rem;
  display: flex;
  align-items: center;
  gap: .6rem;
}

/* image drop zone */
.md-img-drop {
  position: relative;
  border: 2px dashed #d0d7e8;
  border-radius: 14px;
  min-height: 130px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  background: rgba(255,255,255,.7);
  overflow: hidden;
  transition: border-color .2s, background .2s;
}

.md-img-drop:hover {
  border-color: #667eea;
  background: rgba(102,126,234,.04);
}

#md-img-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .5rem;
  color: #aaa;
  font-size: .88rem;
  pointer-events: none;
}

#md-img-placeholder i { font-size: 2rem; }

#md-img-preview {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
  display: block;
}

#md-img-remove {
  position: absolute;
  top: .6rem; right: .6rem;
  background: rgba(0,0,0,.6);
  border: none;
  border-radius: 50%;
  width: 28px; height: 28px;
  color: #fff;
  font-size: .75rem;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  z-index: 5;
}

/* form inputs */
.md-label {
  font-size: .8rem;
  font-weight: 600;
  color: #555;
  margin-bottom: .3rem;
  display: block;
}

.md-input {
  border-radius: 10px !important;
  border-color: #e0e4ee !important;
  background: #fff !important;
  font-size: .9rem;
}

.md-input:focus {
  border-color: #667eea !important;
  box-shadow: 0 0 0 .18rem rgba(102,126,234,.18) !important;
}

.md-color-pick {
  width: 48px;
  height: 38px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  padding: 2px;
  background: none;
}

/* importance stars */
.md-star-rating {
  display: flex;
  gap: .3rem;
  font-size: 1.4rem;
  padding-top: .1rem;
}

.md-star-rating .star {
  cursor: pointer;
  color: #ddd;
  transition: color .15s, transform .1s;
  line-height: 1;
}

.md-star-rating .star:hover,
.md-star-rating .star.active {
  color: #fbbf24;
}

.md-star-rating .star:hover { transform: scale(1.15); }

/* tag input */
.md-tag-input-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: .35rem;
  padding: .4rem .5rem;
  border: 1px solid #e0e4ee;
  border-radius: 10px;
  min-height: 42px;
  cursor: text;
  background: #fff;
  transition: border-color .2s;
}

.md-tag-input-wrap:focus-within {
  border-color: #667eea;
  box-shadow: 0 0 0 .18rem rgba(102,126,234,.18);
}

.md-tag-input-wrap input {
  border: none;
  outline: none;
  font-size: .88rem;
  min-width: 80px;
  flex: 1;
  padding: .1rem .2rem;
  background: transparent;
}

.md-tag-pill {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  padding: .15rem .6rem;
  background: #e0e7ff;
  color: #4338ca;
  border-radius: 999px;
  font-size: .76rem;
  font-weight: 500;
}

.md-tag-pill button {
  cursor: pointer;
  font-size: .6rem;
  opacity: .65;
  padding: 0;
  background: none;
  border: none;
  color: inherit;
  line-height: 1;
}

/* toggle switches */
.md-toggles {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.md-toggle-label {
  display: flex;
  align-items: center;
  gap: .5rem;
  cursor: pointer;
  font-size: .85rem;
  color: #555;
  user-select: none;
}

.md-toggle-check { display: none; }

.md-toggle-slider {
  position: relative;
  width: 36px; height: 20px;
  background: #ddd;
  border-radius: 999px;
  flex-shrink: 0;
  transition: background .2s;
}

.md-toggle-slider::after {
  content: '';
  position: absolute;
  width: 14px; height: 14px;
  background: #fff;
  border-radius: 50%;
  top: 3px; left: 3px;
  transition: transform .2s;
  box-shadow: 0 1px 4px rgba(0,0,0,.18);
}

.md-toggle-check:checked ~ .md-toggle-slider { background: #667eea; }
.md-toggle-check:checked ~ .md-toggle-slider::after { transform: translateX(16px); }

/* ── Mobile ── */
@media (max-width: 576px) {
  .md-grid { grid-template-columns: repeat(2, 1fr); gap: .8rem; }
  .md-card-wrap { height: 270px; }
  .md-img-wrap { height: 135px; }
  .md-controls { flex-direction: column; align-items: stretch; }
}

@media (max-width: 400px) {
  .md-grid { grid-template-columns: 1fr; }
  .md-card-wrap { height: 295px; }
}
</style>
