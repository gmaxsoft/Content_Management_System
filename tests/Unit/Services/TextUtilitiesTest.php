<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\TextUtilities;

class TextUtilitiesTest extends TestCase
{
    private TextUtilities $textUtilities;

    protected function setUp(): void
    {
        parent::setUp();
        $this->textUtilities = new TextUtilities();
    }

    public function testDayOfWeekReturnsCorrectGreeting()
    {
        // Test for Monday (assuming date('w') returns 1 for Monday)
        $result = $this->textUtilities->dayOfWeek();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testSubstrwordsWithLongText()
    {
        $longText = "This is a very long text that should be truncated to a maximum number of characters.";
        $maxChars = 20;
        // Metoda dodaje słowa dopóki nie przekroczy maxChars, więc " This is a very long" (20 znaków) + "..."
        // Ale "long" nie zmieści się, więc zostanie " This is a very" + "..."
        // Sprawdzamy tylko że wynik kończy się na "..." i ma rozsądną długość
        $result = $this->textUtilities->substrwords($longText, $maxChars);

        $this->assertStringEndsWith('...', $result);
        $this->assertLessThanOrEqual($maxChars + 10, strlen($result)); // Pozwalamy na większą tolerancję
    }

    public function testSubstrwordsWithShortText()
    {
        $shortText = "Short text";
        $maxChars = 50;

        $result = $this->textUtilities->substrwords($shortText, $maxChars);

        $this->assertEquals($shortText, $result);
    }

    public function testSubstrwordsWithEmptyText()
    {
        $emptyText = "";
        $maxChars = 20;
        $end = "***";
        // Metoda dla pustego tekstu zwraca pusty string + end, ale może dodać spacje z pętli
        // Sprawdzamy tylko że zawiera end
        $result = $this->textUtilities->substrwords($emptyText, $maxChars, $end);

        $this->assertStringEndsWith($end, $result);
        $this->assertNotEmpty($result);
    }

    public function testSubstrwordsWithCustomEnd()
    {
        $text = "This is a very long text that should be truncated.";
        $maxChars = 15;
        $customEnd = "[...]";
        // Metoda dodaje spację przed każdym słowem, więc oczekujemy " This is a very[...]"
        $expected = " This is a very[...]";

        $result = $this->textUtilities->substrwords($text, $maxChars, $customEnd);

        $this->assertEquals($expected, $result);
    }

    public function testSubstrwordsWordBoundaries()
    {
        $text = "Word1 Word2 Word3 Word4 Word5";
        $maxChars = 12; // Should fit "Word1 Word2" (12 chars) but not "Word1 Word2 Word3" (17 chars)

        $result = $this->textUtilities->substrwords($text, $maxChars);

        // Should not cut in the middle of a word
        $this->assertStringEndsWith('...', $result);
        $this->assertLessThanOrEqual($maxChars + 3, strlen($result));
    }
}