<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;

class SmsHistoryService
{
    private Scrapper $scrapper;
    private $url = "https://receive-smss.com/sms/";
    public function __construct(Scrapper $scrapper)
    {
        $this->scrapper = $scrapper;
    }

    public function getSmsHistory($number)
    {
        $html = $this->scrapper->phpScrapperByGetMethod($this->url . $number);

        if($html){
            return $this->formatSmsHistoryTable($html, $number);
        }

        return null;
    }

    private function formatSmsHistoryTable($html, $number)
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_use_internal_errors(false);

        $rows = $doc->getElementsByTagName('tbody')[0]->getElementsByTagName('tr');

        $data = [];

        $returnArr = ["msg" => "SMS History for $number", "code" => Response::HTTP_OK, "success" => true, "data" => $data];

        try {

            foreach ($rows as $row) {
                // Extract the required values from the table cells
                $from = $row->getElementsByTagName('td')[3]->textContent;
                $text = $row->getElementsByTagName('td')[4]->textContent;
                $createdAt = $row->getElementsByTagName('td')[5]->textContent;

                // Create an array for the current row's data
                $rowData = [
                    'from' => $from,
                    'text' => trim($text),
                    'myNumber' => $number, // Replace with your desired value
                    'createdAt' => $createdAt,
                ];

                // Add the row data to the main data array
                $data[] = $rowData;
            }

        } catch (\Exception $e) {
            $returnArr = ["msg" => "Something went wrong, please try again!", "code" => Response::HTTP_BAD_REQUEST, "success" => false];
        }

        $returnArr["data"] =  $data;
        return $returnArr;
    }
}
