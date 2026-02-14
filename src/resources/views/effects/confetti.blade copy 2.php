<div class="new-year-container">
    <!-- উইজেট (কামান) -->
    <div class="new-year-widget" id="cannonBtn">
        <div class="year-2026">2026</div>
        <div class="victory-text">HAPPY NEW YEAR</div>
    </div>

    <style>
        .new-year-widget {
            position: fixed; bottom: 30px; right: 30px; z-index: 99999;
            display: flex; flex-direction: column; align-items: center;
            cursor: pointer; user-select: none; transition: transform 0.2s;
        }
        .new-year-widget:hover { transform: scale(1.05); }
        .new-year-widget:active { transform: scale(0.9); }

        .year-2026 {
            font-family: 'Arial Black', sans-serif; font-size: 60px; font-weight: 900;
            animation: rainbow 5s infinite, bounce 2s infinite alternate;
            text-shadow: 2px 4px 10px rgba(0,0,0,0.2);
        }

        .victory-text {
            margin-top: -5px; background: #000; color: #fff; padding: 5px 20px;
            border-radius: 50px; font-size: 14px; font-weight: bold; letter-spacing: 2px;
        }

        .confetti-piece {
            position: fixed; z-index: 99998; pointer-events: none;
            will-change: transform, top, left; /* পারফরম্যান্স ভালো রাখার জন্য */
        }

        @keyframes rainbow {
            0% { color: #4285F4; } 25% { color: #EA4335; }
            50% { color: #FBBC05; } 75% { color: #34A853; } 100% { color: #4285F4; }
        }
        @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-10px); } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('cannonBtn');
            const colors = ['#4285F4', '#EA4335', '#FBBC05', '#34A853', '#FF69B4', '#00FFFF'];

            function fire() {
                const rect = btn.getBoundingClientRect();
                const startX = rect.left + rect.width / 2;
                const startY = rect.top;

                const pieceCount = 150; // কাগজের সংখ্যা

                for (let i = 0; i < pieceCount; i++) {
                    createPiece(startX, startY);
                }
            }

            function createPiece(startX, startY) {
                const el = document.createElement('div');
                el.className = 'confetti-piece';

                const size = Math.random() * 10 + 6;
                const color = colors[Math.floor(Math.random() * colors.length)];

                el.style.width = size + 'px';
                el.style.height = (size * 0.7) + 'px';
                el.style.backgroundColor = color;
                el.style.left = startX + 'px';
                el.style.top = startY + 'px';
                el.style.opacity = '1';

                document.body.appendChild(el);

                // ১. লঞ্চ ফেজ (প্যারালাক্স মুভমেন্ট)
                // টেক্সট থেকে স্ক্রিনের মাঝখানে এবং উপরে যাওয়ার লক্ষ্য
                const targetX = Math.random() * window.innerWidth; // পুরো স্ক্রিন জুড়ে টার্গেট
                const targetY = Math.random() * (window.innerHeight * 0.3); // উপরের ৩০% এরিয়াতে

                const launchTime = Math.random() * 600 + 800; // ৮০০ms - ১৪০০ms

                const launchAnim = el.animate([
                    { top: startY + 'px', left: startX + 'px', transform: 'rotate(0deg) scale(0)' },
                    { top: targetY + 'px', left: targetX + 'px', transform: `rotate(${Math.random() * 360}deg) scale(1)` }
                ], {
                    duration: launchTime,
                    easing: 'cubic-bezier(0.1, 0.5, 0.2, 1)' // শুরুতে রকেটের মতো ফাস্ট
                });

                launchAnim.onfinish = () => {
                    // ২. ফল ফেজ (পুরো ডিসপ্লে জুড়ে বৃষ্টির মতো পড়া)
                    const fallTime = Math.random() * 4000 + 4000; // ৪ - ৮ সেকেন্ড
                    const drift = (Math.random() - 0.5) * 400; // ডানে বামে দোলানো

                    el.animate([
                        { top: targetY + 'px', left: targetX + 'px', transform: el.style.transform },
                        {
                            top: window.innerHeight + 20 + 'px',
                            left: (targetX + drift) + 'px',
                            transform: `rotateX(${Math.random() * 2000}deg) rotateY(${Math.random() * 2000}deg) rotateZ(${Math.random() * 1000}deg)`
                        }
                    ], {
                        duration: fallTime,
                        easing: 'linear'
                    }).onfinish = () => el.remove();
                };
            }

            btn.addEventListener('click', fire);

            // অটো স্টার্ট
            setTimeout(fire, 1000);
        });
    </script>
</div>
