<style>
    #snow-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9999;
    }
    .snowflake {
        position: absolute;
        top: -10px;
        color: #87CEFA;
        font-size: 1em;
        user-select: none;
        pointer-events: none;
        animation-name: fall;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }
    @keyframes fall {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(360deg); opacity: 0.3; }
    }
    /* মোবাইল ডিভাইসের জন্য স্নোফ্লেক সংখ্যা কমানো */
    /* @media (max-width: 768px) {
        .snowflake:nth-child(even) { display: none; }
    } */
</style>

<div id="snow-container"></div>

<script>
    function createSnowflake() {
        const container = document.getElementById('snow-container');
        if(!container) return;

        const snowflake = document.createElement('div');
        const particles = ['❄', '❅', '❆', '*']; // স্নোফ্লেকের ধরন

        snowflake.classList.add('snowflake');
        snowflake.innerText = particles[Math.floor(Math.random() * particles.length)];

        // র‍্যান্ডম পজিশন এবং স্টাইল
        const leftPos = Math.random() * 100;
        const duration = Math.random() * 3 + 2; // ২ থেকে ৫ সেকেন্ড
        const size = Math.random() * 0.8 + 0.5; // ০.৫ থেকে ১.৩ em

        snowflake.style.left = leftPos + 'vw';
        snowflake.style.animationDuration = duration + 's';
        snowflake.style.fontSize = size + 'em';
        snowflake.style.opacity = Math.random();

        container.appendChild(snowflake);

        // এনিমেশন শেষ হলে রিমুভ করে দেওয়া যাতে মেমোরি লোড না বাড়ে
        setTimeout(() => {
            snowflake.remove();
        }, duration * 1000);
    }

    // প্রতি ২০০ মিলিসেকেন্ডে একটি করে তুষারপাত তৈরি হবে
    setInterval(createSnowflake, 200);
</script>
