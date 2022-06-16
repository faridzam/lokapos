<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

//printer
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

//models
use App\Models\pos_log_activity_desktop;
use App\Models\pos_store_desktop;
use App\Models\pos_pc_desktop;
use App\Models\pos_deposit_desktop;
use App\Models\pos_category_desktop;
use App\Models\pos_product_desktop;
use App\Models\pos_product_store_desktop;
use App\Models\pos_order_desktop;
use App\Models\pos_order_detail_desktop;
use App\Models\pos_payment_desktop;
use App\Models\pos_saved_cart_desktop;
use App\Models\pos_saved_cart_detail_desktop;


class item
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this -> name = $name;
        $this -> price = $price;
        $this -> dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 13;
        $leftCols = 35;
        if ($this -> dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this -> name, $leftCols);

        $sign = ($this -> dollarSign ? 'Rp.  ' : '');
        $right = str_pad($sign . $this -> price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}

class itemCo
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this -> name = $name;
        $this -> price = $price;
        $this -> dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 13;
        $leftCols = 35;
        if ($this -> dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this -> name, $leftCols) ;

        $sign = ($this -> dollarSign ? 'Rp. ' : '');
        $right = str_pad($sign . $this -> price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left\n$right\n";
    }
}


class printController extends Controller
{

    //PrintInvoice
    public function printInvoice($orderID){

        try {

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $cashierID = Auth::guard('cashier')->user()->id;
            $cashierName = Auth::guard('cashier')->user()->name;
            $order = pos_order_desktop::findOrFail($orderID)->latest()->first();
            $paymentMethod = pos_payment_desktop::findOrfail($order->payment_id)->value('name');
            $orderDetail = pos_order_detail_desktop::where('order_id', $orderID)
            ->get();

            $items = array();
            $itemsAndNote = array();
            $totalQty=0;
            $totalHarga=0;

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($items, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item(""," ". number_format($value->subtotal,0,",",".")."\n"));
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($items, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item("", "Rp. ".number_format($value->subtotal,0,",",".")."\n"));
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                }

            }

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item(""," ". number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item("", "Rp. ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                }

            }

            $total = new item('Tagihan: ',"Rp. ".number_format($order->bill_amount,0,",","."));

            $printer_kasir = pos_pc_desktop::findOrFail($PC)
            ->value('cashier_printer');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."METODE      : ".$paymentMethod."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($items as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);
            $printer -> text(new item('Bayar',"Rp. ".number_format($order->pay_amount,0,",",".")));
            $printer -> text(new item('Kembalian',"Rp. ".number_format($order->change_amount,0,",",".")));

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("TERIMAKASIH TELAH BERKUNJUNG KE SALOKA!");
            $printer -> feed(2);

            $printer -> selectPrintMode();
            $printer -> feed(2);

            $printer -> cut();

            // print 2
            // + note item
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."METODE      : ".$paymentMethod."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($itemsAndNote as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);
            $printer -> text(new item('Bayar',"Rp. ".number_format($order->pay_amount,0,",",".")));
            $printer -> text(new item('Kembalian',"Rp. ".number_format($order->change_amount,0,",",".")));

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("TERIMAKASIH TELAH BERKUNJUNG KE SALOKA!");
            $printer -> feed(2);

            $printer -> selectPrintMode();
            $printer -> feed(2);

            $printer -> cut();
            $printer -> pulse();
            $printer -> close();

            //return redirect('/print-kitchen');
        } catch (Exception $exception) {
            //return redirect('/print-kitchen');
        }

    }

    //PrintInvoice2
    public function printInvoice2(){

        try {
            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }
            $cashierID = Auth::guard('cashier')->user()->id;
            $cashierName = Auth::guard('cashier')->user()->name;

            $order = pos_order_desktop::where('id', request('id'))->first();
            $orderDetail = pos_order_detail_desktop::where('order_id', request('id'))->get();

            $items = array();
            $totalQty=0;
            $totalHarga=0;

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($items, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item(""," ". number_format($value->subtotal,0,",",".")."\n"));
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($items, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item("", "Rp. ".number_format($value->subtotal,0,",",".")."\n"));
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                }

            }

            $total = new item('Total: ',"Rp. ".number_format($order->bill_amount,0,",","."));

            $printer_kasir = pos_pc_desktop::findOrFail($PC)
            ->value('cashier_printer');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("REPRINT");
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("No. Nota : ".$order->no_invoice."\nSTORE    : ".$storeName."\nTanggal  : ".Carbon::now()."\nKasir    : ".$cashierName."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($items as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            // $printer -> text($tax);
            // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text($total);
            $printer -> text(new item('Bayar: ',"Rp. ".number_format($order->pay_amount,0,",",".")));
            $printer -> text(new item('Kembalian: ',"Rp. ".number_format($order->change_amount,0,",",".")));
            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> selectPrintMode();
            $printer -> feed(2);

            $printer -> cut();
            $printer -> pulse();
            $printer -> close();

            unset($cart);
            session()->put('cart', []);
            session()->flash('success', 'invoice reprinted successfully');

            //return redirect('/dashboardInvoice');
        } catch (Exception $exception) {
            //return redirect('/dashboardInvoice');
        }

    }

    //PrintDeposit
    public function printDeposit(){

        try {
            $cashier = Auth::user()->id;
            $cashierName = Auth::user()->name;

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $latest = pos_deposit_desktop::where('pc_id', $PC)
            ->where('cashier_id', $cashier)
            ->latest()
            ->first();

            $subtotal100 = $latest->pec100 * 100000;
            $subtotal50 = $latest->pec50 * 50000;
            $subtotal20 = $latest->pec20 * 20000;
            $subtotal10 = $latest->pec10 * 10000;
            $subtotal5 = $latest->pec5 * 5000;
            $subtotal2 = $latest->pec2 * 2000;
            $subtotal1 = $latest->pec1 * 1000;

            $printer_kasir = pos_pc_desktop::where('id', $PC)
            ->value('cashier_printer');

            //$connector =  new WindowsPrintConnector($printer_kasir);
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);
            $printer -> text("NOTA DEPOSIT\n");
            $printer -> text("store   : ".$storeName."\nTanggal : ".$latest->created_at."\nKasir   : ".substr($cashierName, 3)."\n");

            $printer -> text("________________________________________________");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> text("Rp. 100.000 x ".$latest->pec100."          =      ".'Rp. '.number_format($subtotal100,0,",",".")."\n");
            $printer -> text("Rp. 50.000 x ".$latest->pec50."           =      ".'Rp. '.number_format($subtotal50,0,",",".")."\n");
            $printer -> text("Rp. 20.000 x ".$latest->pec20."           =      ".'Rp. '.number_format($subtotal20,0,",",".")."\n");
            $printer -> text("Rp. 10.000 x ".$latest->pec10."           =      ".'Rp. '.number_format($subtotal10,0,",",".")."\n");
            $printer -> text("Rp. 5.000 x ".$latest->pec5."            =      ".'Rp. '.number_format($subtotal5,0,",",".")."\n");
            $printer -> text("Rp. 2.000 x ".$latest->pec2."            =      ".'Rp. '.number_format($subtotal2,0,",",".")."\n");
            $printer -> text("Rp. 1.000 x ".$latest->pec1."            =      ".'Rp. '.number_format($subtotal1,0,",",".")."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* total */
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text('TOTAL = '."Rp. ".number_format($latest->total,0,",",".")."\n");
            $printer -> selectPrintMode(Printer::MODE_FONT_A);
            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> selectPrintMode();
            $printer -> feed(2);

            $printer -> pulse();
            $printer -> cut();
            $printer -> close();

        } catch (Exception $exception) {

            return back();

        }

    }

    //PrintCloseOrder
    public function printCloseOrder($qty){

        try {

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $cashierID = Auth::guard('cashier')->user()->id;
            $cashierName = Auth::guard('cashier')->user()->name;
            $deposit = pos_deposit_desktop::where('pc_id', $PC)
            ->whereDate('created_at', today())
            ->where('cashier_id', $cashierID)
            ->sum('total');
            $penjualanSession = pos_order_desktop::whereDate('created_at', today())
            ->where('pc_id', $PC)
            ->where('store_id', $storeID)
            ->where('cashier_id', $cashierID)
            ->get();

            if(is_null($penjualanSession)) {

                $totalPendapatanCo = $deposit;
                $no_cos ='CO-'.Carbon::now()->format('Ymd').'-'.$PC.'-'.$cashierID."-rapayu";
                $no_co = str_replace(array('[',']'), '',$no_cos);


                $printer_kasir = pos_pc_desktop::findOrFail($PC)
                ->value('cashier_printer');
                $connector =  new CupsPrintConnector($printer_kasir);
                $printer = new Printer($connector);

                for ($i=1; $i <= $qty; $i++) {
                    /* Name of shop */
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                    $printer -> text("SALOKA THEME PARK\n");
                    $printer -> selectPrintMode();
                    $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
                    $printer -> feed();

                    /* Title of receipt */
                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> selectPrintMode(Printer::MODE_FONT_A);
                    $printer -> text("\nNOTA CLOSE ORDER\n");
                    $printer -> text("CO_ID   : ".$no_co."\nstore   : ".$storeName."\nKasir   : ".$cashierName."\n"."Tanggal : ".Carbon::now());
                    $printer -> feed();
                    $printer -> text("________________________________________________\n");
                    $printer -> feed();
                    $printer -> text("Deposit    : Rp. ".number_format($deposit,0,",",".")."\nPendapatan : Rp. ".number_format(0,0,",",".")."\n"."Total      : ".number_format($totalPendapatanCo,0,",","."));
                    $printer -> feed();
                    $printer -> text("________________________________________________\n");
                    $printer -> feed();

                    /* Items */
                    $printer -> feed();
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                    $printer -> text("RA PAYU MASEH!");
                    $printer -> feed();

                    /* Title of receipt */
                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> selectPrintMode(Printer::MODE_FONT_A);
                    $printer -> text("________________________________________________\n");
                    $printer -> feed();

                    $printer -> text('TOTAL = '."Rp. ".number_format($totalPendapatanCo,0,",",".")."\n");
                    $printer -> text("________________________________________________\n");
                    $printer -> feed();


                    $printer -> cut();
                }

                $printer -> close();

                pos_log_activity_desktop::create([
                    'pic' => $cashierName,
                    'type' => 11,
                    'note' => $cashierName." telah melakukan close order pada :".$storeName,
                ]);

                pos_log_activity_desktop::create([
                    'pic' => $cashierName,
                    'type' => 6,
                    'note' => $cashierName." meninggalkan sistem POS",
                ]);

                // session()->flush();
                // Auth::logout();
                // return redirect('/');

            } else {

                $countTunai = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->where('payment_id', 1)
                ->count();

                $countFintech = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->where('payment_id', 2)
                ->count();

                $countEDC = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->whereIn('payment_id', [3,4,5,6,7,8,9,10])
                ->count();

                $totalTunai = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->where('payment_id', 1)
                ->sum('bill_amount');

                $totalFintech = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->where('payment_id', 2)
                ->sum('bill_amount');

                $totalEDC = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->whereIn('payment_id', [3,4,5,6,7,8,9,10])
                ->sum('bill_amount');

                $penjualanID = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->where('isActive', 1)
                ->pluck('id');

                $voidID = pos_order_desktop::whereDate('created_at', today())
                ->where('pc_id', $PC)
                ->where('store_id', $storeID)
                ->where('cashier_id', $cashierID)
                ->where('isActive', 0)
                ->pluck('id');

            }

            $voidData = pos_order_detail_desktop::select('product_id')
            ->whereDate('created_at', today())
            ->whereIn('order_id', $voidID)
            ->where('isActive', 0)
            ->groupBy('product_id')
            ->distinct()
            ->get();

            $voidMA = array();
            $voidMI = array();
            $voidRE = array();

            foreach($voidData as $value){

                $productCategory = pos_product_store_desktop::where('product_id', $value->product_id)
                ->where('store_id', $storeID)
                ->value('category_id');
                $product = pos_product_desktop::where('id', $value->product_id)
                ->first();
                $productCategoryType = pos_category_desktop::where('id', $productCategory)
                ->value('type');
                $voidQty = pos_order_detail_desktop::where('product_id', $product->id)
                ->where('isActive', 0)
                ->sum('qty');
                $voidAmount = pos_order_detail_desktop::where('product_id', $product->id)
                ->where('isActive', 0)
                ->sum('subtotal');

                if ($productCategoryType === 'makanan') {
                    //
                    array_push($voidMa, new item(Str::limit($product->name, 21, '_')." ( x ".$voidQty." ) ", "Rp. ".number_format($voidAmount,0,",",".")));
                }
                if ($productCategoryType === 'minuman') {
                    //
                    array_push($voidMi, new item(Str::limit($product->name, 21, '_')." ( x ".$voidQty." ) ", "Rp. ".number_format($voidAmount,0,",",".")));
                }
                if ($productCategoryType === 'non-konsumsi') {
                    //
                    array_push($voidRE, new item(Str::limit($product->name, 21, '_')." ( x ".$voidQty." ) ", "Rp. ".number_format($voidAmount,0,",",".")));
                }

            }

            $penjualanData = pos_order_detail_desktop::select('product_id')
            ->whereDate('created_at', today())
            ->whereIn('order_id', $penjualanID)
            ->where('isActive', 1)
            ->groupBy('product_id')
            ->distinct()
            ->get();

            $penjualanMA = array();
            $penjualanMI = array();
            $penjualanRE = array();

            foreach($penjualanData as $value){

                $productCategory = pos_product_store_desktop::where('product_id', $value->product_id)
                ->where('store_id', $storeID)
                ->value('category_id');
                $product = pos_product_desktop::where('id', $value->product_id)
                ->first();
                $productCategoryType = pos_category_desktop::where('id', $productCategory)
                ->value('type');
                $penjualanQty = pos_order_detail_desktop::where('product_id', $product->id)
                ->whereDate('created_at', today())
                ->where('isActive', 1)
                ->sum('qty');
                $penjualanAmount = pos_order_detail_desktop::where('product_id', $product->id)
                ->whereDate('created_at', today())
                ->where('isActive', 1)
                ->sum('subtotal');

                if ($productCategoryType === 'makanan') {
                    //
                    array_push($penjualanMA, new item(Str::limit($product->name, 21, '_')." ( x ".$penjualanQty." ) ", "Rp. ".number_format($penjualanAmount,0,",",".")));
                }
                if ($productCategoryType === 'minuman') {
                    //
                    array_push($penjualanMI, new item(Str::limit($product->name, 21, '_')." ( x ".$penjualanQty." ) ", "Rp. ".number_format($penjualanAmount,0,",",".")));
                }
                if ($productCategoryType === 'non-konsumsi') {
                    //
                    array_push($penjualanRE, new item(Str::limit($product->name, 21, '_')." ( x ".$penjualanQty." ) ", "Rp. ".number_format($penjualanAmount,0,",",".")));
                }

            }

            $totalPendapatan = pos_order_desktop::whereDate('created_at', today())
            ->where('pc_id', $PC)
            ->where('store_id', $storeID)
            ->where('cashier_id', $cashierID)
            ->sum('bill_amount');

            $totalPendapatanCo = $totalPendapatan + $deposit;

            $no_cos ='CO-'.Carbon::now()->format('Ymd').'-'.$PC.'-'.$cashierID;
            $no_co = str_replace(array('[',']'), '',$no_cos);

            $printer_kasir = pos_pc_desktop::findOrFail($PC)
            ->value('cashier_printer');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            for ($i=1; $i <= $qty; $i++) {
                /* Name of shop */
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> text("SALOKA THEME PARK\n");
                $printer -> selectPrintMode();
                $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
                $printer -> feed();

                /* Title of receipt */
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> selectPrintMode(Printer::MODE_FONT_A);
                $printer -> text("\nNOTA CLOSE ORDER\n");
                $printer -> text("CO_ID : ".$no_co."\nstore : ".$storeName."\nKasir : ".$cashierName."\n"."Tanggal : ".Carbon::now());
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();
                $printer -> text("Deposit    : Rp. ".number_format($deposit,0,",",".")."\nPendapatan : Rp. ".number_format($totalPendapatan,0,",","."));
                $printer -> text("\nTotal      : "."Rp. ".number_format($totalPendapatanCo,0,",",".")."\nTunai      : ".$countTunai."(Rp. ".number_format($totalTunai,0,",",".").")"."\nEDC         : ".$countEDC."(Rp. ".number_format($totalEDC,0,",",".").")"."\nFintech     : ".$countFintech."(Rp. ".number_format($totalFintech,0,",",".").")");
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                /* Items */

                $printer -> feed();
                $printer -> setJustification(Printer::JUSTIFY_LEFT);

                if (count($penjualanMA) > 0) {
                    $printer -> text("Makanan :");
                    $printer -> feed(2);

                    foreach ($penjualanMA as $item) {
                        $printer -> text($item);
                    }
                    $printer -> feed();
                }

                if (count($penjualanMI) > 0) {
                    $printer -> text("Minuman :");
                    $printer -> feed(2);

                    foreach ($penjualanMI as $item) {
                        $printer -> text($item);
                    }
                    $printer -> feed();
                }

                if (count($penjualanRE) > 0) {

                    $printer -> text("Retail :");
                    $printer -> feed(2);

                    foreach ($penjualanRE as $item) {
                        $printer -> text($item);
                    }
                    $printer -> feed();

                }

                if (count($voidData) > 0) {
                    /* void */
                    $printer -> text("________________________________________________\n");
                    $printer -> feed();
                    $printer -> text("Void :");
                    $printer -> feed();

                    if (count($voidMA) > 0) {

                        $printer -> text("Makanan :");
                        $printer -> feed(2);

                        foreach ($voidMA as $item) {
                            $printer -> text($item);
                        }

                        $printer -> feed();
                    }

                    if (count($voidMI) > 0) {
                        $printer -> text("Minuman :");
                        $printer -> feed(2);

                        foreach ($voidMI as $item) {
                            $printer -> text($item);
                        }

                        $printer -> feed();
                    }

                    if (count($voidRE) > 0) {
                        $printer -> text("Retail :");
                        $printer -> feed(2);

                        foreach ($voidRE as $item) {
                            $printer -> text($item);
                        }
                        $printer -> feed();
                    }

                }

                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                $printer -> text('TOTAL = '."Rp. ".number_format($totalPendapatanCo,0,",",".")."\n");
                $printer -> text("________________________________________________\n");
                $printer -> feed();


                $printer -> cut();
            }

            $printer -> close();

            pos_log_activity_desktop::create([
                'pic' => $cashierName,
                'type' => 11,
                'note' => $cashierName." telah melakukan close order pada :".$storeName,
            ]);
            pos_log_activity_desktop::create([
                'pic' => $cashierName,
                'type' => 6,
                'note' => $cashierName." meninggalkan sistem POS",
            ]);

            // session()->flush();
            // Auth::logout();
            // return redirect('/');
        } catch (Exception $exception) {

            pos_log_activity_desktop::create([
                'pic' => $cashierName,
                'type' => 11,
                'note' => $cashierName." telah melakukan close order pada :".$storeName,
            ]);

            pos_log_activity_desktop::create([
                'pic' => $cashierName,
                'type' => 6,
                'note' => $cashierName." meninggalkan sistem POS",
            ]);

            // session()->flush();
            // Auth::logout();
            // return redirect('/');
        }

    }

    public function printKitchen(){

        try {
            $admin = Auth::guard('kasir')->user()->name;
            $penjualan = DB::table('pos_activity_item_and_desktops')
            ->where('id_store', session()->get('store'))
            ->get();
            $latest = DB::table('pos_activity_item_and_desktops')->where('id_store', session()->get('store'))->latest('updated_at')->first();
            $namaStore = pos_store_desktop::where('id_store', session()->get('store'))->value('nama_store');
            $dtn = Carbon::now();

            $noteInv = pos_activity_and_desktop::where('id_store', session()->get('store'))
            ->where('no_invoice', $latest->no_invoice)
            ->value('note');

            if(!$penjualan->count() !== 0) {
                $lateinv = $latest->no_invoice;
                $pesananBaru = DB::table('pos_activity_item_and_desktops')
                ->where('no_invoice', '=', $lateinv)
                ->where('id_store', session()->get('store'))
                ->get();
            } else {
                $pesananBaru = $penjualan;
            }

            $items = array();
            $totalQty=0;

            foreach ($pesananBaru as $id => $value) {
                if($value->id_kategori % 2 == 0){
                    $totalQty+= $value->qty;
                    array_push($items, new item(Str::limit($value->nama_item, 21, '_')." x(".$value->qty.")"."\n"."note: ".str_replace(";", "\n- ", $value->note)));
                }
            }

            if($totalQty > 0){
                $ip_target = pos_store_desktop::where('id_store', session()->get('store'))
                ->value('ip_kitchen');

                //$connector = new NetworkPrintConnector("$ip_target", 9100);
                $ch = curl_init($ip_target);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($httpcode !== 0){
                $connector = new NetworkPrintConnector("$ip_target", 9100);
                } else {
                return redirect('/print-bar');
                }

                $printer = new Printer($connector);

                /* Name of shop */
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

                $printer -> text("\nNOTA KITCHEN\n\n");
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> setJustification(Printer::MODE_FONT_A);
                $printer -> selectPrintMode();
                $printer -> text("no. invoice : ".$lateinv."\ntanggal : ".$dtn."\nkasir: ".substr($admin, 3));
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                $printer -> setFont(Printer::FONT_A);
                $printer -> setTextSize(2, 1);

                foreach ($items as $item) {
                    $printer -> text(strtoupper($item));
                }

                $printer -> text("________________________\n");
                $printer -> feed();
                $printer -> text("Note Pesanan: ".$noteInv);
                $printer -> feed();

                $printer -> cut();
                $printer -> close();

                return redirect('/print-bar');

            } else{
                return redirect('/print-bar');
            }


        } catch (Exception $exception) {
            return redirect('/print-bar');
        }

    }

    public function printBar(){

        try {
            $admin = Auth::guard('kasir')->user()->name;
            $penjualan = DB::table('pos_activity_item_and_desktops')
            ->where('id_store', session()->get('store'))
            ->get();
            $latest = DB::table('pos_activity_item_and_desktops')->where('id_store', session()->get('store'))->latest('updated_at')->first();
            $namaStore = pos_store_desktop::where('id_store', session()->get('store'))->value('nama_store');
            $dtn = Carbon::now();

            $noteInv = pos_activity_and_desktop::where('id_store', session()->get('store'))
            ->where('no_invoice', $latest->no_invoice)
            ->value('note');

            if(!$penjualan->count() !== 0) {
                $lateinv = $latest->no_invoice;
                $pesananBaru = DB::table('pos_activity_item_and_desktops')
                ->where('no_invoice', '=', $lateinv)
                ->where('id_store', session()->get('store'))
                ->get();
            } else {
                $pesananBaru = $penjualan;
            }

            $items = array();
            $totalQty=0;

            foreach ($pesananBaru as $id => $value) {
                if($value->id_kategori % 2 !== 0){
                    $totalQty+= $value->qty;
                    array_push($items, new item(Str::limit($value->nama_item, 21, '_')." x(".$value->qty.")"."\n"."note: ".str_replace(";", "\n- ", $value->note)));
                }
            }

            if($totalQty > 0){
                $ip_target = pos_store_desktop::where('id_store', session()->get('store'))
                ->value('ip_bar');

                //$connector =  new NetworkPrintConnector("$ip_target", 9100);
                $ch = curl_init($ip_target);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($httpcode !== 0){
                $connector = new NetworkPrintConnector("$ip_target", 9100);
                } else {
                return redirect('/dashboardPenjualan');
                }

                $printer = new Printer($connector);
                //$logo = EscposImage::load("public/img/logo-saloka.svg");

                /* Name of shop */
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                //$printer -> bitImage($logo);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> text("\nNOTA BAR\n\n");
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> setJustification(Printer::MODE_FONT_A);
                $printer -> selectPrintMode();
                $printer -> text("no. invoice : ".$lateinv."\ntanggal : ".$dtn."\nkasir: ".substr($admin, 3));
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                $printer -> setFont(Printer::FONT_A);
                $printer -> setTextSize(2, 1);

                foreach ($items as $item) {
                    $printer -> text($item);
                }

                $printer -> text("________________________\n");
                $printer -> feed();
                $printer -> text("Note Pesanan: ".$noteInv);
                $printer -> feed();

                $printer -> cut();
                $printer -> close();

                return redirect('/dashboardPenjualan');
            } else{
                return redirect('/dashboardPenjualan');
            }


        } catch (Exception $exception) {
            return redirect('/dashboardPenjualan');
        }

    }

    public function printBarcode(){

        $ip_target = pos_store_desktop::where('id_store', session()->get('store'))
        ->value('ip_kasir');
        $connector =  new CupsPrintConnector("$ip_target");
        $printer = new Printer($connector);
        $printer->setBarcodeHeight(80);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);

        $printer->barcode("1313004042035", Printer::BARCODE_JAN13);
        $printer -> feed();
        $printer -> cut();
        $printer -> close();

        return redirect('dashboardPenjualan');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\pos_print  $pos_print
     * @return \Illuminate\Http\Response
     */
    public function show(pos_print $pos_print)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\pos_print  $pos_print
     * @return \Illuminate\Http\Response
     */
    public function edit(pos_print $pos_print)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\pos_print  $pos_print
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pos_print $pos_print)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\pos_print  $pos_print
     * @return \Illuminate\Http\Response
     */
    public function destroy(pos_print $pos_print)
    {
        //
    }

    public function printCloseOrder2(Request $request){

        try {

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $store = $choosenStore['menu'];
                }
            }

            $thisStore = session()->get('store');
            $admin = Auth::user()->name;
            $dtn = Carbon::now();
            $deposits = pos_deposit::where('id_store', session()->get('store'))
            ->first();
            $deposit = pos_deposit::where('id_store', session()->get('store'))
            ->whereDate('created_at', today())
            ->sum('nominal');
            $penjualanSession = pos_activity_and_desktop::whereDate('created_at', today())
            ->where('id_store', session()->get('store'))
            ->latest()
            ->first();

            if(is_null($penjualanSession)) {

                $dateNow = Carbon::now();
                $adminz = Auth::user()->id;

                $no_cos ='CO-'.$dateNow->format('Ymd').'-'.pos_store_desktop::findOrFail(session('store'))->pluck('id').'-'.$adminz."-rapayu";
                $no_co = str_replace(array('[',']'), '',$no_cos);

                $totalPendapatan = pos_activity_item_and_desktop::where('id_store', session()->get('store'))
                ->whereDate('created_at', today())
                ->where('isDell', 0)
                ->sum('total');

                $totalPendapatanCo = $totalPendapatan + $deposit;

                $ip_client = request()->ip();
                $printer_client = pos_pc_desktop::where('ip', $ip_client)
                ->value('printer');
                $printer_kasir = pos_store_desktop::where('id_store', session()->get('store'))
                ->value('ip_kasir');
                $connector =  new CupsPrintConnector($printer_kasir);
                $printer = new Printer($connector);

                /* Name of shop */
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> text("SALOKA THEME PARK\n");
                $printer -> selectPrintMode();
                $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
                $printer -> feed();

                /* Title of receipt */
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> selectPrintMode(Printer::MODE_FONT_A);
                $printer -> text("\nNOTA SALES\n");
                $printer -> text("CO_ID   : ".$no_co."\nstore   : ".$thisStore[$deposits->id_store]['name']."\nKasir   : ".substr($admin, 3)."\n"."Tanggal : ".$dtn);
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();
                $printer -> text("Deposit    : Rp. ".number_format($deposit,0,",",".")."\nPendapatan : Rp. ".number_format($totalPendapatan,0,",",".")."\n"."Total      : ".number_format($totalPendapatanCo,0,",","."));
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                /* Items */
                $printer -> feed();
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> text("RA PAYU MASEH!");
                $printer -> feed();

                /* Title of receipt */
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> selectPrintMode(Printer::MODE_FONT_A);
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                $printer -> text('TOTAL = '."Rp. ".number_format($totalPendapatanCo,0,",",".")."\n");
                $printer -> text("________________________________________________\n");
                $printer -> feed();


                $printer -> cut();
                $printer -> close();

                //session()->flush();
                return back();

            } elseif($store == 10){

                //shop retail

                $countTunai = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 1)
                ->count();

                $countEDC = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->whereIn('metode', [2,3,4,5,6,7,8,9])
                ->count();

                $countFintech = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 10)
                ->count();

                $totalTunai = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 1)
                ->sum('total_pembelian');

                $totalEDC = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->whereIn('metode', [2,3,4,5,6,7,8,9])
                ->sum('total_pembelian');

                $totalFintech = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 10)
                ->sum('total_pembelian');

                $penjualanInvoice = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->pluck('no_invoice');

                $voidInvoice = void_log_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->pluck('no_invoice');

                $penjualanToday = pos_activity_item_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('isDell', 0)
                ->whereIn('no_invoice', $penjualanInvoice)
                ->groupBy('id_item', 'harga')
                ->get();

                $voidData = pos_activity_item_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('isDell', 1)
                ->whereIn('no_invoice', $voidInvoice)
                ->groupBy('id_item', 'harga')
                ->get();

                $void = array();
                $qtyVoid=0;
                $hargaVoid=0;

                foreach($voidData as $value){
                    $harga = $value->harga;
                    $qtyVoid= pos_activity_item_and_desktop::where('id_item', $value->id_item)
                    ->where('id_store', session()->get('store'))
                    ->where('harga', $value->harga)
                    ->where('isDell', 1)
                    ->whereDate('created_at', today())
                    ->sum('qty');
                    $hargaVoid= $harga * $qtyVoid;
                    array_push($void, new item(Str::limit($value->nama_item, 21, '_')." ( x ".$qtyVoid." ) ", "Rp. ".number_format($hargaVoid,0,",",".")));
                }



                $totalPendapatan = pos_activity_item_and_desktop::where('id_store', session()->get('store'))
                ->whereDate('created_at', today())
                ->where('isDell', 0)
                ->sum('total');

                $totalPendapatanCo = $totalPendapatan + $deposit;

                $ip_client = request()->ip();
                $printer_client = pos_pc_desktop::where('ip', $ip_client)
                ->value('printer');
                $printer_kasir = pos_store_desktop::where('id_store', session()->get('store'))
                ->value('ip_kasir');
                $connector =  new CupsPrintConnector($printer_kasir);
                $printer = new Printer($connector);

                /* Name of shop */
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> text("SALOKA THEME PARK\n");
                $printer -> selectPrintMode();
                $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
                $printer -> feed();

                /* Title of receipt */
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> selectPrintMode(Printer::MODE_FONT_A);
                $printer -> text("\nNOTA SALES ORDER\n");
                $printer -> text("CO_ID : ".$penjualanSession->no_co."\nstore : ".$thisStore[$deposits->id_store]['name']."\nKasir : ".substr($admin, 3)."\n"."Tanggal : ".$dtn);
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();
                $printer -> text("Deposit    : Rp. ".number_format($deposit,0,",",".")."\nPendapatan : Rp. ".number_format($totalPendapatan,0,",","."));
                $printer -> text("\nTotal      : "."Rp. ".number_format($totalPendapatanCo,0,",",".")."\nTunai      : ".$countTunai."\nEDC         : ".$countEDC."\nFintech     : ".$countFintech);
                $printer -> feed();
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                /* Items */

                $items = array();
                $totalQty=0;
                $totalHarga=0;


                foreach($penjualanToday as $value){
                    $harga = $value->harga;
                    $totalQty= pos_activity_item_and_desktop::where('id_item', $value->id_item)
                    ->where('id_store', session()->get('store'))
                    ->where('harga', $value->harga)
                    ->where('isDell', 0)
                    ->whereDate('created_at', today())
                    ->sum('qty');

                    $totalHarga= $harga * $totalQty;
                    array_push($items, new item(Str::limit($value->nama_item, 21, '_')." ( x ".$totalQty." ) ", "Rp. ".number_format($totalHarga,0,",",".")));
                }

                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> feed(2);

                foreach ($items as $item) {
                    $printer -> text($item);
                }

                $printer -> feed();

                /* Title of receipt */
                $printer -> text("________________________________________________\n");
                $printer -> feed();
                $printer -> text("Void :");
                $printer -> feed();
                foreach ($void as $item) {
                    $printer -> text($item);
                }
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                $printer -> text('TOTAL = '."Rp. ".number_format($totalPendapatanCo,0,",",".")."\n");
                $printer -> text("________________________________________________\n");
                $printer -> feed();

                $printer -> cut();
                $printer -> close();

                return back();
            }

            else {

                $countTunai = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 1)
                ->count();

                $countEDC = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->whereIn('metode', [2,3,4,5,6,7,8,9])
                ->count();

                $countFintech = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 10)
                ->count();

                $totalTunai = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 1)
                ->sum('total_pembelian');

                $totalEDC = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->whereIn('metode', [2,3,4,5,6,7,8,9])
                ->sum('total_pembelian');

                $totalFintech = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('id_kasir', Auth::guard('kasir')->user()->id)
                ->where('metode', 10)
                ->sum('total_pembelian');

                $penjualanInvoice = pos_activity_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('no_co', $penjualanSession->no_co)
                ->pluck('no_invoice');

                $voidInvoice = void_log_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->pluck('no_invoice');

                $penjualanTodayMa = pos_activity_item_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('isDell', 0)
                ->whereIn('no_invoice', $penjualanInvoice)
                ->whereRaw('id_kategori % 2 = 0')
                ->groupBy('id_item', 'harga')
                ->get();

                $penjualanTodayMi = pos_activity_item_and_desktop::whereDate('created_at', today())
                ->where('id_store', session()->get('store'))
                ->where('isDell', 0)
                ->whereIn('no_invoice', $penjualanInvoice)
                ->whereRaw('id_kategori % 2 != 0')
                ->groupBy('id_item', 'harga')
                ->get();
            }

            $voidData = pos_activity_item_and_desktop::whereDate('created_at', today())
            ->where('id_store', session()->get('store'))
            ->where('isDell', 1)
            ->whereIn('no_invoice', $voidInvoice)
            ->groupBy('id_item', 'harga')
            ->get();

            $void = array();
            $qtyVoid=0;
            $hargaVoid=0;

            foreach($voidData as $value){
                $harga = $value->harga;
                $qtyVoid= pos_activity_item_and_desktop::where('id_item', $value->id_item)
                ->where('id_store', session()->get('store'))
                ->where('harga', $value->harga)
                ->where('isDell', 1)
                ->whereDate('created_at', today())
                ->sum('qty');
                $hargaVoid= $harga * $qtyVoid;
                array_push($void, new item(Str::limit($value->nama_item, 21, '_')." ( x ".$qtyVoid." ) ", "Rp. ".number_format($hargaVoid,0,",",".")));
            }



            $totalPendapatan = pos_activity_item_and_desktop::where('id_store', session()->get('store'))
            ->whereDate('created_at', today())
            ->where('isDell', 0)
            ->sum('total');

            $totalPendapatanCo = $totalPendapatan + $deposit;

            $ip_client = $request->ip();
            $printer_client = pos_pc_desktop::where('ip', $ip_client)
            ->value('printer');
            $printer_kasir = pos_store_desktop::where('id_store', session()->get('store'))
            ->value('ip_kasir');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);
            $printer -> text("\nNOTA SALES ORDER\n");
            $printer -> text("CO_ID   : ".$penjualanSession->no_co."\nstore   : ".$thisStore[$deposits->id_store]['name']."\nKasir   : ".substr($admin, 3)."\n"."Tanggal : ".$dtn);
            $printer -> feed();
            $printer -> text("________________________________________________\n");
            $printer -> feed();
            $printer -> text("Deposit    : Rp. ".number_format($deposit,0,",",".")."\nPendapatan : Rp. ".number_format($totalPendapatan,0,",","."));
            $printer -> text("\nTotal      : "."Rp. ".number_format($totalPendapatanCo,0,",",".")."\nTunai      : "."Rp. ".number_format($totalTunai,0,",",".")." (".$countTunai." invoice)");
            $printer -> text("\nEDC        : "."Rp. ".number_format($totalEDC,0,",",".")." (".$countEDC." invoice)");
            $printer -> text("\nFintech    : "."Rp. ".number_format($totalFintech,0,",",".")." (".$countFintech." invoice)");
            $printer -> feed();
            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */

            $itemsMa = array();
            $totalQty=0;
            $totalHarga=0;


            foreach($penjualanTodayMa as $value){
                $harga = $value->harga;
                $totalQty= pos_activity_item_and_desktop::where('id_item', $value->id_item)
                ->where('id_store', session()->get('store'))
                ->where('isDell', 0)
                ->where('harga', $value->harga)
                ->whereDate('created_at', today())
                ->sum('qty');
                $totalHarga= $harga * $totalQty;
                array_push($itemsMa, new item(Str::limit($value->nama_item, 21, '_')." ( x ".$totalQty." ) ", "Rp. ".number_format($totalHarga,0,",",".")));
            }

            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> text("Makanan :");
            $printer -> feed(2);

            foreach ($itemsMa as $item) {
                $printer -> text($item);
            }

            $printer -> feed();

            $itemsMi = array();
            $totalQty=0;
            $totalHarga=0;


            foreach($penjualanTodayMi as $value){
                $harga = $value->harga;
                $totalQty= pos_activity_item_and_desktop::where('id_item', $value->id_item)
                ->where('id_store', session()->get('store'))
                ->where('harga', $value->harga)
                ->where('isDell', 0)
                ->whereDate('created_at', today())
                ->sum('qty');
                $totalHarga= $harga * $totalQty;
                array_push($itemsMi, new item(Str::limit($value->nama_item, 21, '_')." ( x ".$totalQty." ) ", "Rp. ".number_format($totalHarga,0,",",".")));
            }

            $printer -> text("Minuman :");
            $printer -> feed(2);

            foreach ($itemsMi as $item) {
                $printer -> text($item);
            }

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();
            $printer -> text("Void :");
            $printer -> feed();
            foreach ($void as $item) {
                $printer -> text($item);
            }
            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> text('TOTAL = '."Rp. ".number_format($totalPendapatanCo,0,",",".")."\n");
            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> cut();
            $printer -> close();

            //session()->flush();
            return back();
        } catch (Exception $exception) {

            //session()->flush();
            return back();
        }

    }

    public function cekKitchen(){
        try {

            $ip_target = pos_store_desktop::where('id_store', session()->get('store'))
            ->value('ip_kitchen');

            //$connector = new NetworkPrintConnector("$ip_target", 9100);
            $ch = curl_init($ip_target);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($httpcode !== 0){
              $connector = new NetworkPrintConnector("$ip_target", 9100);
            } else {
              return redirect('/cek-bar');
            }

            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

            $printer -> text("\nCHECK IT - KITCHEN\n\n");
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> setJustification(Printer::MODE_FONT_A);
            $printer -> selectPrintMode();
            $printer -> feed();

            $printer -> cut();
            $printer -> close();

            return redirect('/cek-bar');
        } catch (Exception $exception) {
            return redirect('/cek-bar');
        }
    }

    public function cekBar(){
        try {

            $ip_target = pos_store_desktop::where('id_store', session()->get('store'))
            ->value('ip_bar');

            //$connector = new NetworkPrintConnector("$ip_target", 9100);
            $ch = curl_init($ip_target);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($httpcode !== 0){
              $connector = new NetworkPrintConnector("$ip_target", 9100);
            } else {
              return redirect('/dashboardPenjualan');
            }

            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

            $printer -> text("\nCHECK IT - BAR\n\n");
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> setJustification(Printer::MODE_FONT_A);
            $printer -> selectPrintMode();
            $printer -> feed();

            $printer -> cut();
            $printer -> close();

            return redirect('/dashboardPenjualan');
        } catch (Exception $exception) {
            return redirect('/dashboardPenjualan');
        }
    }

    public function openCashDrawer(){

        try {

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $printer_kasir = pos_pc_desktop::findOrFail($PC)
            ->value('cashier_printer');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            $printer -> pulse();
            $printer -> close();

            return redirect()->back();
        } catch (Exception $exception) {
            return redirect()->back();
        }

    }

    public function printBill($no_invoice){

        try {
            $admin = Auth::user()->name;

            $penjualan = pos_saved_cart_desktop::where('no_invoice', $no_invoice);
            $dtn = Carbon::now();
            $storeName = pos_store_desktop::where('id_store', session()->get('store'))->value('nama_store');

            if(!$penjualan->count() !== 0) {
                $pesananBaru = DB::table('pos_saved_cart_desktops')
                ->where('no_invoice', '=', $no_invoice)
                ->where('id_store', session()->get('store'))
                ->get();
            } else {
                $pesananBaru = $penjualan;
            }

            $items = array();
            $totalQty=0;
            $totalHarga=0;

            foreach ($pesananBaru as $id => $value) {
                $totalQty+= $value->qty;
                $totalHarga+= $value->total;
                array_push($items, new item(Str::limit($value->nama_item, 21, '_')." (".$value->qty." x Rp. ".number_format($value->harga,0,",",".").")","Rp. ".number_format($value->total,0,",",".")));
            }

            $total = new item('Total: ',"Rp. ".number_format($totalHarga,0,",","."));

            $ip_client = request()->ip();
            $printer_client = pos_pc_desktop::where('ip', $ip_client)
            ->value('printer');

            $printer_kasir = pos_store_desktop::where('id_store', session()->get('store'))
            ->value('ip_kasir');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("PRINT BILL");
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("No. Nota : ".$no_invoice."\nSTORE    : ".$storeName."\nTanggal  : ".$dtn."\nKasir    : ".substr($admin, 3)."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($items as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            // $printer -> text($tax);
            // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text($total);
            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> selectPrintMode();
            $printer -> feed(2);

            $printer -> cut();
            $printer -> close();

            unset($cart);
            session()->put('cart', []);
            session()->flash('success', 'invoice reprinted successfully');

            return redirect('/dashboardSavedCart');
        } catch (Exception $exception) {
            return redirect('/dashboardSavedCart');
        }

    }

    public function printBillAll($id){
        //
        try {

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $cashierID = Auth::guard('cashier')->user()->id;
            $cashierName = Auth::guard('cashier')->user()->name;
            $order = pos_saved_cart_desktop::where('id', $id)->latest()->first();
            $orderDetail = pos_saved_cart_detail_desktop::where('saved_cart_id', $id)
            ->get();

            $items = array();
            $itemsAndNote = array();
            $totalQty=0;
            $totalHarga=0;

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($items, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item(""," ". number_format($value->subtotal,0,",",".")."\n"));
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($items, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item("", "Rp. ".number_format($value->subtotal,0,",",".")."\n"));
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                }

            }

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item(""," ". number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item("", "Rp. ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                }

            }

            $total = new item('Tagihan: ',"Rp. ".number_format($order->bill_amount,0,",","."));

            $printer_kasir = pos_pc_desktop::findOrFail($PC)
            ->value('cashier_printer');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setFont(Printer::FONT_A);
            $printer -> setTextSize(3, 2);
            $printer -> text("PRINT BILL\n\n");
            $printer -> selectPrintMode();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($items as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> feed(2);

            $printer -> selectPrintMode();

            $printer -> cut();

            // print 2
            // + note item
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setFont(Printer::FONT_A);
            $printer -> setTextSize(3, 2);
            $printer -> text("PRINT BILL\n\n");
            $printer -> selectPrintMode();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($itemsAndNote as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> feed(2);

            $printer -> selectPrintMode();

            $printer -> cut();
            $printer -> pulse();
            $printer -> close();

            foreach ($orderDetail as $key => $value) {
                $data = pos_saved_cart_detail_desktop::find($value->id);
                $data->isPrinted = 1;
                $data->update();
            }

            //return redirect('/print-kitchen');
        } catch (Exception $exception) {
            //return redirect('/print-kitchen');
        }

    }

    public function saveCartPrint($id, $invoice){
        //
        try {

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $cashierID = Auth::guard('cashier')->user()->id;
            $cashierName = Auth::guard('cashier')->user()->name;
            $order = pos_saved_cart_desktop::where('id', $id)->latest()->first();
            $orderDetail = pos_saved_cart_detail_desktop::where('saved_cart_id', $id)
            ->where('no_invoice', $invoice)
            ->get();

            $items = array();
            $itemsAndNote = array();
            $totalQty=0;
            $totalHarga=0;

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($items, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item(""," ". number_format($value->subtotal,0,",",".")."\n"));
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($items, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item("", "Rp. ".number_format($value->subtotal,0,",",".")."\n"));
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                }

            }

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item(""," ". number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item("", "Rp. ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                }

            }

            $total = new item('Tagihan: ',"Rp. ".number_format($order->bill_amount,0,",","."));

            $printer_kasir = pos_pc_desktop::findOrFail($PC)
            ->value('cashier_printer');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setFont(Printer::FONT_A);
            $printer -> setTextSize(3, 2);
            $printer -> text("PRINT BILL\n\n");
            $printer -> selectPrintMode();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($items as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> feed(2);

            $printer -> selectPrintMode();

            $printer -> cut();

            // print 2
            // + note item
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setFont(Printer::FONT_A);
            $printer -> setTextSize(3, 2);
            $printer -> text("PRINT BILL\n\n");
            $printer -> selectPrintMode();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($itemsAndNote as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> feed(2);

            $printer -> selectPrintMode();

            $printer -> cut();
            $printer -> pulse();
            $printer -> close();

            foreach ($orderDetail as $key => $value) {
                $data = pos_saved_cart_detail_desktop::find($value->id);
                $data->isPrinted = 1;
                $data->update();
            }

            //return redirect('/print-kitchen');
        } catch (Exception $exception) {
            //return redirect('/print-kitchen');
        }

    }

    public function printBillRemain($id){
        try {

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $cashierID = Auth::guard('cashier')->user()->id;
            $cashierName = Auth::guard('cashier')->user()->name;
            $order = pos_saved_cart_desktop::where('id', $id)->latest()->first();
            $orderDetail = pos_saved_cart_detail_desktop::where('saved_cart_id', $id)
            ->where('isPrinted', 0)
            ->get();

            $items = array();
            $itemsAndNote = array();
            $totalQty=0;
            $totalHarga=0;

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($items, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item(""," ". number_format($value->subtotal,0,",",".")."\n"));
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($items, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($items, new item("", "-------------"));
                    array_push($items, new item("", "Rp. ".number_format($value->subtotal,0,",",".")."\n"));
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($items, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                }

            }

            foreach ($orderDetail as $key => $value) {

                if ($value->discount !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $discountAmount = $value->discount;
                    $totalDiscount = $product->price/100 * $discountAmount * $value->qty;
                    $totalQty+= $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal+$totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("diskon ".$discountAmount."%", " ".number_format($totalDiscount,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item(""," ". number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } elseif ($value->specialPrice !== 0) {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    $SPAmount = $value->specialPrice;
                    $SPMargin = ($product->price - $SPAmount) * $value->qty;

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")"," ".number_format($value->subtotal+$SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("potongan "." ".number_format($SPMargin,0,",",".")," ".number_format($SPMargin,0,",",".")));
                    array_push($itemsAndNote, new item("", "-------------"));
                    array_push($itemsAndNote, new item("", "Rp. ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                } else {
                    $product = pos_product_desktop::where('id', $value->product_id)->first();

                    array_push($itemsAndNote, new item(Str::limit($product->name, 21, '_')." (".$value->qty." x ".number_format($product->price,0,",",".").")", " ".number_format($value->subtotal,0,",",".")));
                    if ($value->note) {
                        array_push($itemsAndNote, new item("note:\n- ".str_replace(" | ", "\n- ", $value->note)."\n"));
                    }
                }

            }

            $total = new item('Tagihan: ',"Rp. ".number_format($order->bill_amount,0,",","."));

            $printer_kasir = pos_pc_desktop::findOrFail($PC)
            ->value('cashier_printer');
            $connector =  new CupsPrintConnector($printer_kasir);
            $printer = new Printer($connector);

            /* Name of shop */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setFont(Printer::FONT_A);
            $printer -> setTextSize(3, 2);
            $printer -> text("PRINT BILL\n\n");
            $printer -> selectPrintMode();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($items as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> feed(2);

            $printer -> selectPrintMode();

            $printer -> cut();

            // print 2
            // + note item
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setFont(Printer::FONT_A);
            $printer -> setTextSize(3, 2);
            $printer -> text("PRINT BILL\n\n");
            $printer -> selectPrintMode();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("SALOKA THEME PARK\n");
            $printer -> selectPrintMode();
            $printer -> text("Jl. Fatmawati No. 154, Gumuksari, Lopait\nKec. Tuntang, Semarang\nJawa Tengah 50773, Indonesia");
            $printer -> feed();

            /* Title of receipt */
            $printer -> text("________________________________________________\n");
            $printer -> feed();


            /* Title of receipt */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> selectPrintMode(Printer::MODE_FONT_A);

            $printer -> text("NO. INV     : ".$order->no_invoice."\nSTORE       : ".$storeName."\nTANGGAL     : ".Carbon::now()."\nKASIR       : ".$cashierName."\n"."CUST/MEJA   : ".$order->note."\n"."ID ORDER    : ".$order->id."\n");

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Items */
            $printer -> feed();
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach ($itemsAndNote as $item) {
                $printer -> text($item);
            }

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            /* Tax and total */
            $printer -> text($total);

            $printer -> text("________________________________________________\n");
            $printer -> feed();

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> feed(2);

            $printer -> selectPrintMode();

            $printer -> cut();
            $printer -> pulse();
            $printer -> close();

            foreach ($orderDetail as $key => $value) {
                $data = pos_saved_cart_detail_desktop::find($value->id);
                $data->isPrinted = 1;
                $data->update();
            }

            //return redirect('/print-kitchen');
        } catch (Exception $exception) {
            //return redirect('/print-kitchen');
        }
    }

}
