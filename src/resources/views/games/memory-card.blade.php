<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Memory Card Game</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #222;
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    h1 {
        text-align: center;
    }
    .game-container {
        display: grid;
        grid-template-columns: repeat(4, 100px);
        grid-gap: 15px;
        margin-top: 20px;
    }
    .card {
        width: 100px;
        height: 100px;
        background-color: #444;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 40px;
        cursor: pointer;
        border-radius: 10px;
        transition: transform 0.3s;
    }
    .card.flipped {
        background-color: #fff;
        color: #222;
        transform: rotateY(180deg);
    }
    .card.matched {
        background-color: #4caf50;
        color: #fff;
        cursor: default;
    }
</style>
</head>
<body>
<div>
    <h1>Memory Card Game</h1>
    <div class="game-container" id="gameContainer"></div>
</div>

<script>
const cardsArray = ['🍎','🍌','🍇','🍒','🍉','🥝','🍍','🍓'];
let gameCards = [...cardsArray, ...cardsArray]; // duplicate for matching
gameCards.sort(() => 0.5 - Math.random()); // shuffle

const gameContainer = document.getElementById('gameContainer');

let firstCard = null;
let secondCard = null;
let lockBoard = false;
let matches = 0;

function createBoard() {
    gameCards.forEach((symbol, index) => {
        const card = document.createElement('div');
        card.classList.add('card');
        card.dataset.symbol = symbol;
        card.addEventListener('click', flipCard);
        gameContainer.appendChild(card);
    });
}

function flipCard() {
    if(lockBoard) return;
    if(this === firstCard) return;

    this.classList.add('flipped');
    this.textContent = this.dataset.symbol;

    if(!firstCard) {
        firstCard = this;
        return;
    }

    secondCard = this;
    checkMatch();
}

function checkMatch() {
    if(firstCard.dataset.symbol === secondCard.dataset.symbol) {
        firstCard.classList.add('matched');
        secondCard.classList.add('matched');
        matches += 1;
        resetBoard();
        if(matches === cardsArray.length) {
            setTimeout(() => alert('🎉 You Won!'), 300);
        }
    } else {
        lockBoard = true;
        setTimeout(() => {
            firstCard.classList.remove('flipped');
            secondCard.classList.remove('flipped');
            firstCard.textContent = '';
            secondCard.textContent = '';
            resetBoard();
        }, 1000);
    }
}

function resetBoard() {
    [firstCard, secondCard] = [null, null];
    lockBoard = false;
}

createBoard();
</script>
</body>
</html>
