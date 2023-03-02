<?php

require_once('functions.php');
require_once('mysql_model.php');
require_once ('users_table.php');


$sqlconn = new connect();
$functions = new LoginHelper();

$sqlconn->dbConnet();
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $message = " ";
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $email = trim($_POST["email"]);
    $rePassword = trim($_POST["repassword"]);
    $userTerms =@trim($_POST["TermsOfUse"]);
    $activationDay =  date("Y.m.d",time());
    $expiry_date = date('Y-m-d H:i:s',time()+ 86400);
    // check if Terms Of Use is accepted
    if(isset($userTerms)&& $userTerms == true){

        $resultArray = $functions->checkRegister($username, $email, $password, $rePassword);

        if (is_array($resultArray))
        {
            $table = 'users';
            // check if db Table is exist
            if ($result = $sqlconn->dbConnet()->query("SHOW TABLES LIKE '".$table."'"))
            {
                $tablecount = (count($result->fetchAll()));
                if($tablecount > 0)
                {
                    //check if the user s exist
                    $tableExists = true;
                    $sql = "SELECT username, email FROM users WHERE username LIKE \" $resultArray[0] \"OR email LIKE \"$resultArray[1] \"";

                    $result = $sqlconn->dbConnet()->query($sql);

                    $foundElements = $result->fetchAll();
                    if (count($foundElements) > 0) {
                        $message = 'Diese Benutzername or E_Mail ist schon existiert Sie können bereit sich Anmelden  <br/>';
                        $foundElements = [];
                    } else {
                        // register new user
                        $activationCod = $functions ->generateActivationCode();
                        $sql = "INSERT INTO users  (username, email, password,is_admin,active,activation_code,activation_expiry,created_at,login) VALUES (\"$resultArray[0]\", \"$resultArray[1]\",\"$resultArray[2]\",0,0,\"$activationCod\",\"$expiry_date\",\"$activationDay\",0 )";
                        echo"<pre>";
                        print_r($sql);
                        echo"</pre>";
                        $registerResult = $sqlconn->dbConnet()->query($sql);
                        $foundElements = [];
                        $message = 'Ihre Registrierung War erfolgreich. Sie werden eine E-Mail erhalten mit Aktivierungscode. <br/>';
                        $functions->sendActivationEmail($resultArray[1],$activationCod);
                        session_start();
                        $_SESSION["successMessage"] = $message;
                        header("Location:./login.php");
                        exit();

                    }
                }
                else {
                    $tableExists = false;
                    //
                }
            }
            else {
                $tableExists = false;
                $message = "Datenbank Problem";
            }

            if (!$tableExists){
                // creating new table
                $functions ->runQuery($sql_create,$sqlconn->dbConnet());
                // register new user
                $activationCod = $functions ->generateActivationCode();
                $sql = "INSERT INTO users  (username, email, password,is_admin,active,activation_code,activation_expiry,created_at,login) VALUES (\"$resultArray[0]\", \"$resultArray[1]\",\"$resultArray[2]\",0,0,\"$activationCod\",\"$expiry_date\",\"$activationDay\",0 )";
                $registerResult = $sqlconn->dbConnet()->query($sql);
                $foundElements = [];
                $message = 'Ihre Registrierung War erfolgreich. Sie werden eine E-Mail erhalten mit Aktivierungscode. <br/>';
                $functions->sendActivationEmail($resultArray[1],$activationCod);
                session_start();
                $_SESSION["successMessage"] = $message;
                header("Location:./login.php");
                exit();
            }



        } else {

            $message .= $resultArray;
        }
    }
    else
    {
        $message="Bitte stimmen Sie den Aussagen in der Nutzungsbedingungen zu ";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#03a6f3">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
<section class="vh-100 bg-image"
         style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img7.webp');">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body p-5">
                            <h2 class="text-uppercase text-center mb-5">Ein Konto erstellen</h2>

                            <form method="POST" <?php echo $_SERVER["PHP_SELF"]; ?>>

                                <div class="form-outline mb-4">
                                    <input type="text" id="form3Example1cg" class="form-control form-control-lg"
                                           name="username" value="<?php echo $username; ?>"/>
                                    <label class="form-label" for="form3Example1cg">Ihr Name</label>
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="email" id="form3Example3cg" class="form-control form-control-lg"
                                           name="email" />
                                    <label class="form-label" for="form3Example3cg">Ihre E-Mail</label>
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="password" id="form3Example4cg" class="form-control form-control-lg"
                                           name="password"/>
                                    <label class="form-label" for="form3Example4cg">Passwort</label>
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="password" id="form3Example4cdg" class="form-control form-control-lg"
                                           name="repassword"/>
                                    <label class="form-label" for="form3Example4cdg">Wiederholen Sie Ihr
                                        Passwort</label>
                                </div>
                                <span> <?php if (isset($message)) { echo $message;} else { echo "  ";} ?> </span>
                                <div class="form-check d-flex justify-content-center mb-5">
                                    <input class="form-check-input me-2" type="checkbox" value="true" id="form2Example3cg" name ="TermsOfUse"/>
                                    <label class="form-check-label" for="form2Example3g">
                                        Ich stimme allen Aussagen in der <a href="#!" class="text-body"><u>Nutzungsbedingungen</u></a>
                                        zu
                                    </label>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <button type="submit"
                                            class="btn btn-success btn-block btn-lg gradient-custom-4 text-body"
                                            style="background: #adc0c8;border: solid 1px #adc0c8 ">Registrieren
                                    </button>
                                </div>

                                <p class="text-center text-muted mt-5 mb-0">Haben Sie schon ein Konto? <a
                                        href="./login.php" class="fw-bold text-body"><u>Melden Sie sich hier an </u></a></p>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>

</html>