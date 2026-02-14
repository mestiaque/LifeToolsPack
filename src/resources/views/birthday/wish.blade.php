@extends('components.lte.guest')

@section('title', __('happy_birthday', ['name' => $name]))

@section('content')

<div class="birthday-page d-flex flex-column align-items-center justify-content-center text-center"
     style="min-height: 88vh; max-height: 90vh; background: linear-gradient(135deg, #00b1f5, #f87171); color: #fff; overflow: hidden; position: relative; padding: 2rem;">

    <!-- Confetti canvas -->
    <canvas id="confettiCanvas" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></canvas>

    <!-- Balloons container -->
    <div id="balloonsContainer" style="position:absolute; bottom:0; width:100%; height:100%; pointer-events:none;"></div>

    <h1 class="display-3 mb-3" style="font-family: 'Comic Sans MS', cursive, sans-serif;">{{__('happy_birthday', ['name' => $name]) }}!</h1>
    <p class="lead mb-4" style="font-size: 1.5rem;">{{ $randomWish }}</p>

    <img src="{{ asset('front/img/hbd2.png') }}"
         alt="Birthday Cake" class="" style="max-width:400px;">

</div>

@endsection

@push('css')
<style>
    /* Required */
#breadcrumb{
    display: none !important;
}
.container-fluid {
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}
.birthday-page{
    margin-top: 12px !important;
}
    /* End Required */

/* Balloon animation */
@keyframes rise {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 1; }
    100% { transform: translateY(-100vh) rotate(360deg); opacity: 1; } /* top of viewport */
}

.balloon {
    position: absolute;
    bottom: 0;
    width: 50px;
    height: 70px;
    background-color: #ff4d6d;
    border-radius: 50% 50% 50% 50%;
    opacity: 1 !important; /* fully opaque */
    animation: rise linear forwards;
}

.balloon:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    width: 2px;
    height: 20px;
    background: #555;
}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>

<script>
// Confetti animation
document.addEventListener('DOMContentLoaded', function() {
    var duration = 5000;
    var end = Date.now() + duration;

    (function frame() {
        confetti({
            particleCount: 3,
            angle: 60,
            spread: 55,
            origin: { x: 0 }
        });
        confetti({
            particleCount: 3,
            angle: 120,
            spread: 55,
            origin: { x: 1 }
        });

        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    }());

    // Balloon animation
    const colors = ['#FF0000', '#FF7F00', '#FFFF00', '#00FF00', '#0000FF', '#4B0082', '#8B00FF'];
    const container = document.getElementById('balloonsContainer');

function createBalloon() {
    const balloon = document.createElement('div');
    balloon.classList.add('balloon');
    const color = colors[Math.floor(Math.random() * colors.length)];
    balloon.style.backgroundColor = color;
    balloon.style.left = Math.random() * 90 + 'vw';

    const duration = 5 + Math.random() * 5; // 5-10s
    balloon.style.animationDuration = duration + 's';
    container.appendChild(balloon);

    setTimeout(() => {
        container.removeChild(balloon);
    }, duration * 1000); // remove after animation
}


    setInterval(createBalloon, 500);

    // Sound effect
    const audio = new Audio('https://www.soundjay.com/human/birthday-candle-blow-1.mp3');
    audio.play().catch(() => { console.log('Autoplay blocked'); });

    // Vibration
    if (navigator.vibrate) {
        navigator.vibrate([200, 100, 200]);
    }
});
</script>
@endpush
