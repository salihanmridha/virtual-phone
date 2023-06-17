<?php

namespace App\Services;

class Scrapper
{

    /**
     * @param string $url
     *
     * @return false|string|null
     */
    public function phpScrapperByGetMethod(string $url)
    {
        $context = stream_context_create(
            array(
                "http" => array(
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                )
            )
        );

        $html = null;

        try {
            $html = file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            $html = null;
        }

        return $html;
    }



}
