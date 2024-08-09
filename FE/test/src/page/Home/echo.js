import Echo from 'laravel-echo';
import io from 'socket.io-client';

window.Pusher = require('pusher-js'); // Nếu bạn dùng Pusher

const echo = new Echo({
    broadcaster: 'socket.io', // Hoặc 'pusher' nếu dùng Pusher
    host: window.location.hostname + ':6001', // Cổng của server
    client: io,
});

export default echo;