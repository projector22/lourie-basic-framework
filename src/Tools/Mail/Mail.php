<?php

namespace LBF\Tools\Mail;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Handle sending email content.
 * 
 * use LBF\Tools\Mail\Mail;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @link    https://github.com/PHPMailer/PHPMailer/
 * @link    https://alexwebdevelop.com/phpmailer-tutorial/
 * 
 * @since   LRS 3.4.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

class Mail {

    /**
     * The default name which will be used when sending mail
     * 
     * The default name to be used when sending an email.
     * 
     * @var string  $default_from_name
     * 
     * @access  private
     * @since   LRS 3.4.0
     */

    private string $default_from_name;

    /**
     * Default from address defined in the database. Value is defined in __construct and are taken from the database
     * 
     * @var string  $default_from
     * 
     * @access public
     * @since   LRS 3.4.0
     */

    public string $default_from;
    
    /**
     * Default smtp address defined in the database. Value is defined in __construct and are taken from the database
     * 
     * @var string  $smtp_address
     * 
     * @access private
     * @since   LRS 3.4.0
     */

    private string $smtp_address;
    
    /**
     * Default username defined in the database. Value is defined in __construct and are taken from the database
     * 
     * @var string  $username
     * 
     * @access private
     * @since   LRS 3.4.0
     */

    private string $username;
    
    /**
     * Default password defined in the database. NB. stored in plain text. Value is defined in __construct and are taken from the database
     * 
     * @var string  $password
     * 
     * @access private
     * @since   LRS 3.4.0
     */

    private string $password;
    
    /**
     * Whether or not to use authentication, defined in database. Value is defined in __construct and are taken from the database
     * 
     * @var string  $requires_auth
     * 
     * @access private
     * @since   LRS 3.4.0
     */

    private string $requires_auth;
    
    /**
     * Choice of encryption types, Choices: None, SSL, TSL. Defined in database. Value is defined in __construct and are taken from the database
     * 
     * @var string  $encrypt_type
     * 
     * @access private
     * @since   LRS 3.4.0
     */

    private string $encrypt_type;
    
    /**
     * Which port to use, typically 25, 465 or 587. Defined in database. Value is defined in __construct and are taken from the database
     * 
     * @var string  $port
     * 
     * @access private
     * @since   LRS 3.4.0
     */

    private string $port;

    /**
     * Whether or not to echo out error messages
     * 
     * @var string  $echo_error
     * 
     * @access  public
     * @since   LRS 3.12.2
     */
    
    public bool $echo_error = true;
    
    /**
     * The attachment type to apply
     * Options: 
     * - 'std' (standard)
     * - 'str' (string)
     * 
     * @var string  $attachment_type    Default: 'std'
     * 
     * @access  public
     * @since   LRS 3.12.2
     */

    public string $attachment_type = 'std';

    /**
     * The encoding of a string attachment
     * 
     * @var string  $encoding   Default: 'base64'
     * 
     * @access  public
     * @since   LRS 3.12.2
     */

    public string $encoding = 'base64';

    /**
     * MIME type of the attachemt
     * 
     * @var string  $type       Default: 'application/pdf'
     * 
     * @access  public
     * @since   LRS 3.12.2
     */

    public string $type = 'application/pdf';


    /**
     * Constructor method, things to do when the class is loaded.
     * 
     * @param   object      $config_object      The object containing the various SMTP properties.
     * @param   string|null $default_from_name  The name which is used as a "From Name".
     *                                          In LRS this is `APP_NAME . ' - ' . SCHOOL_NAME`.
     *                                          Default: null
     * 
     * @access  public
     * @since   LRS 3.4.0
     * @since   LRS 3.28.0  Added params `$config_object` & `$default_from_name`.
     */

    public function __construct(
        object $config_object,
        ?string $default_from_name = null,
    ) {
        $config = $config_object;
        $mail_config = $config->get_email_config();
        $this->smtp_address  = $mail_config->smtp_address;
        $this->username      = $mail_config->username;
        $this->password      = $mail_config->password;
        $this->requires_auth = $mail_config->requires_auth;
        $this->encrypt_type  = $mail_config->encrypt_type;
        $this->port          = $mail_config->port;
        $this->default_from  = $mail_config->default_send_addr;

        $this->default_from_name = $default_from_name ?? $this->default_from;
    } //__construct


    /**
     * Send mail using PHP mailer, handles all the permissions and detail setting
     * 
     * @param   string  $to             The 'to' address to which an email should be sent. Required
     * @param   string  $subject        The subject of the email which should be sent. Required
     * @param   string  $body           The body of the email which should be sent. Required
     * @param   array   $attachment     An attachment to send with the email. Should be in the form of [$path, $name]
     *                                  Default: null.
     * @param   string  $from_name      To use a different "From Name" to the default, set it here.
     *                                  Default: null
     * @param   boolean $body_is_html   Check if the body of the mail being send is in the form of an HTML string.
     *                                  Default: false
     * @param   string  $from           The from address from which to send.
     *                                  Default: null
     * @param   string  $cc             Any cc addresses to send mail to.
     *                                  Default: null
     * @param   string  $bcc            Any bcc addresses to send mail to.
     *                                  Default: null
     * @param   string  $to_name        Add the recipient's name as well as their 'to' email address.
     *                                  Default: null
     * 
     * @return  boolean                 Whether or not the mail sending was successful or not
     * 
     * @access  public
     * @since   LRS 3.4.0
     */

