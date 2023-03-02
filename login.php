<?php
require_once('mysql_model.php');
require_once('functions.php');

$functions = new LoginHelper();
$sqlconn = new connect();
$sqlconn->dbConnet();


if($_SERVER['REQUEST_METHOD']==='POST'){

    $message_err =" ";
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $updateDay =  date("Y.m.d",time());
    $resultArray = $functions->checkRegister(NULL , $email , $password, NULL);

    if(is_array($resultArray))
    {

        $sql = "SELECT * FROM users WHERE email LIKE \"$resultArray[0]\"";
        $result = $sqlconn->dbConnet() -> query($sql);
        $foundElements = $result->fetchAll();

        if(count($foundElements[0])!= 0 )
        {
            $id = $foundElements[0][0];
            $isActive = $foundElements[0][5];

            if($isActive !=0)
            {
                if (isset($foundElements[0][3], $resultArray[1]) && hash_equals($foundElements[0][3], $resultArray[1])) {

                    $sqlUpdate = "UPDATE users SET login = 1 WHERE id LIKE \"$id\"";

                    if($sqlconn->dbConnet() -> query($sqlUpdate))
                    {
                        session_start();
                        $message_err ="Ihre Anmeldung ist erfolgreich";

                        $_SESSION["userArray"] = $foundElements[0];

                      header("Location:./UserPage.php");
                        exit();
                    }
                    else
                    {
                        $message_err = "Datenbank fehler";
                    }


                } else {
                    $message_err ="Überprüfen Sie Ihre E-Mail oder Ihr Passwort oder Falls Sie kein Konto habe Bitte Registrieren Sie Sich";


                }
            }
            else
            {
                $message_err ="Ihr Konto muss Aktiv sein";
            }

        }else
        {
            $message_err .="Überprüfen Sie Ihre Informationen ";
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
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#03a6f3">
    <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="./css/style.css">
    </head>
    <body>


    <div class="pageContainer" style ="width: 100%">
    <section class="vh-100" style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img7.webp');">
        <div class="container py-5 h-100" style="width: 41%">
            <div class="row d-flex justify-content-center align-items-center h-100" >
                <div class="col col-xl-10">
                    <div class="card" style="border-radius: 1rem ">
                        <div class="card-body p-4 p-lg-5 text-black">
                            <p id ="successMessage"><?php if (isset($_SESSION["successMessage"])) {
                                    echo $_SESSION["successMessage"];
                                    $_SESSION["successMessage"] = '';
                                }?></p>
                            <form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" >

                                <div class="d-flex align-items-center mb-3 pb-1">
                                    <span class="h1 fw-bold mb-0">Anmeldung</span>
                                </div>

                                <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Melden Sie sich bei Ihrem Konto an</h5>

                                <div class="form-outline mb-4">
                                    <input type="email" id="form2Example17" class="form-control form-control-lg" name="email" />
                                    <label class="form-label" for="form2Example17">E_Mail Adresse</label>
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="password" id="form2Example27" class="form-control form-control-lg" name ="password"/>
                                    <label class="form-label" for="form2Example27">Passwort</label>
                                </div>

                                <div class="pt-1 mb-4">
                                    <button class="btn btn-dark btn-lg btn-block" type="submit" style="background: #adc0c8;border: solid 1px #adc0c8 ">Anmelden</button>
                                </div>

                                <a class="small text-muted" href="./resetPassword.php">Ihre Passwort vergessen?</a>
                                <p class="mb-5 pb-lg-2" style="color: #393f81;">Haben Sie kein Konto? <a href="./register_php.php" style="color: #393f81;">Hier registrieren Sie sich</a></p>
                                <a href="#!" class="small text-muted">Datenschutz Lesen</a>
                            </form>
                            <br/>
                            <p  style="color: #d92020;"> <?php if(isset($message_err)){echo $message_err;}else{echo" ";}?> </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>



    </body>
    <script>

        let  successMessage = document.querySelector('#successMessage');
        setTimeout(() => {
            successMessage.innerHTML='' ;
        }, 5000)
    </script>
</html>





