<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ config('app.name', 'ESTIAQUE') }} | 404 Brick Breaker</title>

<!--begin::Primary Meta Tags-->
<meta name="title" content="@yield('meta-title', 'M. ESTIAQUE')" />
<meta name="author" content="@yield('meta-author', config('app.name', 'ESTIAQUE'))" />
<meta name="description" content="@yield('meta-description', 'M. Estiaque Ahmed Khan is a skilled Software Engineer and Full-Stack Web Developer specializing in PHP, Laravel, and modern web technologies. Based in Dhaka, Bangladesh, he creates high-quality web applications and innovative solutions.')" />
<meta name="keywords" content="@yield('meta-keywords', 'Estiaque, Web Developer, Encodex, Brick, Breaker, Brick Breaker')" />
<link rel="icon" href="{{ asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">

<!--end::Primary Meta Tags-->


<style>
body{
  margin:0;
  background:radial-gradient(circle at top,#020617,#000);
  color:#e5e7eb;
  font-family:system-ui;
  text-align:center;
  overflow:hidden;
}
h1{margin:10px 0 0}
p{margin:0;opacity:.7}
#top{
  width:90%;
  max-width:900px;
  margin:auto;
  display:flex;
  justify-content:space-between;
  padding:6px;font-size:14px;
  transition: all 0.3s ease;
}
canvas{
  display:block;
  margin:10px auto 20px;
  background:#020617;
  border-radius:16px;
  box-shadow:0 0 50px rgba(56,189,248,.4);
}
button{
  margin:6px;
  padding:10px 16px;
  border-radius:999px;
  border:none;
  font-weight:600;
  cursor:pointer;
  background:linear-gradient(135deg,#38bdf8,#22c55e);
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  transition: all 0.2s ease;
}
button:hover{
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0,0,0,0.5);
}
</style>
</head>

<body>

<h1>404 — Page Not Found</h1>
<p>Break bricks. Forget sadness.</p>

<div id="top">
  <div>Score: <span id="score">0</span></div>
  <div>❤️ <span id="lives">3</span></div>
  <div>🏆 Best: <span id="best">0</span></div>
  <div>Level: <span id="level">1</span></div>
</div>

<canvas id="c"></canvas>

<button onclick="restart()">Restart</button>
<button onclick="mute()">🔊</button>

<div id="msg" style="
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #f87171;
  font-size: 32px;
  font-weight: bold;
  background: rgba(0,0,0,0.6);
  padding: 20px 40px;
  border-radius: 16px;
  display: none;
  text-align: center;
  z-index: 10;
">Game Over 😢</div>

<script>
const c=document.getElementById("c"),x=c.getContext("2d");

// Responsive canvas
function resizeCanvas(){
  const ratio = 16/10;
  let w = Math.min(window.innerWidth, 900);
  let h = w / ratio;
  if(h > window.innerHeight*0.8){
    h = window.innerHeight*0.8;
    w = h * ratio;
  }
  c.width = w;
  c.height = h;
}
window.addEventListener("resize", resizeCanvas);
resizeCanvas();

// Game variables
let score=0,lives=3,level=1,muted=false;
let best=localStorage.bb||0;

const hitSound=new Audio("https://assets.mixkit.co/sfx/preview/mixkit-arcade-game-jump-coin-216.wav");
const loseSound=new Audio("https://assets.mixkit.co/sfx/preview/mixkit-player-losing-or-failing-2042.wav");

const paddle={x:0,w:90,h:14};
let balls=[{x:0,y:0,dx:4,dy:-4,r:8,trail:[]}];
let rows=4,cols=9;
let bricks=[],powers=[],particles=[];

// Paddle & ball positions
function resetPositions(){
  paddle.x = c.width/2 - paddle.w/2;
  balls=[{x:c.width/2,y:c.height-60,dx:4,dy:-4,r:8,trail:[]}];
}
resetPositions();

// Bricks
function makeBricks(){
  bricks=[];
  for(let c1=0;c1<cols;c1++){
    bricks[c1]=[];
    for(let r=0;r<rows;r++)
      bricks[c1][r]={x:0,y:0,hit:false,hue: Math.floor(Math.random()*360)};
  }
}
makeBricks();

// Mouse & touch controls
function clampPaddle(){
  paddle.x = Math.max(0, Math.min(c.width - paddle.w, paddle.x));
}
document.addEventListener("mousemove", e=>{
  const rect=c.getBoundingClientRect();
  paddle.x = e.clientX - rect.left - paddle.w/2;
  clampPaddle();
});
c.addEventListener("touchmove", e=>{
  paddle.x = e.touches[0].clientX - c.getBoundingClientRect().left - paddle.w/2;
  clampPaddle();
});

// Particle effect for powerups
function createParticles(x,y,color){
  for(let i=0;i<10;i++){
    particles.push({x,y,dx:(Math.random()-0.5)*4,dy:(Math.random()-1.5)*3,life:30,color});
  }
}

