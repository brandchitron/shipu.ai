<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        /* Reset and base styles */
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
        
        /* Game board styles */
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
            max-width: 60%;
            max-height: 60%;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.3s ease;
        }
        
        .cell.filled img {
            opacity: 1;
            transform: scale(1);
        }
        
        /* Game status and controls */
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
        
        /* Footer styles */
        .footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
        }
        
        /* Responsive adjustments */
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
                <button id="restart-btn" style="display: none;">New Game</button>
            </div>
        </div>
        
        <div class="footer">
            Credit: ShiPu Ai (ssfmym.kesug.com/ai)<br>
            Powered by LumeTech Co. Ltd
        </div>
    </div>

    <script>
        // Base64 encoded images for 4.png and 0.png
        // In a real implementation, you would replace these with actual image paths
        // or use PHP to encode your actual images
        const img4Base64 = 'data:image/png;base64,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';
        const img0Base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAACXBIWXMAAAsTAAALEwEAmpwYAAAGFUlEQVR4nO2dW4xdUxjHf21pq6WUUlpKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWUUkoppZRSSimllFJKKaWU