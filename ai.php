
<?php
// Define the API Key
$apiKey = 'AIzaSyA4cddcokk62BCdoF_EgGnXb0hchGGa8Bo';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShiPu AI</title>
  <style>
    :root {
      --background-light: #ffffff;
      --foreground-light: #000000;
      --background-dark: #111827;
      --foreground-dark: #f9fafb;
      --primary-light: #3b82f6;
      --primary-dark: #ffffff;
      --muted-foreground-light: #6b7280;
      --muted-foreground-dark: #9ca3af;
      --input-border: #e5e7eb;
      --message-bg-light: #f3f4f6;
      --message-bg-dark: #1f2937;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: var(--background-light);
      color: var(--foreground-light);
      transition: background-color 0.3s, color 0.3s;
    }

    body.dark {
      background-color: var(--background-dark);
      color: var(--foreground-dark);
    }

    main {
      display: flex;
      flex-direction: column;
      height: 100vh;
      max-width: 36rem;
      margin: auto;
    }

    .header {
      padding: 1rem;
      text-align: center;
    }

    .header h1 {
      font-size: 1.25rem;
      font-weight: 600;
    }

    .header p {
      font-size: 0.75rem;
      color: var(--muted-foreground-light);
    }

    body.dark .header p {
      color: var(--muted-foreground-dark);
    }

    .content {
      flex: 1;
      overflow-y: auto;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .message {
      max-width: 80%;
      border-radius: 0.75rem;
      padding: 0.5rem 0.75rem;
      font-size: 0.75rem;
      position: relative;
    }

    .message[data-role="user"] {
      align-self: flex-end;
      background-color: var(--primary-light);
      color: #fff;
    }

    .message[data-role="assistant"] {
      align-self: flex-start;
      background-color: var(--message-bg-light);
      color: var(--foreground-light);
    }

    body.dark .message[data-role="assistant"] {
      background-color: var(--message-bg-dark);
      color: var(--foreground-dark);
    }

    .chat-form {
      display: flex;
      align-items: center;
      border: 1px solid var(--input-border);
      padding: 0.5rem 0.75rem;
      margin: 0.5rem 1rem 1rem;
      border-radius: 9999px;
      position: relative;
    }

    .chat-input {
      flex: 1;
      border: none;
      outline: none;
      background: transparent;
      font-size: 0.85rem;
      color: inherit;
    }

    .submit-button {
      background: none;
      border: none;
      font-size: 1.25rem;
      cursor: pointer;
      color: var(--primary-light);
    }

    body.dark .submit-button {
      color: var(--primary-dark);
    }

    .copy-btn {
      position: absolute;
      top: 5px;
      right: 5px;
      background: transparent;
      border: none;
      font-size: 0.75rem;
      color: gray;
      cursor: pointer;
    }

    .footer {
      text-align: center;
      padding: 0.5rem;
      font-size: 0.7rem;
      color: var(--muted-foreground-light);
    }

    body.dark .footer {
      color: var(--muted-foreground-dark);
    }

    .toggle-darkmode {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
      font-size: 2rem;
    }
  </style>
</head>
<body>
  <main>
    <div class="toggle-darkmode" onclick="toggleDarkMode()">☀︎</div>
    <div class="header">
      <h1>ShiPu AI</h1>
      <p>Created by <a href="https://adibhaialpha.github.io/portfolio" target="_blank">CHITRON BHATTACHARJEE</a><br><br><h2>Dear ShiPu Ai user,</h2>How can I assist you today?</p>
    </div>
    <div class="content" id="chatContent"></div>
    <form id="chatForm" class="chat-form">
      <textarea id="chatInput" class="chat-input" placeholder="Ask something..."></textarea>
      <button type="submit" class="submit-button">➤</button>
    </form>
    <div class="footer">© 2025 ShiPu AI. Powered by LumeTech Co. Ltd.</div>
  </main>

  <script type="module">
    import { GoogleGenAI } from "@google/genai";  

    const apiKey = 'AIzaSyA4cddcokk62BCdoF_EgGnXb0hchGGa8Bo'; // Your API key
    const ai = new GoogleGenAI({ apiKey });

    const chatContent = document.getElementById('chatContent');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');

    const messages = JSON.parse(localStorage.getItem('shippu_messages') || '[]');
    messages.forEach(msg => appendMessage(msg.role, msg.content));

    chatForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const content = chatInput.value.trim();
      if (!content) return;
      appendMessage('user', content);
      chatInput.value = '';

      appendMessage('assistant', 'Typing...', true);

      const response = await ai.models.generateContent({
        model: "gemini-2.0-flash",
        contents: content,
      });

      const lastMsg = chatContent.querySelector('.message[data-typing]');
      if (lastMsg) lastMsg.remove();
      appendMessage('assistant', response.text || 'Sorry, something went wrong.');
    });

    function appendMessage(role, content, typing = false) {
      const msg = document.createElement('div');
      msg.className = 'message';
      msg.dataset.role = role;
      if (typing) msg.dataset.typing = 'true';
      msg.textContent = content;
      if (role === 'assistant' && !typing) {
        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-btn';
        copyBtn.textContent = '📋';
        copyBtn.onclick = () => navigator.clipboard.writeText(content);
        msg.appendChild(copyBtn);
      }
      chatContent.appendChild(msg);
      chatContent.scrollTop = chatContent.scrollHeight;

      messages.push({role, content});
      localStorage.setItem('shippu_messages', JSON.stringify(messages));
    }

    function toggleDarkMode() {
      document.body.classList.toggle('dark');
    }
  </script>
</body>
</html>

