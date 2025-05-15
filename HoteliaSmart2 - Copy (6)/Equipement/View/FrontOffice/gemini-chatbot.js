// Gemini Chatbot Frontend Logic
// This file creates a simple chat UI and handles communication with Gemini API

// WARNING: Never expose your Groq API key in frontend code in production!
// This is for demo/test purposes only. Use a backend proxy for real deployments.
const GROQ_API_KEY = "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";
const GROQ_API_URL = "https://api.groq.com/openai/v1/chat/completions";

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

    // --- Drag logic ---
    let isDragging = false;
    let dragOffsetX = 0;
    let dragOffsetY = 0;
    header.style.cursor = 'move';
    header.addEventListener('mousedown', function(e) {
        if (e.target === minimizeBtn) return; // Don't drag if clicking minimize
        isDragging = true;
        dragOffsetX = e.clientX - chatContainer.getBoundingClientRect().left;
        dragOffsetY = e.clientY - chatContainer.getBoundingClientRect().top;
        document.body.style.userSelect = 'none';
    });
    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        chatContainer.style.transition = 'none';
        let newLeft = e.clientX - dragOffsetX;
        let newTop = e.clientY - dragOffsetY;
        // Boundaries (keep at least 40px visible on each side)
        const minLeft = 40;
        const minTop = 10;
        const maxLeft = window.innerWidth - chatContainer.offsetWidth - 40;
        const maxTop = window.innerHeight - chatContainer.offsetHeight - 10;
        chatContainer.style.left = Math.min(Math.max(newLeft, minLeft), maxLeft) + 'px';
        chatContainer.style.top = Math.min(Math.max(newTop, minTop), maxTop) + 'px';
        chatContainer.style.right = '';
        chatContainer.style.bottom = '';
        chatContainer.style.position = 'fixed';
    });
    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            chatContainer.style.transition = '';
            document.body.style.userSelect = '';
        }
    });
    // Make the chatbot absolutely positioned for dragging
    chatContainer.style.left = chatContainer.style.right;
    chatContainer.style.top = chatContainer.style.bottom;
    chatContainer.style.right = '';
    chatContainer.style.bottom = '';

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

    // Voice-to-Text Button
    const voiceBtn = document.createElement('button');
    voiceBtn.innerHTML = '<span style="font-size:18px;">🎤</span>';
    voiceBtn.title = 'Voice to Text';
    voiceBtn.style.background = '#e3f0ff';
    voiceBtn.style.color = '#2d7be5';
    voiceBtn.style.border = 'none';
    voiceBtn.style.padding = '0 18px';
    voiceBtn.style.cursor = 'pointer';
    voiceBtn.style.fontWeight = 'bold';
    voiceBtn.style.borderRadius = '0 0 0 12px';
    voiceBtn.style.marginRight = '2px';
    inputArea.appendChild(voiceBtn);

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

    // Voice Recognition Logic
    let recognizing = false;
    let recognition;
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'en-US';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.onstart = function() {
            recognizing = true;
            voiceBtn.style.background = '#cbe7ff';
            voiceBtn.innerHTML = '<span style="font-size:18px;">🔴</span>';
        };
        recognition.onend = function() {
            recognizing = false;
            voiceBtn.style.background = '#e3f0ff';
            voiceBtn.innerHTML = '<span style="font-size:18px;">🎤</span>';
        };
        recognition.onerror = function(e) {
            recognizing = false;
            voiceBtn.style.background = '#e3f0ff';
            voiceBtn.innerHTML = '<span style="font-size:18px;">🎤</span>';
            alert('Voice recognition error: ' + e.error);
        };
        recognition.onresult = function(event) {
            if (event.results && event.results[0] && event.results[0][0]) {
                input.value = event.results[0][0].transcript;
                input.focus();
            }
        };
        voiceBtn.onclick = function() {
            if (recognizing) {
                recognition.stop();
                return;
            }
            recognition.start();
        };
    } else {
        voiceBtn.disabled = true;
        voiceBtn.title = 'Voice recognition not supported in this browser';
    }

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
        // Call OpenAI API (GPT-4o)
        fetch(GROQ_API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + GROQ_API_KEY
            },
            body: JSON.stringify({
                model: 'llama3-8b-8192',
                messages: [
                    { role: 'system', content: 'You are HoteliaSmart, an expert assistant for smart cleaning equipment. Only discuss robotic vacuums, smart mops, automated scrubbers, and other AI-powered cleaning devices. Provide technical specifications, troubleshooting, and usage tips. Politely decline any non-cleaning related questions.' },
                    { role: 'user', content: userMsg }
                ],
                max_tokens: 512
            })
        })
        .then(res => res.json())
        .then(data => {
            let botMsg = 'Sorry, I could not understand.';
            // Debug: log the full response for troubleshooting
            console.log('Groq API response:', data);
            if (data && data.choices && data.choices[0] && data.choices[0].message && data.choices[0].message.content) {
                botMsg = data.choices[0].message.content.trim();
            } else if (data && data.error && data.error.message) {
                botMsg = 'Groq API error: ' + data.error.message;
            }
            appendMessage(botMsg, 'bot');
        })
        .catch(() => {
            appendMessage('Error connecting to Groq API.', 'bot');
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
    "What types of smart equipment does Hotelia Smart offer?",
    "How can I manage smart equipment remotely through Hotelia Smart?",
    "Is your smart equipment compatible with existing hotel infrastructure?",
    "Can Hotelia Smart monitor the status of connected equipment?",
    "How does Hotelia Smart help with predictive maintenance for smart equipment?",
    "Can I automate equipment usage schedules with Hotelia Smart?",
    "What energy-saving smart devices are available in Hotelia Smart?",
    "Does Hotelia Smart support IoT-based cleaning or service equipment?",
    "How can I track smart equipment performance in real-time?",
    "Is there a dashboard to control all smart equipment in Hotelia Smart?",
    "What security features are built into your smart equipment?",
    "Can Hotelia Smart send alerts for malfunctioning equipment?",
    "Does your smart equipment integrate with hotel room automation systems?",
    "How does Hotelia Smart help optimize equipment efficiency?",
    "Are Hotelia Smart's smart devices eco-friendly and sustainable?"
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