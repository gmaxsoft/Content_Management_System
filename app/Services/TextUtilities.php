<?php

namespace App\Services;

use App\Services\Interfaces\TextUtilitiesInterface;

class TextUtilities implements TextUtilitiesInterface
{
    public function dayOfWeek(): string
    {
        $dow = date("w");
        switch ($dow) {
            case 0:
                $html = "udanej niedzieli";
                break;
            case 1:
                $html = "udanego poniedziałku";
                break;
            case 2:
                $html = "udanego wtorku";
                break;
            case 3:
                $html = "udanej środy";
                break;
            case 4:
                $html = "udanego czwartku";
                break;
            case 5:
                $html = "udanego piątku";
                break;
            case 6:
                $html = "udanej soboty";
                break;
            default:
                $html = "udanego dnia";
        }

        return $html;
    }

    public function substrwords(string $text, int $maxchar, string $end = '...'): string
    {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);
            $output = '';
            $i = 0;
            while (1) {
                $length = strlen($output) + strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } else {
            $output = $text;
        }
        return $output;
    }
}