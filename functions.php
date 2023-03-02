<?php
require_once ('mysql_model.php');
$dbConn =new connect();

/**
 *
 */
class LoginHelper
{
    /**
     * @param $username
     * @param $email
     * @param $password
     * @param $rePassword
     * @param $submitArr
     * @return mixed
     */
    public function checkRegister($username = null, $email = null, $password = null, $rePassword = null)
    {
        $submitArr = [];
        /**
         * check  Username
         */
        if ($username !== null) {
            if (empty($username)) {
                $message_err = "please enter a Username.";
                return $message_err;
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $message_err = "Bitte verwenden Sie nur Buchstaben, Zahlen und Unterstriche.";
                return $message_err;
            } else {
                array_push($submitArr, $username);
            }
        }

        /**
         * check if  E_Mail
         */
        if ($email !== null)
        {
            if (empty($email)) {
                $message_err = "Bitte geben Sie eine E-Mail ein.";
                return $message_err;
            } else {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message_err = "Keine gültige E-Mail.";
                    return $message_err;
                } else {
                    array_push($submitArr, $email);
                }
            }
        }

        /**
         * check if password is valid and hash it
         */
        if ($password !== null)
        {
            if (empty($password))
            {
                $message_err = "Bitte geben Sie Passwort ein.";
                return $message_err;
            } else {
                $number = preg_match('@[0-9]@', $password);
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $specialChars = preg_match('@[^\w]@', $password);
                if (strlen($password) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
                    $message_err = "Die Passwort muss mindestens 8 Zeichen lang sein und mindestens eine Zahl, einen Großbuchstaben, einen Kleinbuchstaben und ein Sonderzeichen enthalten.";
                    return $message_err;
                } else {
                    $validPassword = crypt(trim($password), PASSWORD_DEFAULT);
                    array_push($submitArr, $validPassword);
                }

//
            }
        }

        /**
         * check if  Re password is valid and hash it
         */
        if ($rePassword !== null) {
            if (strcmp($password, $rePassword) !== 0) {
                $message_err = "die Passwort und wiederholung von der Passwort müssen gleich sein";
                return $message_err;
            } else {
                $number = preg_match('@[0-9]@', $rePassword);
                $uppercase = preg_match('@[A-Z]@', $rePassword);
                $lowercase = preg_match('@[a-z]@', $rePassword);
                $specialChars = preg_match('@[^\w]@', $rePassword);
                if (strlen($rePassword) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
                    $message_err = "Die wiederholung von der Passwort muss mindestens 8 Zeichen lang sein und mindestens eine Zahl, einen Großbuchstaben, einen Kleinbuchstaben und ein Sonderzeichen enthalten.";
                    return $message_err;
                } else {
                    $validRePassword = crypt(trim($rePassword), PASSWORD_DEFAULT);
                    array_push($submitArr, $validRePassword);
                }
            }

        }


        return $submitArr;

    }

    /**
     * @return string
     */
    public function ranPassword (){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i =0;$i < 8; $i++){
            $x = rand(0,$alphaLength);
             array_push($pass ,$alphabet[$x]);
        }
        return implode($pass);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function generateActivationCode(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * @param $sql
     * @param $conn
     * @return void
     */
    public function runQuery($sql, $conn){
        $conn-> query($sql);


    }

    /**
     * @param $email
     * @param $activation_code
     * @return void
     */
    public function sendActivationEmail($email, $activation_code)
    {
        $appUrl= "http://bashar_test_2.atlantishh-local.de/php";
        // create the activation link
        $activation_link = $appUrl."/activate.php?email=$email&activationCode=$activation_code";
        // set email
        $to = $email ;
        $subject = 'Bitte Aktivieren Sie Ihr Konto';

        // email header
        $headers = "From:no-reply@email.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        // set the message

        $message = '<html><head>';
        $message .= ' <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">';
        $message .= '</head><body><div class ="container">';

        $message .= '<div class="card" style="width: 100% ">';
        $message .= '<div class="card-body p-4 p-lg-5 text-black">';
        $message .= '<h5 class="card-title">Passwort Zurücksetzen</h5>';
        $message .= '<pclass="card-text">Bitte drucken Sie den folgenden Link, um Ihr Konto zu aktivieren  :<br/>';
        $message .= '<a href='.$activation_link.'>Akitvierungslink</a></p>';
        $message .= '</div></div></div></body></html>';


        // send the email
        mail( $to, $subject,$message , $headers);

    }

    /**
     * @param $email
     * @param $autoGeneratedPasswort
     * @return void
     */
    public function resetPasswortEmail($email, $autoGeneratedPasswort){
        // creat the basic email ifo to send
        $to =  $email;
        $subject = "Passwort Änderung";
        $url = "http://bashar_test_2.atlantishh-local.de/php/changePassword.php";
        $headers = "From: altantisdx@info.de\r\n";
        $headers .= "Reply-To: ". strip_tags($email) . "\r\n";
        // Always set content-type when sending HTML email

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $message = '<html><head>';
        $message .= ' <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">';
        $message .= '</head><body><div class ="container">';
        $message .= '<div class="card" style="width: 100% ">';

        $message .= '<div class="card-body p-4 p-lg-5 text-black">';
        $message .= '<h5 class="card-title">Passwort Zurücksetzen</h5>';
        $message .= ' <p class="card-text">Ihr neues Passwort ist ';
        $message .=  '<span style="color:#d43d2c">'. strip_tags($autoGeneratedPasswort ).'</span> '.'<br/>Um Ihr Passwort zurückzusetzen,';
         $message .= 'verwenden Sie bitte dieses neue Passwort und klicken Sie auf diesen Link: ';
         $message .=  '<br/><a href="'.$url.'"> Passwort Äenderung </a></p></div>';
        $message .= '</div></div></div></body></html>';

        mail($to,$subject,$message,$headers);
    }

    /**
     * @param $delay
     * @param $function
     * @return void
     */
    public function setTimeOut($delay, $function){
        $executeTime = time()+$delay;
        $active = true;
        while ($active)
        {
            if(time()>=$executeTime){
                $function;
            }
        }
    }


}



