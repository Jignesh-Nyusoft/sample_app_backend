<?php
namespace App\Helpers;

use App\Models\EmailTemplateModel;
use PHPMailer\PHPMailer\PHPMailer;


class EmailHelper
{

    static $host;
    static $port;
    static $username;
    static $password;
    static $from_name;
    static $from_email;
    static $sitename;


    public function __construct()
    {
        self::$host       = Helper::_get_settings('smtp_host');
        self::$port       = Helper::_get_settings('smtp_port');
        self::$username   = Helper::_get_settings('smtp_username');
        self::$password   = Helper::_get_settings('smtp_password');
        self::$from_name  = Helper::_get_settings('smtp_name');
        self::$from_email = Helper::_get_settings('smtp_email');
        self::$sitename   = Helper::_get_settings('site_name');
    }


/**
 * Sends an email using a predefined email template.
 *
 * @param int $emailid The ID of the email template to use.
 * @param array $mailids An array of recipient email addresses and their names. Format: ['email@example.com' => 'Recipient Name'].
 * @param string $subject The subject line of the email. This will be used in the email's header.
 * @param array $attachments (Optional) An array of file paths for attachments to be included in the email.
 *
 * @return array Returns `true` if the email was successfully sent, `false` otherwise.
 */
 #Example -> EmailHelper::SendMailWithTemplate(17, ['juber.sheikh@nyusoft.com'], 'otp via mail');




    public static function CreateMailTemplate($id,$subject = '')
    {  

        $user_data = [
        'LOGO' => Helper::_get_settings('logo'),
        'FACEBOOK_LINK' => Helper::_get_settings('facebook_link'),
        'TWITTER_LINK' => Helper::_get_settings('twitter_link'),
        'LINKEDIN_LINK' => Helper::_get_settings('linkedin_link'),
        'INSTAGRAM_LINK' => Helper::_get_settings('instagram_link'),
        'BASE_URL' => url('/'),
        'LOGOICON' => url('public/setting/logo-icon.png'),
        'FACEBOOK_ICON' => url('public/images/mail/facebook.png'),
        'TWITTER_ICON' => url('public/images/mail/twitter.png'),
        'LINKEDIN_ICON' => url('public/images/mail/linkedin.png'),
        'INSTAGRAM_ICON' => url('public/images/mail/instagram.png'),
        'BACK_URL' => url('/'),
        'COPYRIGHT' => "&copy; " . date('Y') .self::$sitename. " All Rights Reserved.",
        ];
       

        $emailTemplate = EmailTemplateModel::where('id', $id)->where('status', '1')->first();

        if (!$emailTemplate) {
            return [$subject, '', ''];
        }
    
        $subject = $subject ?: $emailTemplate->subject;
        
        $keys = [
            '{FIRST_NAME}', '{LAST_NAME}', '{LINK}', '{LINK_1}', '{NAME}', '{REASON}',
            '{MEDICINE_NAME}', '{DOSAGE}', '{EMAIL}', '{PASSWORD}', '{PHONE}', '{OTP}',
            '{COUNTRY}', '{CATEGORY}', '{SUBJECT}', '{MESSAGE}', '{DATE}', '{TIME}',
            '{USERNAME}', '{PLAN}', '{ADDRESS}', '{ICON}', '{LOGO}', '{FACEBOOK_ICON}',
            '{TWITTER_ICON}', '{LINKEDIN_ICON}', '{INSTAGRAM_ICON}', '{FACEBOOK_LINK}',
            '{TWITTER_LINK}', '{LINKEDIN_LINK}', '{INSTAGRAM_LINK}', '{COPYRIGHT}',
            '{BASE_URL}', '{ROLE}', '{REMARK}', '{TITLE}', '{BACK_URL}', '{CODE}',
            '{BILLING_ID}', '{PROBLEM}', '{DESCRIPTION}', '{DATETIME}', '{STATUS}',
            '{COMMENT}', '{AMOUNT}', '{PACKAGE}', '{USERTYPE}', '{SITENAME}',
            '{ORDER_NUMBER}', '{ORDER_AMOUNT}', '{LOGOICON}',
        ];
    
        $string = $emailTemplate->emailHeader->description . $emailTemplate->body . $emailTemplate->emailFooter->description;
        $only_string = $emailTemplate->body;
    
       
        $replacePlaceholders = function ($str) use ($keys, $user_data) {
            return str_replace($keys, array_map(fn($k) => $user_data[trim($k, '{}')] ?? $k, $keys), $str);
        };
    
        $string = $replacePlaceholders($string);
        $only_string = $replacePlaceholders($only_string);
        $subject = $replacePlaceholders($subject);
    
        $data = ['subject' => $subject,'messagehtml' => $string,'only_string' => $only_string];
        return $data;
    }
    
    



    public static function SendMailWithTemplate($emailid,$mailids = array(),$subject,$attachments = array())
    {
                
        $templateData = self::CreateMailTemplate($emailid, $subject);
        
        if (!$templateData) {
          
            return false;
        }
    
    
        $fromData = array(
            'host' => Helper::_get_settings('smtp_host'),
            'port' => self::$port,
            'username' => self::$username,
            'password' => self::$password,
            'from_name' => self::$from_name,
            'from_email' => self::$from_email,
        );
    
        $replyToMail = $fromData['username'];
        $replyToName = self::$sitename;
        
        $mail = new PHPMailer();
        $IS_SMTP = 1;
        if ($IS_SMTP) {
            $mail->isSMTP();
            $mail->CharSet = "utf-8";
            $mail->Host = $fromData['host'];
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = $fromData['port'];
        }
    
        $mail->Username = $fromData['username'];
        $mail->Password = $fromData['password'];
        $mail->setFrom($fromData['from_email'], $fromData['from_name']);
    
        if ($replyToMail != '') {
            $mail->AddReplyTo($replyToMail, $replyToName);
        }
    
        if (isset($attachments) && count($attachments)) {
            foreach ($attachments as $value) {
                $mail->AddAttachment($value);
            }
        }
    
        $mail->Subject = $subject;
        $mail->MsgHTML( $templateData['messagehtml']);
        if (count($mailids)) {
            foreach ($mailids as $key => $value) {
                $mail->addAddress($key, $value);
            }
        }
        $mail->isHTML(true);
        return $mail->send();
    }



}