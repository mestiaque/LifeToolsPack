<style>
    .baishakh-particle {
        position: fixed;
        top: -50px;
        pointer-events: none;
        z-index: 9999;
        /* Using linear fall animation as the items are heavy/solid */
        animation: baishakhFall linear infinite;
        opacity: 0;
    }

    @keyframes baishakhFall {
        0% { transform: translateY(0) rotate(0deg) scale(0.5); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(110vh) rotate(720deg) scale(1.2); opacity: 0; }
    }
</style>

<script>
    (function() {
        // Updated icons: Drum, Mask, Fish, Sun
        const icons = ['🪘', '🎭', '🐠', '🐟', '☀️', '🎨'];

        function createBaishakhParticle() {
            const el = document.createElement('div');
            el.className = 'baishakh-particle';

            // Random icon selection
            el.innerText = icons[Math.floor(Math.random() * icons.length)];

            // Random position, size, and duration
            const leftPos = Math.random() * 100;
            const fontSize = Math.random() * 20 + 25; // 25px to 45px (larger icons look better)
            const duration = Math.random() * 4 + 6; // 6 to 10 seconds (slower fall)

            el.style.left = leftPos + 'vw';
            el.style.fontSize = fontSize + 'px';
            el.style.animationDuration = duration + 's';

            document.body.appendChild(el);

            // Remove element after animation ends
            setTimeout(() => {
                el.remove();
            }, duration * 1000);
        }

        // Create a new particle every 600ms
        setInterval(createBaishakhParticle, 600);
    })();
</script>
