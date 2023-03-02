<?php
require_once('mysql_model.php');
require_once('functions.php');

$function = new LoginHelper();
$sqlConn = new connect();
if($_SERVER['REQUEST_METHOD'] ==="POST")
{
    $message_err =" ";
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $reNewPassword = $_POST['re_ewPassword'];
    $updateDay =  date("Y.m.d",time());

     $resultArray = $function->checkRegister(null,null, $newPassword, $reNewPassword);

    if(is_array($resultArray))
     {
         $sql = "SELECT username, email,password FROM users WHERE password LIKE \"$oldPassword\" ";
         $result = $sqlConn->dbConnet() -> query($sql);
         $fundElement = $result->fetchAll();
//         print_r($fundElement);
         if(count($fundElement)>0)
         {
             $old_pass = $fundElement[0][2];

            $sqlUpdate = "UPDATE users SET password = \"$resultArray[0]\",updated_at = \"$updateDay\" WHERE password LIKE \"$oldPassword\"";

             if($sqlConn->dbConnet() -> query($sqlUpdate))
             {
                 $message_err = "Datensatz erfolgreich aktualisiert, Sie Könne Jetzt Anmelden <br>";
             }
             else
             {
                 $message_err = "Fehler beim Aktualisieren des Datensatzes: ".$sqlConn->dbConnet()->$php_errormsg . '<br>';
             }
         }
         else
         {
//             FEHLERMELDUNG
         }

     }
     else
     {
         $message_err = $resultArray ;
     }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#03a6f3">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
    <body>
        <div class="pageContainer" style="width: 100%">
            <section class="vh-100"
                     style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img7.webp');">
                <div class="container py-5 h-100" style="width: 41%">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col col-xl-10">
                            <div class="card" style="border-radius: 1rem ">
                                <div class="card-body p-4 p-lg-5 text-black">

                                    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

                                        <div class="d-flex align-items-center mb-3 pb-1">
                                            <span class="h1 fw-bold mb-0">Passwort Änderung</span>
                                        </div>

                                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Ändern Sie Ihr Passwort</h5>

                                        <div class="form-outline mb-4">
                                            <input type="password" id="form2Example17" class="form-control form-control-lg"
                                                   name="oldPassword"/>
                                            <label class="form-label" for="form2Example17" style="font-size: 12px">Geben Sie das Passwort ein, das Sie
                                                in der E-Mail erhalten haben</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="password" id="form2Example27" class="form-control form-control-lg" name="newPassword"/>
                                            <label class="form-label" for="form2Example27" style="font-size: 12px">Das neue Passwort muss 8-20 Zeichen
                                                lang sein, mit mindestens Sonderzeichen, mindestens einem Klein- und einem
                                                Großbuchstaben sowie Zahlen und darf keine Leerzeichen enthalten.</label>
                                        </div>
                                        <div class="form-outline mb-4">
                                            <input type="password" id="form2Example27" class="form-control form-control-lg" name="re_ewPassword"/>
                                            <label class="form-label" for="form2Example27" style="font-size: 12px">Geben Sie zur Bestätigung das neue
                                                Kennwort erneut ein.</label>
                                        </div>

                                        <div class="pt-1 mb-4">
                                            <button class="btn btn-dark btn-lg btn-block" type="submit"
                                                    style="background: #adc0c8;border: solid 1px #adc0c8 ">Ändern
                                            </button>
                                        </div>


                                        <p class="mb-5 pb-lg-2" style="color: #393f81;"><a href="./register_php.php" style="color: #393f81;">zurück zur Register</a>  <br/> <a href="./login.php" style="color: #393f81;">zurück zur Anmeldung</a></p>
                                        <a href="#!" class="small text-muted">Datenschutz Lesen</a>
                                    </form>
                                    <br/>
                                    <p  style="color: #2a2727;"><?php if(isset($message_err)){echo $message_err;}else{echo" ";}?> </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>