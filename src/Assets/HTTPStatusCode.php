<?php

namespace LBF\Assets;

use LBF\Img\SVGImages;

/**
 * Enum with all the various standardhttp status codes available for use.
 * 
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
 * @see https://httpwg.org/specs/rfc9110.html#overview.of.status.codes
 * 
 * use LBF\Assets\HTTPStatusCode;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

enum HTTPStatusCode {
    /**100 */
    case CONTINUE;
    /**101 */
    case SWITCHING_PROTOCOLS;
    /**102 */
    case PROCESSING;
    /**103 */
    case EARLY_HINTS;
    /**200 */
    case OK;
    /**201 */
    case CREATED;
    /**202 */
    case ACCEPTED;
    /**203 */
    case NON_AUTHORITATIVE_INFORMATION;
    /**204 */
    case NO_CONTENT;
    /**205 */
    case RESET_CONTENT;
    /**206 */
    case PARTIAL_CONTENT;
    /**207 */
    case MULTI_STATUS;
    /**208 */
    case ALREADY_REPORTED;
    /**226 */
    case IM_USED;
    /**300 */
    case MULTIPLE_CHOICES;
    /**301 */
    case MOVED_PERMANENTLY;
    /**302 */
    case FOUND;
    /**303 */
    case SEE_OTHER;
    /**304 */
    case NOT_MODIFIED;
    /**305 */
    case USE_PROXY;
    /**306 */
    case RESERVED;
    /**307 */
    case TEMPORARY_REDIRECT;
    /**308 */
    case PERMANENTLY_REDIRECT;
    /**400 */
    case BAD_REQUEST;
    /**401 */
    case UNAUTHORIZED;
    /**402 */
    case PAYMENT_REQUIRED;
    /**403 */
    case FORBIDDEN;
    /**404 */
    case NOT_FOUND;
    /**405 */
    case METHOD_NOT_ALLOWED;
    /**406 */
    case NOT_ACCEPTABLE;
    /**407 */
    case PROXY_AUTHENTICATION_REQUIRED;
    /**408 */
    case REQUEST_TIMEOUT;
    /**409 */
    case CONFLICT;
    /**410 */
    case GONE;
    /**411 */
    case LENGTH_REQUIRED;
    /**412 */
    case PRECONDITION_FAILED;
    /**413 */
    case REQUEST_ENTITY_TOO_LARGE;
    /**414 */
    case REQUEST_URI_TOO_LONG;
    /**415 */
    case UNSUPPORTED_MEDIA_TYPE;
    /**416 */
    case REQUESTED_RANGE_NOT_SATISFIABLE;
    /**417 */
    case EXPECTATION_FAILED;
    /**418 */
    case IM_A_TEAPOT;
    /**421 */
    case MISDIRECTED_REQUEST;
    /**422 */
    case UNPROCESSABLE_ENTITY;
    /**423 */
    case LOCKED;
    /**424 */
    case FAILED_DEPENDENCY;
    /**425 */
    case TOO_EARLY;
    /**426 */
    case UPGRADE_REQUIRED;
    /**428 */
    case PRECONDITION_REQUIRED;
    /**429 */
    case TOO_MANY_REQUESTS;
    /**431 */
    case REQUEST_HEADER_FIELDS_TOO_LARGE;
    /**451 */
    case UNAVAILABLE_FOR_LEGAL_REASONS;
    /**500 */
    case INTERNAL_SERVER_ERROR;
    /**501 */
    case NOT_IMPLEMENTED;
    /**502 */
    case BAD_GATEWAY;
    /**503 */
    case SERVICE_UNAVAILABLE;
    /**504 */
    case GATEWAY_TIMEOUT;
    /**505 */
    case VERSION_NOT_SUPPORTED;
    /**506 */
    case VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL;
    /**507 */
    case INSUFFICIENT_STORAGE;
    /**508 */
    case LOOP_DETECTED;
    /**510 */
    case NOT_EXTENDED;
    /**511 */
    case NETWORK_AUTHENTICATION_REQUIRED;


    /**
     * Returns the actual status code of the specified enum.
     * 
     * @return  int
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function code(): int {
        return match ($this) {
            self::CONTINUE                             => 100,
            self::SWITCHING_PROTOCOLS                  => 101,
            self::PROCESSING                           => 102,
            self::EARLY_HINTS                          => 103,
            self::OK                                   => 200,
            self::CREATED                              => 201,
            self::ACCEPTED                             => 202,
            self::NON_AUTHORITATIVE_INFORMATION        => 203,
            self::NO_CONTENT                           => 204,
            self::RESET_CONTENT                        => 205,
            self::PARTIAL_CONTENT                      => 206,
            self::MULTI_STATUS                         => 207,
            self::ALREADY_REPORTED                     => 208,
            self::IM_USED                              => 226,
            self::MULTIPLE_CHOICES                     => 300,
            self::MOVED_PERMANENTLY                    => 301,
            self::FOUND                                => 302,
            self::SEE_OTHER                            => 303,
            self::NOT_MODIFIED                         => 304,
            self::USE_PROXY                            => 305,
            self::RESERVED                             => 306,
            self::TEMPORARY_REDIRECT                   => 307,
            self::PERMANENTLY_REDIRECT                 => 308,
            self::BAD_REQUEST                          => 400,
            self::UNAUTHORIZED                         => 401,
            self::PAYMENT_REQUIRED                     => 402,
            self::FORBIDDEN                            => 403,
            self::NOT_FOUND                            => 404,
            self::METHOD_NOT_ALLOWED                   => 405,
            self::NOT_ACCEPTABLE                       => 406,
            self::PROXY_AUTHENTICATION_REQUIRED        => 407,
            self::REQUEST_TIMEOUT                      => 408,
            self::CONFLICT                             => 409,
            self::GONE                                 => 410,
            self::LENGTH_REQUIRED                      => 411,
            self::PRECONDITION_FAILED                  => 412,
            self::REQUEST_ENTITY_TOO_LARGE             => 413,
            self::REQUEST_URI_TOO_LONG                 => 414,
            self::UNSUPPORTED_MEDIA_TYPE               => 415,
            self::REQUESTED_RANGE_NOT_SATISFIABLE      => 416,
            self::EXPECTATION_FAILED                   => 417,
            self::IM_A_TEAPOT                          => 418,
            self::MISDIRECTED_REQUEST                  => 421,
            self::UNPROCESSABLE_ENTITY                 => 422,
            self::LOCKED                               => 423,
            self::FAILED_DEPENDENCY                    => 424,
            self::TOO_EARLY                            => 425,
            self::UPGRADE_REQUIRED                     => 426,
            self::PRECONDITION_REQUIRED                => 428,
            self::TOO_MANY_REQUESTS                    => 429,
            self::REQUEST_HEADER_FIELDS_TOO_LARGE      => 431,
            self::UNAVAILABLE_FOR_LEGAL_REASONS        => 451,
            self::INTERNAL_SERVER_ERROR                => 500,
            self::NOT_IMPLEMENTED                      => 501,
            self::BAD_GATEWAY                          => 502,
            self::SERVICE_UNAVAILABLE                  => 503,
            self::GATEWAY_TIMEOUT                      => 504,
            self::VERSION_NOT_SUPPORTED                => 505,
            self::VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL => 506,
            self::INSUFFICIENT_STORAGE                 => 507,
            self::LOOP_DETECTED                        => 508,
            self::NOT_EXTENDED                         => 510,
            self::NETWORK_AUTHENTICATION_REQUIRED      => 511,
        };
    }


    /**
     * Returns a pretty printed formal title for the specified enum.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function status_text(): string {
        return match ($this) {
            self::CONTINUE                             => 'Continue',
            self::SWITCHING_PROTOCOLS                  => 'Switching Protocols',
            self::PROCESSING                           => 'Processing',
            self::EARLY_HINTS                          => 'Early Hints',
            self::OK                                   => 'OK',
            self::CREATED                              => 'Created',
            self::ACCEPTED                             => 'Accepted',
            self::NON_AUTHORITATIVE_INFORMATION        => 'Non-Authoritative Information',
            self::NO_CONTENT                           => 'No Content',
            self::RESET_CONTENT                        => 'Reset Content',
            self::PARTIAL_CONTENT                      => 'Partial Content',
            self::MULTI_STATUS                         => 'Multi-Status',
            self::ALREADY_REPORTED                     => 'Already Reported',
            self::IM_USED                              => 'IM Used',
            self::MULTIPLE_CHOICES                     => 'Multiple Choices',
            self::MOVED_PERMANENTLY                    => 'Moved Permanently',
            self::FOUND                                => 'Found',
            self::SEE_OTHER                            => 'See Other',
            self::NOT_MODIFIED                         => 'Not Modified',
            self::USE_PROXY                            => 'Use Proxy',
            self::RESERVED                             => 'Unused',
            self::TEMPORARY_REDIRECT                   => 'Temporary Redirect',
            self::PERMANENTLY_REDIRECT                 => 'Permanent Redirect',
            self::BAD_REQUEST                          => 'Bad Request',
            self::UNAUTHORIZED                         => 'Unauthorized',
            self::PAYMENT_REQUIRED                     => 'Payment Required',
            self::FORBIDDEN                            => 'Forbidden',
            self::NOT_FOUND                            => 'Not Found',
            self::METHOD_NOT_ALLOWED                   => 'Method Not Allowed',
            self::NOT_ACCEPTABLE                       => 'Not Acceptable',
            self::PROXY_AUTHENTICATION_REQUIRED        => 'Proxy Authentication Required',
            self::REQUEST_TIMEOUT                      => 'Request Timeout',
            self::CONFLICT                             => 'Conflict',
            self::GONE                                 => 'Gone',
            self::LENGTH_REQUIRED                      => 'Length Required',
            self::PRECONDITION_FAILED                  => 'Precondition Failed',
            self::REQUEST_ENTITY_TOO_LARGE             => 'Content Too Large',
            self::REQUEST_URI_TOO_LONG                 => 'URI Too Long',
            self::UNSUPPORTED_MEDIA_TYPE               => 'Unsupported Media Type',
            self::REQUESTED_RANGE_NOT_SATISFIABLE      => 'Range Not Satisfiable',
            self::EXPECTATION_FAILED                   => 'Expectation Failed',
            self::IM_A_TEAPOT                          => 'I\'m a teapot',
            self::MISDIRECTED_REQUEST                  => 'Misdirected Request',
            self::UNPROCESSABLE_ENTITY                 => 'Unprocessable Content',
            self::LOCKED                               => 'Locked',
            self::FAILED_DEPENDENCY                    => 'Failed Dependency',
            self::TOO_EARLY                            => 'Too Early',
            self::UPGRADE_REQUIRED                     => 'Upgrade Required',
            self::PRECONDITION_REQUIRED                => 'Precondition Required',
            self::TOO_MANY_REQUESTS                    => 'Too Many Requests',
            self::REQUEST_HEADER_FIELDS_TOO_LARGE      => 'Request Header Fields Too Large',
            self::UNAVAILABLE_FOR_LEGAL_REASONS        => 'Unavailable For Legal Reasons',
            self::INTERNAL_SERVER_ERROR                => 'Internal Server Error',
            self::NOT_IMPLEMENTED                      => 'Not Implemented',
            self::BAD_GATEWAY                          => 'Bad Gateway',
            self::SERVICE_UNAVAILABLE                  => 'Service Unavailable',
            self::GATEWAY_TIMEOUT                      => 'Gateway Timeout',
            self::VERSION_NOT_SUPPORTED                => 'HTTP Version Not Supported',
            self::VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL => 'Variant Also Negotiates',
            self::INSUFFICIENT_STORAGE                 => 'Insufficient Storage',
            self::LOOP_DETECTED                        => 'Loop Detected',
            self::NOT_EXTENDED                         => 'Not Extended',
            self::NETWORK_AUTHENTICATION_REQUIRED      => 'Network Authentication Required',
        };
    }


    /**
     * Returns the Mozilla explanation of what the specified enum.
     * 
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function explanation(): string {
        return match ($this) {
            self::OK => <<<HTML
            <p>The request succeeded. The result meaning of "success" depends on the HTTP method:</p>
            <ul>
                <li><code>GET</code>: The resource has been fetched and transmitted in the message body.</li>
                <li><code>HEAD</code>: The representation headers are included in the response without any message body.</li>
                <li><code>PUT</code> or <code>POST</code>: The resource describing the result of the action is transmitted in the message body.</li>
                <li><code>TRACE</code>: The message body contains the request message as received by the server.</li>
            </ul>
            HTML,

            self::TEMPORARY_REDIRECT => <<<HTML
            <p>
            The server sends this response to direct the client to get the requested resource at another URI with the same method that was used in the prior request.
            This has the same semantics as the <code>302 Found</code> HTTP response code, with the exception that the user agent <em>must not</em> change the HTTP method used: if a <code>POST</code> was used in the first request, a <code>POST</code> must be used in the second request.
            </p>
            HTML,

            self::PERMANENTLY_REDIRECT => <<<HTML
            <p>
            This means that the resource is now permanently located at another URI, specified by the <code>Location:</code> HTTP Response header.
            This has the same semantics as the <code>301 Moved Permanently</code> HTTP response code, with the exception that the user agent <em>must not</em> change the HTTP method used: if a <code>POST</code> was used in the first request, a <code>POST</code> must be used in the second request.
            </p>
            HTML,

            self::BAD_REQUEST => <<<HTML
            <p>The server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing).</p>
            HTML,

            self::UNAUTHORIZED => <<<HTML
            <p>
            Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated".
            That is, the client must authenticate itself to get the requested response.
            </p>
            HTML,

            self::FORBIDDEN => <<<HTML
            <p>
            The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource.
            Unlike <code>401 Unauthorized</code>, the client's identity is known to the server.
            </p>
            HTML,

            self::NOT_FOUND => <<<HTML
            <p>
            The server cannot find the requested resource.
            In the browser, this means the URL is not recognized.
            In an API, this can also mean that the endpoint is valid but the resource itself does not exist.
            Servers may also send this response instead of <code>403 Forbidden</code> to hide the existence of a resource from an unauthorized client.
            This response code is probably the most well known due to its frequent occurrence on the web.
            </p>
            HTML,

            self::INTERNAL_SERVER_ERROR => <<<HTML
            <p>The server has encountered a situation it does not know how to handle.</p>
            HTML,

            default => "No explenation given. Please see <a href='https://developer.mozilla.org/en-US/docs/Web/HTTP/Status' target='_blank' rel='noopener'>here</a> for more info on HTTP status codes",
        };
    }


    /**
     * Returns the default predefined image to use in an error page.
     * 
     * @return  SVGImages|null
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function image(): ?SVGImages {
        return match ($this) {
            // self::BAD_REQUEST
            self::UNAUTHORIZED => SVGImages::error401,
            self::FORBIDDEN    => SVGImages::error403,
            self::NOT_FOUND    => SVGImages::error404,
                // self::INTERNAL_SERVER_ERROR

            default => null,
        };
    }


    /**
     * Throw a specific HTTP status code error / status according to the Status code parsed.
     * Returns as a JSON
     * 
     * @param   HTTPStatusCode  $code   Parsed enum of the status.
     * 
     * @return  never
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function throw_error(HTTPStatusCode $code): never {
        header('Content-Type: application/json');
        http_response_code($code->code());
        $payload = [];

        if ($code->code >= 400) {
            $payload['status'] = 'error';
        }
        $payload['response_code'] = $code->code();
        $payload['message'] = $code->status_text();

        echo json_encode($payload);
        die;
    }
}
