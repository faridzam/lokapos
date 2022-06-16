<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\printController;

use App\Events\savedCartUpdated;

use App\Models\pos_log_activity_desktop;
use App\Models\pos_deposit_desktop;
use App\Models\pos_raw_material_desktop;
use App\Models\pos_ingredient_desktop;
use App\Models\pos_recipe_desktop;
use App\Models\pos_category_desktop;
use App\Models\pos_product_desktop;
use App\Models\pos_product_store_desktop;
use App\Models\pos_store_desktop;
use App\Models\pos_payment_desktop;
use App\Models\pos_payment_store_desktop;
use App\Models\pos_order_desktop;
use App\Models\pos_order_detail_desktop;
use App\Models\pos_saved_cart_desktop;
use App\Models\pos_saved_cart_detail_desktop;
use App\Models\pos_close_order_desktop;

class penjualanController extends Controller
{
    //

    public function showProduct(Request $request){

        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }

        $categoryID = pos_product_store_desktop::where('store_id', $storeID)
        ->pluck('category_id');
        $productID = pos_product_store_desktop::where('store_id', $storeID)
        ->pluck('product_id');

        $categories = pos_category_desktop::select('id', 'name', 'type')
        ->whereIn('id', $categoryID)
        ->orderBy('name', 'asc')
        ->get();
        $products = pos_product_desktop::join('pos_product_store_desktops', 'pos_product_desktops.id', '=', 'pos_product_store_desktops.product_id')
        ->whereIn('pos_product_desktops.id', $productID)
        ->orderBy('name', 'asc')
        ->get();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ], Response::HTTP_OK);
    }

    public function showPaymentMethods(Request $request){

        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }

        $paymentID = pos_payment_store_desktop::where('store_id', $storeID)
        ->pluck('payment_id');

        $paymentMethods = pos_payment_desktop::select('id', 'name')
        ->whereIn('id', $paymentID)
        ->get();

        return response()->json([
            'paymentMethods' => $paymentMethods,
        ], Response::HTTP_OK);
    }

    public function countSavedCart(Request $request){

        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }
        $cashierID = Auth::guard('cashier')->user()->id;
        $quantity = pos_saved_cart_desktop::where('store_id', $storeID)
        ->where('pc_id', $PC)
        ->where('cashier_id', $cashierID)
        ->whereDate('created_at', today())
        ->count();

        return response()->json([
            'quantity' => $quantity,
        ], Response::HTTP_OK);
    }

    public function storeOrder(Request $request){

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $dateNow = Carbon::now();
            $cashierID = Auth::guard('cashier')->user()->id;
            $sex = pos_order_desktop::where('store_id', $storeID)
            ->where('pc_id', $PC)
            ->where('cashier_id', $cashierID)
            ->whereDate('created_at', today())
            ->count() + 1;
            $TID = pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex);
            $TIDx = str_replace(array('[',']','"'), '',$TID);

            while (pos_order_desktop::where('no_invoice', $TIDx)->get()->isNotEmpty()) {
                $TID = pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex);
                $TIDx = str_replace(array('[',']','"'), '',$TID);
                $sex++;
            }

            $cartObj = json_decode(request('cart'), TRUE);

            $request->validate([
                'cart' => 'required',
                'paymentMethod' => 'required',
                'tagihan' => 'required',
                'kembalian' => 'required',
                'bayar' => 'required',
                'taxAll' => 'required',
                'discountAll' => 'required',
            ]);

            //
            pos_order_desktop::create([
                'no_invoice' => $TIDx,
                'pc_id' => $PC,
                'store_id' => $storeID,
                'cashier_id' => $cashierID,
                'payment_id' => request('paymentMethod'),
                'bill_amount' => request('tagihan'),
                'pay_amount' => request('bayar'),
                'change_amount' => request('kembalian'),
                'tax' => request('taxAll'),
                'discount' => request('discountAll'),
                'note' => request('note'),
            ]);

            $latestOrderId = pos_order_desktop::where('store_id', $storeID)
            ->where('pc_id', $PC)
            ->where('cashier_id', $cashierID)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->first();

            foreach ($cartObj as $key => $value) {

                pos_order_detail_desktop::create([
                    'no_invoice' => $TIDx,
                    'order_id' => $latestOrderId->id,
                    'product_id' => $value['product_id'],
                    'qty' => $value['quantity'],
                    'subtotal' => $value['totalPrice'],
                    'discount' => $value['discount'],
                    'specialPrice' => $value['specialPrice'],
                    'note' => implode(" | ",$value['notes']),
                    'isActive' => 1,
                ]);

                $product = pos_product_desktop::where('id', $value['product_id'])
                ->first();
                $recipe = pos_recipe_desktop::where('id', $product->recipe_id)
                ->first();
                $ingredient = pos_ingredient_desktop::where('recipe_id', $recipe->id)
                ->get();

                for ($i=1; $i <= $value['quantity']; $i++) {

                    foreach ($ingredient as $key => $val) {
                        $data = pos_raw_material_desktop::find($val->raw_material_id);
                        $data->quantity -= intval($val->quantity);
                        $data->update();
                    }

                }

            }

            // Instantiate other controller class in this controller's method
            $print = new printController;
            // Use other controller's method in this controller's method
            $print->printInvoice($latestOrderId->id);

            return redirect('penjualan');

    }

    public function storeSavedOrder(Request $request){

            if (session('store')) {
                foreach (session('store') as $id_store => $choosenStore) {
                    $PC = $choosenStore['id'];
                    $storeID = $choosenStore['store_id'];
                    $storeName = $choosenStore['name'];
                }
            }

            $dateNow = Carbon::now();
            $cashierID = Auth::guard('cashier')->user()->id;
            $sex = pos_order_desktop::where('store_id', $storeID)
            ->where('pc_id', $PC)
            ->where('cashier_id', $cashierID)
            ->whereDate('created_at', today())
            ->count() + 1;
            $TID = pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex);
            $TIDx = str_replace(array('[',']','"'), '',$TID);

            while (pos_order_desktop::where('no_invoice', $TIDx)->get()->isNotEmpty()) {
                $TID = pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex);
                $TIDx = str_replace(array('[',']','"'), '',$TID);
                $sex++;
            }

            $cartObj = json_decode(request('cart'), TRUE);

            $request->validate([
                'cart' => 'required',
                'paymentMethod' => 'required',
                'tagihan' => 'required',
                'kembalian' => 'required',
                'bayar' => 'required',
                'taxAll' => 'required',
                'discountAll' => 'required',
            ]);

            //
            pos_order_desktop::create([
                'no_invoice' => $TIDx,
                'pc_id' => $PC,
                'store_id' => $storeID,
                'cashier_id' => $cashierID,
                'payment_id' => request('paymentMethod'),
                'bill_amount' => request('tagihan'),
                'pay_amount' => request('bayar'),
                'change_amount' => request('kembalian'),
                'tax' => request('taxAll'),
                'discount' => request('discountAll'),
                'note' => request('note'),
            ]);

            $latestOrderId = pos_order_desktop::where('store_id', $storeID)
            ->where('pc_id', $PC)
            ->where('cashier_id', $cashierID)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->first();

            foreach ($cartObj as $key => $value) {

                pos_order_detail_desktop::create([
                    'no_invoice' => $TIDx,
                    'order_id' => $latestOrderId->id,
                    'product_id' => $value['product_id'],
                    'qty' => $value['qty'],
                    'subtotal' => $value['subtotal'],
                    'discount' => $value['discount'],
                    'specialPrice' => $value['specialPrice'],
                    'note' => $value['note'],
                    'isActive' => 1,
                ]);

                $saved_cart_id = $value['saved_cart_id'];
                $orderDetails = pos_saved_cart_detail_desktop::findOrFail($value['id'])->delete();

            }

            $order = pos_saved_cart_desktop::findOrFail($saved_cart_id)->delete();

            event(new savedCartUpdated());

            // Instantiate other controller class in this controller's method
            $print = new printController;
            // Use other controller's method in this controller's method
            $print->printInvoice($latestOrderId->id);

            return redirect('saved-cart');

    }

    public function saveCart(Request $request){
        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }

        $dateNow = Carbon::now();
        $cashierID = Auth::guard('cashier')->user()->id;
        $cartObj = json_decode(request('cart'), TRUE);
        $selectedExisting = json_decode(request('selectedExisting'), TRUE);

        $request->validate([
            'cart' => 'required',
            'tagihan' => 'required',
            'selectedExisting' => 'required',
        ]);

        if ($selectedExisting['id'] !== 0) {
            //existing

            $savedData = pos_saved_cart_desktop::find($selectedExisting['id']);
            $savedData->bill_amount += request('tagihan');
            $savedData->updated_at = Carbon::now();
            $savedData->update();

            foreach ($cartObj as $key => $value) {

                pos_saved_cart_detail_desktop::create([
                    'no_invoice' => $selectedExisting['no_invoice'],
                    'saved_cart_id' => $selectedExisting['id'],
                    'product_id' => $value['product_id'],
                    'qty' => $value['quantity'],
                    'subtotal' => $value['totalPrice'],
                    'discount' => $value['discount'],
                    'specialPrice' => $value['specialPrice'],
                    'note' => implode(" | ",$value['notes']),
                ]);

            }

        } else {
            //new
            $sex = pos_saved_cart_desktop::where('store_id', $storeID)
            ->where('pc_id', $PC)
            ->where('cashier_id', $cashierID)
            ->whereDate('created_at', today())
            ->count() + 1;
            $TID = "SC".pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex)."-".Carbon::now()->toTimeString();
            $TIDx = str_replace(array('[',']','"'), '',$TID);

            while (pos_saved_cart_desktop::where('no_invoice', $TIDx)->get()->isNotEmpty()) {
                $TID = "SC".pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex)."-".Carbon::now()->toTimeString();
                $TIDx = str_replace(array('[',']','"'), '',$TID);
                $sex++;
            }

            pos_saved_cart_desktop::create([
                'no_invoice' => $TIDx,
                'pc_id' => $PC,
                'store_id' => $storeID,
                'cashier_id' => $cashierID,
                'bill_amount' => request('tagihan'),
                'note' => request('note'),
            ]);

            $latestOrderId = pos_saved_cart_desktop::where('store_id', $storeID)
            ->where('pc_id', $PC)
            ->where('cashier_id', $cashierID)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->first();

            foreach ($cartObj as $key => $value) {

                pos_saved_cart_detail_desktop::create([
                    'no_invoice' => $TIDx,
                    'saved_cart_id' => $latestOrderId->id,
                    'product_id' => $value['product_id'],
                    'qty' => $value['quantity'],
                    'subtotal' => $value['totalPrice'],
                    'discount' => $value['discount'],
                    'specialPrice' => $value['specialPrice'],
                    'note' => implode(" | ",$value['notes']),
                ]);

            }

        }

        event(new savedCartUpdated());

        return response()->json([
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    public function saveCartPrint(Request $request){

        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }

        $dateNow = Carbon::now();
        $cashierID = Auth::guard('cashier')->user()->id;
        $cartObj = json_decode(request('cart'), TRUE);
        $selectedExisting = json_decode(request('selectedExisting'), TRUE);

        $request->validate([
            'cart' => 'required',
            'tagihan' => 'required',
            'selectedExisting' => 'required',
        ]);

        $sex = pos_saved_cart_desktop::where('store_id', $storeID)
        ->where('pc_id', $PC)
        ->where('cashier_id', $cashierID)
        ->whereDate('created_at', today())
        ->count() + 1;
        $TID = "SC".pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex)."-".Carbon::now()->toTimeString();
        $TIDx = str_replace(array('[',']','"'), '',$TID);

        while (pos_saved_cart_desktop::where('no_invoice', $TIDx)->get()->isNotEmpty()) {
            $TID = "SC".pos_store_desktop::where('id', '=', $storeID)->pluck('id').'-'.$dateNow->format('Ymd').sprintf("%03d", $cashierID).sprintf("%04d", $sex)."-".Carbon::now()->toTimeString();
            $TIDx = str_replace(array('[',']','"'), '',$TID);
            $sex++;
        }


        if ($selectedExisting['id'] !== 0) {
            //existing
            $savedData = pos_saved_cart_desktop::find($selectedExisting['id']);
            $savedData->bill_amount += request('tagihan');
            $savedData->updated_at = Carbon::now();
            $savedData->update();


            foreach ($cartObj as $key => $value) {

                pos_saved_cart_detail_desktop::create([
                    'no_invoice' => $TIDx,
                    'saved_cart_id' => $selectedExisting['id'],
                    'product_id' => $value['product_id'],
                    'qty' => $value['quantity'],
                    'subtotal' => $value['totalPrice'],
                    'discount' => $value['discount'],
                    'specialPrice' => $value['specialPrice'],
                    'note' => implode(" | ",$value['notes']),
                ]);

            }

            // Instantiate other controller class in this controller's method
            $print = new printController;
            // Use other controller's method in this controller's method
            $print->saveCartPrint($savedData->id, $TIDx);

            event(new savedCartUpdated());

        } else {
            //new
            pos_saved_cart_desktop::create([
                'no_invoice' => $TIDx,
                'pc_id' => $PC,
                'store_id' => $storeID,
                'cashier_id' => $cashierID,
                'bill_amount' => request('tagihan'),
                'note' => request('note'),
            ]);

            $latestOrderId = pos_saved_cart_desktop::where('store_id', $storeID)
            ->where('pc_id', $PC)
            ->where('cashier_id', $cashierID)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->first();

            foreach ($cartObj as $key => $value) {

                pos_saved_cart_detail_desktop::create([
                    'no_invoice' => $TIDx,
                    'saved_cart_id' => $latestOrderId->id,
                    'product_id' => $value['product_id'],
                    'qty' => $value['quantity'],
                    'subtotal' => $value['totalPrice'],
                    'discount' => $value['discount'],
                    'specialPrice' => $value['specialPrice'],
                    'note' => implode(" | ",$value['notes']),
                ]);

            }

            // Instantiate other controller class in this controller's method
            $print = new printController;
            // Use other controller's method in this controller's method
            $print->saveCartPrint($latestOrderId->id, $TIDx);
        }

        event(new savedCartUpdated());

        return response()->json([
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    public function showSavedCart(){

        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }
        $cashierID = Auth::guard('cashier')->user()->id;

        $savedCartsID = pos_saved_cart_desktop::where('pc_id', $storeID)
        ->where('store_id', $storeID)
        ->where('cashier_id', $cashierID)
        ->whereDate('created_at', Carbon::today())
        ->pluck('id');
        $savedCarts = pos_saved_cart_desktop::whereIn('id', $savedCartsID)
        ->orderBy('created_at', 'asc')
        ->get();


        return response()->json([
            'savedCarts' => $savedCarts,
        ], Response::HTTP_OK);
    }
    public function showSavedCartDetail($id){

        $productID = pos_saved_cart_detail_desktop::where('saved_cart_id', $id)
        ->pluck('product_id');
        $productDetails = pos_product_desktop::whereIn('id', $productID)
        ->get();
        $savedCartDetails = pos_saved_cart_detail_desktop::where('saved_cart_id', $id)
        ->select('pos_saved_cart_detail_desktops.*', 'pos_product_desktops.recipe_id', 'pos_product_desktops.name', 'pos_product_desktops.cost', 'pos_product_desktops.price')
        ->join('pos_product_desktops', 'pos_product_desktops.id', '=', 'pos_saved_cart_detail_desktops.product_id')
        ->get();

        return response()->json([
            'savedCartDetails' => $savedCartDetails,
        ], Response::HTTP_OK);
    }

    public function printBillAll($id){
        // Instantiate other controller class in this controller's method
        $print = new printController;
        // Use other controller's method in this controller's method
        $print->printBillAll($id);
    }
    public function printBillRemain($id){
        // Instantiate other controller class in this controller's method
        $print = new printController;
        // Use other controller's method in this controller's method
        $print->printBillRemain($id);
    }

    public function closeOrder(Request $request){
        //
        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }
        $cashierID = Auth::guard('cashier')->user()->id;

        $request->validate([
            'quantity' => 'required',
        ]);


        pos_close_order_desktop::create([
            'pc_id' => $PC,
            'store_id' => $storeID,
            'cashier_id' => $cashierID,
            'pec100' => request('pecahan100'),
            'pec50' => request('pecahan50'),
            'pec20' => request('pecahan20'),
            'pec10' => request('pecahan10'),
            'pec5' => request('pecahan5'),
            'pec2' => request('pecahan2'),
            'pec1' => request('pecahan1'),
            'total' => request('total'),
        ]);

        // Instantiate other controller class in this controller's method
        $print = new printController;
        // Use other controller's method in this controller's method
        $print->printCloseOrder(request('quantity'));

    }

    public function getInvoices(){
        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }
        $cashierID = Auth::guard('cashier')->user()->id;

        $orderToday = pos_order_desktop::whereDate('created_at', today())
        ->where('pc_id', $PC)
        ->where('store_id', $storeID)
        ->where('cashier_id', $cashierID)
        ->get();
        $incomeToday = pos_order_desktop::whereDate('created_at', today())
        ->where('pc_id', $PC)
        ->where('store_id', $storeID)
        ->where('cashier_id', $cashierID)
        ->sum('bill_amount');
        $depositToday = pos_deposit_desktop::whereDate('created_at', today())
        ->where('pc_id', $PC)
        ->where('cashier_id', $cashierID)
        ->sum('total');

        return response()->json([
            'orderToday' => $orderToday,
            'incomeToday' => $incomeToday,
            'depositToday' => $depositToday,
        ], Response::HTTP_OK);
    }
    public function getItemSales(){
        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
                $storeID = $choosenStore['store_id'];
                $storeName = $choosenStore['name'];
            }
        }
        $cashierID = Auth::guard('cashier')->user()->id;

        $orderTodayID = pos_order_desktop::whereDate('created_at', today())
        ->where('pc_id', $PC)
        ->where('store_id', $storeID)
        ->where('cashier_id', $cashierID)
        ->pluck('id');

        $orderTodayProduct = pos_order_detail_desktop::whereDate('created_at', today())
        ->whereIn('order_id', $orderTodayID)
        ->select('product_id')
        ->distinct()
        ->pluck('product_id');

        $orderToday = [];

        foreach ($orderTodayProduct as $key => $value) {

            $product = pos_product_desktop::where('id', $value)->first();
            $quantity = pos_order_detail_desktop::whereDate('created_at', Carbon::today())
            ->where('product_id', $value)
            ->sum('qty');
            $total = pos_order_detail_desktop::whereDate('created_at', Carbon::today())
            ->where('product_id', $value)
            ->sum('subtotal');

            array_push($orderToday, (object)[
                'product_id' => $value,
                'name' => $product->name,
                'quantity' => $quantity,
                'total' => $total,
            ]);

        }


        $incomeToday = pos_order_desktop::whereDate('created_at', today())
        ->where('pc_id', $PC)
        ->where('store_id', $storeID)
        ->where('cashier_id', $cashierID)
        ->sum('bill_amount');
        $depositToday = pos_deposit_desktop::whereDate('created_at', today())
        ->where('pc_id', $PC)
        ->where('cashier_id', $cashierID)
        ->sum('total');

        return response()->json([
            'orderToday' => $orderToday,
            'incomeToday' => $incomeToday,
            'depositToday' => $depositToday,
        ], Response::HTTP_OK);
    }
}
