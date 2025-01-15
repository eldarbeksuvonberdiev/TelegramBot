import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo.channel(`orderStatus`)
    .listen('OrderStatusEvent', (e) => {
        console.log('Received Event Data:', e);
        const messageList = document.getElementById('newMessage');
    });
// console.log('File URL:', e.file);

// const messageList = document.getElementById('newMessage');
// const newMessage = document.createElement('h5');


// newMessage.innerHTML = `
// <strong style="color: red">
// ${e.sender_id == userId ? 'You' : toUser.name.charAt(0).toUpperCase() + toUser.name.slice(1)}
// : </strong>${e.message.msg}
// `;

// if (e.sender_id !== userId) {
//     // messageList.appendChild(newMessage);
//     messageList.append(newMessage);
// }