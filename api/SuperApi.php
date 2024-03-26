<?php
/*
 * This file is part of the Simple REST Full API project.
 *
 * (c) Denis Puzik <scorpion3dd@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace api;

/**
 * Class SuperApi abstract
 * @package api
 */
abstract class SuperApi
{
    const API = 'api';
    const VERSION_1 = 'v1';

    const API_RESERVATION = 'reservation';
    const API_TICKET = 'ticket';
    const API_CALLBACK = 'callback';

    const API_SECRET_KEY = 'a1b2c3d4e5f6a1b2c3d4e5f6';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public $requestUri = [];
    public $headers = [];
    public $postData;
    public $jsonData = [];
    public $requestParams = [];

    protected $method = '';
    protected $action = '';
    protected $output = '';


    /**
     * SuperApi constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->getOutput();
        $this->getHeaders();
        $this->getRequests();
        $this->getPostData();
        $this->getJsonData();
        $this->getMethod();

        $this->setHeaders();
    }

    /**
     * Get Output
     */
    protected function getOutput(): void
    {
        $this->output = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Set requests
     */
    protected function getRequests(): void
    {
        if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != ''){
            $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        }
        if(isset($_REQUEST)){
            $this->requestParams = $_REQUEST;
        }
    }

    /**
     * Get headers
     */
    protected function getHeaders(): void
    {
        $this->headers = $this->getallheaders();
    }

    /**
     * @return array
     */
    protected function getallheaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get Post Data
     */
    protected function getPostData(): void
    {
        $this->postData = file_get_contents('php://input');
    }

    /**
     * Get Jost Data
     */
    protected function getJsonData(): void
    {
        if(!empty($this->postData)){
            $this->jsonData = json_decode($this->postData, true);
            if(isset($this->jsonData['data'])){
                $this->requestParams = array_merge($this->requestParams, $this->jsonData['data']);
            }
        }
    }

    /**
     * Get method
     *
     * @throws \Exception
     */
    protected function getMethod(): void
    {
        if(isset($_SERVER['REQUEST_METHOD'])){
            $this->method = $_SERVER['REQUEST_METHOD'];
        }
        if ($this->method == self::METHOD_POST && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == self::METHOD_DELETE) {
                $this->method = self::METHOD_DELETE;
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == self::METHOD_PUT) {
                $this->method = self::METHOD_PUT;
            } else {
                throw new \Exception("Unexpected Method Header");
            }
        }
        if(is_null($this->method)){
            $this->method = self::METHOD_GET;
        }
    }

    /**
     * Set headers
     */
    protected function setHeaders(): void
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
    }

    /**
     * Routing
     *
     * @return bool
     */
    protected function routing(): bool
    {
        return isset($this->requestUri[0]) && $this->requestUri[0] == self::API
            && isset($this->requestUri[1]) && $this->requestUri[1] == self::VERSION_1
            && (isset($this->requestUri[2]) && $this->requestUri[2] == self::API_RESERVATION
                || isset($this->requestUri[2]) && $this->requestUri[2] == self::API_TICKET
                || isset($this->requestUri[2]) && $this->requestUri[2] == self::API_CALLBACK
            );
    }

    /**
     * Is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return isset($this->requestParams['secret_key'])
            && $this->requestParams['secret_key'] == self::API_SECRET_KEY;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        if($this->isAuthenticated()){
            if($this->routing()) {
                $this->action = $this->getAction();
                if (method_exists($this, $this->action)) {
                    return $this->{$this->action}();
                } else {
                    throw new \RuntimeException('API invalid action', 405);
                }
            }
            else{
                throw new \RuntimeException('API Not Found', 404);
            }
        }
        else{
            throw new \RuntimeException('API Not Authenticated', 403);
        }
    }

    /**
     * @return int
     */
    protected function getId(): int
    {
        $id = 0;
        if(!empty($this->requestParams['id'])){
            $id = (int)$this->requestParams['id'];
        }
        return $id;
    }

    /**
     * @param $data
     * @param int $status
     * @return false|string
     */
    protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    /**
     * @param $code
     * @return mixed
     */
    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            403 => 'Not Authenticated',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }

    /**
     * @return string|null
     */
    protected function getAction()
    {
        switch ($this->method) {
            case 'GET':
                if(isset($this->requestParams) && !empty(isset($this->requestParams['id']))){
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }

    abstract protected function indexAction();

    abstract protected function viewAction();

    abstract protected function createAction();

    abstract protected function updateAction();

    abstract protected function deleteAction();
}
