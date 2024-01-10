<?php


namespace App\Objects;


use App\Models\PdfAddressStartPosition;
use Codedge\Fpdf\Fpdf\Fpdf;

class pdfAddress extends FPDF
{
    protected $col = 0; // Current column
    protected $row = 0; // Current column
    private $wrongChars = ["ě","č", "Č", "ř", "ų"];
    private $rightChars = ["e", "c", "C", "r", "u"];
    protected $y0;      // Ordinate of column start

    private $myAddress;
    private $start = [
        0 => ["x" => 0, "y" => 0],
        1 => ["x" => 0, "y" => 1],
        2 => ["x" => 1, "y" => 0],
        3 => ["x" => 1, "y" => 1],
    ];

    private $startPosition = 0;

    public function init($myAddress, $start = -1)
    {
        if ($start < 0 || $start > 3)
            $this->startPosition = PdfAddressStartPositionObject::getPosition();
        else
            $this->startPosition = $start;

        $this->SetCol($this->start[$this->startPosition]["x"]);
        $this->SetRow($this->start[$this->startPosition]["y"]);

        $this->myAddress = $myAddress;
        $this->AddPage("L");

    }

    function SetCol($col)
    {
        // Set position at a given column
        $this->col = $col;
        $x = 10 + $col * 148;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    function SetRow($row)
    {
        $this->row = $row;
        $y = 5 + $row * 105;
        $this->SetLeftMargin(10 + $this->col * 148);

        $this->SetTopMargin($y);
        $this->SetY($y);
    }

    function next()
    {
        if ($this->row == 1) {
            $this->SetCol(intval(!$this->col));
        }
        $this->SetRow(intval(!$this->row));
        $this->startPosition++;
        if ($this->startPosition == 4)
            $this->startPosition = 0;

        if (!$this->row && !$this->col)
            $this->AddPage("L");
    }

    public function show($address, $shippingMethod)
    {
        $this->showAddress("Sender:", $this->myAddress, 4, 10);

        $this->SetXY(70 + $this->col * 148, 5 + $this->row * 105);
        $this->SetLeftMargin(20 + $this->col * 148);
        if ($shippingMethod) {
            $this->SetFont('Arial', '', 11);
            $this->Rect(87 + $this->col * 148, 2 + $this->row * 105, 18, 15);
            $this->Rect(105 + $this->col * 148, 2 + $this->row * 105, 40, 15);
            $this->SetXY(90 + $this->col * 148, 5 + $this->row * 105);
            $this->Image('../public/storage/posta_logo.png', null, null,12);
            $this->SetXY(105 + $this->col * 148, 2 + $this->row * 105);
            $this->Cell(40, 6, "P.P.", 0, 1, 'C');
            $this->SetXY(105 + $this->col * 148, 6 + $this->row * 105);
            $this->Cell(40, 6, "ID 463696001", 0, 1, 'C');
            $this->SetXY(105 + $this->col * 148, 10 + $this->row * 105);
            $this->Cell(40, 6, "CZECH REPUBLIC", 0, 1, 'C');
            $this->SetXY(87 + $this->col * 148, 30 + $this->row * 105);
            $this->Cell(0, 6, $shippingMethod->price . ' ' . $shippingMethod->method->name, 0, 1, 'L');
        }

        $this->SetXY(63 + $this->col * 148, 48 + $this->row * 105);
        $this->SetLeftMargin(69 + $this->col * 148);


        $max = $this->showAddress("Addresser:", $address, 6, 14);

        $this->showLines($max);

        $this->next();
    }

    private function showAddress($caption, $address, $h, $f)
    {
        $this->SetFont('Arial', '', $f);

        $max = $this->checkAddressWidth($address, $h);
        if ($max > 73) {
            $x = $this->lMargin - ($max - 73);
            $this->SetLeftMargin($x);
            $this->SetX($x);
        }
        $this->SetFont('Arial', '', 6);

        $this->Cell(0, $h, iconv('UTF-8', 'windows-1250', $caption), 0, 1, 'L');
        $this->SetFont('Arial', '', $f);

        try {
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1250', str_replace($this->wrongChars, $this->rightChars, $address->name)), 0, 1, 'L');
        } catch (\Exception $e) {
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1252', str_replace($this->wrongChars, $this->rightChars, $address->name)), 0, 1, 'L');

        }
        try {
        if ($address->extra != null)
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1250', str_replace($this->wrongChars, $this->rightChars, $address->extra)), 0, 1, 'L');
        } catch (\Exception $e) {
            \Debugbar::info($address->extra);
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1252', str_replace($this->wrongChars, $this->rightChars, $address->extra)), 0, 1, 'L');

        }
        try {
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1250', str_replace($this->wrongChars, $this->rightChars, $address->street)), 0, 1, 'L');
        } catch (\Exception $e) {
            \Debugbar::info($address->street);
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1252', str_replace($this->wrongChars, $this->rightChars, $address->street)), 0, 1, 'L');

        }
        if ($address->number != null || $address->flat != null)
            $this->Cell(0, $h, ($address->number != null ? iconv('UTF-8', 'windows-1252', $address->number) : '') . "/" . ($address->flat != null ? iconv('UTF-8', 'windows-1252', $address->flat) : ''), 0, 1, 'L');
        $this->Cell($this->GetStringWidth($address->postal) + $h / 2, $h, iconv('UTF-8', 'windows-1252', $address->postal), 0, 0, 'L');
        try {
            \Debugbar::info($address->city);
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1252', str_replace($this->wrongChars, $this->rightChars, $address->city)), 0, 1, 'L');
        } catch (\Exception $e) {
            $this->Cell(0, $h, iconv('UTF-8', 'windows-1250', str_replace($this->wrongChars, $this->rightChars, $address->city)), 0, 1, 'L');
        }
        $this->Cell(0, $h, iconv('UTF-8', 'windows-1252', Countries::getCountryByCode($address->country)), 0, 1, 'L');

        return $max;
    }

    public function checkAddressWidth($address, $h)
    {
        $max = $this->GetStringWidth($address->name);
        if ($address->extra != null && $this->GetStringWidth($address->extra) > $max)
            $max = $this->GetStringWidth($address->extra);
        if ($address->street != null && $this->GetStringWidth($address->street) > $max)
            $max = $this->GetStringWidth($address->street);
        if (($address->postal != null || $address->city != null) && (($this->GetStringWidth($address->postal) + $h / 2) + $this->GetStringWidth($address->city) > $max))
            $max = ($this->GetStringWidth($address->postal) + $h / 2) + $this->GetStringWidth($address->city);

        return $max;

    }

    public function Output($dest = 'I', $name = 'doc.pdf', $isUTF8 = false)
    {
        PdfAddressStartPositionObject::setPosition($this->startPosition);
        return parent::Output($dest, $name, $isUTF8);
    }

    public function showLines($max){
        $x = 5 + $this->col * 148;
        $y = 3 + $this->row * 105;

        $this->rect($x,$y, 50,25);
        $this->line($x,$y, $x + 50, $y + 25);
        $this->line($x,$y + 25, $x + 50, $y);

        $x = 67 + $this->col * 148;
        $y = 49 + $this->row * 105;
        $x2 = 0;
        if ($max > 73) {
            $x2 = ($max - 73);
        }
        $this->rect($x-$x2,$y, 78 +$x2,36);

    }
}

