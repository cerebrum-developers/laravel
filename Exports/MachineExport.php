<?php

namespace App\Exports;

use App\Models\Machines;
use League\CommonMark\Reference\Reference;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;





class MachineExport implements FromCollection, WithHeadings, WithEvents, WithColumnWidths
{
    protected $dataArray;
    protected $machin;


    public function __construct(array $dataArray, array $machin)
    {
        $this->dataArray = $dataArray;
        $this->machin = $machin;
    }

    public function collection()
    {
        return collect($this->dataArray);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'Current (Amp)',
            'voltage (V)',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Set width of column A to 20
            'B' => 20, // Set width of column B to 20
            'C' => 20, // Set width of column C to 20
            'D' => 20, // Set width of column C to 20

        ];
    }



    public function registerEvents(): array
    {
        $machin = $this->machin;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($machin) {
                $cellRange = 'A1:D1';
        
                $event->sheet->getDelegate()->getStyle($cellRange)
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFF00');
        
                // Make the titles bold
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
        
                // Insert new rows before the existing first row
                $event->sheet->getDelegate()->insertNewRowBefore(1, 6);
        
                // Machin ID
                $event->sheet->getDelegate()->setCellValue('A1', $machin['machinId']);
                $event->sheet->getDelegate()->mergeCells('A1:D1');
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A1:D1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        
                // Reference ID
                $event->sheet->getDelegate()->setCellValue('A2', $machin['refrenceId']);
                $event->sheet->getDelegate()->mergeCells('A2:D2');
                $event->sheet->getDelegate()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A2:D2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        
                // Range
                $event->sheet->getDelegate()->setCellValue('A3', $machin['maxCurrent']);
                $event->sheet->getDelegate()->mergeCells('A3:D3');
                $event->sheet->getDelegate()->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A3:D3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        
                // Min Range
                $event->sheet->getDelegate()->setCellValue('A4', $machin['minCurrent']);
                $event->sheet->getDelegate()->mergeCells('A4:D4');
                $event->sheet->getDelegate()->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A4:D4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        
                // Voltage
                $event->sheet->getDelegate()->setCellValue('A5', $machin['maxVoltage']);
                $event->sheet->getDelegate()->mergeCells('A5:D5');
                $event->sheet->getDelegate()->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A5:D5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        
                // Min Voltage
                $event->sheet->getDelegate()->setCellValue('A6', $machin['minVoltage']);
                $event->sheet->getDelegate()->mergeCells('A6:D6');
                $event->sheet->getDelegate()->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $event->sheet->getDelegate()->getStyle('A6:D6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            },
        ];
        
        
        
    }

    
}
