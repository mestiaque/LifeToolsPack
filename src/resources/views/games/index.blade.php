@extends('components.lte.guest')
@section('title', 'Games')

@section('content')
<div class="container py-4">

    <div class="row g-4">

        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('games.bounce') }}" class="game-link">
                <div class="game-card">
                    <div class="game-icon">🎈</div>
                    <div class="game-title">Bounce</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('games.bricks-break') }}" class="game-link">
                <div class="game-card">
                    <div class="game-icon">🧱</div>
                    <div class="game-title">Bricks Break</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('games.carrom') }}" class="game-link">
                <div class="game-card">
                    <div class="game-icon">🎯</div>
                    <div class="game-title">Carrom</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('games.snake') }}" class="game-link">
                <div class="game-card">
                    <div class="game-icon">🐍</div>
                    <div class="game-title">Snake</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('games.egg-catching') }}" class="game-link">
                <div class="game-card">
                    <div class="game-icon">🥚</div>
                    <div class="game-title">Egg Catching</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ route('games.memory-card') }}" class="game-link">
                <div class="game-card">
                    <div class="game-icon">🎮</div>
                    <div class="game-title">Memory Card</div>
                </div>
            </a>
        </div>

    </div>

</div>

@push('css')
<style>
.game-link {
    text-decoration: none;
    color: inherit;
}

.game-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 22px 10px;
    text-align: center;
    border: 1px solid #eee;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.04);
}

.game-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.12);
    border-color: #0d6efd;
}

.game-icon {
    font-size: 36px;
    margin-bottom: 8px;
    transition: transform 0.3s ease;
}

.game-card:hover .game-icon {
    transform: scale(1.25);
}

.game-title {
    font-weight: 600;
    font-size: 14px;
    margin-top: 4px;
}
</style>
@endpush

@endsection



{{--


🧠 Memory-based Brain Games

Memory Card Flip – একই ছবি/symbol match করা

Number Sequence Recall – কিছু সেকেন্ড দেখিয়ে নম্বর লুকানো

Simon Says (Color/Sound) – pattern মনে রাখা

🔢 Logic & Math Games

Sudoku (classic বা mini version)

Math Speed Challenge – limited time-এ যোগ/বিয়োগ/গুণ

Number Puzzle (2048-style)

Logic Grid Puzzle (who lives where টাইপ)

🧩 Puzzle Games

Sliding Puzzle (8/15 puzzle)

Jigsaw Puzzle (image upload সাপোর্ট দিলে আরও cool)

Maze Solver – shortest path খোঁজা

Tower of Hanoi

⚡ Speed & Focus Games

Reaction Time Test – screen-এ signal এলে click

Find the Different Object

Color-Word Conflict (Stroop Test)

Typing Reflex Game

🧠 Language / Word Games

Word Scramble

Hangman

Word Search Grid

Vocabulary Quiz

🎯 Strategy & Thinking Games

Tic Tac Toe (AI সহ)

Chess (basic engine)

Connect Four

Matchstick Game (turn-based logic)

🌐 Web/Desktop-এর জন্য Extra Features

Difficulty levels (Easy / Medium / Hard)

Timer + Score system

Daily Brain Challenge

Progress tracking

Multiplayer (web হলে)

🛠️ Tech Stack আইডিয়া (যদি জানতে চাও)

Web: HTML, CSS, JavaScript / React

Desktop: Python (Tkinter / PyQt), Java (JavaFX), Electron

Game Logic: JavaScript বা Python সবচেয়ে সহজ

--}}
