<?php
namespace Api;

/**
 * Abstract base class that defines common core functinality among action handlers for requests to website APIs. The
 * class expects to handle POST requests with request bodies containing JSON encoded data.
 */
class ActionHandler {

    /** @var \Util\Logger */
    protected $logger;

    /** @var mixed[] */
    protected $queryString;

    /** @var mixed[] */
    protected $requestBody;

    /**
     * Constructs a new instance of the action handler.
     * 
     * The handler will decode the JSON body and the query string associated with the request and store the results
     * internally.
     *
     * @param [type] $logger
     */
    public function __construct($logger) {
        $this->logger = $logger;
        $this->requestBody = \json_decode(\file_get_contents('php://input'), true);
        $this->queryString = array();
        \parse_str($_SERVER['QUERY_STRING'], $this->queryString);
    }

    /**
     * Verifies that the provided parameter name exists in the requst body. If it does not, the server will send
     * a BAD_REQUEST response to the client and the script will exit.
     *
     * @param string $name the name of the paramter expected in the request body
     * @param string|null $message the message to send back to the client if the check fails. I null, a default message
     * will be sent
     * @return void
     */
    public function requireParam($name, $message = null) {
        if (!\array_key_exists($name, $this->requestBody)) {
            $message = $message == null ? "Missing required request body parameter: $name" : $message;
            $this->respond(new Response(Response::BAD_REQUEST, $message));
        }
    }

    /**
     * Sends the provided response object to the client.
     * 
     * This function will exit the script after invocation.
     *
     * @param Response $response the response to send back to the client
     * @return void
     */
    public function respond($response) {
        $this->logger->info('Sending HTTP response: ' . $response->getCode() . ': ' . $response->getMessage());
        \header('Content-Type: application/json; charset=UTF-8');
        \http_response_code($response->getCode());
        echo $response->serialize();
        exit(0);
    }

    /**
     * Get the value of requestBody
     */ 
    public function getRequestBodyAsArray() {
        return $this->requestBody;
    }

    /**
     * Get the value of queryString
     */ 
    public function getQueryStringAsArray() {
        return $this->queryString;
    }
}
