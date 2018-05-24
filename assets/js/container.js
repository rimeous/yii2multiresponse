$(document).ready(function() {
    $(function() {
        let config = JSON.parse($('#afterload_config').text(), function(key, value) {
            if (typeof value === "string" &&
                value.startsWith("/Function(") &&
                value.endsWith(")/")) {
                value = value.substring(10, value.length - 2);
                return eval("(" + value + ")");
            }
            return value;
        });

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
                let callbackFunction = widget.callback;
                callbackFunction(response);
            };
        });
    })
});