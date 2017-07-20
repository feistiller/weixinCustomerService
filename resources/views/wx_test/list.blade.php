<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>对话列表</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <style>
        .t-font{
            font-size: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="col-md-12">
        <table class="table">
            <thead class="thead-inverse">
            <tr class="t-font">
                <th>id</th>
                <th>用户id</th>
                <th>对话发起时间</th>
                <th>最后对话时间</th>
                <th>最后回复条数</th>
                <th>当前状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data as $item)
                <tr>
                    <th>{{$item->id}}</th>
                    <td>{{$item->useropenid}}</td>
                    <td>{{$item->createtime}}</td>
                    <td>{{$item->finalchattime}}</td>
                    <td>{{$item->finalchatnum}}</td>
                    <td>{{$item->status}}</td>
                    <td><a class="btn btn-info btn-sm" href="/talk?useropenid={{$item->useropenid}}">发起对话</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>