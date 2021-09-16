import { update as updateSnake, draw as drawSnake, SNAKE_SPEED , getSnakeHead, snakeIntersection } from './snake.js'
import { update as updateFood, draw as drawFood } from './food.js'
import { outSideGrid } from './grid.js'



let lastRenderTime = 0
let gameOver = false
const gameBoard = document.getElementById('game-board')

function main(currentTime) {
  if (gameOver) {
    if (confirm('Tu as perdu ! Appuie sur OK pour recommencer')) {
      window.location = 'https://assets.roomsthegame.com/snake/snake'
    }
    return
  }

  window.requestAnimationFrame(main)
  const secondsSinceLastRender = (currentTime - lastRenderTime) / 1000
  if (secondsSinceLastRender < 1 / SNAKE_SPEED) return



  lastRenderTime = currentTime

  update()
  draw()
}

window.requestAnimationFrame(main)

function update() {
  updateSnake()
  updateFood()
  checkDeath()
}

function draw() {
  gameBoard.innerHTML = ''
  drawSnake(gameBoard)
  drawFood(gameBoard)
}

function checkDeath() {
  gameOver = outSideGrid(getSnakeHead()) || snakeIntersection()
  if(gameOver){
    var audio = new Audio('./sounds/snake_hiss.wav');
    audio.play();
  }
}
