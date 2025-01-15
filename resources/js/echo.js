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




window.orderData = {
    status: null,
};

window.Echo
    .channel('orderStatus')
    .listen('OrderStatusEvent', (e) => {
        // console.log('Status:', e.status);
        window.orderData.status = e.status;
    });
window.Echo
    .channel('orderId')
    .listen('OrderIdEvent', (e) => {
        console.log('OrderId:', e.orderId);
        const status = window.orderData.status;
        const messageList = document.getElementById(`statusOfOrder_${e.orderId}`);
        const newMessage = document.createElement('div');

        messageList.innerHTML = '';

        let buttonClass = window.orderData.status === 0 ? 'btn-danger' :
            window.orderData.status === 1 ? 'btn-primary' : 'btn-success';

        let buttoMessage = window.orderData.status === 0 ? 'Rejected' : window.orderData.status === 1 ? 'Given' : 'Accepted';

        newMessage.innerHTML = `
            <button class="btn ${buttonClass}">
                ${buttoMessage}
            </button>
        `;
        messageList.append(newMessage);

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