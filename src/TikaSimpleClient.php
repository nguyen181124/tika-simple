<?php


namespace HocVT\TikaSimple;


use GuzzleHttp\Client;
use GuzzleHttp\Utils;

class TikaSimpleClient
{
    protected $client;

    public function __construct(string $host = 'http://127.0.0.1:9998')
    {
        $this->client = new Client([
            'base_uri' => $host,
        ]);
    }

    public function version() : string {
        $endpoint = '/version';
        $method = 'GET';
        return $this->request($method, $endpoint);
    }

    public function language(string $string) : string{
        $endpoint = '/language/stream';
        $data = [
            'body' => $string,
        ];
        $method = 'PUT';
        return $this->request($method, $endpoint, $data);
    }

    /**
     * @param string|resource $content
     */
    public function mime($content){
        $endpoint = '/detect/stream';
        $data = [
            'body' => $content,
        ];
        $method = 'PUT';
        return $this->request($method, $endpoint, $data);
    }
    public function mimeFile($path){
        try{
            $fh = fopen($path, 'r+');
            return $this->mime($fh);
        } finally {
            if(is_resource($fh)){
                fclose($fh);
            }
        }
    }

    /**
     * @param $content
     * @param string $output
     * @param bool $recursive get all meta or only content
     * @return array
     */
    public function rmeta($content, string $output = '', bool $recursive = true){
        $endpoint = '/rmeta' . ( $output ? "/" . $output : "" );
        $data = [
            'body' => $content,
        ];
        $method = 'PUT';
        $result = Utils::jsonDecode($this->request($method, $endpoint, $data), true);
        if($recursive){
            return $result[0] ?? $result;
        }else{
            return $result[0]['X-TIKA:content'] ?? $result['X-TIKA:content'];
        }
    }

    /**
     * @param $path
     * @param string $output
     * @param bool $recursive
     * @return array
     */
    public function rmetaFile($path, string $output = '', bool $recursive = true){
        try{
            $fh = fopen($path, 'r+');
            return $this->rmeta($fh, $output, $recursive);
        } finally {
            if(is_resource($fh)){
                fclose($fh);
            }
        }
    }

    protected function request(string $method, string $endpoint, array $data = []) : string{
        $response = $this->client->request($method, $endpoint, $data);
        return $response->getBody()->getContents();
    }
}