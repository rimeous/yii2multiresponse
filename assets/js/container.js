
(function() {
    let config = window.widgetConfig;
    console.log(config);
    window.widgetSockets = {};

    $.each(config, function (index, widget) {
        let url = widget.url;
        let socket = window.widgetSockets[index] = new WebSocket(url);

        socket.onopen = function (e) {
            widget.containers.forEach(function (token) {
                console.log('register-' + token);
                socket.send(JSON.stringify({'action': 'register', 'token': token}));
            });
        };

        socket.onmessage = function (e) {
            let response = JSON.parse(e.data);
            let callbackFunction = eval("(" + widget.callback + ")");;
            callbackFunction(response);
        };
    });

    setTimeout(function () {
        console.log(window.state)
    }, 5000)
})();
