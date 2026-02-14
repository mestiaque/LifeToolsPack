<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ক্ল্যাসিক ক্যারম বোর্ড</title>
    <style>
        body { background: #333; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: white; font-family: sans-serif; overflow: hidden; }
        canvas { background: #f3e5ab; border: 15px solid #5d4037; border-radius: 10px; box-shadow: 0 0 50px rgba(0,0,0,0.5); cursor: crosshair; }
        #ui { position: absolute; top: 10px; text-align: center; pointer-events: none; }
    </style>
</head>
<body>

<div id="ui">
    <h2>ক্যারম গেম</h2>
    <p>মাউস দিয়ে ড্র্যাগ করে স্ট্রাইকার মারুন</p>
</div>
<canvas id="carromBoard"></canvas>

<script>
const canvas = document.getElementById('carromBoard');
const ctx = canvas.getContext('2d');

canvas.width = 500;
canvas.height = 500;

let isDragging = false;
let mouseX, mouseY;

// গুটি ও স্ট্রাইকার অবজেক্ট
class Piece {
    constructor(x, y, radius, color, isStriker = false) {
        this.x = x;
        this.y = y;
        this.radius = radius;
        this.color = color;
        this.isStriker = isStriker;
        this.vx = 0;
        this.vy = 0;
        this.friction = 0.985; // ঘর্ষণ
    }

    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();
        ctx.strokeStyle = "#000";
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.closePath();
    }

    update() {
        this.x += this.vx;
        this.y += this.vy;
        this.vx *= this.friction;
        this.vy *= this.friction;

        // দেওয়াল কলিশন
        if (this.x + this.radius > canvas.width || this.x - this.radius < 0) {
            this.vx = -this.vx;
            this.x = this.x < this.radius ? this.radius : canvas.width - this.radius;
        }
        if (this.y + this.radius > canvas.height || this.y - this.radius < 0) {
            this.vy = -this.vy;
            this.y = this.y < this.radius ? this.radius : canvas.height - this.radius;
        }

        // পকেট চেক (চার কোণায় পকেট)
        const holes = [[0,0], [500,0], [0,500], [500,500]];
        holes.forEach(h => {
            let dist = Math.hypot(this.x - h[0], this.y - h[1]);
            if (dist < 30) {
                if (this.isStriker) {
                    this.resetStriker();
                } else {
                    this.x = -100; // পকেটে পড়ে গেলে সরিয়ে ফেলা
                    this.vx = 0; this.vy = 0;
                }
            }
        });

        if (Math.abs(this.vx) < 0.1) this.vx = 0;
        if (Math.abs(this.vy) < 0.1) this.vy = 0;
    }

    resetStriker() {
        this.x = 250; this.y = 400;
        this.vx = 0; this.vy = 0;
    }
}

let pieces = [];
let striker = new Piece(250, 400, 18, '#ffeb3b', true); // হলুদ স্ট্রাইকার

// গুটি সাজানো (মাঝখানে)
function initBoard() {
    pieces = [];
    const centerX = 250, centerY = 250;
    // কুইন (লাল)
    pieces.push(new Piece(centerX, centerY, 14, '#f44336'));
    // সাদা ও কালো গুটি (সিম্পল ৩টি)
    pieces.push(new Piece(centerX-35, centerY, 14, '#fff'));
    pieces.push(new Piece(centerX+35, centerY, 14, '#000'));
    pieces.push(new Piece(centerX, centerY-35, 14, '#000'));
    pieces.push(new Piece(centerX, centerY+35, 14, '#fff'));
}

// কলিশন হ্যান্ডলিং (গুটিগুলোর একে অপরের সাথে ধাক্কা)
function resolveCollision(p1, p2) {
    let dist = Math.hypot(p1.x - p2.x, p1.y - p2.y);
    if (dist < p1.radius + p2.radius) {
        let tempVx = p1.vx;
        let tempVy = p1.vy;
        p1.vx = p2.vx; p1.vy = p2.vy;
        p2.vx = tempVx; p2.vy = tempVy;
    }
}

// মাউস ইভেন্ট
canvas.addEventListener('mousedown', (e) => {
    if (striker.vx === 0 && striker.vy === 0) {
        isDragging = true;
    }
});

canvas.addEventListener('mousemove', (e) => {
    let rect = canvas.getBoundingClientRect();
    mouseX = e.clientX - rect.left;
    mouseY = e.clientY - rect.top;
});

canvas.addEventListener('mouseup', (e) => {
    if (isDragging) {
        let dx = mouseX - striker.x;
        let dy = mouseY - striker.y;
        striker.vx = dx * -0.15; // শক্তি বা পাওয়ার
        striker.vy = dy * -0.15;
        isDragging = false;
    }
});

function drawBoardLayout() {
    // পকেট আঁকা
    ctx.fillStyle = "#111";
    ctx.beginPath(); ctx.arc(0, 0, 30, 0, Math.PI*2); ctx.fill();
    ctx.beginPath(); ctx.arc(500, 0, 30, 0, Math.PI*2); ctx.fill();
    ctx.beginPath(); ctx.arc(0, 500, 30, 0, Math.PI*2); ctx.fill();
    ctx.beginPath(); ctx.arc(500, 500, 30, 0, Math.PI*2); ctx.fill();

    // স্ট্রাইকার লাইন
    ctx.strokeStyle = "#5d4037";
    ctx.lineWidth = 2;
    ctx.strokeRect(50, 380, 400, 40);
}

function gameLoop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBoardLayout();

    if (isDragging) {
        ctx.beginPath();
        ctx.moveTo(striker.x, striker.y);
        ctx.lineTo(mouseX, mouseY);
        ctx.strokeStyle = "rgba(0,0,0,0.5)";
        ctx.stroke();
    }

    striker.update();
    striker.draw();

    pieces.forEach(p => {
        p.update();
        p.draw();
        resolveCollision(striker, p);
    });

    // গুটিগুলোর নিজেদের মধ্যে কলিশন
    for(let i=0; i<pieces.length; i++) {
        for(let j=i+1; j<pieces.length; j++) {
            resolveCollision(pieces[i], pieces[j]);
        }
    }

    requestAnimationFrame(gameLoop);
}

initBoard();
gameLoop();
</script>

</body>
</html>
