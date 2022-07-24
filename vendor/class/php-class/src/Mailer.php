<?php 
    namespace Class;

    use Rain\Tpl;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    class Mailer{

        const USERNAME = "teste.paulo.daniel@outlook.com";
        const PASSWORD = "TesteMail442";
        const NAMEFROM = "Hcode Store";

        private $mail;

        public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
        {

            $conf = array(
                "tpl_dir" => "views/email",
                'cache_dir' => 'views-cache/',
                'debug' => false
            );



            Tpl::configure($conf);

            $tpl = new Tpl();

            foreach ($data as $key => $value) {
                $tpl->assign($key, $value);
            }

            $html = $tpl->draw($tplName, true);

            $this->mail = new PHPMailer(true);
            try {
                $this->mail->isSMTP();
                $this->mail->CharSet = 'UTF-8';
                $this->mail->Host = "smtp.office365.com";
                $this->mail->SMTPAuth = true;
                $this->mail->SMTPSecure = "tls";
                $this->mail->Username = "teste.paulo.daniel@outlook.com";
                $this->mail->Password = "TesteMail442";
                $this->mail->Port = 587;
        
                $this->mail->setFrom(Mailer::USERNAME, Mailer::NAMEFROM);
                $this->mail->addAddress($toAddress, $toName);
        
                $this->mail->Subject = $subject;
                $this->mail->msgHTML($html);
                $this->mail->AltBody = "Olá Entraremos em contato em breve";
            } catch (\Exception $e) {
                echo "Erro ao enviar email: " . $e->getMessage();
            }
        }

        public function send()
        {
            return $this->mail->send();
        }
    }
?>