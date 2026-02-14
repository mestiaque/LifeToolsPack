<div class="new-year-container">
    <!-- মূল স্থির উইজেট -->
    <div class="new-year-widget" id="mainWidget">
        <div class="year-static" id="rainbowYear">2026</div>
        <div class="victory-text">HAPPY NEW YEAR</div>
    </div>

    <style>
        .new-year-widget {
            position: fixed; bottom: 20px; right: 20px; z-index: 100000;
            display: flex; flex-direction: column; align-items: center;
            cursor: pointer; user-select: none; transition: transform 0.2s;
        }
        .new-year-widget:hover { transform: scale(1.05); }

        /* ২০২৬ টেক্সটের জন্য ৭ রঙের রেইনবো অ্যানিমেশন */
        .year-static, .year-rocket-clone {
            font-family: 'Georgia', 'Cursive', serif;
            font-size: 60px;
            font-weight: 900;
            line-height: 1;
            transition: color 0.5s ease;
            filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.3));
        }

        /* Happy New Year টেক্সটের জন্য অ্যানিমেটেড গ্রেডিয়েন্ট */
        .victory-text {
            margin-top: 5px; padding: 5px 20px; border-radius: 50px;
            font-size: 14px; font-weight: bold; letter-spacing: 2px;
            color: white; border: none;
            background: linear-gradient(270deg, #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #8b00ff);
            background-size: 400% 400%;
            animation: moveGradient 5s linear infinite;
        }

        @keyframes moveGradient {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .year-rocket-clone { position: fixed; z-index: 100001; pointer-events: none; }

        .confetti-piece {
            position: fixed; z-index: 99999; pointer-events: none;
            will-change: transform;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const widget = document.getElementById('mainWidget');
            const yearText = document.getElementById('rainbowYear');
            let isLaunching = false;
            // ৭টি রেইনবো কালার
            const colors = ['#FF0000', '#FF7F00', '#FFFF00', '#00FF00', '#0000FF', '#4B0082', '#9400D3'];
            let colorIndex = 0;

            // ২০২৬ টেক্সটের কালার পরিবর্তন
            setInterval(() => {
                yearText.style.color = colors[colorIndex];
                colorIndex = (colorIndex + 1) % colors.length;
            }, 500);

            function launchRocket() {

                if (isLaunching) return; // চলাকালীন click ignore
                isLaunching = true;

                const rect = widget.getBoundingClientRect();

                const clone = document.createElement('div');
                clone.className = 'year-rocket-clone';
                clone.innerText = '2026';
                clone.style.color = yearText.style.color;
                clone.style.left = rect.left + 'px';
                clone.style.top = rect.top + 'px';
                document.body.appendChild(clone);

                const targetX = window.innerWidth / 2 - (rect.width / 2);
                const targetY = 100;

                // ওড়ার সময় ঘুরতে থাকবে (Rotate 720deg)
                const launch = clone.animate([
                    { transform: 'translate(0, 0) scale(1) rotate(0deg)', opacity: 1 },
                    { transform: `translate(${targetX - rect.left}px, ${-(rect.top - targetY)}px) scale(0.4) rotate(720deg)`, opacity: 1 }
                ], {
                    duration: 1000,
                    easing: 'cubic-bezier(0.1, 0.5, 0.3, 1)'
                });

                launch.onfinish = () => {
                    explode(window.innerWidth / 2, targetY, () => {
                        isLaunching = false; // confetti শেষ হলে reset
                    });
                    clone.remove();
                };

                setTimeout(() => {
                    isLaunching = false;
                }, 1500); // 1.5 সেকেন্ড পরে আবার click চালু
            }

            function explode(originX, originY) {
                const count = getConfettiCount(); // কাগজের টুকরা বাড়ানো হয়েছে
                for (let i = 0; i < count; i++) {
                    const el = document.createElement('div');
                    el.className = 'confetti-piece';

                    const size = Math.random() * 9 + 5;
                    el.style.width = size + 'px';
                    el.style.height = (size * 0.6) + 'px';
                    el.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];

                    el.style.left = '0px';
                    el.style.top = '0px';
                    el.style.transform = `translate(${originX}px, ${originY}px)`;
                    document.body.appendChild(el);

                    const angle = Math.random() * Math.PI * 2;
                    const speed = Math.random() * 20 + 5; // বিস্ফোরণের শক্তি বাড়ানো হয়েছে
                    let vx = Math.cos(angle) * speed;
                    let vy = Math.sin(angle) * speed;
                    let posX = originX;
                    let posY = originY;
                    let rotationX = 0;
                    let rotationY = 0;
                    const rotSpeedX = Math.random() * 15;
                    const rotSpeedY = Math.random() * 15;

                    function update() {
                        vx *= 0.97;
                        vy += 0.18; // গ্র্যাভিটি
                        posX += vx;
                        posY += vy;
                        rotationX += rotSpeedX;
                        rotationY += rotSpeedY;

                        el.style.transform = `translate(${posX}px, ${posY}px) rotateX(${rotationX}deg) rotateY(${rotationY}deg)`;

                        if (posY < window.innerHeight + 50) {
                            requestAnimationFrame(update);
                        } else {
                            el.remove();
                        }
                    }
                    requestAnimationFrame(update);
                }
            }

            function getConfettiCount() {
                const width = window.innerWidth;

                if (width < 600) return 110;     // ছোট mobile screen
                if (width < 1024) return 300;   // tablet / medium screen
                return 300;                     // বড় desktop screen
            }

            widget.addEventListener('click', launchRocket);
            // ২ সেকেন্ড পর প্রথমবার অটো-লঞ্চ
            setTimeout(launchRocket, 2000);
        });
    </script>
</div>
