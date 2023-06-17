<?php
namespace App\Services;

use App\Models\VirtualPhone;
use App\Services\Scrapper;
class VirtualPhoneInitialScarap
{
    private Scrapper $scrapper;
    private $url = "https://receive-smss.com/";
    public function __construct(Scrapper $scrapper)
    {
        $this->scrapper = $scrapper;
    }

    /**
     * @param string $number
     *
     * @return null
     */
    public function initialScrap()
    {
        $html = $this->scrapper->phpScrapperByGetMethod($this->url);
        $countClasses = $this->countClasses($html,"number-boxes-itemm-number");

        return $this->storeVirtualPhone($html, $countClasses);
    }

    private function countClasses($html, $className)
    {
        $className = $className;
        $pattern = '/class\s*=\s*[\'"]([^\'"]*\\b' . preg_quote($className, '/') . '\\b[^\'"]*)[\'"]/i';

        $count = preg_match_all($pattern, $html, $matches);

        return $count;
    }

    private function storeVirtualPhone($html, $cocuntClasses)
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_use_internal_errors(false);

        $xpath = new \DOMXPath($doc);

        $numberElements = $xpath->query('//div[contains(@class, "number-boxes-itemm-number")]');
        $countryElements = $xpath->query('//div[contains(@class, "number-boxess-item-country")]');

        if ($numberElements->length === $countryElements->length) {
            $count = $numberElements->length;
            for ($i = 0; $i < $count; $i++) {
                $number = str_replace("+", "", $numberElements->item($i)->textContent);
                $country = $countryElements->item($i)->textContent;
                // Process the extracted values as needed
                echo "Number: $number, Country: $country" . PHP_EOL;
            }
        } else {
            echo "Error: Number of elements mismatched." . PHP_EOL;
        }
    }
}
