<div class="new-year-container">
    <!-- মূল স্থির উইজেট -->
    <div class="new-year-widget" id="mainWidget">
        <div class="year-static">2026</div>
        <div class="victory-text">HAPPY NEW YEAR</div>
    </div>

    <style>
        .new-year-widget {
            position: fixed; bottom: 30px; right: 30px; z-index: 100000;
            display: flex; flex-direction: column; align-items: center;
            cursor: pointer; user-select: none; transition: transform 0.2s;
        }
        .new-year-widget:hover { transform: scale(1.05); }

        .year-static, .year-rocket-clone {
            font-family: 'Arial Black', sans-serif; font-size: 60px; font-weight: 900;
            line-height: 1; background: linear-gradient(to bottom, #ffeb3b, #f44336);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.3));
        }

        .year-rocket-clone { position: fixed; z-index: 100001; pointer-events: none; }

        .victory-text {
            background: #000; color: #fff; padding: 4px 15px; border-radius: 50px;
            font-size: 12px; font-weight: bold; letter-spacing: 2px; margin-top: 5px;
        }

        .confetti-piece {
            position: fixed; z-index: 99999; pointer-events: none;
            will-change: transform;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const widget = document.getElementById('mainWidget');
            const colors = ['#4285F4', '#EA4335', '#FBBC05', '#34A853', '#FF69B4', '#00FFFF'];

            function launchRocket() {
                const rect = widget.getBoundingClientRect();

                // ১. ক্লোন তৈরি
                const clone = document.createElement('div');
                clone.className = 'year-rocket-clone';
                clone.innerText = '2026';
                clone.style.left = rect.left + 'px';
                clone.style.top = rect.top + 'px';
                document.body.appendChild(clone);

                const targetX = window.innerWidth / 2 - (rect.width / 2);
                const targetY = 100;

                // ২. উপরে যাওয়ার অ্যানিমেশন
                const launch = clone.animate([
                    { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                    { transform: `translate(${targetX - rect.left}px, ${-(rect.top - targetY)}px) scale(0.5)`, opacity: 1 }
                ], {
                    duration: 800,
                    easing: 'ease-out'
                });

                launch.onfinish = () => {
                    explode(window.innerWidth / 2, targetY);
                    clone.remove();
                };
            }

            function explode(originX, originY) {
                const count = 100;
                for (let i = 0; i < count; i++) {
                    const el = document.createElement('div');
                    el.className = 'confetti-piece';

                    const size = Math.random() * 8 + 6;
                    el.style.width = size + 'px';
                    el.style.height = (size * 0.6) + 'px';
                    el.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];

                    // শুরুতে সেন্টারে থাকবে
                    el.style.left = '0px';
                    el.style.top = '0px';
                    el.style.transform = `translate(${originX}px, ${originY}px)`;
                    document.body.appendChild(el);

                    // ভেলোসিটি এবং ফিজিক্স ডাটা
                    const angle = Math.random() * Math.PI * 2;
                    const speed = Math.random() * 15 + 5;
                    let vx = Math.cos(angle) * speed;
                    let vy = Math.sin(angle) * speed;
                    let posX = originX;
                    let posY = originY;
                    let rotationX = 0;
                    let rotationY = 0;
                    const rotSpeedX = Math.random() * 10;
                    const rotSpeedY = Math.random() * 10;

                    // ৩. রিকোয়েস্ট অ্যানিমেশন ফ্রেম (এটি গ্লিচ মুক্ত স্মুথ মুভমেন্ট দেয়)
                    function update() {
                        vx *= 0.98; // বাতাস প্রতিরোধ
                        vy += 0.15; // গ্র্যাভিটি (নিচে টানা)
                        posX += vx;
                        posY += vy;
                        rotationX += rotSpeedX;
                        rotationY += rotSpeedY;

                        el.style.transform = `translate(${posX}px, ${posY}px) rotateX(${rotationX}deg) rotateY(${rotationY}deg)`;

                        if (posY < window.innerHeight) {
                            requestAnimationFrame(update);
                        } else {
                            el.remove(); // স্ক্রিনের নিচে চলে গেলে রিমুভ
                        }
                    }

                    requestAnimationFrame(update);
                }
            }

            widget.addEventListener('click', launchRocket);
            // ২ সেকেন্ড পর অটো স্টার্ট
            setTimeout(launchRocket, 2000);
        });
    </script>
</div>
