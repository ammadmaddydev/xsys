<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Retailer;
use App\RetailerOrder;
use App\RetailerOrderProduct;
use Response;



class RetailerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    //Registered a route with the name of 'retailer'
    public function index()
    {
        return view('retailer');
    }

    public function make_number($order_id){

        $id_length= strlen($order_id);

        if($id_length==1){
            $order_zeros='0000';
        }
        if($id_length==2){
            $order_zeros='000';
        }
        if($id_length==3){
            $order_zeros='00';
        }
        if($id_length==4){
            $order_zeros='0';
        }
        if($id_length==5){
            $order_zeros='';
        }

        /* $order_zeros='';
         for($i=0; $i<=strlen($order_id); $i++){
             $order_zeros.=0;
         }*/
        $current_month=date('m');
        $current_year=date('y');
        $number=$current_month.$current_year.$order_zeros.$order_id;
        return $number;
    }

    public function create_retailer(Request $request){

        //GET THE IMAGE
        $exploded= explode(',', $request->input('uploadImage'));

        $decoded= base64_decode($exploded[1]);

        if(str_contains($exploded[0],'jpeg')){
            $extension="jpg";
        }
        else{
            $extension="png";
        }

        //SAVE IT TO retailer_images FOLDER
        $file_name=str_random().'.'.$extension;
        $path=public_path().'/retailers_img/'.$file_name;
        file_put_contents($path, $decoded);

        //GET ALL OTHER FIELDS
        $fullname= $request->input('fullname');
        $cnic= $request->input('cnic');
        $phone_no= $request->input('phone_no');

        //Getting the logged in user id
        $user=Auth::user()->id;

        //query of getting users with the given phone number or cnic.
        $check_exist=Retailer::where('phone_no','=',$phone_no)->orWhere('cnic',$cnic)->count();

        //Check if the user is already exist or not.
        if($check_exist>0){

            //Use for any type of conflict.
            return 406;
        }
        else{
            //If not exist, consider it as a new retailer also push the details on database.
            try{
                DB::beginTransaction();
                $retailer = Retailer::create(array('name' => $fullname,
                    'cnic' => $cnic,
                    'phone_no'=>$phone_no,
                    'image'=>$file_name,
                    'created_by'=>$user));
                DB::commit();
                //Use 201 for succesfull work.
                return 201;
            }
            catch(\Exception $exc){
                DB::rollBack();
                return $exc;
            }
        }

    }

    //Registered a route with the name of 'retailer/edit_retailer'
    public function edit_retailer(Request $request){
        //GET THE IMAGE
        $exploded= explode(',', $request->input('uploadImage'));

        $decoded= base64_decode($exploded[1]);

        if(str_contains($exploded[0],'jpeg')){
            $extension="jpg";
        }
        else{
            $extension="png";
        }

        //SAVE IT TO retailer_images FOLDER
        $file_name=str_random().'.'.$extension;
        $path=public_path().'/retailers_img/'.$file_name;
        file_put_contents($path, $decoded);
        //GET ALL OTHER FIELDS

        $id=$request->input('id');
        $fullname= $request->input('fullname');
        $cnic= $request->input('cnic');
        $phone_no= $request->input('phone_no');

        //Getting the logged in user id
        $user=Auth::user()->id;

        //query of getting users with the given phone number or cnic.
        $check_exist=Retailer::where('id','!=',$id)->where('phone_no','=',$phone_no)->count();

        //Check if the user is already exist or not.
        if($check_exist>0){

            //Use for any type of conflict.
            return 406;
        }
        else{
            //If not exist, consider it as a new retailer also push the details on database.
            try{
                DB::beginTransaction();
                $retailer = Retailer::where('id', $id)->update(array('name' => $fullname,
                    'cnic' => $cnic,
                    'phone_no'=>$phone_no,
                    'image'=>$file_name,
                    'updated_by'=>$user));
                DB::commit();
                //Use 201 for succesfull work.
                return 201;
            }
            catch(\Exception $exc){
                DB::rollBack();
                return $exc;
            }
        }
    }


    //Registered a route with the name of 'retailer/delete_retailer'
    public function deactivate_retailer(Request $request){
        $id=$request->input('id');

        //Getting the logged in user id
        $user=Auth::user()->id;
        try{
            DB::beginTransaction();
            Retailer::where('id', $id)->update(array('is_deleted'=>1, 'updated_by'=>$user));
            DB::commit();
            return 201;
        }
        catch(\Exception $exc){
            DB::rollBack();
            return $exc;
        }
    }

    //Registered a route with the name of 'retailer/reactivate_retailer'
    public function activate_retailer(Request $request){
        $id=$request->input('id');

        //Getting the logged in user id
        $user=Auth::user()->id;
        try{
            DB::beginTransaction();
            Retailer::where('id', $id)->update(array('is_deleted'=>0, 'updated_by'=>$user));
            DB::commit();
            return 201;
        }
        catch(\Exception $exc){
            DB::rollBack();
            return $exc;
        }
    }

    //Registered a route with the name of 'retailer/get_retailers'
    public function get_all_retailers(){
        $records=Retailer::where('is_deleted',0)->get();

        return Response::json($records);
    }

    //Registered a route with the name of 'retailer/order/create'
    public function create_new_order(Request $request){
        $estimation_date= $request->input('estimation_date');
        $duration= $request->input('retailer_credit_duration');
        $ageing_duration=date('Y-m-d', strtotime($estimation_date. ' + '.$duration.' days'));
        $retailer_id= $request->input('retailer_id');
        $retailer_outlet_id= $request->input('retailer_outlet_id');
        $sales_officer_id= $request->input('sales_officer_id');
        $warehouse_id= $request->input('warehouse_id');
        $products= $request->input('products');
        $user=Auth::user()->id;



        $order_created=RetailerOrder::create([
            'retailer_id'=>$retailer_id,

            'outlet_id'=>$retailer_outlet_id,
            'sales_officer_id'=>$sales_officer_id,
            'total_cost'=>0,
            'remaining_payment'=>0,
            'retailer_order_status_id'=>1,
            'warehouse_id'=>$warehouse_id,
            'ageing_date'=>$ageing_duration,
            'created_by'=>$user
        ]);
        $order_id= $order_created->id;

        $order_number='CR'.$this->make_number($order_id);



        RetailerOrder::where('id',$order_id)->update(['order_no'=>$order_number]);
        $total_order_price=0;
        foreach($products as $product){
            $products_arr=array(
                'retailer_order_id'=>$order_id,
                'product_color_id'=>$product['product_color_id'],
                'product_qty'=>$product['quantity'],
                'unit_price'=>$product['cost_per_set'],
                'total_price'=>($product['quantity']*$product['cost_per_set']),
                'created_by'=>$user
            );
            $total_order_price+=$product['quantity']*$product['cost_per_set'];
            RetailerOrderProduct::create($products_arr);
        }

        RetailerOrder::where('id',$order_id)->update(['total_cost'=>$total_order_price,'remaining_payment'=>$total_order_price]);
        return 201;
    }


    public function get_outlet_details($id) {
        $query = DB::table('tbl_retailer_outlet')
                        ->join('tbl_retailer', 'tbl_retailer.id', '=', 'tbl_retailer_outlet.retailer_id')
                        ->join('tbl_city', 'tbl_city.id', '=', 'tbl_retailer_outlet.city_id')
                        ->select('tbl_retailer_outlet.name', 'tbl_retailer.retailer_no', 'tbl_city.name as cname'
                                , 'tbl_retailer_outlet.address')
                        ->where(['tbl_retailer_outlet.id' => $id])->get();
        return $query;
    }
    //Registered a route with the name of 'retailer/create_order'
    public function create_order(){
        return View('create_retailer_order');
    }

    public function retailer_list(){
        return view('retailer_list');
    }

    public function retailer_master_info(){
        return View('RetailerMasterDetails');
    }
}
