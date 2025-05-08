// Gemini Chatbot Frontend Logic
// This file creates a simple chat UI and handles communication with Gemini API

const GEMINI_API_KEY = "AIzaSyAikYIHfLwwr9pxxfMp8pwNEevIKkIa0zs";
const GEMINI_API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=" + GEMINI_API_KEY;

function createChatbotBox() {
    // Create chat container
    const chatContainer = document.createElement('div');
    chatContainer.id = 'gemini-chatbot-container';
    chatContainer.style.position = 'fixed';
    chatContainer.style.bottom = '30px';
    chatContainer.style.right = '30px';
    chatContainer.style.width = '350px';
    chatContainer.style.maxHeight = '500px';
    chatContainer.style.background = '#fff';
    chatContainer.style.border = '1px solid #ccc';
    chatContainer.style.borderRadius = '12px';
    chatContainer.style.boxShadow = '0 4px 16px rgba(0,0,0,0.15)';
    chatContainer.style.zIndex = '9999';
    chatContainer.style.display = 'flex';
    chatContainer.style.flexDirection = 'column';
    chatContainer.style.overflow = 'hidden';
    
    // Header
    const header = document.createElement('div');
    header.style.background = '#2d7be5';
    header.style.color = '#fff';
    header.style.padding = '12px';
    header.style.fontWeight = 'bold';
    header.style.textAlign = 'center';
    header.style.display = 'flex';
    header.style.alignItems = 'center';
    header.style.justifyContent = 'space-between';
    
    // Title
    const title = document.createElement('span');
    title.innerText = 'HoteliaSmart Chatbot';
    header.appendChild(title);

    // Minimize button
    const minimizeBtn = document.createElement('button');
    minimizeBtn.innerHTML = '&#8211;';
    minimizeBtn.title = 'Minimize';
    minimizeBtn.style.background = 'transparent';
    minimizeBtn.style.border = 'none';
    minimizeBtn.style.color = '#fff';
    minimizeBtn.style.fontSize = '20px';
    minimizeBtn.style.cursor = 'pointer';
    minimizeBtn.style.marginLeft = '8px';
    minimizeBtn.style.fontWeight = 'bold';
    header.appendChild(minimizeBtn);

    chatContainer.appendChild(header);

    // Messages area
    const messages = document.createElement('div');
    messages.id = 'gemini-chatbot-messages';
    messages.style.flex = '1';
    messages.style.padding = '12px';
    messages.style.overflowY = 'auto';
    messages.style.background = '#f7f7f7';
    chatContainer.appendChild(messages);

    // Input area
    const inputArea = document.createElement('div');
    inputArea.style.display = 'flex';
    inputArea.style.borderTop = '1px solid #eee';
    inputArea.style.background = '#fff';

    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = 'Type your message...';
    input.style.flex = '1';
    input.style.padding = '10px';
    input.style.border = 'none';
    input.style.outline = 'none';
    inputArea.appendChild(input);

    const sendBtn = document.createElement('button');
    sendBtn.innerText = 'Send';
    sendBtn.style.background = '#2d7be5';
    sendBtn.style.color = '#fff';
    sendBtn.style.border = 'none';
    sendBtn.style.padding = '0 30px';
    sendBtn.style.cursor = 'pointer';
    sendBtn.style.fontWeight = 'bold';
    sendBtn.style.borderRadius = '0 0 12px 0';
    inputArea.appendChild(sendBtn);

    chatContainer.appendChild(inputArea);

    // Suggestions area
    const suggestionArea = document.createElement('div');
    suggestionArea.style.display = 'flex';
    suggestionArea.style.flexWrap = 'wrap';
    suggestionArea.style.gap = '8px';
    suggestionArea.style.padding = '10px 12px 0 12px';
    suggestionArea.style.background = '#f7f7f7';
    suggestionArea.style.borderTop = '1px solid #eee';
    suggestionArea.style.justifyContent = 'flex-start';
    chatContainer.appendChild(suggestionArea);

    // Append to body
    document.body.appendChild(chatContainer);

    // --- Suggestion logic ---
    function getRandomSuggestions(arr, n) {
        const copy = arr.slice();
        const result = [];
        while (result.length < n && copy.length) {
            const idx = Math.floor(Math.random() * copy.length);
            result.push(copy.splice(idx, 1)[0]);
        }
        return result;
    }
    function renderSuggestions() {
        suggestionArea.innerHTML = '';
        const randomSuggestions = getRandomSuggestions(suggestions, 2);
        randomSuggestions.forEach(function(s) {
            const btn = document.createElement('button');
            btn.innerText = s;
            btn.style.background = '#e3f0ff';
            btn.style.color = '#2d7be5';
            btn.style.border = 'none';
            btn.style.borderRadius = '6px';
            btn.style.padding = '6px 12px';
            btn.style.margin = '0';
            btn.style.cursor = 'pointer';
            btn.style.fontSize = '13px';
            btn.style.transition = 'background 0.2s';
            btn.onmouseover = function() { btn.style.background = '#d0e6ff'; };
            btn.onmouseout = function() { btn.style.background = '#e3f0ff'; };
            btn.onclick = function() {
                input.value = s;
                input.focus();
            };
            suggestionArea.appendChild(btn);
        });
    }
    renderSuggestions();
    // Optionally, refresh suggestions every time the chatbot is opened or after sending a message
    // setInterval(renderSuggestions, 30000); // Uncomment to refresh every 30s

    // Minimize logic
    let minimized = false;
    const contentElements = [messages, inputArea, suggestionArea];
    minimizeBtn.addEventListener('click', function() {
        minimized = !minimized;
        contentElements.forEach(function(el) {
            el.style.display = minimized ? 'none' : '';
        });
        minimizeBtn.innerHTML = minimized ? '&#9633;' : '&#8211;';
        minimizeBtn.title = minimized ? 'Restore' : 'Minimize';
        chatContainer.style.height = minimized ? 'auto' : '';
        chatContainer.style.maxHeight = minimized ? 'unset' : '500px';
    });
    // Send message handler
    function appendMessage(text, sender) {
        const msg = document.createElement('div');
        msg.style.margin = '8px 0';
        msg.style.padding = '8px 12px';
        msg.style.borderRadius = '8px';
        msg.style.maxWidth = '80%';
        msg.style.wordBreak = 'break-word';
        if (sender === 'user') {
            msg.style.background = '#e3f0ff';
            msg.style.alignSelf = 'flex-end';
            msg.style.marginLeft = 'auto';
        } else {
            msg.style.background = '#f1f1f1';
            msg.style.alignSelf = 'flex-start';
            msg.style.marginRight = 'auto';
        }
        msg.innerText = text;
        messages.appendChild(msg);
        messages.scrollTop = messages.scrollHeight;
    }

    function setLoading(loading) {
        if (loading) {
            sendBtn.disabled = true;
            sendBtn.innerText = '...';
        } else {
            sendBtn.disabled = false;
            sendBtn.innerText = 'Send';
        }
    }

    function sendMessage() {
        const userMsg = input.value.trim();
        if (!userMsg) return;
        appendMessage(userMsg, 'user');
        input.value = '';
        setLoading(true);
        // Call Gemini API
        fetch(GEMINI_API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                contents: [{ parts: [{ text: userMsg }] }]
            })
        })
        .then(res => res.json())
        .then(data => {
            let botMsg = 'Sorry, I could not understand.';
            // Debug: log the full response for troubleshooting
            console.log('Gemini API response:', data);
            if (data && data.candidates && data.candidates[0] && data.candidates[0].content && data.candidates[0].content.parts && data.candidates[0].content.parts[0] && data.candidates[0].content.parts[0].text) {
                botMsg = data.candidates[0].content.parts[0].text;
            } else if (data && data.promptFeedback && data.promptFeedback.blockReason) {
                botMsg = 'Gemini API blocked the request: ' + data.promptFeedback.blockReason;
            } else if (data && data.error && data.error.message) {
                botMsg = 'Gemini API error: ' + data.error.message;
            }
            appendMessage(botMsg, 'bot');
        })
        .catch(() => {
            appendMessage('Error connecting to Gemini API.', 'bot');
        })
        .finally(() => setLoading(false));
    }

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
    sendBtn.addEventListener('click', sendMessage);
}

