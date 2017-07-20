<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>聊天</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    <div class="col-md-12">
        <div style="padding-top: 30px">
            <div style="border-style:solid;width: 100%;height: 600px">
                <div id="showText" style="border-bottom:solid;height: 400px;overflow-y: auto">
                </div>
                <div style="height: 200px;">
                    <textarea id="text" style="border-bottom: solid;width: 100%;height:150px"></textarea>
                    <button id="sendMessage" style="float: right;margin-right: 20px" class="btn btn-info btn-sm">发送消息
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
        integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
        crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {
        getUserTalk()
    })
    function GetQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)return unescape(r[2]);
        return null;
    }
    function getUserTalk() {
        $.ajax({
            type: 'GET',
            url: '/showTalk?useropenid=' + GetQueryString('useropenid'),
            success: function (result) {
                $('#showText').html(result)
            }
        })
    }
    $('#sendMessage').click(function () {
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/sendMessage?useropenid=' + GetQueryString('useropenid'),
            data: {
                content: $('#text').val()
            },
            success: function () {
                $('#text').val(null)
                getUserTalk()
            }
        })
    })

</script>
</body>
</html>