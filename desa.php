<?php
include_once "conexion.php";
include_once "global/SessionSGO.php";
SessionSGO::init();


$sql = "SELECT EnDesarrollo FROM desarrollo LIMIT 1";
$enDesarrollo = mysql_fetch_object(mysql_query($sql))->EnDesarrollo;

if (sizeof($_POST)) 
{
    $clave = isset($_POST['pass']) ? md5($_POST['pass']) : '';
    $sql = "SELECT Pass = '$clave' as validacion FROM desarrollo LIMIT 1";
    $validacion = mysql_fetch_object(mysql_query($sql))->validacion;

    if (isset($_POST['acceso']) && $validacion) 
        SessionSGO::set('desarrollador', 1 - SessionSGO::get('desarrollador'));

    if (isset($_POST['cambiarmodo']) && $validacion) 
    {
        $cambiaModo = 1 - $enDesarrollo;

        $sql = "UPDATE desarrollo SET EnDesarrollo = '$cambiaModo' LIMIT 1";
        mysql_query($sql);

        $sql = "SELECT EnDesarrollo FROM desarrollo LIMIT 1";
        $enDesarrollo = mysql_fetch_object(mysql_query($sql))->EnDesarrollo;
    }

}


if (isset($_GET['config']) || sizeof($_POST)) { ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Desarrollo </title>
    </head>
    <body>
        <form action="" method="POST">
            <label for="pass">Clave</label>
            <input type="password" id="pass" name="pass" value=""><br>
            
            <label>modo: <?php echo $enDesarrollo ? 'Mantenimiento' : 'Producción' ?></label>
            <button name="cambiarmodo">Cambiar a <?php echo $enDesarrollo ? 'Producción' : 'Mantenimiento' ?></button><br>
            <label>acceso: <?php echo SessionSGO::get('desarrollador') ? 'Desarrollador' : 'Usuario Web' ?></label>
            <button name="acceso">Cambiar a <?php echo SessionSGO::get('desarrollador') ? 'Usuario Web' : 'Desarrollador' ?></button><br>
        </form>

        <script type="text/javascript">

            document.getElementById("pass").onkeydown = function(e)
            {
                if (e.keyCode == 13) e.preventDefault();
            };

        </script>

    </body>
    </html>

<?php } ?>
