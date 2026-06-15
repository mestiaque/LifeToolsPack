<script>
(function () {
  'use strict';

  /* ── URLs from meta tags ── */
  const META   = (name) => document.querySelector(`meta[name="${name}"]`)?.content ?? '';
  const STORE  = META('md-store-url');
  const LIST   = META('md-list-url');
  const UPD    = META('md-update-base');   // /admin/memorable-days/{id}
  const DEL    = META('md-delete-base');
  const STORAGE = META('storage-url');     // /storage
  const CSRF   = META('csrf-token');

  /* ── State ── */
  let allDays  = [];
  let tagsList = [];
  let editId   = null;
  let currentCat = '';

  /* ── Category config ── */
  const CAT = {
    birthday    : { emoji: '🎂', grad: 'linear-gradient(135deg,#f093fb,#f5576c)' },
    love        : { emoji: '💕', grad: 'linear-gradient(135deg,#f5576c,#f093fb)' },
    family      : { emoji: '👨‍👩‍👧', grad: 'linear-gradient(135deg,#a18cd1,#fbc2eb)' },
    friendship  : { emoji: '🤝', grad: 'linear-gradient(135deg,#fa709a,#fee140)' },
    career      : { emoji: '💼', grad: 'linear-gradient(135deg,#4facfe,#00f2fe)' },
    travel      : { emoji: '✈️', grad: 'linear-gradient(135deg,#43e97b,#38f9d7)' },
    achievement : { emoji: '🏆', grad: 'linear-gradient(135deg,#f6d365,#fda085)' },
    health      : { emoji: '💪', grad: 'linear-gradient(135deg,#84fab0,#8fd3f4)' },
    grief       : { emoji: '🕯️', grad: 'linear-gradient(135deg,#4a4a6a,#9b8fa6)' },
    other       : { emoji: '⭐', grad: 'linear-gradient(135deg,#667eea,#764ba2)' },
    ''          : { emoji: '✨', grad: 'linear-gradient(135deg,#667eea,#764ba2)' },
  };

  function catCfg(cat) { return CAT[cat] ?? CAT['']; }

  /* ── Next annual occurrence (month/day only, year-independent) ──
     Returns the nearest future date with the same month & day.
     If this year's occurrence has already passed, returns next year's. */
  function getNextOccurrence(dateStr) {
    const today = new Date(); today.setHours(0,0,0,0);
    const d = parseLocalDate(dateStr);
    if (!d || isNaN(d)) return null;

    const thisYear = new Date(today.getFullYear(), d.getMonth(), d.getDate());
    return thisYear >= today
      ? thisYear
      : new Date(today.getFullYear() + 1, d.getMonth(), d.getDate());
  }

  /* ── Proximity: "কতদিন পরে এই event আবার আসবে" ── */
  function proximity(dateStr) {
    const today = new Date(); today.setHours(0,0,0,0);
    const next  = getNextOccurrence(dateStr);
    if (!next) return { label: '—', cls: 'prox-past' };

    const diff = Math.round((next - today) / 86400000);

    if (diff === 0) return { label: '🎉 Today!',        cls: 'prox-today' };
    if (diff === 1) return { label: '⏰ Tomorrow',       cls: 'prox-soon' };
    if (diff <= 7)  return { label: `⏰ In ${diff} days`, cls: 'prox-soon' };
    if (diff <= 30) return { label: `📅 In ${Math.round(diff/7)}w`, cls: 'prox-upcoming' };
    const months = Math.round(diff / 30);
    if (months < 12) return { label: `📅 In ${months} mo`, cls: 'prox-future' };
    return { label: '📅 In ~1 yr', cls: 'prox-future' };
  }

  /* ── Sort by next annual occurrence (nearest first) ── */
  function sortByProximity(days) {
    return [...days].sort((a, b) => {
      const na = getNextOccurrence(a.event_date) || new Date(9999, 0, 1);
      const nb = getNextOccurrence(b.event_date) || new Date(9999, 0, 1);
      return na - nb;
    });
  }

  /* ── Render stars (read-only) ── */
  function stars(n, total = 5) {
    let h = '';
    for (let i = 1; i <= total; i++) {
      h += `<span class="${i <= n ? 'md-star-filled' : 'md-star-empty'}">★</span>`;
    }
    return h;
  }

  /* ── Safe date parser — handles "YYYY-MM-DD", "YYYY-MM-DD HH:MM:SS", ISO strings ── */
  function parseLocalDate(dateStr) {
    if (!dateStr) return null;
    const clean = String(dateStr).substring(0, 10); // keep only YYYY-MM-DD
    const [y, m, d] = clean.split('-').map(Number);
    if (!y || !m || !d) return null;
    return new Date(y, m - 1, d); // local midnight — no timezone shift
  }

  /* ── Format date ── */
  function fmt(dateStr) {
    const d = parseLocalDate(dateStr);
    if (!d || isNaN(d)) return '—';
    return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
  }

  /* ── Build a single card element ── */
  function buildCard(day) {
    const wrap = document.createElement('div');
    wrap.className = 'md-card-wrap';
    wrap.dataset.id = day.id;

    const cfg   = catCfg(day.category);
    const prox  = proximity(day.event_date);
    const color = day.color || '#667eea';
    const tags  = Array.isArray(day.tags) ? day.tags : [];
    const imgSrc = day.image_url ? `${STORAGE}/${day.image_url}` : null;

    /* ── front ── */
    const imgHtml = imgSrc
      ? `<img src="${imgSrc}" alt="${esc(day.title)}" loading="lazy">`
      : `<div class="md-placeholder" style="background:${cfg.grad}">${cfg.emoji}</div>`;

    const badgeHtml = day.is_private
      ? `<div class="md-lock" title="Private"><i class="fas fa-lock"></i></div>`
      : (day.repeat_yearly
          ? `<div class="md-repeat"><i class="fas fa-redo-alt"></i>Yearly</div>`
          : '');

    /* ── back ── */
    const catLabel  = day.category ? day.category.charAt(0).toUpperCase() + day.category.slice(1) : 'Memory';
    const locHtml   = day.location
      ? `<div class="md-back-meta"><i class="fas fa-map-marker-alt"></i>${esc(day.location)}</div>` : '';
    const impHtml   = day.importance_level
      ? `<div class="md-back-meta"><i class="fas fa-star"></i><div class="md-back-stars">${stars(day.importance_level)}</div></div>` : '';
    const tagsHtml  = tags.length
      ? `<div class="md-back-tags">${tags.map(t => `<span class="md-back-tag">#${esc(t)}</span>`).join('')}</div>` : '';
    const descHtml  = day.description
      ? `<div class="md-back-desc">${esc(day.description)}</div>` : '';

    wrap.innerHTML = `
      <div class="md-card">
        <!-- FRONT -->
        <div class="md-card-front">
          <div class="md-img-wrap">
            ${imgHtml}
            <div class="md-img-overlay"></div>
            <div class="md-color-bar" style="background:${color}"></div>
            ${badgeHtml}
          </div>
          <div class="md-card-body">
            <p class="md-card-title">${esc(day.title)}</p>
            <div class="md-card-date"><i class="fas fa-calendar-alt"></i>${fmt(day.event_date)}</div>
            <span class="md-proximity ${prox.cls}">${prox.label}</span>
          </div>
        </div>
        <!-- BACK -->
        <div class="md-card-back" style="--accent:${color}">
          <div class="md-back-top-row">
            ${day.category ? `<div class="md-back-cat">${cfg.emoji} ${catLabel}</div>` : '<div></div>'}
            <button class="md-edit-btn" data-id="${day.id}" title="Edit">
              <i class="fas fa-pen"></i>
            </button>
          </div>
          <p class="md-back-title">${esc(day.title)}</p>
          ${descHtml}
          ${locHtml}
          ${impHtml}
          ${tagsHtml}
        </div>
      </div>`;

    /* flip on tap (mobile – no hover) */
    const card = wrap.querySelector('.md-card');
    wrap.addEventListener('click', (e) => {
      if (e.target.closest('.md-edit-btn')) return;
      if (!window.matchMedia('(hover: hover)').matches) {
        wrap.classList.toggle('flipped');
      }
    });

    return wrap;
  }

  function esc(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* ── Render grid ── */
  const grid = document.getElementById('md-grid');

  function render(days) {
    grid.innerHTML = '';
    const sorted = sortByProximity(days);
    if (!sorted.length) {
      grid.innerHTML = `<div class="md-empty"><i class="fas fa-book-open"></i><p>No memories yet. Add your first one!</p></div>`;
      return;
    }
    sorted.forEach(d => grid.appendChild(buildCard(d)));
  }

  /* ── Filtered render ── */
  function applyFilters() {
    const q = document.getElementById('md-search').value.toLowerCase().trim();
    if (!Array.isArray(allDays)) allDays = [];
    let days = allDays;
    if (currentCat) days = days.filter(d => d.category === currentCat);
    if (q) {
      days = days.filter(d => {
        const tags = Array.isArray(d.tags) ? d.tags.join(' ') : (d.tags || '');
        return (d.title || '').toLowerCase().includes(q)
            || (d.category || '').toLowerCase().includes(q)
            || tags.toLowerCase().includes(q);
      });
    }
    render(days);
  }

  /* ── Load data ── */
  async function loadDays() {
    try {
      const res = await fetch(LIST, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
      const data = await res.json();
      allDays = Array.isArray(data) ? data : [];
      buildChips();
      applyFilters();
    } catch (e) {
      allDays = [];
      grid.innerHTML = `<div class="md-empty"><i class="fas fa-exclamation-circle"></i><p>Failed to load memories.</p></div>`;
    }
  }

  /* ── Category filter chips ── */
  function buildChips() {
    const cats = [...new Set(allDays.map(d => d.category).filter(Boolean))];
    const chipsWrap = document.getElementById('md-chips');
    chipsWrap.innerHTML = `<button class="md-chip ${currentCat==='' ? 'active' : ''}" data-cat="">All</button>`;
    cats.forEach(cat => {
      const cfg = catCfg(cat);
      const btn = document.createElement('button');
      btn.className = `md-chip ${currentCat === cat ? 'active' : ''}`;
      btn.dataset.cat = cat;
      btn.textContent = `${cfg.emoji} ${cat.charAt(0).toUpperCase() + cat.slice(1)}`;
      chipsWrap.appendChild(btn);
    });
  }

  document.getElementById('md-chips').addEventListener('click', (e) => {
    const chip = e.target.closest('.md-chip');
    if (!chip) return;
    document.querySelectorAll('.md-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    currentCat = chip.dataset.cat;
    applyFilters();
  });

  document.getElementById('md-search').addEventListener('input', applyFilters);

  /* ══════════════════════════════════════════
     MODAL
     ══════════════════════════════════════════ */
  const modal        = new bootstrap.Modal(document.getElementById('md-modal'));
  const form         = document.getElementById('md-form');
  const modalTitle   = document.getElementById('md-modal-title');
  const deleteBtn    = document.getElementById('md-delete-btn');
  const imgDrop      = document.getElementById('md-img-drop');
  const imgInput     = document.getElementById('md-img-input');
  const imgPreview   = document.getElementById('md-img-preview');
  const imgPlaceholder = document.getElementById('md-img-placeholder');
  const imgRemoveBtn = document.getElementById('md-img-remove');
  const removeImgHid = document.getElementById('md-remove-image');
  const tagWrap      = document.getElementById('md-tag-wrap');
  const tagInput     = document.getElementById('md-tag-input');
  const tagsHid      = document.getElementById('md-tags');
  const importanceHid = document.getElementById('md-importance');
  const starsEl      = document.querySelectorAll('#md-importance-stars .star');
  const reminderWrap   = document.getElementById('md-reminder-days-wrap');
  const reminderDaysEl = document.getElementById('md-reminder-days');

  /* ── Open "Add" modal ── */
  document.getElementById('md-add-btn').addEventListener('click', () => openModal(null));

  /* ── Open "Edit" via event delegation ── */
  grid.addEventListener('click', (e) => {
    const btn = e.target.closest('.md-edit-btn');
    if (!btn) return;
    const id = parseInt(btn.dataset.id);
    const day = allDays.find(d => d.id === id);
    if (day) openModal(day);
  });

  function openModal(day) {
    editId = day?.id ?? null;
    resetModal();

    if (day) {
      modalTitle.textContent = 'Edit Memory';
      deleteBtn.style.display = '';
      form['title'].value        = day.title      || '';
      form['event_date'].value   = day.event_date  ? day.event_date.substring(0,10) : '';
      form['description'].value  = day.description || '';
      form['category'].value     = day.category    || '';
      form['location'].value     = day.location    || '';
      form['color'].value        = day.color       || '#667eea';
      document.getElementById('md-private').checked   = !!day.is_private;
      document.getElementById('md-repeat').checked    = !!day.repeat_yearly;
      document.getElementById('md-reminder').checked  = !!day.reminder_enabled;
      reminderDaysEl.value = day.reminder_days_before ?? '';
      reminderWrap.style.display = day.reminder_enabled ? '' : 'none';

      setStars(day.importance_level || 0);

      const tags = Array.isArray(day.tags) ? day.tags : [];
      tagsList = [...tags];
      renderTags();

      if (day.image_url) {
        showPreview(`${STORAGE}/${day.image_url}`);
      }
    } else {
      modalTitle.textContent = 'New Memory';
      deleteBtn.style.display = 'none';
    }

    modal.show();
  }

  function resetModal() {
    form.reset();
    tagsList = [];
    renderTags();
    setStars(0);
    hidePreview();
    removeImgHid.value = '0';
    reminderWrap.style.display = 'none';
    form['color'].value = '#667eea';
  }

  /* ── Image drop/click ── */
  imgDrop.addEventListener('click', () => imgInput.click());

  imgDrop.addEventListener('dragover', (e) => {
    e.preventDefault();
    imgDrop.style.borderColor = '#667eea';
  });

  imgDrop.addEventListener('dragleave', () => {
    imgDrop.style.borderColor = '';
  });

  imgDrop.addEventListener('drop', (e) => {
    e.preventDefault();
    imgDrop.style.borderColor = '';
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
      const dt = new DataTransfer();
      dt.items.add(file);
      imgInput.files = dt.files;
      previewFile(file);
    }
  });

  imgInput.addEventListener('change', () => {
    if (imgInput.files[0]) previewFile(imgInput.files[0]);
  });

  imgRemoveBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    hidePreview();
    imgInput.value = '';
    removeImgHid.value = '1';
  });

  function previewFile(file) {
    const reader = new FileReader();
    reader.onload = (e) => showPreview(e.target.result);
    reader.readAsDataURL(file);
  }

  function showPreview(src) {
    imgPreview.src = src;
    imgPreview.style.display = 'block';
    imgPlaceholder.style.display = 'none';
    imgRemoveBtn.style.display = 'flex';
    removeImgHid.value = '0';
  }

  function hidePreview() {
    imgPreview.style.display = 'none';
    imgPreview.src = '';
    imgPlaceholder.style.display = 'flex';
    imgRemoveBtn.style.display = 'none';
  }

  /* ── Stars ── */
  starsEl.forEach(star => {
    star.addEventListener('click', () => setStars(parseInt(star.dataset.val)));
    star.addEventListener('mouseover', () => highlightStars(parseInt(star.dataset.val)));
    star.addEventListener('mouseout', ()  => highlightStars(parseInt(importanceHid.value) || 0));
  });

  function setStars(n) {
    importanceHid.value = n;
    highlightStars(n);
  }

  function highlightStars(n) {
    starsEl.forEach(s => s.classList.toggle('active', parseInt(s.dataset.val) <= n));
  }

  /* ── Tags ── */
  tagInput.addEventListener('keydown', (e) => {
    if ((e.key === 'Enter' || e.key === ',') && tagInput.value.trim()) {
      e.preventDefault();
      const val = tagInput.value.trim().replace(/,+$/, '');
      if (val && !tagsList.includes(val)) {
        tagsList.push(val);
        renderTags();
      }
      tagInput.value = '';
    }
    if (e.key === 'Backspace' && !tagInput.value && tagsList.length) {
      tagsList.pop();
      renderTags();
    }
  });

  function renderTags() {
    const pills = tagsList.map((t, i) =>
      `<span class="md-tag-pill">${esc(t)}<button type="button" data-i="${i}">✕</button></span>`
    );
    tagWrap.innerHTML = pills.join('') + `<input type="text" id="md-tag-input" placeholder="add a tag…">`;
    tagWrap.querySelector('input').addEventListener('keydown', (e) => {
      if ((e.key === 'Enter' || e.key === ',') && e.target.value.trim()) {
        e.preventDefault();
        const val = e.target.value.trim().replace(/,+$/, '');
        if (val && !tagsList.includes(val)) { tagsList.push(val); renderTags(); }
        else e.target.value = '';
      }
      if (e.key === 'Backspace' && !e.target.value && tagsList.length) {
        tagsList.pop(); renderTags();
      }
    });
    tagWrap.querySelectorAll('.md-tag-pill button').forEach(btn => {
      btn.addEventListener('click', () => { tagsList.splice(btn.dataset.i, 1); renderTags(); });
    });
    tagsHid.value = JSON.stringify(tagsList);
  }

  /* ── Reminder toggle ── */
  document.getElementById('md-reminder').addEventListener('change', (e) => {
    reminderWrap.style.display = e.target.checked ? '' : 'none';
  });

  /* ── Form submit ── */
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    tagsHid.value = JSON.stringify(tagsList);

    const fd = new FormData(form);
    fd.delete('id');

    /* explicitly set every field so FormData doesn't rely on passive DOM state */
    fd.set('is_private',           document.getElementById('md-private').checked   ? '1' : '0');
    fd.set('repeat_yearly',        document.getElementById('md-repeat').checked    ? '1' : '0');
    fd.set('reminder_enabled',     document.getElementById('md-reminder').checked  ? '1' : '0');
    fd.set('reminder_days_before', reminderDaysEl.value ?? '');
    fd.set('importance_level',     importanceHid.value ?? '');

    let url = STORE;
    if (editId) {
      url = `${UPD}/${editId}`;
    }

    const saveBtn = document.getElementById('md-save-btn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving…';

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        body: fd,
      });

      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || 'Server error');
      }

      const saved = await res.json();

      if (!Array.isArray(allDays)) allDays = [];

      if (editId) {
        const idx = allDays.findIndex(d => d.id === editId);
        if (idx >= 0) allDays[idx] = saved; else allDays.unshift(saved);
      } else {
        allDays.unshift(saved);
      }

      modal.hide();
      buildChips();
      applyFilters();
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Oops!', text: err.message, confirmButtonColor: '#667eea' });
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fas fa-save me-1"></i>Save Memory';
    }
  });

  /* ── Delete ── */
  deleteBtn.addEventListener('click', async () => {
    if (!editId) return;
    const result = await Swal.fire({
      title: 'Delete this memory?',
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'Yes, delete',
    });
    if (!result.isConfirmed) return;

    try {
      const res = await fetch(`${DEL}/${editId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok) throw new Error('Delete failed');
      allDays = allDays.filter(d => d.id !== editId);
      modal.hide();
      buildChips();
      applyFilters();
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Oops!', text: err.message });
    }
  });

  /* ── Boot ── */
  loadDays();
})();
</script>
