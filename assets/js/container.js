$(document).ready(function() {
    $(function() {
        let socket = new WebSocket('ws://socket-test.loc:3066');
        socket.onmessage = function(e) {
            let response = JSON.parse(e.data);
            console.log(response.token);
            console.log(response.message);
            $('#afterload_'+response.token).html(response.message)
        };
        socket.onopen = function(e) {
            socket.send( JSON.stringify({'action' : 'register', 'userKey' : '3456345'}) );
        };
    })
});