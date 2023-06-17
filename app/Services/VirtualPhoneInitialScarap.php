<?php
namespace App\Services;

use App\Models\VirtualPhone;
use App\Services\Scrapper;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\HttpFoundation\Response;

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
        $virtualPhone = VirtualPhone::all();

        if ($virtualPhone->count() > 0){
            return ["msg" => "All data fetched and stored in database successfully.", "code" => Response::HTTP_OK, "success" => true];
        }

        $html = $this->scrapper->phpScrapperByGetMethod($this->url);

        if($html){
            $countClasses = $this->countClasses($html,"number-boxes-itemm-number");

            if ($countClasses > 0){
                return $this->storeVirtualPhone($html, $countClasses);
            }

        }

        return null;

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

        $count = $numberElements->length;

        $returnArr = ["msg" => "All data fetched and stored in database successfully.", "code" => Response::HTTP_OK, "success" => true];

        try {
            for ($i = 0; $i < $count; $i++) {
                $number = $numberElements->item($i)->textContent;
                $country = $countryElements->item($i)->textContent;
                $numberCode = $this->extractPhoneCode($number);
                $numberWithoutCode = substr($number, strlen($numberCode) + 1);

                VirtualPhone::create([
                    "countryCode" => $numberCode,
                    "countryName" => $country,
                    "number" => $numberWithoutCode,
                ]);
            }
        } catch (\Exception $e) {
            $returnArr = ["msg" => "Something went wrong, please try again!", "code" => Response::HTTP_BAD_REQUEST, "success" => false];
        }

        return $returnArr;

    }

    private function extractPhoneCode($number)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $parsedNumber = $phoneUtil->parse($number, null);
            return $parsedNumber->getCountryCode();

        } catch (\libphonenumber\NumberParseException $e) {
            return $e->getMessage();
        }
    }
}
