<?php

namespace AppBundle\Service;

class Scraper
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Scraper constructor.
     * @param $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return \DOMElement
     */
    public function getLastChapters(){
        return $this->getElementsById("chapters-list");
    }

    /**
     * @param $id
     * @return \DOMElement
     */
    public function getElementsById($id)
    {
        $dom = $this->getDomDocument($this->baseUrl);

        return $dom->getElementById($id);
    }

    /**
     * @param $url
     * @param $tagName
     * @param $className
     *
     * @return \DOMNodeList|null
     */
    public function getElementsByClassName($tagName, $className){
        $dom = $this->getDomDocument($this->baseUrl);

        if ($dom) {
            $xpath = new \DOMXpath($dom);
            $tags = $xpath->query('//' .$tagName . '[contains(@class,"' .$className. '")]');

            return $tags;
        }

        return null;
    }

    /**
     * @param $url
     * @return \DOMDocument|null
     */
    public function getDomDocument($url){
        $dom = new \DOMDocument();

        $internalErrors = libxml_use_internal_errors(true);

        try {
            $dom->loadHTML($this->getHtml($url));
        }catch (\Exception $exception){
            $dom = null;
        }

        libxml_use_internal_errors($internalErrors);

        return $dom;
    }

    /**
     * @param $url
     * @return mixed
     */
    private function getHtml($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);

        return $server_output;
    }
}