<style>



    .app-title {
        font-size: 2.6rem;
        font-weight: 700;
        margin-bottom: 1.4rem;
        color: #396780;
        text-align: center;
        letter-spacing: 1px;
        text-shadow: 0 2px 8px #e4ebf2;
    }

    .controls {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.2rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    #search-input {
        border: none;
        background: rgba(255,255,255,0.7);
        border-radius: 2rem;
        padding: 0.7em 1.3em;
        font-size: 1rem;
        box-shadow: 0 1.5px 6px 1px #e8eff6;
        transition: box-shadow .18s;
        width: 230px;
        outline: none;
    }
    #search-input:focus { box-shadow: 0 4px 14px 0 #c8e8fd; }
    #color-filter {
        border: none;
        padding: 0.7em 1.1em;
        border-radius: 1.3em;
        background: rgba(248,250,255,0.65);
        box-shadow: 0 1px 6px #d8e6f9;
        font-size: 1rem;
        color: #396780;
        outline: none
    }

    .notes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        grid-auto-rows: 8px;
        grid-auto-flow: dense;
        gap: 1.3rem;
        align-items: start;
    }

    .notes-grid.is-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: clamp(240px, 52vh, 560px);
        padding: 1rem;
    }

    .notes-empty-state {
        text-align: center;
        padding: 1rem 1.4rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.35);
        box-shadow: 0 10px 26px rgba(153, 184, 218, 0.18);
        backdrop-filter: blur(4px);
        color: #3d5d7f;
    }

    .notes-empty-state .empty-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #35597f;
        margin-bottom: 0.3rem;
    }

    .notes-empty-state .empty-subtitle {
        font-size: 0.92rem;
        color: #6d88a7;
    }

    @media (max-width: 900px) {
        .notes-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 600px) {
        .notes-grid {
            grid-template-columns: 1fr;
        }
    }

    .note-card {
        display: block;
        width: 100%;
        margin-bottom: 0;
        /* Remove min-height if you want cards to shrink to content */
        min-height: 0;
    }

    /* Note Card */
    .note-card {
        background: rgba(255,255,255,0.47);
        border-radius: 18px;
        box-shadow: 0 6px 24px 0 #e1eafc, 0 2px 5px 1px #e4eefd;
        padding: 1.1em 1.1em 0.5em 1.15em;
        min-height: 130px;
        position: relative;
        overflow: hidden;
        transition: transform .18s cubic-bezier(.4,2.3,.3,1), box-shadow .19s, background .25s;
        cursor: pointer;
        opacity: 0.94;
    }
    .note-card:hover, .note-card:focus-visible {
        transform: scale(1.025);
        box-shadow: 0 14px 30px #bddafc66, 0 2px 9px 0 #d9eafd;
        z-index: 1;
        opacity: 1;
    }

    .note-color-bar {
        width: 36px; height: 5px;
        border-radius: 8px 8px 0 0;
        position: absolute; left: 14px; top: 11px;
        box-shadow: 0 2px 5px #cad9ee90;
        opacity: 0.85;
    }
    .note-title {
        font-size: 1.18rem;
        font-weight: 600;
        margin-top: 16px;
        margin-bottom: 5px;
        color: #2c415c;
        letter-spacing: 0.17px;
    }
    .note-desc {
        color: #3b678d;
        font-size: 1rem;
        margin-bottom: 1rem;
        white-space: pre-line;
        min-height: 32px;
    }
    .note-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.96em;
        color: #82a3d2;
        margin-top: 0.7em;
        margin-bottom: 3px;
        gap: 8px;
    }
    .note-date {
        font-size: 0.89em;
        color: #a3b8da;
    }
    .note-actions {
        display: flex;
        gap: 0.2em;
    }
    .note-action-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.25em;
        padding: 0.18em 0.35em;
        color: #5686bf;
        border-radius: 50%;
        transition: background .16s, color .16s;
    }
    .note-action-btn:hover {
        background: #eaf4ff;
        color: #3161a1;
    }

    .pin-btn.pinned {
        color: #ffb61e;
    }
    .pin-btn:not(.pinned):hover {
        color: #a4aab7;
    }

    /* Floating Action Button */
    .fab {
        position: fixed;
        right: 30px; bottom: 35px;
        width: 58px; height: 58px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, rgba(207,236,254,0.45), rgba(250,255,255,0.77));
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px 0 #96cbf883;
        font-size: 2.45rem;
        color: #2978ad;
        cursor: pointer;
        z-index: 5;
        transition: background .2s, box-shadow .18s;
        outline: none;
    }
    .fab:hover { box-shadow: 0 10px 28px #bdf0ffcc; color: #0f3e65; }

    /* Modal styles */
    /* .modal {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        inset: 0;
        background: rgba(47,102,164,.13);
        z-index: 100;
        animation: fade-in 0.23s cubic-bezier(.4,2.3,.3,1);
    }
    .modal.hidden { display: none; }
    .modal-content {
        padding: 2rem 1.5rem 1.2rem;
        border-radius: 22px;
        min-width: 300px;
        max-width: 400px;
        background: rgba(255,255,255,0.85);
        box-shadow: 0 8px 42px 0 rgba(189,227,255,0.65), 0 2px 8px 0 #e4f6f8;
        animation: pop-modal .23s cubic-bezier(.47,1.68,.53,1);
    } */

    /* Glass effect utility */
    .glass {
        background: rgba(255,255,255,0.62);
        backdrop-filter: blur(8px) saturate(1.4);
    }


    @keyframes pop-modal {
        0% { transform: scale(0.87); opacity: 0.0;}
        100%{ transform: scale(1); opacity: 1;}
    }
    @keyframes fade-in {
        0% { opacity: 0;}
        100%{ opacity: 1;}
    }

    /* Modal form */
    #note-form .form-group {
        margin-bottom: 1em;
    }
    #note-title, #note-desc {
        width: 100%;
        padding: 0.7em 1em;
        /* border: none; */
        /* border-radius: 13px; */
        outline: none;
        font-size: 1.07em;
        background: rgba(255,255,255,0.79);
        box-shadow: 0 1.5px 6px #ddeeff23;
        margin-top: 0.2em;
        color: #000;
    }
    #note-desc { min-height: 60px; resize: vertical;}
    .color-picker-group { display: flex; align-items: center; gap: 1em;}
    #color-picker { display: flex; gap: 0.3em;}
    .color-swatch {
        width: 26px; height: 26px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px #d6eaffdd;
        cursor: pointer;
        transition: border .17s, box-shadow .18s;
        opacity: 0.89;
        outline: 2.5px solid transparent;
    }
    .color-swatch.selected, .color-swatch:hover {
        border: 2.5px solid #8cbae8;
        outline: 2.5px solid #54a1ea44;
        opacity: 1.00;
    }
    .pin-group label { font-size: 1.07em; color: #6597c6;}
    .modal-actions {
        display: flex;
        gap: 1.2em;
        justify-content: flex-end;
        margin-top: 13px;
    }
    .modal-actions button {
        font-size: 1.03em;
        border: none;
        border-radius: 9px;
        padding: 0.49em 1.3em;
        margin-top: 4px;
        cursor: pointer;
        background: linear-gradient(128deg, #dceffd 30%, #f4faff 100%);
        color: #4681b2;
        box-shadow: 0 2px 8px #cadeff36;
        transition: background .15s, color .16s;
    }
    .modal-actions .danger {
        background: linear-gradient(128deg, #ffe3e7 40%, #fff2f6 100%);
        color: #ca234b;
    }
    .modal-actions button:hover { background: #eaf6ff; }
    .modal-actions .danger:hover { background: #ffe2eb; color: #bf143b;}

    ::-webkit-scrollbar-thumb {
        background: #dfecfb;
        border-radius: 26px;
    }

    /* Responsive small screens */
    @media (max-width: 580px) {
        /* .container { margin: 0.8em; padding: 0.4em;} */
        .app-title { font-size: 1.6rem;}
        .modal-content { max-width: 98vw; }
    }

    .glass-swal {
        backdrop-filter: blur(12px) saturate(1.3);
        border-radius: 18px;
        box-shadow: 0 8px 42px 0 rgba(189,227,255,0.65), 0 2px 8px 0 #e4f6f8;
    }

    textarea {
        overflow-y: scroll;
        scrollbar-width: none; /* Firefox */
    }

    textarea::-webkit-scrollbar {
        display: none; /* Chrome, Edge, Safari */
    }

</style>
