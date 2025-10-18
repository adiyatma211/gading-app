<?php

return [
    // Lebar kertas thermal dalam milimeter. Umum: 58, 72, 80
    'thermal_width_mm' => env('THERMAL_WIDTH_MM', 72),

    // Tinggi kertas dalam milimeter (perkiraan). Akan di-override dinamis berdasarkan jumlah item.
    'thermal_height_mm' => env('THERMAL_HEIGHT_MM', 800),

    // Nama printer Windows untuk escpos (opsional). Contoh: "POS-58" atau "EPSON TM-T82".
    'thermal_printer_name' => env('THERMAL_PRINTER', ''),
];

