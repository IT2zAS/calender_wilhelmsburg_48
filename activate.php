<?php

require_once (__DIR__.'/functions.php');
require_once (__DIR__.'/mysql_model.php');
$function = new LoginHelper();
$dbConn = new connect();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $message_err =" ";
    $email = trim($_POST["email"]);
    $activationCode = trim($_POST["activationCode"]);
    $activationDay =  date("Y.m.d",time());
    // find the user
    $sql = "SELECT * FROM users WHERE email LIKE \"$email\" AND activation_code LIKE \"$activationCode\" " ;
    $result = $dbConn->dbConnet() -> query($sql)->fetchAll();

        if(count($result)>0){
            $isActive = $result[0][5];
            // check if is Active
            if($isActive == 0)
            {
                $id = $result[0][0];
                // change the activation code and activate the account.
                $newActivationCod = $function->generateActivationCode();
                $activeSql = "UPDATE users SET active = 1 ,activation_code = \"$newActivationCod\" ,activated_at = \"$activationDay\" WHERE id LIKE \"$id\"";
                $dbConn->dbConnet()-> query($activeSql);
                if($dbConn->dbConnet()-> query($activeSql))
                {
                    session_start();
                    $_SESSION["successMessage"] = " Ihr Konto hat erfolgreich Aktiviert,Sie könne jetzt anmelden.";
                    header("Location:./login.php");
                    exit();

                }
            }


        }
        else
        {
            $message_err = "Überprüfen Sie Ihre Information.";
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Konto Aktivierung</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#03a6f3">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>


<div class="pageContainer" style ="width: 100%">
    <section class="vh-100" style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img7.webp');">
        <div class="container py-5 h-100" style="width: 41%">
            <div class="row d-flex justify-content-center align-items-center h-100" >
                <div class="col col-xl-10">
                    <div class="card" style="border-radius: 1rem ">
                        <div class="card-body p-4 p-lg-5 text-black">

                            <form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" >

                                <div class="d-flex align-items-center mb-3 pb-1">
                                    <span class="h1 fw-bold mb-0">Aktivierung</span>
                                </div>

                                <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Melden Sie sich bei Ihrem Konto an</h5>

                                <div class="form-outline mb-4">
                                    <input type="email" id="form2Example17" class="form-control form-control-lg" name="email" value="<?php if(count($_GET) > 0){echo $_GET['email'];}else{echo '';} ?>" />
                                    <label class="form-label" for="form2Example17">E-Mail Adresse</label>
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="password" id="form2Example27" class="form-control form-control-lg" name ="activationCode" value="<?php if(count($_GET) > 0){echo $_GET['activationCode'];}else{echo '';} ?>"/>
                                    <label class="form-label" for="form2Example27"> Aktivierungscode</label>
                                </div>

                                <div class="pt-1 mb-4">
                                    <button class="btn btn-dark btn-lg btn-block" type="submit" style="background: #adc0c8;border: solid 1px #adc0c8 ">Aktivieren</button>
                                </div>
                                <a href="#!" class="small text-muted">Datenschutz Lesen</a>
                                &nbsp  &nbsp  &nbsp
                                <a href="./login.php" class="small text-muted">Anmeldung </a>
                            </form>
                            <br/>
                            <p  style="color: #171616;"> <?php if(isset($message_err)){echo $message_err;}else{echo" ";}?> </p>
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