// Draw loop
function draw(){
  // Animated background gradient
  const grad=x.createLinearGradient(0,0,0,c.height);
  grad.addColorStop(0,`hsl(${(Date.now()/50)%360},20%,5%)`);
  grad.addColorStop(1,"#000");
  x.fillStyle=grad;
  x.fillRect(0,0,c.width,c.height);

  // Bricks
  for(let C=0;C<cols;C++)
    for(let R=0;R<rows;R++){
      let b=bricks[C][R];
      if(!b.hit){
        b.x=30+C*(c.width/cols-10);
        b.y=40+R*28;
        let gradB=x.createLinearGradient(b.x,b.y,b.x+c.width/cols-10,b.y+18);
        gradB.addColorStop(0,`hsl(${b.hue},80%,55%)`);
        gradB.addColorStop(1,`hsl(${(b.hue+60)%360},80%,55%)`);
        x.fillStyle=gradB;
        x.fillRect(b.x,b.y,c.width/cols-10,18);
        x.shadowColor=`hsl(${b.hue},80%,70%)`;
        x.shadowBlur=8;
      }
    }
  x.shadowBlur=0;

  // Paddle
  const paddleY=c.height-25;
  x.fillStyle="#22c55e";
  x.shadowColor="#22c55e";
  x.shadowBlur=15;
  x.fillRect(paddle.x,paddleY,paddle.w,paddle.h);
  x.shadowBlur=0;

  // Balls
  balls.forEach(ball=>{
    // Ball trail
    ball.trail.push({x:ball.x,y:ball.y});
    if(ball.trail.length>10) ball.trail.shift();
    for(let i=0;i<ball.trail.length;i++){
      x.beginPath();
      x.arc(ball.trail[i].x,ball.trail[i].y,ball.r*(i/10),0,Math.PI*2);
      x.fillStyle=`rgba(56,189,248,${i/ball.trail.length*0.5})`;
      x.fill();
    }

    // Ball
    x.beginPath();
    x.arc(ball.x,ball.y,ball.r,0,Math.PI*2);
    x.fillStyle="#38bdf8";
    x.shadowColor="#38bdf8";
    x.shadowBlur=12;
    x.fill();
    x.shadowBlur=0;

    ball.x+=ball.dx;
    ball.y+=ball.dy;

    // Wall collisions
    if(ball.x<ball.r||ball.x>c.width-ball.r) ball.dx*=-1;
    if(ball.y<ball.r) ball.dy*=-1;

    // Paddle collision / bottom
    // if(ball.y>paddleY){
    //   if(ball.x>paddle.x && ball.x<paddle.x+paddle.w) ball.dy*=-1;
    //   else{
    //     lives--;
    //     if(!muted) loseSound.play();
    //     resetPositions();
    //     if(lives<=0) return gameOver();
    //   }
    // }

    if(ball.y + ball.r >= paddleY){ // ball bottom touches paddle
    if(ball.x + ball.r > paddle.x && ball.x - ball.r < paddle.x + paddle.w){
        // collision detected, reverse Y
        ball.dy *= -1;
        // optional: adjust X based on where it hits for spin effect
    } else {
        // ball missed paddle
        lives--;
        if(!muted) loseSound.play();
        balls.splice(balls.indexOf(ball), 1);
        if(balls.length === 0) gameOver();
    }
    }

    // Brick collisions
    bricks.flat().forEach(b=>{
      if(!b.hit && ball.x>b.x && ball.x<b.x+(c.width/cols-10) && ball.y>b.y && ball.y<b.y+18){
        b.hit=true;
        ball.dy*=-1;
        score+=10;
        if(Math.random()<.2){
          const type=Math.random()<.5?"big":"multi";
          powers.push({x:b.x+(c.width/cols-10)/2,y:b.y,type});
          createParticles(ball.x,ball.y,`hsl(${b.hue},80%,55%)`);
        }
        if(!muted) hitSound.play();
      }
    });
  });

  // Powerups
  powers.forEach((p,i)=>{
    p.y+=2;
    x.fillStyle=p.type=="big"?"#facc15":"#60a5fa";
    x.shadowColor=x.fillStyle;
    x.shadowBlur=12;
    x.fillRect(p.x,p.y,16,16);
    x.shadowBlur=0;
    if(p.y>c.height-25 && p.x>paddle.x && p.x<paddle.x+paddle.w){
      if(p.type=="big") paddle.w=140;
      if(p.type=="multi") balls.push({...balls[0],dx:-balls[0].dx,trail:[]});
      powers.splice(i,1);
    }
  });

  // Particles
  particles.forEach((p,i)=>{
    x.fillStyle=p.color;
    x.globalAlpha=p.life/30;
    x.fillRect(p.x,p.y,3,3);
    p.x+=p.dx; p.y+=p.dy; p.life--;
    if(p.life<=0) particles.splice(i,1);
  });
  x.globalAlpha=1;

  // Level up
  if(bricks.flat().every(b=>b.hit)){
    level++;
    rows++;
    makeBricks();
    balls.forEach(b=>{b.dx*=1.15;b.dy*=1.15;});
  }

  updateUI();
  requestAnimationFrame(draw);
}

// UI
function updateUI(){
  document.getElementById("score").textContent=score;
  document.getElementById("lives").textContent=lives;
  document.getElementById("level").textContent=level;
  best=Math.max(best,score);
  localStorage.bb=best;
  document.getElementById("best").textContent=best;
}

// Game over
function gameOver(){
  document.getElementById("msg").style.display = "block";
}

// Restart
function restart(){
  document.getElementById("msg").style.display = "none";
  score = 0;
  lives = 3;
  level = 1;
  rows = 4;
  paddle.w = 90;
  powers = [];
  particles = [];
  resetPositions();
  makeBricks();
}

// Mute
function mute(){muted=!muted}

// Start
draw();
</script>

</body>
</html>
