<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
return
        [
            'boolean_data' => [
                '0' => 'No',
                '1' => 'Yes'
            ],
            'po_status' => [
                'Draft' => 1,
                'Live PO' => 2,
                'Negotiating with Supplier' => 3,
                'Supplier Confirmed' => 4,
                'Book In' => 5,
                'Part Delivered' => 6,
                'Arrived' => 7,
                'Receiving' => 8,
                'Completed' => 9,
                'Cancelled' => 10,
            ],
            'po_import_type' => [
                'UK Purchase Order' => 1,
                'Import Purchase Order' => 2
            ],
            'supplier_category' => [
                'Stock Supplier' => 1,
                'No Stock Supplier' => 2,
                'Carrier' => 3,
                'Drop Shipping Supplier' => 4
            ],
            'incoterms' => [
                'EXW' => 'EXW (Ex Works)',
                'FCA' => 'FCA (Free Carrier)',
                'FAS' => 'FAS (Free Alongside Ship)',
                'FOB' => 'FOB (Free on Board)',
                'CFR' => 'CFR (Cost and Freight)',
                'CIF' => 'CIF (Cost, Insurance and Freight)',
                'CPT' => 'CPT (Carriage Paid to)',
                'CIP' => 'CIP (Carriage and Insurance Paid To)',
                'DAT' => 'DAT (Delivered at Terminal)',
                'DAP' => 'DAP (Delivered at Place)',
                'DDP' => 'DDP (Delivered Duty Paid)'
            ],
            'shippment' => [
                'Air' => 1,
                'Sea' => 2,
                'Truck' => 3,
                'Sea and Truck' => 4
            ],
            'po_status_color_code' => [
                1 => '#7f7f7f',
                2 => '#ef31e5',
                3 => '#415600',
                4 => '#009759',
                5 => '#911eb4',
                6 => '#f48231',
                7 => '#2ca2a2',
                8 => '#4362d7',
                9 => '#3cb34a',
                10 => '#e6194a',
            ]
];
