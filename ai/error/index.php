<?php
// 404.php - Custom 404 Error Page with Tic Tac Toe Game
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
        }
        
        .container {
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        
        h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: #ff6b6b;
        }
        
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #eaeaea;
        }
        
        /* Game Board Styles */
        .game-container {
            margin: 2rem auto;
        }
        
        .board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 10px;
            margin: 0 auto;
            max-width: 300px;
        }
        
        .cell {
            width: 100%;
            aspect-ratio: 1/1;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .cell:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .cell img {
            max-width: 70%;
            max-height: 70%;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.3s ease;
        }
        
        .cell.filled img {
            opacity: 1;
            transform: scale(1);
        }
        
        /* Game Status and Controls */
        .status {
            font-size: 1.5rem;
            margin: 1rem 0;
            min-height: 2rem;
            color: #4ecdc4;
        }
        
        .controls {
            margin: 1.5rem 0;
        }
        
        button {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 30px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 10px;
        }
        
        button:hover {
            background-color: #ff8e8e;
            transform: translateY(-2px);
        }
        
        /* Footer Styles */
        .footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 480px) {
            h1 {
                font-size: 2.5rem;
            }
            
            .board {
                max-width: 250px;
            }
            
            button {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Page not found</h1>
        <p>Oops! The page you're looking for doesn't exist.</p>
        
        <div class="game-container">
            <div class="status" id="status">Play Tic Tac Toe while you're here!</div>
            <div class="board" id="board">
                <?php for($i = 0; $i < 9; $i++): ?>
                <div class="cell" data-index="<?php echo $i; ?>">
                    <img src="/placeholder.svg" alt="" class="marker">
                </div>
                <?php endfor; ?>
            </div>
            <div class="controls" id="controls">
                <button id="restart-btn" style="display: none;">Restart</button>
            </div>
        </div>
        
        <div class="footer">
            Credit: ShiPu Ai (ssfmym.kesug.com/ai)<br>
            Powered by LumeTech Co. Ltd
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Game state
            let gameActive = true;
            let currentPlayer = 'X';
            let gameState = ['', '', '', '', '', '', '', '', ''];
            
            // Winning conditions
            const winningConditions = [
                [0, 1, 2],
                [3, 4, 5],
                [6, 7, 8],
                [0, 3, 6],
                [1, 4, 7],
                [2, 5, 8],
                [0, 4, 8],
                [2, 4, 6]
            ];
            
            // Status messages
            const statusDisplay = document.getElementById('status');
            const winningMessage = () => `"${currentPlayer === 'X' ? '4' : 'O'}" Wins!`;
            const drawMessage = () => `Game ended in a draw!`;
            const currentPlayerTurn = () => `${currentPlayer === 'X' ? '4' : 'O'}'s turn`;
            
            // Image URLs for 4.png and 0.png
            const img4Url = 'http://ssfmym.kesug.com/20250410_033055.png';
            const img0Url = 'http://ssfmym.kesug.com/20250410_033234.png';
            
            // Preload images
            const img4 = new Image();
            img4.src = img4Url;
            
            const img0 = new Image();
            img0.src = img0Url;
            
            // Update status display
            function updateStatusDisplay() {
                statusDisplay.innerHTML = currentPlayerTurn();
            }
            
            // Handle cell click
            function handleCellClick(clickedCellEvent) {
                const clickedCell = clickedCellEvent.target.closest('.cell');
                if (!clickedCell) return;
                
                const clickedCellIndex = parseInt(clickedCell.getAttribute('data-index'));
                
                if (gameState[clickedCellIndex] !== '' || !gameActive) {
                    return;
                }
                
                handleCellPlayed(clickedCell, clickedCellIndex);
                handleResultValidation();
            }
            
            // Handle cell played
            function handleCellPlayed(clickedCell, clickedCellIndex) {
                gameState[clickedCellIndex] = currentPlayer;
                
                const img = clickedCell.querySelector('img');
                if (currentPlayer === 'X') {
                    img.src = img4Url;
                    img.alt = '4';
                } else {
                    img.src = img0Url;
                    img.alt = '0';
                }
                
                clickedCell.classList.add('filled');
            }
            
            // Handle result validation
            function handleResultValidation() {
                let roundWon = false;
                for (let i = 0; i < winningConditions.length; i++) {
                    const [a, b, c] = winningConditions[i];
                    const condition = gameState[a] && gameState[a] === gameState[b] && gameState[a] === gameState[c];
                    
                    if (condition) {
                        roundWon = true;
                        break;
                    }
                }
                
                if (roundWon) {
                    statusDisplay.innerHTML = winningMessage();
                    gameActive = false;
                    document.getElementById('restart-btn').style.display = 'inline-block';
                    return;
                }
                
                let roundDraw = !gameState.includes('');
                if (roundDraw) {
                    statusDisplay.innerHTML = drawMessage();
                    gameActive = false;
                    document.getElementById('restart-btn').style.display = 'inline-block';
                    return;
                }
                
                currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
                updateStatusDisplay();
            }
            
            // Handle restart game
            function handleRestartGame() {
                gameActive = true;
                currentPlayer = 'X';
                gameState = ['', '', '', '', '', '', '', '', ''];
                statusDisplay.innerHTML = currentPlayerTurn();
                document.querySelectorAll('.cell').forEach(cell => {
                    cell.classList.remove('filled');
                    const img = cell.querySelector('img');
                    img.src = '';
                    img.alt = '';
                });
                document.getElementById('restart-btn').style.display = 'none';
            }
            
            // Event listeners
            document.getElementById('board').addEventListener('click', handleCellClick);
            document.getElementById('restart-btn').addEventListener('click', handleRestartGame);
            
            // Initialize game
            updateStatusDisplay();
        });
    </script>
</body>
</html>