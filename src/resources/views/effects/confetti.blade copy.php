<div class="new-year-container">
    <!-- উইজেট ডিজাইন -->
    <div class="new-year-widget" id="manualBtn">
        <div class="year-2026">2026</div>
        <div class="victory-text">HAPPY NEW YEAR</div>
    </div>

    <style>
        /* ১. উইজেট এবং টেক্সট স্টাইল */
        .new-year-widget {
            position: fixed; bottom: 30px; right: 30px; z-index: 99999;
            display: flex; flex-direction: column; align-items: center;
            cursor: pointer; user-select: none; transition: transform 0.2s;
        }
        .new-year-widget:hover { transform: scale(1.1); }
        .new-year-widget:active { transform: scale(0.9); }

        .year-2026 {
            font-family: 'Georgia', serif; font-size: 60px; font-weight: 900;
            animation: rainbow 5s infinite, bounce 2s infinite alternate;
            text-shadow: 2px 4px 10px rgba(0,0,0,0.2);
        }

        .victory-text {
            margin-top: -5px; background: linear-gradient(45deg, #f44336, #2196f3, #ffeb3b, #4caf50);
            color: white; padding: 5px 20px; border-radius: 50px;
            font-size: 14px; font-weight: bold; letter-spacing: 2px;
        }

        /* ২. কনফেটি পার্টিকেল স্টাইল */
        .confetti-particle {
            position: fixed;
            width: 10px;
            height: 10px;
            z-index: 99998;
            pointer-events: none;
            top: -10px;
        }

        /* ৩. অ্যানিমেশনগুলো */
        @keyframes rainbow {
            0% { color: #f44336; } 25% { color: #2196f3; }
            50% { color: #ffeb3b; } 75% { color: #4caf50; } 100% { color: #f44336; }
        }
        @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-15px); } }

        /* পড়ন্ত কনফেটির মুভমেন্ট */
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('manualBtn');
            const colors = ['#f44336', '#2196f3', '#ffeb3b', '#4caf50', '#ff9800', '#e91e63'];

            function createConfetti() {
                const particleCount = 100; // কতগুলো কাগজ পড়বে

                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'confetti-particle';

                    // র‍্যান্ডম সাইজ এবং কালার
                    const color = colors[Math.floor(Math.random() * colors.length)];
                    const size = Math.random() * 8 + 5 + 'px';
                    const leftPos = Math.random() * 100 + 'vw';
                    const duration = Math.random() * 3 + 2 + 's'; // পড়ার গতি
                    const delay = Math.random() * 2 + 's';

                    particle.style.backgroundColor = color;
                    particle.style.width = size;
                    particle.style.height = size;
                    particle.style.left = leftPos;
                    particle.style.borderRadius = Math.random() > 0.5 ? '50%' : '0px';

                    // অ্যানিমেশন সেট করা
                    particle.style.animation = `fall ${duration} linear ${delay} forwards`;

                    document.body.appendChild(particle);

                    // কাজ শেষ হলে এলিমেন্ট ডিলিট করে দেওয়া (মেমরি ক্লিন রাখতে)
                    setTimeout(() => {
                        particle.remove();
                    }, 5000);
                }
            }

            // ক্লিক করলে কনফেটি শুরু হবে
            btn.addEventListener('click', createConfetti);

            // পেজ লোড হওয়ার ২ সেকেন্ড পর অটোমেটিক একবার হবে
            setTimeout(createConfetti, 2000);
        });
    </script>
</div>



{{-- resources/views/components/new-year-widget.blade.php --}}
<div class="new-year-container">
    <div class="new-year-widget" id="manualBtn">
        <div class="year-2026">2026</div>
        <div class="victory-text">HAPPY NEW YEAR</div>
    </div>

    <style>
        /* ====== Widget Style ====== */
        .new-year-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            user-select: none;
            transition: transform 0.2s;
        }
        .new-year-widget:hover { transform: scale(1.1); }
        .new-year-widget:active { transform: scale(0.9); }

        .year-2026 {
            font-family: 'Georgia', serif;
            font-size: 60px;
            font-weight: 900;
            animation: rainbow 5s infinite, bounce 2s infinite alternate;
            text-shadow: 2px 4px 10px rgba(0,0,0,0.2);
        }

        .victory-text {
            margin-top: -5px;
            background: linear-gradient(45deg, #f44336, #2196f3, #ffeb3b, #4caf50);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            text-align: center;
        }

        /* ====== Confetti ====== */
        .confetti-particle {
            position: fixed;
            width: 10px;
            height: 10px;
            z-index: 99998;
            pointer-events: none;
            top: 0;
        }

        /* ====== Animations ====== */
        @keyframes rainbow {
            0% { color: #f44336; }
            25% { color: #2196f3; }
            50% { color: #ffeb3b; }
            75% { color: #4caf50; }
            100% { color: #f44336; }
        }

        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-15px); }
        }

        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('manualBtn');
            const colors = ['#f44336', '#2196f3', '#ffeb3b', '#4caf50', '#ff9800', '#e91e63'];

            function createConfetti() {
                const particleCount = 100;
                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'confetti-particle';

                    const color = colors[Math.floor(Math.random() * colors.length)];
                    const size = Math.random() * 8 + 5 + 'px';
                    const leftPos = Math.random() * 100 + 'vw';
                    const duration = Math.random() * 3 + 2 + 's';
                    const delay = Math.random() * 2 + 's';

                    particle.style.backgroundColor = color;
                    particle.style.width = size;
                    particle.style.height = size;
                    particle.style.left = leftPos;
                    particle.style.borderRadius = Math.random() > 0.5 ? '50%' : '0px';
                    particle.style.animation = `fall ${duration} linear ${delay} forwards`;

                    document.body.appendChild(particle);

                    setTimeout(() => {
                        particle.remove();
                    }, 5000);
                }
            }

            btn.addEventListener('click', createConfetti);

            // Page load হলে স্বয়ংক্রিয় confetti
            setTimeout(createConfetti, 2000);
        });
    </script>
</div>
