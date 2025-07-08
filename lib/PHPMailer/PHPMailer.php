<?php
/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5.
 *
 * @see       https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

/**
 * PHPMailer - PHP email creation and transport class.
 *
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 */
class PHPMailer
{
    const CHARSET_ASCII = 'us-ascii';
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8 = 'utf-8';

    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_CALENDAR = 'text/calendar';
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const CONTENT_TYPE_MULTIPART_MIXED = 'multipart/mixed';
    const CONTENT_TYPE_MULTIPART_RELATED = 'multipart/related';

    const ENCODING_7BIT = '7bit';
    const ENCODING_8BIT = '8bit';
    const ENCODING_BASE64 = 'base64';
    const ENCODING_BINARY = 'binary';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

    /**
     * Email priority.
     * Options: null (default), 1 = High, 3 = Normal, 5 = low.
     * When null, the header is not set at all.
     *
     * @var int|null
     */
    public $Priority;

    /**
     * The character set of the message.
     *
     * @var string
     */
    public $CharSet = self::CHARSET_UTF8;

    /**
     * The MIME Content-type of the message.
     *
     * @var string
     */
    public $ContentType = self::CONTENT_TYPE_PLAINTEXT;

    /**
     * The message encoding.
     * Options: "8bit", "7bit", "binary", "base64", and "quoted-printable".
     *
     * @var string
     */
    public $Encoding = self::ENCODING_8BIT;

    /**
     * Holds the most recent mailer error message.
     *
     * @var string
     */
    public $ErrorInfo = '';

    /**
     * The From email address for the message.
     *
     * @var string
     */
    public $From = '';

    /**
     * The From name of the message.
     *
     * @var string
     */
    public $FromName = '';

    /**
     * The envelope sender of the message.
     * This will usually be turned into a Return-Path header by the receiver,
     * and is the address that bounces will be sent to.
     *
     * @var string
     */
    public $Sender = '';

    /**
     * The Subject of the message.
     *
     * @var string
     */
    public $Subject = '';

    /**
     * An HTML or plain text message body.
     * If HTML then call isHTML(true).
     *
     * @var string
     */
    public $Body = '';

    /**
     * The plain-text message body.
     * This body can be read by mail clients that do not have HTML email
     * capability such as mutt & Eudora.
     * Clients that can read HTML will view the normal Body.
     *
     * @var string
     */
    public $AltBody = '';

    /**
     * An iCal message part body.
     * Only supported in simple alt or alt_inline message types
     * To generate iCal event structures, use classes like EasyPeasyICS or iCalcreator.
     *
     * @see http://sprain.ch/blog/downloads/php-class-easypeasyics-create-ical-files-with-php/
     * @see http://kigkonsult.se/iCalcreator/
     *
     * @var string
     */
    public $Ical = '';

    /**
     * Word-wrap the message body to this number of chars.
     * Set to 0 to not wrap. A useful value here is 78, for RFC2822 section 2.1.1 compliance.
     *
     * @see static::STD_LINE_LENGTH
     *
     * @var int
     */
    public $WordWrap = 0;

    /**
     * Which method to use to send mail.
     * Options: "mail", "sendmail", or "smtp".
     *
     * @var string
     */
    public $Mailer = 'mail';

    /**
     * The path to the sendmail program.
     *
     * @var string
     */
    public $Sendmail = '/usr/sbin/sendmail';

    /**
     * Whether mail() uses a fully sendmail-compatible MTA.
     * One which supports sendmail's "-oi -f" options.
     *
     * @var bool
     */
    public $UseSendmailOptions = true;

    /**
     * The email address that a reading confirmation should be sent to, also known as read receipt.
     *
     * @var string
     */
    public $ConfirmReadingTo = '';

    /**
     * The hostname to use in the Message-ID header and as default HELO string.
     * If empty, PHPMailer attempts to find one with, in order,
     * $_SERVER['SERVER_NAME'], gethostname(), php_uname('n'), or the value
     * 'localhost.localdomain'.
     *
     * @see PHPMailer::$Helo
     *
     * @var string
     */
    public $Hostname = '';

    /**
     * An ID to be used in the Message-ID header.
     * If empty, a unique id will be generated.
     * You can set your own, but it must be in the format "<id@domain>",
     * as defined in RFC5322 section 3.6.4 or it will be ignored.
     *
     * @see https://tools.ietf.org/html/rfc5322#section-3.6.4
     *
     * @var string
     */
    public $MessageID = '';

    /**
     * The message Date to be used in the Date header.
     * If empty, the current date will be added.
     *
     * @var string
     */
    public $MessageDate = '';

    /**
     * SMTP hosts.
     * Either a single hostname or multiple semicolon-delimited hostnames.
     * You can also specify a different port
     * for each host by using this format: [hostname:port]
     * (e.g. "smtp1.example.com:25;smtp2.example.com").
     * You can also specify encryption type, for example:
     * (e.g. "tls://smtp1.example.com:587;ssl://smtp2.example.com:465").
     * Hosts will be tried in order.
     *
     * @var string
     */
    public $Host = 'localhost';

    /**
     * The default SMTP server port.
     *
     * @var int
     */
    public $Port = 25;

    /**
     * The SMTP HELO/EHLO name used for the SMTP connection.
     * Default is $Hostname. If $Hostname is empty, PHPMailer attempts to find
     * one with the same method described above for $Hostname.
     *
     * @see PHPMailer::$Hostname
     *
     * @var string
     */
    public $Helo = '';

