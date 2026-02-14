<style>
    .heart-particle {
        position: fixed; bottom: -20px; color: #ff4d6d;
        pointer-events: none; z-index: 9999; animation: heartFloat linear forwards;
    }
    @keyframes heartFloat {
        0% { transform: translateY(0) scale(0.5); opacity: 1; }
        100% { transform: translateY(-110vh) scale(1.5); opacity: 0; }
    }
</style>
<script>
    (function() {
        const hearts = ['❤️', '💖', '💗', '💕'];
        setInterval(() => {
            const el = document.createElement('div');
            el.className = 'heart-particle';
            el.innerText = hearts[Math.floor(Math.random() * hearts.length)];
            el.style.left = Math.random() * 100 + 'vw';
            el.style.fontSize = (Math.random() * 10 + 10) + 'px';
            const duration = Math.random() * 5 + 5;
            el.style.animationDuration = duration + 's';
            document.body.appendChild(el);
            setTimeout(() => el.remove(), duration * 1000);
        }, 500);
    })();
</script>
