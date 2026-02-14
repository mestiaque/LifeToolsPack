<!-- ১. স্টাইল পার্ট -->
<style>
    #firework-container {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        z-index: 99999; pointer-events: none; overflow: hidden;
    }
    .victory-day-widget {
        position: fixed; bottom: 20px; right: 20px;
        z-index: 100000; display: flex; flex-direction: column; align-items: center;
    }
    .bd-flag {
        width: 80px; height: 50px; background: #006a4e; border-radius: 4px;
        position: relative; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        cursor: pointer; pointer-events: auto; animation: flagWave 3s ease-in-out infinite alternate;
    }
    .bd-flag::before {
        content: ''; position: absolute; width: 24px; height: 24px;
        background: #f42a41; border-radius: 50%; top: 50%; left: 45%; transform: translate(-50%, -50%);
    }
    .victory-text {
        margin-top: 10px; background: #006a4e; color: white; padding: 5px 12px;
        border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .firework-trail {
        position: absolute; width: 4px; height: 4px; border-radius: 50%;
        box-shadow: 0 0 10px white; animation: launchTravel linear forwards;
    }
    .victory-particle {
        position: absolute; width: 6px; height: 6px; border-radius: 50%;
        animation: particleFly 2.5s ease-out forwards;
    }
    @keyframes flagWave {
        from { transform: rotate(-5deg); } to { transform: rotate(5deg); }
    }
    @keyframes launchTravel {
        from { opacity: 1; } to { transform: translate(var(--dx), var(--dy)); opacity: 0; }
    }
    @keyframes particleFly {
        0% { transform: translate(0,0) scale(1); opacity: 1; }
        100% { transform: translate(var(--tx), var(--ty)); opacity: 0; scale: 0.2; }
    }
</style>

<!-- ২. HTML পার্ট -->
<div id="firework-container"></div>
<div class="victory-day-widget">
    <div class="bd-flag" onclick="manualLaunch()" title="Click for Fireworks"></div>
    <div class="victory-text">বিজয়ের মাস ডিসেম্বর</div>
    <div id="launch-point" style="position: absolute; bottom: 60px; left: 50%;"></div>
</div>

<!-- ৩. জাভাস্ক্রিপ্ট পার্ট -->
<script>
    (function() {
        const container = document.getElementById('firework-container');
        const colors = ['#f42a41', '#006a4e', '#FFD700', '#FFFFFF', '#39FF14'];

        function launchFirework() {
            const startX = window.innerWidth - 60; // পতাকার পজিশন থেকে
            const startY = window.innerHeight - 80;

            const ex = Math.random() * window.innerWidth;
            const ey = Math.random() * (window.innerHeight * 0.4);

            const trail = document.createElement('div');
            const color = colors[Math.floor(Math.random() * colors.length)];

            trail.className = 'firework-trail';
            trail.style.backgroundColor = 'white';
            trail.style.left = startX + 'px';
            trail.style.top = startY + 'px';
            trail.style.setProperty('--trail-color', color);

            const dx = ex - startX;
            const dy = ey - startY;
            trail.style.setProperty('--dx', dx + 'px');
            trail.style.setProperty('--dy', dy + 'px');

            trail.style.animationDuration = '1.2s';
            container.appendChild(trail);

            setTimeout(() => {
                explode(ex, ey, color);
                trail.remove();
            }, 1200);
        }

        function explode(x, y, color) {
            for (let i = 0; i < 50; i++) {
                const p = document.createElement('div');
                p.className = 'victory-particle';
                p.style.backgroundColor = color;
                p.style.left = x + 'px';
                p.style.top = y + 'px';
                p.style.boxShadow = `0 0 8px ${color}`;

                const angle = Math.random() * Math.PI * 2;
                const dist = 40 + Math.random() * 120;
                p.style.setProperty('--tx', (Math.cos(angle) * dist) + 'px');
                p.style.setProperty('--ty', (Math.sin(angle) * dist + 60) + 'px');

                container.appendChild(p);
                setTimeout(() => p.remove(), 2500);
            }
        }

        // অটোমেটিক লঞ্চ
        setInterval(launchFirework, 500);

        // ম্যানুয়াল লঞ্চ ফাংশন (পতাকায় ক্লিক করলে)
        window.manualLaunch = function() {
            for(let i=0; i<3; i++) setTimeout(launchFirework, i * 300);
        };
    })();
</script>
