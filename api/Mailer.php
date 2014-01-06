<?php

namespace api;

class Mailer extends \library\Module
{
    public function init()
    {
        /*
         * prefix api/mailer
         * /send                post
         */
        $this->app->group('/api/mailer', function () {
            $this->app->post('/send', array($this, 'postSendMail'));
        });
    }
    /*
     * /api/mailer/send Json input spec
     *
     * {
     *     "to": "femc7488@gmail.com,dachichang@piray.com.tw",
     *     "subject": "test mail",
     *     "content": "helloworld"
     * }
     *
     */
    public function postSendMail()
    {
        $sendData = $this->helper->receiveJson();
        if (isset($sendData['to']) && isset($sendData['subject']) && isset($sendData['content'])) {

            $mailer = $this->createGmailMailer();

            foreach (explode(',', $sendData['to']) as $mailAddress) {
                $mailer->AddAddress($mailAddress);
            }
            $mailer->Subject = $sendData['subject'];
            $mailer->Body = $sendData['content'];

            if ($mailer->send()) {
                $this->helper->sendJson(200, array(
                    'status' => 200,
                    'message' => "Mail send finish."
                ));
                return;
            } else {
                $this->helper->sendJson(500, array(
                    'status' => 500,
                    'message' => $mailer->ErrorInfo
                ));
                return;
            }
        }

        $this->helper->sendJson(500, array(
            'status' => 500,
            'message' => "Error input"
        ));
    }
    private function createGmailMailer()
    {
        $mail = new \PHPMailer();
        // use SMTP
        $mail->IsSMTP();
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        // mail content setting
        $mail->CharSet = "utf-8";
        $mail->Encoding = "base64";
        $mail->IsHTML(true);
        // gmail authenticate
        $mail->SMTPAuth = true;
        $mail->Username = "dachichang@piray.com.tw";
        $mail->Password = "saLL5032";
        // send user setting
        $mail->setFrom('dachichang@piray.com.tw', 'Piray Service');
        return $mail;
    }
}

