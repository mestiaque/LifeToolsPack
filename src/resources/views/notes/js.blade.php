        // Ensure SweetAlert2 is loaded
        if (typeof Swal === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            script.onload = function() { window.Swal = Swal; };
            document.head.appendChild(script);
        }
<script>
    $(function() {
        // ---- Config ---- //
        const COLORS = [
            '#ffffff', '#f9fafb', '#bae5ff', '#dbeafe', '#e0e7ff',
            '#fbe7ff', '#fff6e5', '#fafbe5', '#e8f6df',
            '#ffdfe8', '#eafffd', '#ffe3b0', '#fed9b7',
            '#d4ffea', '#f0e4ff', '#f8dfff'
        ];

        let notes = [];
        let actionNoteId = null; // Note id being updated or deleted

        // LocalStorage fallback functions
        function lsSupported() { return !!window.localStorage; }
        function lsKey() { return '_liquid_glass_notes_v1'; }
        function getLocalNotes() {
            try {
                return JSON.parse(localStorage.getItem(lsKey())||'[]');
            } catch { return []; }
        }
        function setLocalNotes(arr) { localStorage.setItem(lsKey(), JSON.stringify(arr)); }

        // --- Util --- //
        function formatDate(ts) {
            let d = new Date(ts); if (isNaN(d)) return '';
            return d.toLocaleDateString(undefined, {month:'short', day:'numeric', year:'numeric'});
        }

        function debounce(func, wait) {
            let t; return function(...args) { clearTimeout(t); t=setTimeout(() => func.apply(this,args), wait);}
        }

        function isApiEnabled() {
            // dumb check: can we GET /api/notes? Try once.
            if (isApiEnabled._result !== undefined) return isApiEnabled._result;
            let ok = false;
            $.ajax({url: '/admin/get-notes', async: false, method: 'GET', success:()=>ok=true, error:()=>ok=false});
            isApiEnabled._result = ok;
            return ok;
        }

        // --- Render Color Filter Only --- //
        function renderColorFilter() {
            const $sel = $('#color-filter'); $sel.empty();
            $sel.append(`<option value="">All Colors</option>`);
            COLORS.forEach(c=> {
                $sel.append(`<option value="${c}">&#9632; ${c}</option>`);
            });
        }

        // --- CRUD AJAX/LS --- //
        function api(fn, ...args) {
            if (isApiEnabled()) return fn.api(...args);
            else if (lsSupported())  return fn.ls(...args);
            else throw 'No storage supported';
        }

        function loadNotes(params, cb) {
            api({
                api: (params, cb) => $.get('/admin/get-notes', params, cb),
                ls : (params, cb) => {
                    let all = getLocalNotes();
                    // filter by search and color
                    if (params.search) all = all.filter(n=> (n.title+n.description).toLowerCase().includes(params.search.toLowerCase()));
                    if (params.color) all = all.filter(n=> n.color === params.color);
                    // pinned and recency
                    all.sort((a,b) =>
                        (b.is_pinned - a.is_pinned) ||
                        new Date(b.updated_at)-new Date(a.updated_at)
                    );
                    cb(all);
                }
            }, params, function(ds){
                notes = ds;
                renderNotesGrid();
            });
        }
        function createNote(data, cb) {
            api({
                api: (d,cb)=>$.post('/admin/notes', d, cb),
                ls: (d,cb)=>{
                    let all = getLocalNotes(),
                        dt = (new Date()).toISOString(), id = Math.max(0,...all.map(n=>n.id||0))+1;
                    let note = {...d, id, created_at: dt, updated_at: dt};
                    all.push(note); setLocalNotes(all);
                    cb(note); }
            }, data, cb);
        }
        function updateNote(id, data, cb) {
            api({
                api: (id,d,cb)=>$.ajax({url:'/admin/notes/'+id,method:'PUT',data:d,success:cb}),
                ls: (id,d,cb)=>{
                    let all = getLocalNotes(), n = all.find(n=>n.id==id); if (n){
                        Object.assign(n, d, {updated_at: (new Date()).toISOString()});
                        setLocalNotes(all); cb(n); }
                }
            }, id, data, cb);
        }
        function deleteNote(id, cb) {
            api({
                api: (id,cb)=>$.ajax({url:'/admin/notes/'+id,method:'DELETE',success:cb}),
                ls: (id,cb)=>{
                    let all = getLocalNotes().filter(n=>n.id!=id); setLocalNotes(all); cb();
                }
            }, id, cb);
        }

        // --- Render Notes --- //
        function renderNotesGrid() {
            const $grid = $('#notes-grid'); $grid.empty();
            if (!notes.length) {
                $grid.append(`<div class="w-100" style="text-align:center;opacity:.86;">No notes found.</div>`);
                return;
            }
            // Build note cards
            for(const note of notes) {
                const $card = $(`
                    <div class="note-card glass" tabindex="0" data-id="${note.id}">
                        <div class="note-color-bar" style="background:${note.color};"></div>
                        <div class="note-title edit-btn-x">${escapeHtml(note.title)}</div>
                        <div class="note-desc edit-btn-x">${escapeHtml(note.description||'')}</div>
                        <div class="note-footer">
                          <span class="note-date">${formatDate(note.created_at||note.updated_at)}</span>
                          <div class="note-actions">
                            <button class="note-action-btn color-btn" title="Change Color"><i class="fas fa-palette"></i></button>
                            <button class="note-action-btn delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
                            <button class="note-action-btn pin-btn${note.is_pinned?' pinned':''}" title="Pin"><i class="fas fa-thumbtack"  style="vertical-align: middle;"></i></button>
                          </div>
                        </div>
                    </div>
                `);
                // Quick hover - highlight border
                $card.hover(
                    ()=>$card.css('box-shadow','0 14px 30px #bddafc88, 0 2px 9px #d9eafd').css('opacity',1.00),
                    ()=>$card.css('box-shadow','0 6px 24px 0 #e1eafc, 0 2px 5px 1px #e4eefd').css('opacity',0.94)
                );
                $grid.append($card);
            }
        }

        function escapeHtml(str) {
            return String(str).replace(/[<>&"']/g, c=>({ '<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;',"'":'&#39;' })[c]);
        }

        // --- Modal Management (Bootstrap) --- //
        function showModal($m) {
            if ($m.hasClass('modal')) {
                var modal = bootstrap.Modal.getOrCreateInstance($m[0]);
                modal.show();
            } else {
                $m.removeClass('hidden');
            }
        }
        function hideModal($m) {
            if ($m.hasClass('modal')) {
                var modal = bootstrap.Modal.getOrCreateInstance($m[0]);
                modal.hide();
            } else {
                $m.addClass('hidden');
            }
        }
        function openNoteModal(note) {
            $('#note-form')[0].reset();
            $('#note-id').val(note && note.id || '');
            $('#note-title').val(note&&note.title||'');
            $('#note-desc').val(note&&note.description||'');
            $('#note-pin').prop('checked', note&&note.is_pinned||false);
            $('#modal-title').text(note&&note.id ? 'Edit Note' : 'New Note');
            showModal($('#note-modal'));
        }
        function closeNoteModal() { hideModal($('#note-modal')); }

        // --- Event Delegation --- //
        // Add Note (show modal)
        $('#btn-add-note').on('click', ()=>openNoteModal(null));
        // Modal Cancel
        $('#btn-cancel').on('click', closeNoteModal);
        $('#btn-delete-cancel').on('click', ()=>hideModal($('#delete-modal')));
        // Save Note (submit form)
        $('#note-form').on('submit', function(e){
            e.preventDefault();
            const id = $('#note-id').val();
            // Color is not set in modal anymore; keep previous color or default
            let note = notes.find(n=>n.id==id);
            const data = {
                title: $('#note-title').val().trim(),
                description: $('#note-desc').val(),
                color: note ? note.color : COLORS[0],
                is_pinned: $('#note-pin').is(':checked') ? 1 : 0
            };
            let done = ()=>{
                closeNoteModal();
                loadNotes(getSearchParams());
            };
            if (id) updateNote(id, data, done);
            else createNote(data, done);
        });
        // Color swatch click: open color picker modal
        let colorChangeNoteId = null;
        $('#notes-grid').on('click', '.color-btn', function(e){
            e.stopPropagation();
            let $card = $(this).closest('.note-card');
            let id = $card.data('id');
            let note = notes.find(n=>n.id==id);
            if (!note) return;
            colorChangeNoteId = id;
            // Render swatches in modal
            const $swatches = $('#color-modal-swatches');
            $swatches.empty();
            COLORS.forEach(color => {
                const $sw = $('<div>').css({
                    width: '32px', height: '32px', borderRadius: '50%', background: color, border: '2px solid #ccc', cursor: 'pointer', display: 'inline-block', margin: '2px', boxShadow: color===note.color?'0 0 0 3px #007bff':'none'
                });
                $sw.attr('tabindex',0).attr('title', color);
                $sw.on('click keydown', function(e){
                    if (e.type==='click'||e.key==='Enter') {
                        $('#color-modal').modal('hide');
                        updateNote(colorChangeNoteId, {...note, color: color}, ()=>loadNotes(getSearchParams()));
                    }
                });
                $swatches.append($sw);
            });
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('color-modal'));
            modal.show();
        });

        // Edit/Delete/Pin - delegate to .notes-grid
        $('#notes-grid').on('click', '.edit-btn-x', function(e){
            e.stopPropagation();
            let id = $(this).closest('.note-card').data('id');
            let note = notes.find(n=>n.id==id);
            openNoteModal(note);
        });
        $('#notes-grid').on('click', '.delete-btn', function(e){
            e.stopPropagation();
            let id = $(this).closest('.note-card').data('id');
            let note = notes.find(n=>n.id==id);
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this note?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                focusCancel: true,
                customClass: {
                    popup: 'glass-swal',
                    title: 'glass-swal-title',
                    htmlContainer: 'glass-swal-text',
                    confirmButton: 'btn-encodex-delete',
                    cancelButton: 'btn-encodex-clear',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteNote(id, ()=>{
                        loadNotes(getSearchParams());
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The note has been deleted.',
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'glass-swal',
                                title: 'glass-swal-title',
                                htmlContainer: 'glass-swal-text',
                                confirmButton: 'btn-encodex-active',
                            }
                        });
                    });
                }
            });
        });
        // Pin/Unpin
        $('#notes-grid').on('click', '.pin-btn', function(e){
            e.stopPropagation();
            let $card = $(this).closest('.note-card');
            let id = $card.data('id');
            let note = notes.find(n=>n.id==id);
            if (!note) return;
            const newPin = note.is_pinned ? 0 : 1;
            updateNote(id, {...note, is_pinned: newPin }, ()=>loadNotes(getSearchParams()));
        });

        // --- Search and Filter --- //
        renderColorFilter();
        $('#color-filter').on('change', function(){
            loadNotes(getSearchParams());
        });
        $('#search-input').on('input', debounce(function(){
            loadNotes(getSearchParams());
        },180));

        function getSearchParams() {
            return {
                search: $('#search-input').val().trim(),
                color: $('#color-filter').val()
            };
        }

        // No color picker to render in modal anymore

        // --- Startup: Initial Load --- //
        loadNotes({});

        // Allow pressing Escape to close modals (Bootstrap handles Escape for Bootstrap modals)
        // If you want to close custom modals, keep this:
        $(document).on('keydown', function(e){
            if (e.key==='Escape') { hideModal($('#delete-modal')); }
        });

        // --- Utility: RGB to HEX --- //
        function rgb2hex(rgb) {
            let m = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            return m ? "#"+((1<<24)+(+m[1]<<16)+(+m[2]<<8)+(+m[3])).toString(16).slice(1) : rgb;
        }
    });
</script>

