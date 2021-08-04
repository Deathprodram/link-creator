<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>Создать короткую ссылку</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" hef="/assets/css/loader.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
        <style>
            input {
                margin-top: 20px;
            }
            #error-block {
                display: none;
                margin-top: 10px;
                background-color: red;
                padding: 5px;
                color: #333;
            }
            #success-block {
                display: none;
                margin-top: 10px;
                background-color: lawngreen;
                padding: 5px;
                color: #333;
            }
        </style>
    </head>
    <body>
        <div class="col-sm-6 col-sm-offset-3">
            <h3>Создайте себе короткую ссылку</h3>
            <div id="error-block"></div>
            <div id="success-block"></div>

            <form id="create-short-link">
                <input type="text" name="url" class="form-control" placeholder="Url-адрес">
                <input type="submit" class="btn btn-success btn-block" value="Создать">
            </form>
        </div>

        <div class="loader"></div>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#create-short-link').submit(function(e) {
            e.preventDefault();
            let url = $('input[name="url"]').val();
            let reg = new RegExp(
                /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/
            );

            if (!reg.test(url)) {
                errorBlock('Некорректный url-адрес');
                return false;
            }

            getAjaxPost({
                url: '/short_link/create',
                data: {url: url},
                loader: true
            }).then((res) => {
                    if (res.status) successBlock(
                        'Ваш url: <a href="'+ res.message +'" target="_blank">' + res.message + '</a>'
                    );
                    else errorBlock(res.message);
                });
        });

        // functions
        async function getAjaxPost(send_data) {
            return new Promise(function(resolve, reject){
                let beforeSend = null;
                let complete = null;
                if (send_data.hasOwnProperty('loader')) {
                    beforeSend = $('.loader').show();
                    complete = $('.loader').hide();
                }
                $.ajax({
                    url: send_data.url,
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: send_data.data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: () => beforeSend,
                    complete: () => complete,
                    success: data => resolve(data),
                });
            });
        }

        function errorBlock(error_text) {
            $('#error-block').html(error_text);
            $('#success-block').fadeOut();
            $('#error-block').fadeIn();
            $('input[name="url"]').css('borderColor', 'red');
        }

        function successBlock(success_text) {
            $('#success-block').html(success_text);
            $('#error-block').fadeOut();
            $('#success-block').fadeIn();
            $('input[name="url"]').css('borderColor', 'lawngreen');
        }
    </script>
</html>
