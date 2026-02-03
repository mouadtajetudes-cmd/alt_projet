import axios from 'axios';
import { io } from 'socket.io-client';

const GATEWAY_URL = 'http://localhost:6090';
const WS_URL = 'http://localhost:3001';

let socket = null;

window.testService = async (service) => {
    const card = document.querySelector(`[data-service="${service}"]`);
    const resultDiv = card.querySelector('.result');
    
    resultDiv.innerHTML = '⏳ Testing...';
    resultDiv.className = 'result loading';
    
    try {
        let url;
        if (service === 'gateway') {
            url = GATEWAY_URL;
        } else {
            url = `${GATEWAY_URL}/${service}/`;
        }
        
        console.log(`Testing ${service}: ${url}`);
        const response = await axios.get(url, { timeout: 5000 });
        
        const status = response.data.status || response.data.service || 'OK';
        resultDiv.innerHTML = `✅ ${status}`;
        resultDiv.className = 'result success';
        console.log(`✅ ${service} OK:`, response.data);
    } catch (error) {
        console.error(`❌ ${service} error:`, error);
        let message;
        if (error.code === 'ERR_NETWORK') {
            message = 'Network Error - Check if gateway is running';
        } else if (error.response) {
            message = error.response.data?.message || `HTTP ${error.response.status}`;
        } else {
            message = error.message;
        }
        resultDiv.innerHTML = `❌ ${message}`;
        resultDiv.className = 'result error';
    }
};

window.connectWS = () => {
    if (socket?.connected) return;
    
    const statusDiv = document.getElementById('status');
    const messagesDiv = document.getElementById('ws-messages');
    const connectBtn = document.getElementById('ws-connect');
    const disconnectBtn = document.getElementById('ws-disconnect');
    
    statusDiv.textContent = 'Connecting...';
    statusDiv.className = 'status connecting';
    
    socket = io(WS_URL, {
        transports: ['websocket', 'polling'],
        reconnection: true,
        reconnectionDelay: 1000,
        reconnectionAttempts: 5
    });
    
    socket.on('connect', () => {
        statusDiv.textContent = 'Connected';
        statusDiv.className = 'status connected';
        connectBtn.disabled = true;
        disconnectBtn.disabled = false;
        addMessage('System', 'Connected to WebSocket server');
        console.log('✅ WebSocket connected');
    });
    
    socket.on('disconnect', () => {
        statusDiv.textContent = 'Disconnected';
        statusDiv.className = 'status disconnected';
        connectBtn.disabled = false;
        disconnectBtn.disabled = true;
        addMessage('System', 'Disconnected from WebSocket server');
        console.log('❌ WebSocket disconnected');
    });
    
    socket.on('connect_error', (error) => {
        statusDiv.textContent = 'Connection Error';
        statusDiv.className = 'status disconnected';
        addMessage('Error', `Connection failed: ${error.message}`);
        console.error('WebSocket connection error:', error);
    });
    
    socket.on('message', (data) => {
        addMessage('Server', JSON.stringify(data));
    });
    
    socket.on('error', (error) => {
        addMessage('Error', error.message || 'Unknown error');
    });
};

window.disconnectWS = () => {
    if (socket) {
        socket.disconnect();
        socket = null;
    }
};

function addMessage(from, message) {
    const messagesDiv = document.getElementById('ws-messages');
    const messageEl = document.createElement('div');
    messageEl.className = 'ws-message';
    messageEl.innerHTML = `
        <span class="ws-from">${from}:</span>
        <span class="ws-text">${message}</span>
        <span class="ws-time">${new Date().toLocaleTimeString()}</span>
    `;
    messagesDiv.appendChild(messageEl);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 ALT Frontend initialized');
});