    /**
     * What kind of encryption to use on the SMTP connection.
     * Options: '', static::ENCRYPTION_STARTTLS, or static::ENCRYPTION_SMTPS.
     *
     * @var string
     */
    public $SMTPSecure = '';

    /**
     * Whether to enable TLS encryption automatically if a server supports it,
     * even if `SMTPSecure` is not set to 'tls'.
     * Be aware that in PHP >= 5.6 this requires that the server's certificates are valid.
     *
     * @var bool
     */
    public $SMTPAutoTLS = true;

    /**
     * Whether to use SMTP authentication.
     * Uses the Username and Password properties.
     *
     * @see PHPMailer::$Username
     * @see PHPMailer::$Password
     *
     * @var bool
     */
    public $SMTPAuth = false;

    /**
     * Username to use for SMTP authentication.
     * @var string
     */
    public $Username = '';

    /**
     * Password to use for SMTP authentication.
     * @var string
     */
    public $Password = '';

    /**
     * SMTP port number - 587 for STARTTLS, 465 for SSL.
     * @var int
     */
    public $SMTPPort;

    /**
     * Array of addresses the email will be sent to
     * @var array
     */
    protected $to = [];

    public function __construct($exceptions = null) {
        // Nothing here - simplified version
    }

    /**
     * Create a message and send it.
     * Uses the sending method specified by $Mailer.
     * @throws Exception
     * @return bool false on error - See the ErrorInfo property for details of the error
     */
    public function send()
    {
        try {
            // Set up SMTP if needed
            if ($this->Mailer == 'smtp') {
                $this->ErrorInfo = 'SMTP is not implemented in this simplified version';
                return false;
            }
            
            // Using PHP mail() function
            $toArr = array();
            foreach ($this->to as $toaddr) {
                $toArr[] = $this->addrFormat($toaddr);
            }
            $to = implode(', ', $toArr);
            
            if (empty($to)) {
                $this->ErrorInfo = 'No recipient address specified';
                return false;
            }
            
            $headers = $this->createHeader();
            
            $result = mail($to, $this->Subject, $this->Body, $headers);
            
            if (!$result) {
                $this->ErrorInfo = 'Mail error: ' . (error_get_last() ? error_get_last()['message'] : 'Unknown error');
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            $this->ErrorInfo = $e->getMessage();
            return false;
        }
    }
    
    protected function createHeader() {
        $headers = array();
        
        $headers[] = "From: {$this->FromName} <{$this->From}>";
        if (!empty($this->Sender)) {
            $headers[] = "Return-Path: {$this->Sender}";
        }
        
        if ($this->ContentType == self::CONTENT_TYPE_HTML) {
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset={$this->CharSet}";
        } else {
            $headers[] = "Content-Type: text/plain; charset={$this->CharSet}";
        }
        
        return implode("\r\n", $headers);
    }
    
    /**
     * Set the From and FromName properties.
     * @param string $address The email address to set
     * @param string $name The name to associate with the address
     * @return bool
     */
    public function setFrom($address, $name = '', $auto = true)
    {
        $this->From = $address;
        $this->FromName = $name;
        return true;
    }
    
    /**
     * Add a recipient.
     * @param string $address The email address to add
     * @param string $name The name to associate with the address
     * @return bool true on success, false if address already used or invalid
     */
    public function addAddress($address, $name = '')
    {
        $this->to[] = array($address, $name);
        return true;
    }
    
    /**
     * Set the message type to HTML.
     * @param bool $isHtml True for HTML, false for plain text
     * @return void
     */
    public function isHTML($isHtml = true)
    {
        $this->ContentType = $isHtml ? self::CONTENT_TYPE_HTML : self::CONTENT_TYPE_PLAINTEXT;
    }
    
    /**
     * Format an address pair for use in email headers
     * @param array $addr An array containing an address and optionally a name
     * @return string
     */
    protected function addrFormat($addr) 
    {
        if (!empty($addr[1])) {
            return $addr[1] . ' <' . $addr[0] . '>';
        }
        return $addr[0];
    }

    /**
     * Set the SMTP host.
     * @param string $host
     * @return void
     */
    public function Host($host) {
        $this->Host = $host;
    }

    /**
     * Set the SMTP port.
     * @param int $port
     * @return void
     */
    public function Port($port) {
        $this->Port = $port;
    }

    /**
     * Set the SMTP username.
     * @param string $username
     * @return void
     */
    public function Username($username) {
        $this->Username = $username;
    }

    /**
     * Set the SMTP password.
     * @param string $password
     * @return void
     */
    public function Password($password) {
        $this->Password = $password;
    }

    /**
     * Set the SMTP security.
     * @param string $security
     * @return void
     */
    public function SMTPSecure($security) {
        $this->SMTPSecure = $security;
    }

    /**
     * Set the SMTP authentication.
     * @param bool $auth
     * @return void
     */
    public function SMTPAuth($auth) {
        $this->SMTPAuth = $auth;
    }
    
    /**
     * Set mailer to use SMTP.
     * @return void
     */
    public function isSMTP() {
        $this->Mailer = 'smtp';
    }
    
    /**
     * Set mailer to use PHP mail() function.
     * @return void
     */
    public function isMail() {
        $this->Mailer = 'mail';
    }
    
    public function addCC($address, $name = '') {
        // Not implemented in this simple version
    }
    
    public function addBCC($address, $name = '') {
        // Not implemented in this simple version
    }
    
    public function addReplyTo($address, $name = '') {
        // Not implemented in this simple version
    }
    
    public function addAttachment($path, $name = '') {
        // Not implemented in this simple version
        return false;
    }
}
