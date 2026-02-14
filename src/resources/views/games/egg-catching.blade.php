<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>মুরগির ডিম ধরার গেম</title>
    <style>
        body { margin: 0; overflow: hidden; background: #87CEEB; font-family: Arial, sans-serif; }
        #game-container { position: relative; width: 100vw; height: 100vh; background: linear-gradient(#87CEEB, #e1ffdb); }

        /* দড়ি বা বাঁশ */
        #rope { position: absolute; top: 100px; width: 100%; height: 10px; background: #8B4513; }

        /* মুরগিগুলো */
        .chicken-container { position: absolute; top: 40px; display: flex; justify-content: space-around; width: 100%; }
        .chicken { font-size: 50px; position: relative; transition: transform 0.2s; }

        /* ঝুড়ি */
        #basket {
            position: absolute; bottom: 20px; left: 50%;
            width: 100px; height: 60px; background: #CD853F;
            border-radius: 0 0 50px 50px; transform: translateX(-50%);
            border: 4px solid #8B4513; z-index: 10;
        }
        #basket::before { content: "🧺"; font-size: 50px; position: absolute; top: -35px; left: 20px; }

        /* ডিম এবং টয়লেট */
        .item { position: absolute; font-size: 30px; z-index: 5; }

        /* স্কোর */
        #ui { position: absolute; top: 20px; left: 20px; font-size: 24px; font-weight: bold; color: #333; }
    </style>
</head>
<body>

<div id="game-container">
    <div id="ui">স্কোর: <span id="score">0</span></div>
    <div id="rope"></div>

    <div class="chicken-container">
        <div class="chicken" id="c1">🐔</div>
        <div class="chicken" id="c2">🐔</div>
        <div class="chicken" id="c3">🐔</div>
        <div class="chicken" id="c4">🐔</div>
        <div class="chicken" id="c5">🐔</div>
    </div>

    <div id="basket"></div>
</div>

<script>
    const basket = document.getElementById('basket');
    const container = document.getElementById('game-container');
    const scoreElement = document.getElementById('score');
    let score = 0;

    // ১. মাউস দিয়ে ঝুড়ি মুভমেন্ট
    document.addEventListener('mousemove', (e) => {
        let x = e.clientX;
        basket.style.left = x + 'px';
    });

    // ২. আইটেম (ডিম/টয়লেট) তৈরি করা
    function createItem() {
        const chickens = document.querySelectorAll('.chicken');
        const randomChicken = chickens[Math.floor(Math.random() * chickens.length)];
        const rect = randomChicken.getBoundingClientRect();

        const item = document.createElement('div');
        item.classList.add('item');

        // ৭০% সম্ভাবনা ডিম পড়ার, ৩০% টয়লেট
        const isEgg = Math.random() > 0.3;
        item.innerText = isEgg ? '🥚' : '💩';
        item.dataset.type = isEgg ? 'egg' : 'poop';

        item.style.left = (rect.left + rect.width / 4) + 'px';
        item.style.top = '100px';
        container.appendChild(item);

        // মুরগির একটু নড়াচড়া (অ্যানিমেশন)
        randomChicken.style.transform = 'scale(1.2) translateY(-10px)';
        setTimeout(() => randomChicken.style.transform = 'scale(1)', 200);

        moveItem(item);
    }

    // ৩. আইটেম নিচে পড়া এবং কলিশন চেক
    function moveItem(item) {
        let top = 100;
        const speed = Math.random() * 3 + 2; // র‍্যান্ডম স্পিড

        const fallInterval = setInterval(() => {
            top += speed;
            item.style.top = top + 'px';

            // ঝুড়ির পজিশন
            const bRect = basket.getBoundingClientRect();
            const iRect = item.getBoundingClientRect();

            // কলিশন ডিটেকশন (ঝুড়িতে ধরা পড়া)
            if (
                iRect.bottom >= bRect.top &&
                iRect.right >= bRect.left &&
                iRect.left <= bRect.right &&
                iRect.top <= bRect.bottom
            ) {
                if (item.dataset.type === 'egg') {
                    score += 10;
                } else {
                    score -= 5;
                    container.style.backgroundColor = '#ffcccc'; // স্ক্রিন লাল হওয়া
                    setTimeout(() => container.style.backgroundColor = '', 200);
                }
                scoreElement.innerText = score;
                clearInterval(fallInterval);
                item.remove();
            }

            // যদি নিচে পড়ে যায়
            if (top > window.innerHeight) {
                clearInterval(fallInterval);
                item.remove();
            }
        }, 10);
    }

    // ৪. গেম লুপ (প্রতি ১ সেকেন্ডে একটি আইটেম পড়বে)
    setInterval(createItem, 1000);

</script>

</body>
</html>
