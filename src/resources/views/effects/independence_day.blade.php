<!-- ১. স্টাইল পার্ট -->
<style>
    #independence-container {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        z-index: 99999; pointer-events: none; overflow: hidden;
    }
    .independence-widget {
        position: fixed; bottom: 20px; right: 20px;
        z-index: 100000; display: flex; flex-direction: column; align-items: center;
    }
    .bd-flag-circle {
        width: 70px; height: 70px; background: #006a4e; border-radius: 50%;
        position: relative; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        cursor: pointer; pointer-events: auto; border: 3px solid #fff;
        animation: pulse 2s infinite;
    }
    .bd-flag-circle::after {
        content: ''; position: absolute; width: 35px; height: 35px;
        background: #f42a41; border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%);
    }
    .ind-text {
        margin-top: 10px; background: #f42a41; color: white; padding: 5px 15px;
        border-radius: 20px; font-size: 13px; font-weight: bold; white-space: nowrap; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .firework-trail-ind {
        position: absolute; width: 4px; height: 4px; border-radius: 50%;
        background: #fff; box-shadow: 0 0 10px #f42a41; animation: launch linear forwards;
    }
    .particle-ind {
        position: absolute; width: 5px; height: 5px; border-radius: 50%;
        animation: explode 2s ease-out forwards;
    }
    @keyframes pulse {
        0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); }
    }
    @keyframes launch {
        to { transform: translate(var(--dx), var(--dy)); opacity: 0; }
    }
    @keyframes explode {
        0% { transform: translate(0,0); opacity: 1; }
        100% { transform: translate(var(--tx), var(--ty)); opacity: 0; }
    }
</style>

<!-- ২. HTML পার্ট -->
<div id="independence-container"></div>
<div class="independence-widget">
    <div class="bd-flag-circle" onclick="launchIndependence()" title="Click for Celebration"></div>
    <div class="ind-text">স্বাধীনতা দিবস - ২৬শে মার্চ</div>
</div>

<!-- ৩. জাভাস্ক্রিপ্ট পার্ট -->
<script>
    (function() {
        const container = document.getElementById('independence-container');
        const colors = ['#f42a41', '#006a4e', '#FFD700', '#ffffff'];

        function createFirework() {
            const startX = window.innerWidth - 60;
            const startY = window.innerHeight - 80;
            const ex = Math.random() * window.innerWidth;
            const ey = Math.random() * (window.innerHeight * 0.4);

            const trail = document.createElement('div');
            const color = colors[Math.floor(Math.random() * colors.length)];

            trail.className = 'firework-trail-ind';
            trail.style.left = startX + 'px';
            trail.style.top = startY + 'px';

            const dx = ex - startX;
            const dy = ey - startY;
            trail.style.setProperty('--dx', dx + 'px');
            trail.style.setProperty('--dy', dy + 'px');

            container.appendChild(trail);

            setTimeout(() => {
                for (let i = 0; i < 40; i++) {
                    const p = document.createElement('div');
                    p.className = 'particle-ind';
                    p.style.backgroundColor = color;
                    p.style.left = ex + 'px';
                    p.style.top = ey + 'px';
                    p.style.boxShadow = `0 0 8px ${color}`;

                    const angle = Math.random() * Math.PI * 2;
                    const dist = 50 + Math.random() * 100;
                    p.style.setProperty('--tx', (Math.cos(angle) * dist) + 'px');
                    p.style.setProperty('--ty', (Math.sin(angle) * dist + 40) + 'px');

                    container.appendChild(p);
                    setTimeout(() => p.remove(), 2000);
                }
                trail.remove();
            }, 1000);
        }

        // অটোমেটিক লঞ্চ
        setInterval(createFirework, 4000);

        // ক্লিক করলে ৩টি একসাথে
        window.launchIndependence = function() {
            for(let i=0; i<3; i++) setTimeout(createFirework, i * 300);
        };
    })();
</script>
