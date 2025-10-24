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
            }, {
                headers: {
                    'X-WP-Nonce': window.arcMaze?.nonce || ''
                },
                withCredentials: true
            });

            if (result.data.success && result.data.message) {
                setResponse(result.data.message);
            } else {
                setResponse('No response received from API');
            }
            setMessage('');
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.message;
            setResponse('Error: ' + errorMessage);
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
        <div className="flex flex-col h-[calc(100vh-200px)] max-w-7xl my-5 border border-gray-300 rounded-lg bg-white font-sans">
            <div className="flex-1 p-5 overflow-y-auto bg-gray-50 border-b border-gray-300">
                {response ? (
                    <div className="bg-white p-4 rounded-md shadow-sm whitespace-pre-wrap break-words">
                        {response}
                    </div>
                ) : (
                    <div className="text-gray-400 text-center py-10 italic">
                        AI responses will appear here...
                    </div>
                )}
            </div>

            <div className="p-5 bg-white flex gap-2.5 items-end">
                <textarea
                    className="flex-1 p-2.5 border border-gray-300 rounded font-sans text-sm resize-y min-h-[60px] focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 disabled:bg-gray-100 disabled:cursor-not-allowed"
                    placeholder="Type your message..."
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    onKeyPress={handleKeyPress}
                    disabled={loading}
                    rows={3}
                />
                <button
                    className="px-6 py-2.5 bg-blue-600 text-white border-none rounded cursor-pointer text-sm font-medium transition-colors h-fit hover:bg-blue-700 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed"
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
