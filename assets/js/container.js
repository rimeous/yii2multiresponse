$(document).ready(function() {
    $(function() {
        let config = JSON.parse($('#afterload_config').text());

        $.each(config.block_html, function(index, widget) {
            let url = widget.url;
            let socket = new WebSocket(url);
            socket.onopen = function(e) {
                widget.containers.forEach(function (token) {
                    console.log('register-' + token);
                    socket.send(JSON.stringify({'action': 'register', 'token': token}));
                });
            };

            socket.onmessage = function(e) {
                let response = JSON.parse(e.data);
                console.log(response.token);
                console.log(response.message);
                $('#afterload_'+response.token).html(response.message)
            };
        });
    })
});