/**
 * Gemini Chat Integration
 * This file handles the integration with Google's Gemini API for the chatbot functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the chat interface once DOM is loaded
    initChatbot();
});

// Gemini API key
const GEMINI_API_KEY = 'AIzaSyC2dr21tn7ZM-MzjXidKSvD0vcvLgPEF2A';
const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

/**
 * Initialize the chatbot interface and event listeners
 */
function initChatbot() {
    const chatToggle = document.getElementById('chatToggle');
    const chatContainer = document.getElementById('chatContainer');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendButton = document.getElementById('sendButton');
    const closeButton = document.getElementById('closeChat');

    // Toggle chat visibility
    if (chatToggle) {
        chatToggle.addEventListener('click', function() {
            chatContainer.classList.toggle('chat-open');
            if (chatContainer.classList.contains('chat-open')) {
                chatInput.focus();
            }
        });
    }

    // Close chat when close button is clicked
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            chatContainer.classList.remove('chat-open');
        });
    }

    // Send message when button is clicked
    if (sendButton) {
        sendButton.addEventListener('click', sendMessage);
    }

    // Send message when Enter key is pressed
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    // Add welcome message
    addMessage('bot', 'Hello! I\'m your hotel supplies assistant. How can I help you today?');
}

/**
 * Send user message to Gemini API and display response
 */
async function sendMessage() {
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');
    const userMessage = chatInput.value.trim();
    
    if (!userMessage) return;
    
    // Display user message
    addMessage('user', userMessage);
    chatInput.value = '';
    
    // Show typing indicator
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'message bot-message typing-indicator';
    typingIndicator.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
    chatMessages.appendChild(typingIndicator);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    try {
        // Prepare request to Gemini API
        const response = await fetch(`${GEMINI_API_URL}?key=${GEMINI_API_KEY}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                contents: [{
                    parts: [{ text: userMessage }]
                }]
            })
        });
        
        const data = await response.json();
        
        // Remove typing indicator
        chatMessages.removeChild(typingIndicator);
        
        // Process and display response
        if (data.candidates && data.candidates[0] && data.candidates[0].content) {
            const botResponse = data.candidates[0].content.parts[0].text;
            addMessage('bot', botResponse);
        } else if (data.error) {
            addMessage('bot', `Sorry, I encountered an error: ${data.error.message || 'Unknown error'}`);
        } else {
            addMessage('bot', 'Sorry, I couldn\'t process your request. Please try again.');
        }
    } catch (error) {
        // Remove typing indicator
        chatMessages.removeChild(typingIndicator);
        
        // Display error message
        console.error('Error calling Gemini API:', error);
        addMessage('bot', 'Sorry, there was an error connecting to my brain. Please try again later.');
    }
}

/**
 * Add a message to the chat interface
 * @param {string} sender - 'user' or 'bot'
 * @param {string} text - The message text
 */
function addMessage(sender, text) {
    const chatMessages = document.getElementById('chatMessages');
    const messageElement = document.createElement('div');
    messageElement.className = `message ${sender}-message`;
    
    // Process markdown-like formatting in bot messages
    if (sender === 'bot') {
        // Convert line breaks to <br>
        text = text.replace(/\n/g, '<br>');
        
        // Convert **text** to <strong>text</strong>
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Convert *text* to <em>text</em>
        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Convert `code` to <code>code</code>
        text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    } else {
        // For user messages, just handle line breaks
        text = text.replace(/\n/g, '<br>');
    }
    
    messageElement.innerHTML = text;
    chatMessages.appendChild(messageElement);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}