// Only create the chatbot box if not already present
if (!window.geminiChatbotLoaded) {
    window.geminiChatbotLoaded = true;
    window.addEventListener('DOMContentLoaded', createChatbotBox);
}

    const suggestions = [
        "How can I automate hotel bookings?",
        "What smart room controls do you offer?",
        "Tell me about your hotel IoT devices.",
        "How do I request housekeeping via smart systems?",
        "Can I control lighting and AC remotely?",
        "What are your smart check-in solutions?",
        "Do you provide guest room tablets?",
        "How does your energy management system work?",
        "Can I integrate your system with my PMS?",
        "What security features are available?",
        "How do I order room service using smart devices?",
        "Show me your smart minibar solutions.",
        "Do you offer voice assistant integration?",
        "How can I monitor occupancy in real time?",
        "What is your smart TV solution for hotels?"
    ];
    const suggestionBox = document.createElement('div');
    suggestionBox.style.position = 'absolute';
    suggestionBox.style.left = '0';
    suggestionBox.style.right = '0';
    suggestionBox.style.bottom = '44px';
    suggestionBox.style.background = '#fff';
    suggestionBox.style.border = '1px solid #ccc';
    suggestionBox.style.borderTop = 'none';
    suggestionBox.style.zIndex = '10000';
    suggestionBox.style.maxHeight = '160px';
    suggestionBox.style.overflowY = 'auto';
    suggestionBox.style.display = 'none';
    suggestionBox.style.fontSize = '14px';
    suggestionBox.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
    inputArea.style.position = 'relative';
    inputArea.appendChild(suggestionBox);
    function showSuggestions(filtered) {
        suggestionBox.innerHTML = '';
        if (!filtered.length) {
            suggestionBox.style.display = 'none';
            return;
        }
        filtered.forEach(function(s, idx) {
            const item = document.createElement('div');
            item.innerText = s;
            item.style.padding = '8px 12px';
            item.style.cursor = 'pointer';
            item.onmouseover = function() { item.style.background = '#f0f6ff'; };
            item.onmouseout = function() { item.style.background = '#fff'; };
            item.onclick = function() {
                input.value = s;
                suggestionBox.style.display = 'none';
                input.focus();
            };
            suggestionBox.appendChild(item);
        });
        suggestionBox.style.display = 'block';
    }
    input.addEventListener('input', function() {
        const val = input.value.trim().toLowerCase();
        if (!val) {
            suggestionBox.style.display = 'none';
            return;
        }
        const filtered = suggestions.filter(s => s.toLowerCase().includes(val));
        showSuggestions(filtered);
    });
    input.addEventListener('blur', function() {
        setTimeout(() => { suggestionBox.style.display = 'none'; }, 150);
    });
    input.addEventListener('focus', function() {
        const val = input.value.trim().toLowerCase();
        if (val) {
            const filtered = suggestions.filter(s => s.toLowerCase().includes(val));
            showSuggestions(filtered);
        }
    });