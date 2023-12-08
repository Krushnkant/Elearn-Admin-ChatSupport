<?php
include("include/config.php");
require('fpdf.php');
$orderId = $_GET['orderId'];

function numberTowords($number) { 
    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        '0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? 'and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
        } else {
            $str[] = null;
        }
    }
    $str = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ?
    "." . $words[$point / 10] . " " . 
    $words[$point = $point % 10] : '';
    $rettxt = $result . "Rupees Only";// . $points . " Paise";
    //$rettxt = $result . "Rupees  " . $points . " Paise";
    return ucwords(str_replace("   ", " ", $rettxt)); 
}

class PDF extends FPDF
{
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';

}

$GetOrderDetails = mysqli_query($db, "SELECT o.*, u.FullName, u.MobileNo FROM `order` AS o JOIN `user` AS u ON o.UserId = u.UserId WHERE o.orderId = '". $orderId ."'");
$FetchOrderDetails = mysqli_fetch_array($GetOrderDetails);

$CustomOrderId = $FetchOrderDetails['CustomOrderId'];
$TotalOrderCost = $FetchOrderDetails['TotalOrderCost'];
$OrderCostInWords = numberTowords($FetchOrderDetails['TotalOrderCost']);
if(is_float($TotalOrderCost)) {
    $TotalOrderCost = $TotalOrderCost;
} else {
    $TotalOrderCost = number_format($TotalOrderCost,2);
}
$VarProductId = $FetchOrderDetails['ProductId'];
$OrderAttrTermId = $FetchOrderDetails['attributeTermId'];
$InvoiceNo = $FetchOrderDetails['InvoiceNo'];
$FullName = ucwords($FetchOrderDetails['FullName']);
$MobileNo = $FetchOrderDetails['MobileNo'];



// $GetInventoryQ = mysqli_query($db, "SELECT * FROM `Inventory` WHERE `ProductVarientId` = ". $VarProductId." AND `AttrTermId` = ".$OrderAttrTermId);
// $FetchInventory = mysqli_fetch_array($GetInventoryQ);
// $Sku = $FetchInventory['pv_SKU'];

$timestamp = strtotime($FetchOrderDetails['OrderDate']); 
$Orderdate = date('d-m-Y', $timestamp);

$pdf = new PDF();
$stylesheet = file_get_contents('fpdf.css'); // external css
// First page
$pdf->SetMargins(5, 5);
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->Cell(46,8,'Bill Of Supply');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(46,8,'Shree Ganeshay Namh', 0, 'C', 0);
$pdf->MultiCell(46,8,'', 0, 'C', 0);

$pdf->SetFont('Arial','',10);
$pdf->Cell(59, 10, ' Cash', '', 0, 'L');
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','',14);
$pdf->Cell(10, 10, 'UR', 1, 0, 'C', 1);
$pdf->Cell(10, 10, '', '', 0, 'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',10);
$pdf->SetWidths(array(25,35));
$pdf->Row('headrow',array("   Bill No \n   Bill Date \n   Customer \n   Mobile","".$InvoiceNo." \n".$Orderdate."\n".$FullName."\n".$MobileNo.""));
$pdf->MultiCell(138,5,'', 0, 'C', 0);

$pdf->SetWidths(array(7,50,10,20,25,26.5));
$pdf->SetAligns(array('C','C','C','C','C','C'));
$pdf->Row('tableHead',array('Sr.','Brand','Qty.','Rate','Disc.Amt','Amount'));
$pdf->SetAligns(array('C','L','C','R','R','R'));


$OrderItemsQuery = mysqli_query($db, "SELECT oi.*, pv.PV_Name FROM `orderItems` AS oi INNER JOIN `productvarient` AS pv ON oi.ProductId = pv.ProductVarientId WHERE oi.OrderId = '".$CustomOrderId."'");
$TotalRows = mysqli_num_rows($OrderItemsQuery);
$item = 1;
$ItemListArr = array();
while($FetchOrderItemsData = mysqli_fetch_array($OrderItemsQuery)){

	$ProductItem = $FetchOrderItemsData['PV_Name'];
	$ItemTotalPayble = $FetchOrderItemsData['totalItemAmount'];
	$OrderItemPrice = $FetchOrderItemsData['OrderItemPrice'];
	$DiscountAmt =  $FetchOrderItemsData['SubDiscount'];
	$ItemQuantity = $FetchOrderItemsData['ItemQuantity'];

	if(is_float($DiscountAmt)) {
        $DiscountAmt = $DiscountAmt;
    } else {
        $DiscountAmt = number_format($DiscountAmt,2);
    }
    if(is_float($OrderItemPrice)) {
        $OrderItemPrice = $OrderItemPrice;
    } else {
        $OrderItemPrice = number_format($OrderItemPrice,2);
    }if(is_float($ItemTotalPayble)) {

        $ItemTotalPayble = $ItemTotalPayble;
    } else {
        $ItemTotalPayble = number_format($ItemTotalPayble,2);
    }

    // $ItemListArr = array($item, $ProductItem, $ItemQuantity, $OrderItemPrice, $DiscountAmt, $ItemTotalPayble);
    $pdf->Row('commonTable', array($item, $ProductItem, $ItemQuantity, $OrderItemPrice, $DiscountAmt, $ItemTotalPayble));
    $item++;
}

// $TotalItem = count($ItemListArr);
$RemainRow = 17 - $TotalRows;
// for($i=0;$i<$TotalItem;$i++)
// 	$pdf->Row('commonTable',$ItemListArr);
for($r=0;$r<$RemainRow;$r++)
	$pdf->Row('blankTable',array('','','','','',''));
$pdf->SetWidths(array(87,25,26.5));
$pdf->SetAligns(array('L','L','R'));
$pdf->Row('tableFoot',array('Rupees: '.$OrderCostInWords,'Total', $TotalOrderCost));

$x_axis=$pdf->getx();
$c_width=30;
$c_height=15;
$pdf->vcell($c_width,$c_height,$x_axis,'Terms & Conditions: ');
$x_axis=$pdf->getx();
$pdf->vcell(50,$c_height,$x_axis,"COMPOSITION TAXABLE PERSON NOT ELIGIBLE TO COLLECT TAX ON SUPPLY GOODS.");
$x_axis=$pdf->getx();
$pdf->vcell(30.5,$c_height,$x_axis,"     Net Amount ");
$x_axis=$pdf->getx();
$pdf->vcell(28,$c_height,$x_axis,$TotalOrderCost);
$pdf->Ln();
$pdf->SetFont('Arial','',10);
$pdf->Row('footrow',array("[1] Please check the product/s before leaving. \n[2] No exchange, No return, No guarantee\n"));
$pdf->SetWidths(array(69,69));
$pdf->SetAligns(array('R','L'));
$pdf->Row('thankLine',array("* Thank You *"," Visit Again *"));

$pdf->Output('D','uttama_'.$InvoiceNo.'.pdf');
// $pdf->Output();
?>
