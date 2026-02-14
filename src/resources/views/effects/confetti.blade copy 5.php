<div class="new-year-container">
    <!-- মূল স্থির উইজেট (শুধু বাটন হিসেবে কাজ করবে) -->
    <div class="new-year-widget" id="mainWidget">
        <div class="year-static" id="yearDisplay">2026</div>
        <div class="victory-text">HAPPY NEW YEAR</div>
    </div>

    <!-- কনফেটি এবং রকেট অ্যানিমেশনের জন্য ক্যানভাস -->
    <canvas id="animationCanvas" style="position: fixed; top: 0; left: 0; pointer-events: none; z-index: 99999;"></canvas>

    <style>
        .new-year-widget {
            position: fixed; bottom: 20px; right: 20px; z-index: 100000;
            display: flex; flex-direction: column; align-items: center;
            cursor: pointer; user-select: none; transition: transform 0.2s;
        }
        .new-year-widget:hover { transform: scale(1.05); }

        .year-static {
            font-family: 'Georgia', 'Cursive', serif;
            font-size: 60px; font-weight: 900; line-height: 1;
            filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.3));
        }

        .victory-text {
            margin-top: 5px; padding: 5px 20px; border-radius: 50px;
            font-size: 14px; font-weight: bold; letter-spacing: 2px;
            color: white;
            background: linear-gradient(270deg, #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #8b00ff);
            background-size: 400% 400%;
            animation: moveGradient 5s linear infinite;
        }

        @keyframes moveGradient {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('animationCanvas');
            const ctx = canvas.getContext('2d');
            const widget = document.getElementById('mainWidget');
            const yearDisplay = document.getElementById('yearDisplay');

            let width, height;
            let particles = [];
            let rockets = [];
            const colors = ['#FF0000', '#FF7F00', '#FFFF00', '#00FF00', '#0000FF', '#4B0082', '#9400D3'];
            let colorIndex = 0;

            // রিসাইজ হ্যান্ডলার
            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            // টেক্সট কালার পরিবর্তন (স্থির উইজেটের জন্য)
            setInterval(() => {
                yearDisplay.style.color = colors[colorIndex];
                colorIndex = (colorIndex + 1) % colors.length;
            }, 500);

            class Confetti {
                constructor(x, y) {
                    this.x = x;
                    this.y = y;
                    const angle = Math.random() * Math.PI * 2;
                    const speed = Math.random() * 12 + 5;
                    this.vx = Math.cos(angle) * speed;
                    this.vy = Math.sin(angle) * speed;
                    this.size = Math.random() * 8 + 4;
                    this.color = colors[Math.floor(Math.random() * colors.length)];
                    this.rotationX = Math.random() * 360;
                    this.rotSpeedX = Math.random() * 10;
                    this.gravity = 0.2;
                    this.friction = 0.98;
                }
                update() {
                    this.vx *= this.friction;
                    this.vy += this.gravity;
                    this.x += this.vx;
                    this.y += this.vy;
                    this.rotationX += this.rotSpeedX;
                }
                draw() {
                    ctx.save();
                    ctx.translate(this.x, this.y);
                    ctx.rotate(this.rotationX * Math.PI / 180);
                    ctx.fillStyle = this.color;
                    ctx.fillRect(-this.size/2, -this.size/4, this.size, this.size/2);
                    ctx.restore();
                }
            }

            class Rocket {
                constructor(startX, startY) {
                    this.x = startX;
                    this.y = startY;
                    this.targetX = width / 2;
                    this.targetY = 150;
                    this.progress = 0;
                    this.rotation = 0;
                    this.color = yearDisplay.style.color;
                }
                update() {
                    this.progress += 0.02; // গতি নিয়ন্ত্রণ
                    this.rotation += 14.4; // ৭২০ ডিগ্রি ঘোরার জন্য

                    // ইজিং (Cubic Out)
                    const t = this.progress;
                    const ease = 1 - Math.pow(1 - t, 3);

                    this.currentX = this.x + (this.targetX - this.x) * ease;
                    this.currentY = this.y + (this.targetY - this.y) * ease;

                    if (this.progress >= 1) {
                        explode(this.targetX, this.targetY);
                        return false;
                    }
                    return true;
                }
                draw() {
                    ctx.save();
                    ctx.translate(this.currentX, this.currentY);
                    ctx.rotate(this.rotation * Math.PI / 180);
                    const scale = 1 - (this.progress * 0.6);
                    ctx.scale(scale, scale);
                    ctx.font = "bold 60px Georgia";
                    ctx.fillStyle = this.color;
                    ctx.textAlign = "center";
                    ctx.shadowBlur = 10;
                    ctx.shadowColor = "rgba(0,0,0,0.3)";
                    ctx.fillText("2026", 0, 0);
                    ctx.restore();
                }
            }

            function explode(x, y) {
                const count = width < 600 ? 150 : 350;
                for (let i = 0; i < count; i++) {
                    particles.push(new Confetti(x, y));
                }
            }

            function launchRocket() {
                const rect = widget.getBoundingClientRect();
                rockets.push(new Rocket(rect.left + rect.width/2, rect.top));
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);

                // রকেট আপডেট ও ড্র
                rockets = rockets.filter(r => {
                    const alive = r.update();
                    if (alive) r.draw();
                    return alive;
                });

                // কনফেটি আপডেট ও ড্র
                particles = particles.filter(p => {
                    p.update();
                    p.draw();
                    return p.y < height + 50;
                });

                requestAnimationFrame(animate);
            }

            widget.addEventListener('click', launchRocket);
            animate();

            // অটো লঞ্চ ২ সেকেন্ড পর
            setTimeout(launchRocket, 2000);
        });
    </script>
</div>
