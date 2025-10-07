import { useState } from '@wordpress/element';
import axios from 'axios';
import './style.css';

const App = () => {
    const [message, setMessage] = useState('');
    const [response, setResponse] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSend = async () => {
        if (!message.trim()) return;

        setLoading(true);
        setResponse('');

        try {
            const result = await axios.post('/wp-json/arc-maze/v1/message', {
                message: message
            });

            setResponse(result.data.message || 'Success');
            setMessage('');
        } catch (error) {
            setResponse('Error: ' + (error.response?.data?.message || error.message));
        } finally {
            setLoading(false);
        }
    };

    const handleKeyPress = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    return (
        <div className="arc-maze-chat-container">
            <div className="arc-maze-response-area">
                {response ? (
                    <div className="arc-maze-response">
                        {response}
                    </div>
                ) : (
                    <div className="arc-maze-placeholder">
                        AI responses will appear here...
                    </div>
                )}
            </div>

            <div className="arc-maze-input-area">
                <textarea
                    className="arc-maze-input"
                    placeholder="Type your message..."
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    onKeyPress={handleKeyPress}
                    disabled={loading}
                    rows={3}
                />
                <button
                    className="arc-maze-send-button"
                    onClick={handleSend}
                    disabled={loading || !message.trim()}
                >
                    {loading ? 'Sending...' : 'Send'}
                </button>
            </div>
        </div>
    );
};

export default App;
