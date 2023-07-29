<!DOCTYPE html>

<html lang="id" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Website transaksi jual beli untuk Seleksi Asisten Lab Programming 2023">
    <title>@yield('title')</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <style>
        .circular-indicator {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: red;
            color: #fff;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        @keyframes slide-in-out {
            0% {
                transform: translateY(-100%);
            }

            10% {
                transform: translateY(0);
            }

            90% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-200%);
            }
        }

        .slideInOut {
            animation: slide-in-out 2s ease-in-out;
            transform: translateY(-200%);
        }
    </style>
    <script>
        var pusher = new Pusher('cd075e7da50dd8f19cff', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('labpro-monolith');

        channel.bind('new-keranjang', function(data) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/api/auth/self');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var userData = JSON.parse(xhr.responseText);
                        if (data.message.user_id === userData.data.user.id) {
                            var cartButtonBadge = document.getElementById('cart-button-badge');
                            if (cartButtonBadge) {
                                cartButtonBadge.textContent = data.message.count;
                            }
                        }
                    } else {
                        console.error('Error while fetching data:', xhr.status);
                    }
                }
            };
            xhr.send();
        });
    </script>
</head>

<body>
    @yield('body')
</body>

</html>