    public function send_mail( 
        string $to, 
        string $subject, 
        string $body, 
        ?array $attachment = null, 
        ?string $from_name = null, 
        bool $body_is_html = false, 
        ?string $from = null, 
        string|array|null $cc = null, 
        string|array|null $bcc = null, 
        ?string $to_name = null
    ): bool {
        $mail = new PHPMailer( true );
        try {

            /**
             * Set From address
             * 
             * @since   LRS 3.4.0
             */

            if ( is_null( $from ) ) {
                $from = $this->default_from;
            }

            /**
             * Set the From name
             * 
             * @since   LRS 3.4.0
             */

            if ( is_null( $from_name ) ) {
                $from_name = $this->default_from_name;
            }

            $mail->setFrom( $from, $from_name );


            /**
             * Set the To address
             * 
             * @since   LRS 3.4.0
             */

            $send_to = $this->split_multi_send_addresses( $to );
            if ( is_null( $to_name ) ) {
                foreach ( $send_to as $s_to ) {
                    $mail->addAddress( $s_to );
                }
            } else {
                foreach ( $send_to as $s_to ) {
                    $mail->addAddress( $s_to, $to_name );
                }
            }

            /**
             * Set the Attachemnt
             * 
             * @since   LRS 3.4.0
             */

            if ( !is_null( $attachment ) ) {
                switch ( $this->attachment_type ) {
                    case 'str':
                        $mail->AddStringAttachment( $attachment[0], $attachment[1], encoding: $this->encoding, type: $this->type );
                        break;
                    default:
                        if ( is_array( $attachment[0] ) ) {
                            foreach( $attachment as $attach ) {
                                $mail->addAttachment( $attach[0], $attach[1] );
                            }
                        } else {
                            $mail->addAttachment( $attachment[0], $attachment[1] );
                        }
                }
            }

            /**
             * Set a CC address
             * 
             * @since   LRS 3.4.0
             */

            if ( !is_null( $cc ) ) {
                if ( is_string( $cc ) ) {
                    $cc_to = $this->split_multi_send_addresses( $cc );
                } else {
                    $cc_to =  $cc;
                }
                foreach ( $cc_to as $cc_s) {
                    $mail->addCC( $cc_s );
                }
            }

            /**
             * Set a BCC address
             * 
             * @since   LRS 3.4.0
             */

            if ( !is_null( $bcc ) ) {
                if ( is_string( $bcc) ) {
                    $bcc_to = $this->split_multi_send_addresses( $bcc );
                } else {
                    $bcc_to = $bcc;
                }
                foreach ( $bcc_to as $bcc_s ) {
                    $mail->addBCC( $bcc_s );
                }
            }

            /**
             * Set the Subject
             * 
             * @since   LRS 3.4.0
             */

            $mail->Subject = $subject;

            /**
             * Handle if the body of the email has HTML content
             * 
             * @since   LRS 3.4.0
             */

            if ( $body_is_html ) {
                $mail->isHTML( true );
            }
            $mail->Body = $body;

            /**
             * Prepare and send
             * 
             * @since   LRS 3.4.0
             */

            $mail->isSMTP();
            $mail->Host       = $this->smtp_address;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->Port       = $this->port;
            if ( $this->requires_auth == '1' ) {
                $mail->SMTPAuth   = true;
                $mail->SMTPSecure = $this->encrypt_type;
            }

            $save_limit = ini_get( 'memory_limit' );
            ini_set( 'memory_limit', '256M' );
            $time_limit = ini_get( 'max_execution_time' );
            ini_set( 'max_execution_time', '3600' );
            $mail->send();
            ini_set( 'memory_limit', $save_limit );
            ini_set( 'max_execution_time', $time_limit );
        } catch ( Exception $e ) {
            if ( $this->echo_error ) {
                echo $e->errorMessage();
            }
            return false;
        } catch ( \Exception $e ) {
            if ( $this->echo_error ) {
                echo $e->getMessage();
            }
            return false;
        }
        return true;
    }


    /**
     * Tests for and splits multiple addresses from a single string
     * 
     * @param   string  $address    The address string to be examined
     * 
     * @return  array   $addresses  The addresses array (even if there is only one) to be used
     * 
     * @access  private
     * @since   LRS 3.4.0
     * @since   LRS 3.12.2  Added handling for split by semicolon
     */

    private function split_multi_send_addresses( string $address ): array {
        $parts = [];
        $split_one = explode( ',', $address );
        foreach ( $split_one as $split ) {
            $split_two = explode( ';', $split );
            $parts = array_merge( $parts, $split_two );
        }
        foreach ( $parts as $part ) {
            $addresses[] = trim($part);
        }
        return $addresses;
    }

}