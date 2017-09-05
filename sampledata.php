// $data['cartonpack'][0] = array(
                //     'ordernumber' => '1087717',
                //     'printquantity' => '1', 
                //     'style' => '112379490',
                //     'description' => 'wts:KYLIEBAG2:8pk:multi',
                //     'productindicator' => '(00) 193327670001155097',
                //     'productindicatorbarcode' => '00193327670001155097',
                //     'packnumber' => '113245741',
                //     'packtype'=> 'A',
                //     'group'=> 'Accessories',
                //     'dept' =>'Accessories',
                //     'class' =>'Bags',
                //     'subclass'=> 'Casual',
                //     'carton' => array(
                //         array(
                //         'number' => '(400) 1087717 (02) 113245741 (30) 1',
                //         'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                //         ),
                //     ),
                //   );
                // $data['cartonpack'][1] = array(
                //     'ordernumber' => '1087717',
                //     'printquantity' => '1', 
                //     'style' => '112379490',
                //     'description' => 'wts:KYLIEBAG2:8pk:multi',
                //     'productindicator' => '(00) 193327670001155097',
                //     'productindicatorbarcode' => '00193327670001155097',
                //     'packnumber' => '113245741',
                //     'packtype'=> 'A',
                //     'group'=> 'Accessories',
                //     'dept' =>'Accessories',
                //     'class' =>'Bags',
                //     'subclass'=> 'Casual',
                //     'carton' => array(
                //         array(
                //         'number' => '(400) 1087717 (02) 113245741 (30) 1',
                //         'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                //         ),
                //     ),
                //   );
                // $data['cartonpack'][2] = array(
                //     'ordernumber' => '1087717',
                //     'printquantity' => '1', 
                //     'style' => '112379490',
                //     'description' => 'wts:KYLIEBAG2:8pk:multi',
                //     'productindicator' => '(00) 193327670001155097',
                //     'productindicatorbarcode' => '00193327670001155097',
                //     'packnumber' => '113245741',
                //     'packtype'=> 'A',
                //     'group'=> 'Accessories',
                //     'dept' =>'Accessories',
                //     'class' =>'Bags',
                //     'subclass'=> 'Casual',
                //     'carton' => array(
                //         array(
                //         'number' => '(400) 1087717 (02) 113245741 (30) 1',
                //         'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                //         ),
                //     ),
                //   );
                // $data['cartonpack'][3] = array(
                //     'ordernumber' => '1087717',
                //     'printquantity' => '1', 
                //     'style' => '112379490',
                //     'description' => 'wts:KYLIEBAG2:8pk:multi',
                //     'productindicator' => '(00) 193327670001155097',
                //     'productindicatorbarcode' => '00193327670001155097',
                //     'packnumber' => '113245741',
                //     'packtype'=> 'A',
                //     'group'=> 'Accessories',
                //     'dept' =>'Accessories',
                //     'class' =>'Bags',
                //     'subclass'=> 'Casual',
                //     'carton' => array(
                //         array(
                //         'number' => '(400) 1087717 (02) 113245741 (30) 1',
                //         'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                //         ),
                //     ),
                //   );
                // $data['cartonloose'][] = array(
                //     'ordernumber' => '1087717',
                //     'cartonquantity' => '6', 
                //     'style' => '112379490',
                //     'description' => 'wts:KYLIEBAG2:8pk:multi',
                //     'productindicatorbarcode' => '(00) 193327670001155097',
                //     'productindicator' => '00193327670001155097',
                //     'itemnumber' => '113245741',
                //     'size'=> '1SIZ',
                //     'colour'=> 'BLK~Black',
                //     'carton' => array(
                //         array(
                //         'barcode' => '(400) 1087717 (02) 113245741 (30) 1',
                //         'number' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                //         ),
                //     ),
                //   );
                // $data['ratiopack'][] = array(
                //     'itemnumber'    => '112805541',
                //     'description1'  => '0103LV34919',
                //     'description2'  => 'GRN~Sierre XL',
                //     'size'          => 'XL',
                //     'stockroomlocator' => '347015',
                //     'barcode'       => '400001561726',
                //     'barcodetype' => 'ean13'
                //     );
                // $data['simplepack'][] = array(
                //     'itemnumber'    => '112805541',
                //     'description1'  => '0103LV34919',
                //     'description2'  => 'GRN~Sierre XL',
                //     'size'          => 'XL',
                //     'stockroomlocator' => 'PACK 6',
                //     'barcode'       => '400001561726',
                //     'barcodetype' => 'ean13'
                //     );
                // $data['looseitem'][] = array(
                //     'itemnumber'    => '112805541',
                //     'description1'  => '0103LV34919',
                //     'description2'  => 'GRN~Sierre XL',
                //     'size'          => 'XL',
                //     'stockroomlocator' => '347015',
                //     'barcode'       => '400001561726',
                //     'barcodetype' => 'ean13'
                //     );
                // dd($data);
                // $pdf = PDF::loadView('labels.new',['data' => $data]);
                // return $pdf->stream();

                $data['cartonpack'][0] = array(
                    'ordernumber' => '1087717',
                    'printquantity' => '1', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'packnumber' => '113245741',
                    'packtype'=> 'A',
                    'group'=> 'Accessories',
                    'dept' =>'Accessories',
                    'class' =>'Bags',
                    'subclass'=> 'Casual',
                    'carton' => array(
                        0 => array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                        1 => array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                        2 => array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
            
            $data['cartonpack'][3] = array(
                    'ordernumber' => '1087717',
                    'printquantity' => '1', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'packnumber' => '113245741',
                    'packtype'=> 'A',
                    'group'=> 'Accessories',
                    'dept' =>'Accessories',
                    'class' =>'Bags',
                    'subclass'=> 'Casual',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
            $data['cartonpack'][4] = array(
                    'ordernumber' => '1087717',
                    'printquantity' => '1', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'packnumber' => '113245741',
                    'packtype'=> 'A',
                    'group'=> 'Accessories',
                    'dept' =>'Accessories',
                    'class' =>'Bags',
                    'subclass'=> 'Casual',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
            $data['cartonloose'][] = array(
                    'ordernumber' => '1087717',
                    'cartonquantity' => '6', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'itemnumber' => '113245741',
                    'size'=> '1SIZ',
                    'colour'=> 'BLK~Black',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
            $data['cartonloose'][] = array(
                    'ordernumber' => '1087717',
                    'cartonquantity' => '6', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'itemnumber' => '113245741',
                    'size'=> '1sdfsdfSIZ',
                    'colour'=> 'BLK~Black',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );