<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go Live Example</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .container {
            text-align: center;
        }

        .go-live-button {
            font-size: 18px;
            padding: 10px 20px;
            border: none;
            color: white;
            background-color: #e74c3c;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .go-live-button:hover {
            background-color: #c0392b;
        }

        .live-stream {
            margin-top: 20px;
            padding: 20px;
            border: 2px solid #e74c3c;
            border-radius: 10px;
            background-color: white;
            position: relative;
        }

        .hidden {
            display: none;
        }

        .stream-placeholder {
            width: 100%;
            height: 300px;
            background-color: #ddd;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .countdown {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        video {
            width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <button id="goLiveButton" class="go-live-button">🔴 Go Live</button>
        <div id="liveStream" class="live-stream hidden">
            <div id="countdown" class="countdown">3</div>
            <div id="streamPlaceholder" class="hidden">
                <p>Your live stream:</p>
                <video id="webcam" autoplay></video>
            </div>
        </div>
    </div>
    <script>
        // JavaScript code
        document.addEventListener('DOMContentLoaded', () => {
            const goLiveButton = document.getElementById('goLiveButton');
            const liveStream = document.getElementById('liveStream');
            const countdownElem = document.getElementById('countdown');
            const streamPlaceholder = document.getElementById('streamPlaceholder');
            const webcam = document.getElementById('webcam');
            
            let countdownTimer;

            async function startWebcam() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    webcam.srcObject = stream;
                } catch (error) {
                    console.error('Error accessing webcam:', error);
                }
            }

            function startCountdown() {
                let timeLeft = 3;
                countdownElem.textContent = timeLeft;

                countdownTimer = setInterval(() => {
                    timeLeft -= 1;
                    countdownElem.textContent = timeLeft;

                    if (timeLeft <= 0) {
                        clearInterval(countdownTimer);
                        countdownElem.textContent = 'Go Live!';
                        setTimeout(() => {
                            countdownElem.style.display = 'none';
                            streamPlaceholder.classList.remove('hidden');
                            startWebcam();
                        }, 1000);
                    }
                }, 1000);
            }

            goLiveButton.addEventListener('click', () => {
                if (liveStream.classList.contains('hidden')) {
                    liveStream.classList.remove('hidden');
                    goLiveButton.textContent = '🔴 Live';
                    startCountdown();
                } else {
                    liveStream.classList.add('hidden');
                    goLiveButton.textContent = '🔴 Go Live';
                    countdownElem.style.display = 'block';
                    streamPlaceholder.classList.add('hidden');
                    clearInterval(countdownTimer);
                    countdownElem.textContent = '3';
                }
            });
        });
    </script>
</body>
</html>
