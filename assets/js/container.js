$(document).ready(function() {
    $(function() {
        let config = JSON.parse($('#afterload_config').text());
        let url = config.TestWidget.url;
        let socket = new WebSocket(url);
        socket.onopen = function(e) {
            config.TestWidget.containers.forEach(function (token) {
                socket.send(JSON.stringify({'action': 'register', 'token': token}));
            });
        };

        socket.onmessage = function(e) {
            let response = JSON.parse(e.data);
            console.log(response.token);
            console.log(response.message);
            $('#afterload_'+response.token).html(response.message)
        };
    })
});