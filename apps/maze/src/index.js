import { render } from '@wordpress/element';
import App from './App';

const root = document.getElementById('arc-maze-admin-root');

if (root) {
    render(<App />, root);
}
