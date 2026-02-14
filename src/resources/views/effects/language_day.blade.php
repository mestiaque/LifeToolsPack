<!-- ১. স্টাইল পার্ট -->
<style>
    /* বর্ণমালার স্টাইল (আপনার দেওয়া) */
    .letter-particle {
        position: fixed; top: -30px; color: #2c3e50; font-weight: bold;
        font-family: 'SolaimanLipi', Arial, sans-serif; pointer-events: none;
        z-index: 99999; animation: letterFall linear infinite;
        text-shadow: 0 0 5px rgba(0,0,0,0.1);
    }

    @keyframes letterFall {
        0% { transform: translateY(0) rotate(0deg); opacity: 0; }
        10% { opacity: 0.9; }
        90% { opacity: 0.7; }
        100% { transform: translateY(105vh) rotate(360deg); opacity: 0; }
    }

    /* ভাষা দিবসের বিশেষ উইজেট (শহীদ মিনার স্টাইল) */
    .language-day-widget {
        position: fixed; bottom: 20px; right: 20px;
        z-index: 100000; display: flex; flex-direction: column; align-items: center;
    }

    .monument-icon {
        width: 60px; height: 60px; background: #f42a41; /* শহীদের রক্ত */
        border-radius: 50%; position: relative; border: 4px solid #1a1a1a;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3); cursor: pointer;
        pointer-events: auto; display: flex; justify-content: center; align-items: center;
    }

    /* শহীদ মিনারের সিম্বলিক ৩টি পিলার */
    .monument-icon::before {
        content: ''; position: absolute; width: 8px; height: 30px;
        background: #fff; left: 15px; bottom: 10px; box-shadow: 10px -5px 0 #fff, 20px 0 0 #fff;
    }

    .lang-text {
        margin-top: 10px; background: #1a1a1a; color: white; padding: 5px 12px;
        border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap;
    }

    @media (max-width: 768px) {
        .letter-particle:nth-child(odd) { display: none; }
    }
</style>

<!-- ২. HTML পার্ট -->
<div class="language-day-widget">
    <div class="monument-icon" onclick="burstLetters()" title="২১শে ফেব্রুয়ারি">
        <span style="color: white; font-size: 20px; font-weight: bold; position: relative; left: 5px;">অ</span>
    </div>
    <div class="lang-text">আন্তর্জাতিক মাতৃভাষা দিবস</div>
</div>

<!-- ৩. জাভাস্ক্রিপ্ট পার্ট -->
<script>
    (function() {
        const letters = [
            'অ', 'আ', 'ই', 'ঈ', 'উ', 'ঊ', 'ঋ', 'এ', 'ঐ', 'ও', 'ঔ',
            'ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ',
            'ট', 'ঠ', 'ড', 'ঢ', 'ণ', 'ত', 'থ', 'দ', 'ধ', 'ন',
            'প', 'ফ', 'ব', 'ভ', 'ম', 'য', 'র', 'ল', 'শ', 'ষ',
            'স', 'হ', 'ড়', 'ঢ়', 'য়', 'ৎ', 'ঃ', '্'
        ];

        function createLetter(manualX = null, manualY = null) {
            const el = document.createElement('div');
            el.className = 'letter-particle';
            el.innerText = letters[Math.floor(Math.random() * letters.length)];

            const leftPos = manualX ? manualX : Math.random() * 100;
            const fontSize = Math.random() * 12 + 18;
            const duration = Math.random() * 5 + 5;

            el.style.left = manualX ? manualX + 'px' : leftPos + 'vw';
            if(manualY) el.style.top = manualY + 'px';

            el.style.fontSize = fontSize + 'px';
            el.style.animationDuration = duration + 's';

            const colorOptions = ['#2c3e50', '#1a1a1a', '#8b0000', '#004d40'];
            el.style.color = colorOptions[Math.floor(Math.random() * colorOptions.length)];

            document.body.appendChild(el);
            setTimeout(() => el.remove(), duration * 1000);
        }

        // অটোমেটিক তুষারপাতের মতো বর্ণ পড়া
        setInterval(createLetter, 500);

        // উইজেটে ক্লিক করলে একসাথে অনেকগুলো বর্ণ উড়বে
        window.burstLetters = function() {
            const rect = document.querySelector('.monument-icon').getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top;

            for(let i = 0; i < 15; i++) {
                setTimeout(() => {
                    createLetter(x + (Math.random() * 100 - 50), y);
                }, i * 50);
            }
        };
    })();
</script>
