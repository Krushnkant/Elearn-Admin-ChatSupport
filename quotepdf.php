<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
require_once dirname(__FILE__).'/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

include("include/Connection.php");

try {
    // get the HTML
    // ob_start();
    // include dirname(__FILE__).'/res/example07a.php';
    // include dirname(__FILE__).'/res/example07b.php';
    // $content = ob_get_clean();

    $quoteId = $_GET['quoteId'];

    $GetQuoteData = mysqli_query($db, "SELECT q.*, c.* FROM quotation AS q JOIN customer AS c ON q.Customerid = c.Customerid WHERE q.QuotationId = ".$quoteId);
    $FetchQuoteData = mysqli_fetch_array($GetQuoteData);
    $QuotationNumber = $FetchQuoteData['QuotationNumber'];
    $quoteWithPrice = $FetchQuoteData['quoteWithPrice'];
    $salesUserId = $FetchQuoteData['salesUserId'];
    $ReferenceBy = $FetchQuoteData['referenceBy'];
    $refMobileNo = $FetchQuoteData['refMobileNo'];
    $TotalMRP = $FetchQuoteData['TotalMRP'];
    $TotalSaleMRP = $FetchQuoteData['TotalSaleMRP'];
    $TotalDiscount = $FetchQuoteData['TotalDiscount'];
    $TotalQuantity = $FetchQuoteData['TotalQuantity'];
    $FinalAmount = $FetchQuoteData['FinalAmount'];
    $QuoteDate = date('d M, Y', strtotime($FetchQuoteData['quoteDate']));
    $QuoteDate2 = date('d/m/Y', strtotime($FetchQuoteData['quoteDate']));

    $CustomerName = $FetchQuoteData['CustomerName'];
    $Mobileno = $FetchQuoteData['Mobileno'];
    $Address = "";
    if($FetchQuoteData['Address'] != ''){
        $Address .= $FetchQuoteData['Address'];
    }
    if($FetchQuoteData['City'] != ''){
        $Address .= ', '.$FetchQuoteData['City'];
    }
    if($FetchQuoteData['State'] != ''){
        $Address .= ', '.$FetchQuoteData['State'];
    }

    $salesUserIdQ = mysqli_query($db, "SELECT Fullname FROM user WHERE UserId = ".$salesUserId);
    $FetchSalesUser = mysqli_fetch_array($salesUserIdQ);
    $SalesPersonName = $FetchSalesUser['Fullname'];

    $CompanyProfQ = mysqli_query($db,"SELECT * FROM companyProfile WHERE Userid = 1");
    $FetchCompany = mysqli_fetch_array($CompanyProfQ);
    $Terms = $FetchCompany['QuotDiscription'];
    $Icon = '';
    if($FetchCompany['CompanyImage'] != ''){
        $Icon = 'assets/images/company/'.$FetchCompany['CompanyImage'];
    }
    $CompanyName = $FetchCompany['CompanyName'];
    $CompanyEmail = $FetchCompany['CompanyEmail'];
    $CompanyAddreess = $FetchCompany['CompanyAddreess'];

    $HTMLContent = '<style type="text/css">
    <!--
    table { vertical-align: top; }
    tr    { vertical-align: top; }
    td    { vertical-align: top; }
    -->
    </style>';

    $FullColSpanRow = 8;
    $ColHeadForMrpPrice = '';
    $ColGrp = '<colgroup>
                <col style="width: 5%; text-align: center">
                <col style="width: 26%; text-align: center">
                <col style="width: 16%; text-align: center">
                <col style="width: 10%; text-align: center">
                <col style="width: 10%; text-align: center">
                <col style="width: 10%; text-align: center">
                <col style="width: 10%; text-align: center">
                <col style="width: 12%; text-align: center">
            </colgroup>';

    if($quoteWithPrice == 3){
        $ColGrp = '<colgroup>
                    <col style="width: 5%; text-align: center">
                    <col style="width: 25%; text-align: center">
                    <col style="width: 15%; text-align: center">
                    <col style="width: 9%; text-align: center">
                    <col style="width: 9%; text-align: center">
                    <col style="width: 9%; text-align: center">
                    <col style="width: 9%; text-align: center">
                    <col style="width: 9%; text-align: center">
                    <col style="width: 10%; text-align: center">
                </colgroup>';

        $ColHeadForMrpPrice = '<th style="border-bottom: solid 1px gray; padding:8px 0;">MRP</th>';
        $FullColSpanRow = 9;
    }

    $HTMLContent .= '<page backcolor="#FEFEFE" style="font-size: 12pt">
                        <bookmark title="Lettre" level="0" ></bookmark>
                        <table cellspacing="0" style="width: 100%; text-align: center; font-size: 14px; border-bottom: dotted 1px black;">
                            <tr>
                                <td style="width: 25%; color: #444444;">
                                    <img style="width: 100%;" src="'.$Icon.'" alt="Logo"><br>
                                </td>
                                <td style="width: 50%;">
                                	<h3 style="text-align: center; font-size: 20pt; margin-bottom: 0;">'.$CompanyName.'</h3>
			                        <h5 style="text-align: center; margin-bottom: 0;">'.$CompanyEmail.'</h5>
			                        <p style="padding-bottom:10px; text-align: center; font-size: 10pt margin-bottom: 0;">'.$CompanyAddreess.'</p>
                                </td>
                                <td style="width: 25%;">
                                </td>
                            </tr>
                        </table>
                        <br>
                        <div style="width:100%; margin-top:0px; padding-top:0px; text-align: center; font-size: 15pt;"><b>Quotation</b></div>
                        <table cellspacing="0" style="width: 100%;">
                            <colgroup>
                                <col style="width: 12%;">
                                <col style="width: 62%;">
                                <col style="width: 12%;">
                                <col style="width: 14%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td style="font-size: 12pt; padding:2px 0;">
                                        Customer
                                    </td>
                                    <td style="font-size: 12pt; padding:2px 0;">
                                        : <b>'.$CustomerName.'</b>
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Quotation No
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        : '.$QuotationNumber.'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Address
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        : '.$Address.'
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Date
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        : '.$QuoteDate.'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Mobile
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        : '.$Mobileno.'
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table cellspacing="0" style="width: 100%;">
                            <colgroup>
                                <col style="width: 12%;">
                                <col style="width: 30%;">
                                <col style="width: 8%;">
                                <col style="width: 30%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Reference
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        : '.$ReferenceBy.'
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Mobile
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        : '.$refMobileNo.'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Sales Person
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        : '.$SalesPersonName.'
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;"></td>
                                    <td style="font-size: 10pt; padding:2px 0;"></td>
                                </tr>
                            </tbody>
                        </table>
                        <table cellspacing="0" style="width: 100%; margin-top:10px;  font-size: 10pt; margin-bottom:10px;">
                            '.$ColGrp.'
                            <thead>
                                <tr style="background: #ffe6e6;   ">
                                    <th colspan="'.$FullColSpanRow.'" style="text-align: center; border-top : solid 1px gray; border-bottom: solid 1px grey;  padding:8px 0;"> Item Details </th>
                                </tr>
                                <tr>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">No.</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Description</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Image</th>
                                    '.$ColHeadForMrpPrice.'
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Sale Price</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Qty</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Gross Amt</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Disc</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Total</th>
                                </tr>
                            </thead>
                            <tbody>';

                                $GetProductCatQ = mysqli_query($db, "SELECT CategoryName FROM quotationItem WHERE Quotationid = ".$quoteId." GROUP BY CategoryName");
                                while($FetchProductCat = mysqli_fetch_array($GetProductCatQ)){

                                    $CategoryName = $FetchProductCat['CategoryName'];

                                    $HTMLContent .= '<tr style="background: #ffe6e6;">
                                                        <th colspan="'.$FullColSpanRow.'" style="text-align: center; border-top : solid 1px black;  padding:8px 0;"> '.$CategoryName.' </th>
                                                    </tr>';

                                    $GetQuoteItemQ = mysqli_query($db, "SELECT qi.*, p.* FROM quotationItem AS qi JOIN product AS p ON qi.Productid = p.Productid WHERE qi.CategoryName LIKE '".$CategoryName."' AND qi.Quotationid = ".$quoteId." ORDER BY qi.QuotationItemId");

                                    $no = 1;
                                    $CatProductMRP = 0;
                                    $CatProductSaleMRP = 0;
                                    $CatProductQuontity = 0;
                                    $CatItemGrossAmt = 0;
                                    $CatProductDiscount = 0;
                                    $CatProductFinalPrice = 0;

                                    while($FetchQuoteItem = mysqli_fetch_array($GetQuoteItemQ)){

                                        $Description = substr(trim(strip_tags($FetchQuoteItem['Description'])), 0, 45);
                                        $Sku = $FetchQuoteItem['Sku'];
                                        $UnitTypeid = $FetchQuoteItem['UnitTypeid'];
                                        $ProductImage = $FetchQuoteItem['ProductImage'];
                                        $ProductMRP = $FetchQuoteItem['ProductMRP'];
                                        $productSaleMRP = $FetchQuoteItem['productSaleMRP'];
                                        $ProductQuontity = $FetchQuoteItem['ProductQuontity'];
                                        $itemGrossAmt = $FetchQuoteItem['itemGrossAmt'];
                                        $ProductDiscount = $FetchQuoteItem['ProductDiscount'];
                                        $ProductFinalPrice = $FetchQuoteItem['ProductFinalPrice'];

                                        $CatProductMRP += $ProductMRP;
                                        $CatProductSaleMRP += $productSaleMRP;
                                        $CatProductQuontity += $ProductQuontity;
                                        $CatItemGrossAmt += $itemGrossAmt;
                                        $CatProductDiscount += $ProductDiscount;
                                        $CatProductFinalPrice += $ProductFinalPrice;

                                        if($ProductImage == ''){
                                            $ProductImage = 'assets/images/no-item.png';
                                        } else {
                                            $ProductImage = 'assets/images/product/'.$ProductImage;
                                        }

                                        $UnitType = "";
                                        if($UnitTypeid != 0){
                                            $GetUnitQ = mysqli_query($db, "SELECT ShortName FROM unittype WHERE UnitTypeId = ".$UnitTypeid);
                                            $FetchUnitType = mysqli_fetch_array($GetUnitQ);
                                            $UnitType = ' '.$FetchUnitType['ShortName'];
                                        }

                                        $MrpPriceColValTh = '';
                                        if($quoteWithPrice == 3){
                                            $MrpPriceColValTh = '<th style="font-weight : 10px; padding:8px 0;">'.number_format($ProductMRP, 2, '.', ',').'</th>';
                                        }

                                        $HTMLContent .= '<tr>
                                                            <th style="font-weight : 10px; padding:8px 0;">'.$no.'</th>
                                                            <th style="font-weight : 10px; padding:8px 0; text-align: left;"><b>'.$Sku.'</b><p style="line-height: 1.3; margin-top: 4px;">'.$Description.'</p></th>
                                                            <th style="font-weight : 10px; padding:8px 0;"><img style="width: 70%;" src="'.$ProductImage.'" alt="Logo"></th>
                                                            '.$MrpPriceColValTh.'
                                                            <th style="font-weight : 10px; padding:8px 0;">'.number_format($productSaleMRP, 2, '.', ',').'</th>
                                                            <th style="font-weight : 10px; padding:8px 0;">'.$ProductQuontity.$UnitType.'</th>
                                                            <th style="font-weight : 10px; padding:8px 0;">'.number_format($itemGrossAmt, 2, '.', ',').'</th>
                                                            <th style="font-weight : 10px; padding:8px 0;">'.number_format($ProductDiscount, 2, '.', ',').'</th>
                                                            <th style="font-weight : 10px; padding:8px 0;">'.number_format($ProductFinalPrice, 2, '.', ',').'</th>
                                                        </tr>';
                                        $no++;
                                    }

                                    $CatProductTotalMrpCol = '';
                                    if($quoteWithPrice == 3){
                                        $CatProductTotalMrpCol = '<td  style="text-align: center; border-top : solid 0.5px gray; border-bottom: solid 0.5px grey; padding:8px 0;">'.number_format($CatProductMRP, 2, '.', ',').'</td>';
                                    }

                                    $HTMLContent .= '<tr>
                                        <td colspan="3" style="text-align: center; border-top : solid 0.5px gray; border-bottom: solid 0.5px grey; padding:8px 0;"></td>
                                        '.$CatProductTotalMrpCol.'
                                        <td  style="text-align: center; border-top : solid 0.5px gray; border-bottom: solid 0.5px grey; padding:8px 0;">'.number_format($CatProductSaleMRP, 2, '.', ',').'</td>
                                        <td  style="text-align: center; border-top : solid 0.5px gray; border-bottom: solid 0.5px grey; padding:8px 0;">'.$CatProductQuontity.'</td>
                                        <td  style="text-align: center; border-top : solid 0.5px gray; border-bottom: solid 0.5px grey; padding:8px 0;">'.number_format($CatItemGrossAmt, 2, '.', ',').'</td>
                                        <td  style="text-align: center; border-top : solid 0.5px gray; border-bottom: solid 0.5px grey; padding:8px 0;">'.number_format($CatProductDiscount, 2, '.', ',').'</td>
                                        <td  style="text-align: center; border-top : solid 0.5px gray; border-bottom: solid 0.5px grey; padding:8px 0;">'.number_format($CatProductFinalPrice, 2, '.', ',').'</td>
                                    </tr>';
                                }

                                $FootMrpHeadCol = '';
                                $FootTotalMrpCol = '';
                                $TotalFinalColspan = 4;
                                $CatProductTotalMrpCol = '';
                                if($quoteWithPrice == 3){
                                    $FootMrpHeadCol = '<th style="border-top : solid 0.5px black; border-bottom: solid 1px black; padding:10px 0;">MRP</th>';
                                    $FootTotalMrpCol = '<th  style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">'.number_format($TotalMRP, 2, '.', ',').'</th>';
                                    $TotalFinalColspan = 5;
                                }
                                
                                $HTMLContent .= '<tr>
                                                    <td colspan="'.$FullColSpanRow.'" style="padding:4px 0;"></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">Total</th>
                                                    '.$FootTotalMrpCol.'
                                                    <th  style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">'.number_format($TotalSaleMRP, 2, '.', ',').'</th>
                                                    <th  style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">'.$TotalQuantity.'</th>
                                                    <th  style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">'.number_format($TotalDiscount, 2, '.', ',').'</th>
                                                    <th  style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">'.number_format($TotalDiscount, 2, '.', ',').'</th>
                                                    <th  style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">'.number_format($FinalAmount, 2, '.', ',').'</th>
                                                </tr>
                                                <tr>
                                                    <td colspan="'.$TotalFinalColspan.'" style="padding:8px 0; border-bottom: solid 1px black;"></td>
                                                    <td colspan="3" style="padding:8px 0; text-align:left; padding-left : 10px; border-bottom: solid 1px black; border-left: solid 1px black;">Total Amount</td>
                                                    <td style="padding:8px 0; border-bottom: solid 1px black;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>'.number_format($FinalAmount, 2, '.', ',').'</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <br>';
                            $HTMLContent .= $Terms.'

                        <table cellspacing="0" style="width: 100%; margin-top: 10px;">
                            <tr>
                                <td  style="padding:10px 0; width :50%; border-top : solid 0.5px gray; border-bottom: solid 1px gray; text-align:left; color:gray;"><i>[This Document is computer generated.]</i> </td>
                                <td  style="padding:10px 0; width :50%; border-top : solid 0.5px gray; border-bottom: solid 1px gray; text-align:right; color:gray;">Quotation No : <b>('.$QuotationNumber.')</b> '.$QuoteDate2.'</td>
                            </tr>
                        </table>
                    </page>';
        // echo $HTMLContent;
    $html2pdf = new Html2Pdf('P', 'A4', 'fr');
    $html2pdf->pdf->SetDisplayMode('fullpage');
    // $html2pdf->setDefaultFont("barlowsemiboldfontsforpdf");
    $html2pdf->writeHTML($HTMLContent);
    $html2pdf->output($QuotationNumber.'.pdf');

} catch (Html2PdfException $e) {
    $html2pdf->clean();

    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
