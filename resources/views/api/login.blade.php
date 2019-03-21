
<form  action="/apilogin" method="post" >
    {{csrf_field()}}

    账号 ：<input type="text" name="email"></br>
    密码 ：<input type="password" name="password"></br>
    <input type="hidden" value="{{$rediret}}" name="rediret">
    <input type="submit" value="登录">
</form>
