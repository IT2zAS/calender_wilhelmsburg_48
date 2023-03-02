 <?php
require_once('mysql_model.php');
require_once('functions.php');

$functions = new LoginHelper();
$dbConn = new connect();
$dbConn->dbConnet();

session_start();
if(isset($_SESSION['userArray']))
{
    $sId = $_SESSION['userArray']['id'];
    //================== check if the account exist in Database  ======================== //
    $sql = "SELECT * FROM users WHERE id LIKE \"$sId\"";
    $result = $dbConn->dbConnet()->query($sql);
    $foundElements = $result->fetchAll();

    $userId = $foundElements['0']['id'];
    $userName = $foundElements['0']['username'];
    $password = $foundElements['0']['password'];
    $imgUrl = $foundElements['0']['imgUrl'];

    // ====================check if Method is POST============================//;
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         $message_err = " ";
         $updateDate = date("Y.m.d H:i:s", time());

         // ================= get the  file (in this case image ) form data from js and sed Response ===============//
         if(count($_FILES) > 0)
         {
             //change image name
             $temp = explode(".", $_FILES["userImg"]["name"]);
             $newFileName = $userName.'.'.$userId.'.'.$updateDate . '.' . end($temp);
             $targetPath = "uploadedfiles/". $newFileName;
             $imgUpdateSql = "UPDATE users SET imgUrl =\"$newFileName\" WHERE  id LIKE \"$userId\" ";
             $dbConn->dbConnet()->query($imgUpdateSql);
              //Move uploaded files to directory
             move_uploaded_file($_FILES['userImg']['tmp_name'],$targetPath);
             $sql = "SELECT * FROM users WHERE id LIKE \"$userId\"";
             $jsonFile = $dbConn->dbConnet()->query($sql)->fetchAll();
             header("Content-Type: application/json; charset=UTF-8");
             echo json_encode($jsonFile, JSON_THROW_ON_ERROR);
             exit();


         }


        //================== change user name start ======================== //
        if (isset($_POST['username'])) {
            $newName = $_POST['username'];

            $resultArray = $functions->checkRegister($newName, NULL, NULL, NULL);
            if (is_array($resultArray)) {
                echo $foundElements[0]['active'];
                print_r($resultArray[0]);
                if ($foundElements[0]['active'] == 1 && $foundElements[0]['login'] == 1)
                {
                    $newImageUrl = 'https://bashar_test_2.atlantishh-local.de/php/'.$newName;
                    $updateSql = "UPDATE users SET username = \"$newImageUrl\" WHERE  id LIKE \"$sId\" ";
                    $dbConn->dbConnet()->query($updateSql);
                    if ($dbConn->dbConnet()->query($updateSql)) {

                        $sql = "SELECT * FROM users WHERE id LIKE \"$sId\"";
                        $newResult = $dbConn->dbConnet()->query($sql)->fetchAll();
                        $userName = $newResult['0']['username'];
                        $message_err = "Ihrer Name hat erfolgreich geändert";
                    }
                } else {
                    echo"error mesage";
                    $message_err = "Überprüfen Sie Ihre Daten";
                }


            } else {
                $message_err = $resultArray;

            }
        }
        //================== change user name end ======================== //
        //================== Logout start ======================== //
        elseif (isset($_POST['logout']) && $_POST['logout'] === 'logout')
        {
            $updateSql = "UPDATE users SET login = 0 WHERE  id LIKE \"$sId\" ";
            $dbConn->dbConnet()->query($updateSql);
            if ($dbConn->dbConnet()->query($updateSql))
            {
                print_r($_POST['logout']);
                session_destroy();
                unset($_SESSION['userArray']);
                header('location:login.php');
            }
        }
         //================== Logout end ======================== //

    }
}
else
{
    session_destroy();
    header('location:login.php');
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $userName . "s Page"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#03a6f3">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css"
          integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>
    <link rel="stylesheet" href="./css/style.css"/>

</head>

<body>
            <section class="vh-100 bg-image"
                     style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img7.webp');">
                <div class="mask d-flex align-items-center h-100 gradient-custom-3">
                    <div class="container h-100">
                        <div class="row d-flex justify-content-center align-items-center h-100">
                            <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                                <div class="card" style="border-radius: 15px">
                                    <div class="card-body p-5">
                                        <h2 class="text-uppercase text-center mb-5"><?php echo "Hallo" . " " . $userName; ?> </h2>
                                        <div id="showEditImgForm" class="row w-100">
                                            <img id="userImage" src="<?php echo'./uploadedfiles/'.$imgUrl ?>" class="card-img-top" alt="">
                                        </div>

                                        <div class="row w-100 " style="margin-top:5px ">
                                            <div class="col">
                                                <?php echo 'Name: <span id ="user">' . $userName. '</span>'?>
                                            </div>
                                            <div class="col">

                                            </div>
                                            <div class="col " style="text-align: right">
                                                <button id="changeNameShow" type="button" class="btn showBtn ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                        <path
                                                            d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="editNameForm" class="row w-100 hidden" style="padding-right: 0">
                                            <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>"
                                                  class="row gy-2 gx-3 align-items-center">
                                                <div class="col-md-9">
                                                    <div class="form-outline">
                                                        <input type="text" id="form11Example2" class="form-control"
                                                               placeholder="neuer Name" name ="username"/>

                                                    </div>
                                                </div>

                                                <div class="col-auto" style="margin: 0; padding: 0">
                                                    <button type="submit" class="btn btn-primary"
                                                            style="background: #adc0c8;border: solid 1px #adc0c8 ">Ändern
                                                    </button>
                                                </div>
                                            </form>
                                            <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>"
                                                      class="row gy-2 gx-3 align-items-center">
                                                    <div class="col-md-9">
                                                        <div class="form-outline">
                                                            <input type="password" id="form11Example1" class="form-control"
                                                                   placeholder="neues Passwort" name ="password"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto" style="text-align: right">
                                                        <button type="submit" class="btn btn-primary"
                                                                style="background: #adc0c8;border: solid 1px #adc0c8 ">Ändern
                                                        </button>
                                                    </div>

                                                </form>
                                            <form  id = 'myForm'
                                                  class="row gy-2 gx-3 align-items-center">
                                                <div class="col-md-9">
                                                    <div class="form-outline">
                                                        <input type="file" id="imgInput" class="form-control"
                                                               placeholder="Foto Bearbeiten" name ="image"/>
                                                    </div>
                                                </div>
                                                <div class="col-auto" style="text-align: right">
                                                    <button type="submit" class="btn btn-primary" id="imgSend"
                                                            style="background: #adc0c8;border: solid 1px #adc0c8 ">Ändern
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                        <br>
                                        <div class="row w-100">

                                            <div class="col">

                                                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                                    <input type="hidden" name="logout" value="logout" />
                                                    <input type="submit" value="LOGOUT" class="btn btn-dark btn-lg btn-block" style="background: #adc0c8;border: solid 1px #adc0c8 ;color:white"/>
                                                </form>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                            <p  style="color: #d92020;"> <?php if(isset($message_err)){echo $message_err;}else{echo" ";}?> </p>
                    </div>
                </div>
                </div>
            </section>

</body>
<script src="./js/userPage.js"></script>

</html> 