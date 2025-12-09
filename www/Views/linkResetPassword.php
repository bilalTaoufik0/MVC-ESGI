<form action="/updatePassword" method="POST">
    <input type="password" name="pwd">
    <input type="password" name="pwdConfirm">
    <input type="hidden" name ="email" value=<?=$email?>>
    <input type="submit">
</form>