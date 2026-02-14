<!-- ১. স্টাইল পার্ট -->
<style>
    #firework-container {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        z-index: 99999; pointer-events: none; overflow: hidden;
    }
    .new-year-widget {
        position: fixed; bottom: 20px; right: 30px;
        z-index: 100000; display: flex; flex-direction: column; align-items: center;
        cursor: pointer; pointer-events: auto;
    }

    /* ফ্যান্সি ২০২৬ টেক্সট স্টাইল */
    .year-2026 {
        font-family: 'Georgia', 'Cursive', serif;
        font-size: 55px;
        font-weight: 900;
        line-height: 1;
        user-select: none;
        transition: transform 0.3s;
        /* রেইনবো অ্যানিমেশন */
        animation: rainbowCycle 7s infinite, bounceYear 2s infinite alternate;
        text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
    }

    .year-2026:hover { transform: scale(1.2); }

    /* প্রতি ১ সেকেন্ড পর পর কালার চেঞ্জ করার জন্য রেইনবো অ্যানিমেশন */
    @keyframes rainbowCycle {
        0%, 100% { color: #ff0000; } /* Red */
        14% { color: #ff7f00; }      /* Orange */
        28% { color: #ffff00; }      /* Yellow */
        42% { color: #00ff00; }      /* Green */
        56% { color: #0000ff; }      /* Blue */
        70% { color: #4b0082; }      /* Indigo */
        84% { color: #8b00ff; }      /* Violet */
    }

    @keyframes bounceYear {
        from { transform: translateY(0); }
        to { transform: translateY(-10px); }
    }

    .victory-text {
        margin-top: 1px; background: linear-gradient(to right, var(--primary-color), #0078d4, var(--secondary-color)) !important; color: white; padding: 4px 15px;
        border-radius: 20px; font-size: 14px; font-weight: bold; white-space: nowrap;
        letter-spacing: 2px;
    }

    .firework-trail {
        position: absolute; width: 4px; height: 4px; border-radius: 50%;
        box-shadow: 0 0 10px white; animation: launchTravel linear forwards;
    }
    .victory-particle {
        position: absolute; width: 6px; height: 6px; border-radius: 50%;
        animation: particleFly 2.5s ease-out forwards;
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
<div class="new-year-widget" onclick="manualLaunch()">
    <div class="year-2026">2026</div>
    <div class="victory-text">HAPPY NEW YEAR</div>
</div>

<!-- ৩. জাভাস্ক্রিপ্ট পার্ট -->
<script>
    (function() {
        const container = document.getElementById('firework-container');
        // আতশবাজির জন্য রেইনবো কালার প্যালেট
        const colors = ['#e81416', '#ffa500', '#faeb36', '#79c314', '#487de7', '#4b369d', '#70369d'];

        function launchFirework() {
            const startX = window.innerWidth - 80;
            const startY = window.innerHeight - 80;

            const ex = Math.random() * window.innerWidth;
            const ey = Math.random() * (window.innerHeight * 0.5);

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
            const particles = 60;
            for (let i = 0; i < particles; i++) {
                const p = document.createElement('div');
                p.className = 'victory-particle';
                p.style.backgroundColor = color;
                p.style.left = x + 'px';
                p.style.top = y + 'px';
                p.style.boxShadow = `0 0 10px ${color}`;

                const angle = Math.random() * Math.PI * 2;
                const dist = 50 + Math.random() * 150;
                p.style.setProperty('--tx', (Math.cos(angle) * dist) + 'px');
                p.style.setProperty('--ty', (Math.sin(angle) * dist + 80) + 'px');

                container.appendChild(p);
                setTimeout(() => p.remove(), 2500);
            }
        }

        // অটোমেটিক লঞ্চ (প্রতি ৭০০ মিলিসেকেন্ডে)
        setInterval(launchFirework, 700);

        // ২০২৬ এ ক্লিক করলে বিশেষ ৩টি বিস্ফোরণ
        window.manualLaunch = function() {
            for(let i=0; i<4; i++) setTimeout(launchFirework, i * 250);
        };
    })();
</script>
