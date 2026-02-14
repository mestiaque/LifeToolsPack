<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nokia Bounce - Classic Edition</title>
    <style>
        body { margin: 0; background: #222; display: flex; justify-content: center; align-items: center; height: 100vh; overflow: hidden; font-family: sans-serif; }
        canvas { background: #87CEEB; border: 5px solid #555; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        #ui { position: absolute; top: 20px; color: white; font-size: 20px; text-shadow: 2px 2px 5px black; pointer-events: none; }
    </style>
</head>
<body>

<div id="ui">Rings: <span id="rings">0</span> / 3 | Life: ❤️❤️❤️</div>
<canvas id="gameCanvas"></canvas>

<script>
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    const ringsElement = document.getElementById('rings');

    canvas.width = 800;
    canvas.height = 400;

    // গেম অবজেক্টস
    let ball = { x: 50, y: 300, radius: 15, dx: 0, dy: 0, speed: 4, jumpPower: -10, gravity: 0.5, onGround: false };
    let collectedRings = 0;
    let lives = 3;

    // প্ল্যাটফর্ম, রিং এবং কাঁটা (লেভেল ডিজাইন)
    let platforms = [
        { x: 0, y: 350, w: 200, h: 50 },
        { x: 250, y: 280, w: 150, h: 20 },
        { x: 450, y: 200, w: 150, h: 20 },
        { x: 650, y: 300, w: 150, h: 100 }
    ];

    let rings = [
        { x: 320, y: 230, r: 25, collected: false },
        { x: 520, y: 150, r: 25, collected: false },
        { x: 720, y: 250, r: 25, collected: false }
    ];

    let spikes = [
        { x: 300, y: 340, w: 30, h: 10 },
        { x: 500, y: 340, w: 30, h: 10 }
    ];

    // কিবোর্ড কন্ট্রোল
    let keys = {};
    window.addEventListener('keydown', e => keys[e.code] = true);
    window.addEventListener('keyup', e => keys[e.code] = false);

    function update() {
        // মুভমেন্ট
        if (keys['ArrowRight']) ball.dx = ball.speed;
        else if (keys['ArrowLeft']) ball.dx = -ball.speed;
        else ball.dx = 0;

        if (keys['ArrowUp'] && ball.onGround) {
            ball.dy = ball.jumpPower;
            ball.onGround = false;
        }

        // গ্র্যাভিটি প্রয়োগ
        ball.dy += ball.gravity;
        ball.x += ball.dx;
        ball.y += ball.dy;

        // প্ল্যাটফর্ম কলিশন
        ball.onGround = false;
        platforms.forEach(p => {
            if (ball.x + ball.radius > p.x && ball.x - ball.radius < p.x + p.w &&
                ball.y + ball.radius > p.y && ball.y + ball.radius < p.y + p.h + 10) {
                if (ball.dy > 0) {
                    ball.dy = 0;
                    ball.y = p.y - ball.radius;
                    ball.onGround = true;
                }
            }
        });

        // রিং সংগ্রহ
        rings.forEach(r => {
            if (!r.collected) {
                let dist = Math.hypot(ball.x - r.x, ball.y - r.y);
                if (dist < r.r + ball.radius) {
                    r.collected = true;
                    collectedRings++;
                    ringsElement.innerText = collectedRings;
                }
            }
        });

        // কাঁটায় লাগলে মৃত্যু
        spikes.forEach(s => {
            if (ball.x + ball.radius > s.x && ball.x - ball.radius < s.x + s.w &&
                ball.y + ball.radius > s.y && ball.y - ball.radius < s.y + s.h) {
                resetBall();
            }
        });

        // স্ক্রিন বাউন্ডারি
        if (ball.y > canvas.height) resetBall();
        if (ball.x < 0) ball.x = 0;
        if (ball.x > canvas.width) ball.x = canvas.width;
    }

    function resetBall() {
        ball.x = 50;
        ball.y = 300;
        ball.dy = 0;
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // প্ল্যাটফর্ম আঁকা (ইটের মতো রঙ)
        ctx.fillStyle = '#8B4513';
        platforms.forEach(p => ctx.fillRect(p.x, p.y, p.w, p.h));

        // রিং আঁকা (সোনালী রিং)
        rings.forEach(r => {
            if (!r.collected) {
                ctx.beginPath();
                ctx.strokeStyle = 'yellow';
                ctx.lineWidth = 5;
                ctx.arc(r.x, r.y, r.r, 0, Math.PI * 2);
                ctx.stroke();
            }
        });

        // কাঁটা আঁকা (লাল ট্রায়াঙ্গেল)
        ctx.fillStyle = 'red';
        spikes.forEach(s => {
            ctx.beginPath();
            ctx.moveTo(s.x, s.y + s.h);
            ctx.lineTo(s.x + s.w / 2, s.y);
            ctx.lineTo(s.x + s.w, s.y + s.h);
            ctx.fill();
        });

        // লাল বল (সিগনেচার বাউন্স বল)
        ctx.beginPath();
        ctx.fillStyle = 'red';
        ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 2;
        ctx.stroke();

        // বলের শাইন ইফেক্ট
        ctx.beginPath();
        ctx.fillStyle = 'rgba(255,255,255,0.3)';
        ctx.arc(ball.x - 5, ball.y - 5, 5, 0, Math.PI * 2);
        ctx.fill();

        requestAnimationFrame(() => {
            update();
            draw();
        });
    }

    draw();
</script>

</body>
</html>
    