<canvas id="snowCanvas" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></canvas>

<script>
    (function() {
        const canvas = document.getElementById('snowCanvas');
        const ctx = canvas.getContext('2d');

        let width, height;
        let snowflakes = [];
        const particles = ['❄', '❅', '❆', '*'];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }

        window.addEventListener('resize', resize);
        resize();

        class Snowflake {
            constructor() {
                this.reset();
                // শুরুতে স্ক্রিনের যেকোনো জায়গায় পজিশন দেওয়ার জন্য (যাতে লোড হতেই পুরো স্ক্রিনে তুষার দেখা যায়)
                this.y = Math.random() * height;
            }

            reset() {
                this.char = particles[Math.floor(Math.random() * particles.length)];
                this.x = Math.random() * width;
                this.y = -20; // স্ক্রিনের সামান্য উপর থেকে শুরু হবে
                this.size = (Math.random() * 12 + 8); // ০.৫ থেকে ১.৩ em এর সমতুল্য পিক্সেল
                this.speed = Math.random() * 3 + 2; // ২ থেকে ৫ সেকেন্ডের গতির সাথে সামঞ্জস্যপূর্ণ
                this.opacity = Math.random() * 0.7 + 0.3;
                this.rotation = 0;
                this.rotationSpeed = Math.random() * 0.05;
                this.wind = (Math.random() - 0.5) * 0.5; // হালকা ডানে-বামে দোলানো
            }

            update() {
                this.y += this.speed;
                this.x += this.wind;
                this.rotation += this.rotationSpeed;

                // স্ক্রিনের নিচে চলে গেলে আবার উপর থেকে শুরু হবে
                if (this.y > height + 20) {
                    this.reset();
                }
            }

            draw() {
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.rotation);
                ctx.globalAlpha = this.opacity;
                ctx.fillStyle = '#87CEFA';
                ctx.font = `${this.size}px serif`;
                ctx.textAlign = 'center';
                ctx.fillText(this.char, 0, 0);
                ctx.restore();
            }
        }

        // স্নোফ্লেক সংখ্যা সেট করা
        const snowflakeCount = window.innerWidth < 768 ? 40 : 80;
        for (let i = 0; i < snowflakeCount; i++) {
            snowflakes.push(new Snowflake());
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);

            snowflakes.forEach(snowflake => {
                snowflake.update();
                snowflake.draw();
            });

            requestAnimationFrame(animate);
        }

        animate();
    })();
</script>
