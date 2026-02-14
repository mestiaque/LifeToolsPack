<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classic Snake Game</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: 'Arial', sans-serif;
        }
        #game-wrapper {
            text-align: center;
        }
        canvas {
            background-color: #fff;
            border: 10px solid #2c3e50;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
        .score-board {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div id="game-wrapper">
    <div class="score-board">Score: <span id="scoreVal">0</span></div>
    <canvas id="snakeGame" width="400" height="400"></canvas>
    <p>কিবোর্ডের Arrow Keys দিয়ে মুভ করুন</p>
</div>

<script>
    const canvas = document.getElementById("snakeGame");
    const ctx = canvas.getContext("2d");
    const scoreElement = document.getElementById("scoreVal");

    const box = 20; // প্রতি ঘরের সাইজ
    let score = 0;
    let gameSpeed = 100;

    // সাপের পজিশন শুরু
    let snake = [];
    snake[0] = { x: 9 * box, y: 10 * box };

    // খাবার
    let food = {
        x: Math.floor(Math.random() * 19 + 1) * box,
        y: Math.floor(Math.random() * 19 + 1) * box
    };

    let d; // ডিরেকশন

    document.addEventListener("keydown", direction);

    function direction(event) {
        let key = event.keyCode;
        if(key == 37 && d != "RIGHT") d = "LEFT";
        else if(key == 38 && d != "DOWN") d = "UP";
        else if(key == 39 && d != "LEFT") d = "RIGHT";
        else if(key == 40 && d != "UP") d = "DOWN";
    }

    // নিজে নিজেকে কামড় দিলে গেম ওভার
    function collision(head, array) {
        for(let i = 0; i < array.length; i++) {
            if(head.x == array[i].x && head.y == array[i].y) return true;
        }
        return false;
    }

    function draw() {
        ctx.fillStyle = "white";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        for(let i = 0; i < snake.length; i++) {
            ctx.fillStyle = (i == 0) ? "#2c3e50" : "#34495e"; // সাপের মাথা একটু গাঢ়
            ctx.fillRect(snake[i].x, snake[i].y, box, box);

            ctx.strokeStyle = "white";
            ctx.strokeRect(snake[i].x, snake[i].y, box, box);
        }

        // খাবার আঁকা
        ctx.fillStyle = "#e74c3c"; // লাল খাবার
        ctx.fillRect(food.x, food.y, box, box);

        // বর্তমান পজিশন
        let snakeX = snake[0].x;
        let snakeY = snake[0].y;

        // ডিরেকশন অনুযায়ী মুভমেন্ট
        if( d == "LEFT") snakeX -= box;
        if( d == "UP") snakeY -= box;
        if( d == "RIGHT") snakeX += box;
        if( d == "DOWN") snakeY += box;

        // খাবার খেলে কি হবে
        if(snakeX == food.x && snakeY == food.y) {
            score++;
            scoreElement.innerHTML = score;
            food = {
                x: Math.floor(Math.random() * 19 + 1) * box,
                y: Math.floor(Math.random() * 19 + 1) * box
            };
        } else {
            snake.pop(); // লেজ কেটে দাও
        }

        let newHead = { x: snakeX, y: snakeY };

        // দেওয়াল অথবা নিজের গায়ে লাগলে গেম ওভার
        if(snakeX < 0 || snakeX >= canvas.width || snakeY < 0 || snakeY >= canvas.height || collision(newHead, snake)) {
            clearInterval(game);
            alert("গেম ওভার! আপনার স্কোর: " + score);
            location.reload(); // পেজ রিলোড হবে গেম রিসেট করতে
        }

        snake.unshift(newHead); // নতুন মাথা যোগ করো
    }

    let game = setInterval(draw, gameSpeed);

</script>

</body>
</html>
