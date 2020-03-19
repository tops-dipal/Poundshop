<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SupplierMaster;
use App\SupplierContact;
use Illuminate\Support\Facades\View;
use App\Http\Requests\Api\Common\CreateRequest;
use Batch;
use App\Events\SendMail;
use App\References;
use App\SupplierReferences;

class SupplierController extends Controller
{

    function __construct(Request $request)
    {
        $this->middleware('permission:supplier-list', ['only' => ['index']]);

        $this->middleware('permission:supplier-create', ['only' => ['create','store']]);

        $this->middleware('permission:supplier-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:supplier-delete', ['only' => ['destroy']]);
        
        $route = $request->route();
        
        if(!empty($route))
        {   
            $action_array = explode('@',$route->getActionName());
            
            $function_name = !empty($action_array[1]) ? $action_array[1] : ''; 
            
            if(!empty($function_name))
            {    
                if($function_name == 'save_general_info')
                {   
                    CreateRequest::$roles_array = [
                                        'name' => 'required',
                                      ];
                }

                if($function_name == 'save_payment_info')
                {   
                    CreateRequest::$roles_array = [
                                        'id' => 'required',
                                      ];
                }

                if($function_name == 'save_terms_and_condition')
                {   
                    CreateRequest::$roles_array = [
                                        'id' => 'required',
                                      ];
                }

                if($function_name == 'save_contacts')
                {   
                    CreateRequest::$roles_array = [
                                        'supplier_id' => 'required',
                                      ];
                }

                if($function_name == 'destory_contacts') 
                {   
                    CreateRequest::$roles_array = [ 
                                                    'ids.*' => 'required', 
                                                    'supplier_id' => 'required', 
                                                 ]; 
                }


                if($function_name == 'save_default_contacts') 
                {   
                    CreateRequest::$roles_array = [ 
                                                    'contact_id' => 'required', 
                                                    'supplier_id' => 'required', 
                                                ]; 
                }
            }
        }                              
    }
    
    /**
     * Display a listing of the resource.
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {   
        try
        {
        
            $columns=[
                    0 => 'supplier_master.id',
                    1 => 'supplier_master.name',
                    2 => 'account_no',
                    3 => 'credit_limit_allowed',
                    4 => 'prime_contact',
                    5 => 'prime_email',
                    6 => 'prime_phone',
                    7 => 'prime_city',
                    8 => 'supplier_category',
            ];

            $adv_search_array = array();

            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }

            $params  = array(
                 'order_column'    => $columns[$request->order[0]['column']],
                 'order_dir'       => $request->order[0]['dir'],
                 'search'          => $request->search['value'],
                 'advanceSearch'   => $adv_search_array
            );

            
            $result=SupplierMaster::getAllSupplier($request->length, $params);
            $data = [];
            
            // listing data
            if (!empty($result)) {
                    $data = $result->getCollection()->transform(function ($result) use ($data, $request) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                        $name = "-";
                        if(!empty($result->name))
                        {
                            if($request->user()->can('supplier-edit'))
                            {
                                $name = '<a href="'.url('supplier/form'.$result->id).'#general">'.ucwords($result->name).'</a>';
                            }   
                            else
                            {
                                $name = ucwords($result->name);
                            } 
                        }   
                        $tempArray[] = $name;
                        $tempArray[] = !empty($result->account_no) ? $result->account_no : '-';
                        $tempArray[] = !empty($result->credit_limit_allowed) ? $result->credit_limit_allowed : '-';
                        $tempArray[] = !empty($result->prime_contact) ? ucwords($result->prime_contact) : '-';
                        $tempArray[] = !empty($result->prime_email) ? $result->prime_email : '-';
                        $tempArray[] = !empty($result->prime_phone) ? $result->prime_phone : '-';
                        $tempArray[] = !empty($result->prime_city) ? ucwords($result->prime_city) : '-';
                        $tempArray[] = !empty($result->supplier_category) ? ucwords(supplierCategory($result->supplier_category)) : "";
                        $viewActionButton = View::make('supplier.action-buttons', ['object' => $result]);
                        $tempArray[]      = $viewActionButton->render();
                        return $tempArray;
                    });
            }
            
            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $result->total(), // Total number of records
                "recordsFiltered" => $result->total(),
                "data"            => $data // Total data array
            ];
           
            return response()->json($jsonData);
        } catch (Exception $ex) {
            
        }   
    }

     /**
     * Store a newly created resource in storage.
     * @author : Shubham Dayma
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save_general_info(CreateRequest $request)
    {
        try
        {
            $db_array['id'] = !empty($request->id) ? $request->id : NULL;
            $db_array['name'] = !empty($request->name) ? $request->name : NULL;
            $db_array['account_no'] = !empty($request->account_no) ? $request->account_no : NULL;
            $db_array['min_po_amt'] = !empty($request->min_po_amt) ? $request->min_po_amt : NULL;
            $db_array['avg_lead_time'] = !empty($request->avg_lead_time) ? $request->avg_lead_time : NULL;
            $db_array['supplier_category'] = !empty($request->supplier_category) ? $request->supplier_category : NULL;
            $db_array['credit_limit_allowed'] = !empty($request->credit_limit_allowed) ? $request->credit_limit_allowed : NULL;
            $db_array['address_line1'] = !empty($request->address_line1) ? $request->address_line1 : NULL;
            $db_array['address_line2'] = !empty($request->address_line2) ? $request->address_line2 : NULL;
            /*$db_array['country_id'] = !empty($request->country_id) ? $request->country_id : NULL;
            $db_array['state_id'] = !empty($request->state_id) ? $request->state_id : NULL;
            $db_array['city_id'] = !empty($request->city_id) ? $request->city_id : NULL;*/
            $db_array['country_id'] = !empty($request->country_id) ? $request->country_id : NULL;
            if(isset($request->state_id))
               {
                    $state=\App\State::where('name',$request->state_id)->where('country_id',$request->country_id)->first();
                    if(empty($state))
                    {
                        $stateObj=new \App\State;
                        $stateObj->name=$request->state_id;
                        $stateObj->country_id=$request->country_id;
                        $stateObj->save();
                        $db_array['state_id'] =$stateObj->id;
                    }
                    else
                    {
                        $db_array['state_id']  = $state->id;
                    }
               }
               if(isset($request->city_id))
               {
                    $city=\App\City::where('name',$request->city_id)->where('state_id',$db_array['state_id'])->first();
                  
                    if(empty($city))
                    {

                        $cityObj=new \App\City;
                        $cityObj->name=$request->city_id;
                        $cityObj->state_id=$db_array['state_id'];
                        $cityObj->save();
                        $db_array['city_id'] =$cityObj->id;
                    }
                    else
                    {
                        $db_array['city_id'] = $city->id;
                    }
               }           
            $db_array['zipcode'] = !empty($request->zipcode) ? $request->zipcode : NULL;
            $db_array['date_rel_start'] = !empty($request->date_rel_start) ? db_date($request->date_rel_start) : NULL;
            $db_array['comment'] = !empty($request->comment) ? $request->comment : NULL;
            
            if(empty($db_array['id']))
            {
                // Create new supplier 
                $db_array['created_by'] = $request->user()->id;
                
                $supplier_id = SupplierMaster::create($db_array)->id;
            }
            else
            {
                // Update existing supplier
                $db_array['modified_by'] = $request->user()->id;

                SupplierMaster::where('id', $db_array['id'])->update($db_array);
                
                $supplier_id = $db_array['id'];
            }
            
            if(!empty($supplier_id))
            {
                if(!empty($db_array['id']))
                {
                    return $this->sendResponse('Supplier has been updated successfully', 200);
                }   
                else
                {
                    return $this->sendResponse('Supplier has been created successfully', 200, array('id' => $supplier_id));
                } 
            }   
            else
            {
                return $this->sendError('Supplier does not created, please try again', 422);
            } 
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        } 
    }        

    /**
     * Store a update payment terms of supplier.
     * @author : Shubham Dayma
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    
    public function save_payment_info(CreateRequest $request)
    {
        try
        {
            $db_array['id'] = !empty($request->id) ? $request->id : NULL;
            $db_array['payment_term'] = !empty($request->payment_term) ? $request->payment_term : NULL;
            $db_array['payment_days'] = !empty($request->payment_days) ? $request->payment_days : NULL;
            $db_array['allow_retro_discount'] = !empty($request->allow_retro_discount) ? $request->allow_retro_discount : 0;
            $db_array['retro_amount'] = !empty($request->retro_amount) ? $request->retro_amount : NULL;
            $db_array['retro_from_date'] = !empty($request->retro_from_date) ? $request->retro_from_date : NULL;
            $db_array['retro_to_date'] = !empty($request->retro_to_date) ? $request->retro_to_date : NULL;
            $db_array['retro_percent_discount'] = !empty($request->retro_percent_discount) ? $request->retro_percent_discount : NULL;
            $db_array['allow_overall_discount'] = !empty($request->allow_overall_discount) ? $request->allow_overall_discount : 0;
            $db_array['overall_percent_discount'] = !empty($request->overall_percent_discount) ? $request->overall_percent_discount : NULL;
            $db_array['allow_period_discount'] = !empty($request->allow_period_discount) ? $request->allow_period_discount : 0;
            $db_array['period_discount_days'] = !empty($request->period_discount_days) ? $request->period_discount_days : NULL;
            $db_array['period_percent_discount'] = !empty($request->period_percent_discount) ? $request->period_percent_discount : NULL;
            $db_array['beneficiary_name'] = !empty($request->beneficiary_name) ? $request->beneficiary_name : NULL;
            $db_array['bene_address1'] = !empty($request->bene_address1) ? $request->bene_address1 : NULL;
            $db_array['bene_address2'] = !empty($request->bene_address2) ? $request->bene_address2 : NULL;
            $db_array['bene_country'] = !empty($request->bene_country) ? $request->bene_country : NULL;
            $db_array['bene_state'] = !empty($request->bene_state) ? $request->bene_state : NULL;
            $db_array['bene_city'] = !empty($request->bene_city) ? $request->bene_city : NULL;
            $db_array['bene_zipcode'] = !empty($request->bene_zipcode) ? $request->bene_zipcode : NULL;
            $db_array['bene_account_no'] = !empty($request->bene_account_no) ? $request->bene_account_no : NULL;
            $db_array['bene_bank_name'] = !empty($request->bene_bank_name) ? $request->bene_bank_name : NULL;
            $db_array['bank_address1'] = !empty($request->bank_address1) ? $request->bank_address1 : NULL;
            $db_array['bank_address2'] = !empty($request->bank_address2) ? $request->bank_address2 : NULL;
            $db_array['bank_country'] = !empty($request->bank_country) ? $request->bank_country : NULL;
            $db_array['bank_state'] = !empty($request->bank_state) ? $request->bank_state : NULL;
            $db_array['bank_city'] = !empty($request->bank_city) ? $request->bank_city : NULL;
            $db_array['bank_zipcode'] = !empty($request->bank_zipcode) ? $request->bank_zipcode : NULL;
            $db_array['bank_swift_code'] = !empty($request->bank_swift_code) ? $request->bank_swift_code : NULL;
            $db_array['bank_iban_no'] = !empty($request->bank_iban_no) ? $request->bank_iban_no : NULL;

            // if payment_term  != X days after devlivery date, set values to blank
            if($db_array['payment_term'] != '3')
            {
                $db_array['payment_days'] = NULL;
                $db_array['allow_period_discount'] = 0;
                $db_array['period_discount_days'] = NULL;
                $db_array['period_percent_discount'] = NULL;
            }    

            if(empty($db_array['allow_retro_discount']) && @$db_array['allow_retro_discount'] != 1)
            {
                $db_array['retro_amount'] = NULL;
                $db_array['retro_from_date'] = NULL;
                $db_array['retro_to_date'] = NULL;
                $db_array['retro_percent_discount'] = NULL;
            }    
            else
            {
                $db_array['retro_from_date'] = db_date($db_array['retro_from_date']);
                $db_array['retro_to_date'] = db_date($db_array['retro_to_date']);
            }

            if($db_array['allow_overall_discount'] == 0)
            {
                $db_array['overall_percent_discount'] = NULL;
            }

            if($db_array['allow_period_discount'] == 0)
            {
                $db_array['period_discount_days'] = NULL;
                $db_array['period_percent_discount'] = NULL;
            }

            $db_array['modified_by'] = $request->user()->id;
            $db_array['updated_at'] = date('Y-m-d H:i:s');

            if(SupplierMaster::where('id', $db_array['id'])->update($db_array))
            {
                return $this->sendResponse('Supplier has been updated successfully', 200, array('id' => $db_array['id']));
            }   
            else
            {
                return $this->sendError('Supplier does not updated, please try again', 422);
            } 
        }   
        catch (Exception $ex) {
           return $this->sendError($ex->getMessage(), 400);
        }
    }

    /**
     * Store a update contacts of supplier.
     * @author : Shubham Dayma
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    
    public function save_contacts(CreateRequest $request)
    {
        try
        {
            $resp = 0;
            $db_array['id'] = !empty($request->id) ? $request->id : NULL;
            $db_array['supplier_id'] = !empty($request->supplier_id) ? $request->supplier_id : NULL;
            $db_array['name'] = !empty($request->name) ? $request->name : NULL;
            $db_array['email'] = !empty($request->email) ? $request->email : NULL;
            $db_array['phone'] = !empty($request->phone) ? $request->phone : NULL;
            $db_array['mobile'] = !empty($request->mobile) ? $request->mobile : NULL;
            $db_array['designation'] = !empty($request->designation) ? $request->designation : NULL;
            $db_array['is_primary'] = !empty($request->is_primary) ? 1 : 0;
            
            if($db_array['is_primary'] == 1)
            {
                SupplierContact::where('supplier_id', $db_array['supplier_id'])->update(array('is_primary' => 0));
            }
            else
            {
                $supplier_contacts = SupplierContact::where('supplier_id', $db_array['supplier_id'])->pluck('id')->toArray();
                
                if(empty($supplier_contacts))
                {
                    $db_array['is_primary'] = 1;
                }    
            }    

            if(empty($db_array['id']))
            {
                $db_array['created_by'] = $request->user()->id;    
                $db_array['created_at'] = date('Y-m-d H:i:s');
                $resp = SupplierContact::create($db_array);
            }
            else
            {
                $db_array['modified_by'] = $request->user()->id;
                $db_array['updated_at'] = date('Y-m-d H:i:s');
                $resp = SupplierContact::where('id', $db_array['id'])->update($db_array);
            }

            if($resp)
            {
                if(empty($db_array['id']))
                {
                    return $this->sendResponse('Supplier contact has been created successfully', 200);
                }   
                else
                { 
                    return $this->sendResponse('Supplier contact has been updated successfully', 200);
                }
            }   
            else
            {
                return $this->sendError('Supplier contact does not updated, please try again', 422);
            }    
        }   
        catch (Exception $ex) {
           return $this->sendError($ex->getMessage(), 400);
        }
    }


    /**
     * Store a update terms_and_condition of supplier.
     * @author : Shubham Dayma
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    
    public function save_terms_and_condition(CreateRequest $request)
    {
        try
        {
            $db_array['id'] = !empty($request->id) ? $request->id : NULL;            
            $db_array['term_condition'] = !empty($request->term_condition) ? $request->term_condition : NULL;
            $db_array['modified_by'] = $request->user()->id;
            $db_array['updated_at'] = date('Y-m-d H:i:s');
            if(SupplierMaster::where('id', $db_array['id'])->update($db_array))
            {
                return $this->sendResponse('Supplier has been updated successfully', 200, array('id' => $db_array['id']));
            }   
            else
            {
                return $this->sendError('Supplier does not updated, please try again', 422);
            }    
        }   
        catch (Exception $ex) {
           return $this->sendError($ex->getMessage(), 400);
        }
    }

    
    /**
     * Store a newly created resource in storage.
     * @author : Shubham Dayma
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(CreateRequest $request)
    // {
    //     try
    //     {
    //         // takes only fields of supplier master
    //         $supplier_master = $request->except('supplier_contacts', '_token', 'created_at', 'primary_contact');
            
    //         // format date as per db requirment
    //         $supplier_master['date_rel_start'] = db_date($supplier_master['date_rel_start']);

    //         // if payment_term  != X days after devlivery date, set values to blank
    //         if($supplier_master['payment_term'] != '3')
    //         {
    //             $supplier_master['payment_days'] = NULL;
    //             $supplier_master['allow_period_discount'] = '0';
    //             $supplier_master['period_discount_days'] = NULL;
    //             $supplier_master['period_percent_discount'] = NULL;
    //         }    

    //         if(empty($supplier_master['allow_retro_discount']) && @$supplier_master['allow_retro_discount'] != '1')
    //         {
    //             $supplier_master['retro_amount'] = NULL;
    //             $supplier_master['retro_from_date'] = NULL;
    //             $supplier_master['retro_to_date'] = NULL;
    //             $supplier_master['retro_percent_discount'] = NULL;
    //         }    
    //         else
    //         {
    //             $supplier_master['retro_from_date'] = db_date($supplier_master['retro_from_date']);
    //             $supplier_master['retro_to_date'] = db_date($supplier_master['retro_to_date']);
    //         }

    //         if(empty($supplier_master['id']))
    //         {
    //             // Create new supplier 
    //             $supplier_master['created_by'] = $request->user()->id;
                
    //             $supplier_id = SupplierMaster::create($supplier_master)->id;
    //         }
    //         else
    //         {
    //             // Update existing supplier
    //             $supplier_master['modified_by'] = $request->user()->id;

    //             SupplierMaster::where('id', $supplier_master['id'])->update($supplier_master);
                
    //             $supplier_id = $supplier_master['id'];
    //         }

    //         $obj_contact = new SupplierContact;
            
    //         $exist_contacts = array();

    //         if(!empty($supplier_master['id']))
    //         { 
    //             // Get existing supplier contacts
    //             $exist_contacts = $obj_contact->where('supplier_id', $supplier_master['id'])->pluck('id')->all();
    //         }    

    //         // take form fields for supplier contacts
    //         if(!empty($request->only('supplier_contacts')))
    //         {    
    //             $sup_insert_contact = array();
                
    //             $sup_update_contact = array();
                
    //             $updateWhereIn = array();

    //             $supplier_contacts = $request->only('supplier_contacts');
                
    //             $primary_contact_email = !empty($request->only('primary_contact')) ? $request->only('primary_contact')['primary_contact'] : '' ;
                
    //             foreach($supplier_contacts['supplier_contacts'] as $key => $supplier_contact)
    //             {    
    //                 // formdata for each supplier is stored in querystring format
    //                 parse_str($supplier_contact, $contact_array);
                    
    //                 // set primary contact with extra form field name email
    //                 $contact_array['is_primary'] = !empty($contact_array['email'] == $primary_contact_email) ? '1' : '0';

    //                 if(empty($contact_array['id']))
    //                 {    
    //                     // create insert array
    //                     $contact_array['supplier_id'] = $supplier_id;

    //                     $contact_array['created_by'] = $request->user()->id;

    //                     $contact_array['created_at'] = date('Y-m-d H:i:s');

    //                     $contact_array['updated_at'] = $contact_array['created_at'];

    //                     $sup_insert_contact[] = $contact_array;
    //                 }
    //                 else
    //                 {
    //                     // if id found in existing array, unset that id 
    //                     if (($key = array_search($contact_array['id'], $exist_contacts)) !== false) 
    //                     {
    //                         unset($exist_contacts[$key]);
    //                     }
                        
    //                     // create update array
    //                     $contact_array['supplier_id'] = $supplier_id;
                        
    //                     $contact_array['modified_by'] = $request->user()->id;

    //                     $contact_array['updated_at'] = date('Y-m-d H:i:s');

    //                     $sup_update_contact[] = $contact_array;
    //                 }    
    //             }
                
    //             // Bulk insert
    //             if(!empty($sup_insert_contact))
    //             {    
    //                 $sup_insert_contact = object_to_array($sup_insert_contact);
                    
    //                 $obj_contact->insert($sup_insert_contact);
    //             }

    //             // Batch update
    //             if(!empty($sup_update_contact))
    //             {
    //                 $sup_update_contact = object_to_array($sup_update_contact);
                    
    //                 Batch::update($obj_contact, $sup_update_contact, 'id');
    //             }    
    //         }    
            
    //         // if any existing product left delete those records
    //         if(!empty($exist_contacts))
    //         {
    //             $obj_contact->destroy($exist_contacts);
    //         }    

    //         if(!empty($supplier_id))
    //         {
    //             if(!empty($supplier_master['id']))
    //             {
    //                 return $this->sendResponse('Supplier has been updated successfully', 200);
    //             }   
    //             else
    //             {
    //                 return $this->sendResponse('Supplier has been created successfully', 200);
    //             } 
    //         }   
    //         else
    //         {
    //             return $this->sendError('Supplier does not created, please try again', 422);
    //         } 
    //     }
    //     catch (Exception $ex) {
            
    //         return $this->sendError($ex->getMessage(), 400);
    //    }  
    // }   

    /**
     * Remove the specified resource from storage.
     * @author : Shubham Dayma
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // check id and delete record(s)
        if(!empty($request->id))
        {   
            if(SupplierMaster::whereIn('id', $request->id)->delete()){
               SupplierContact::whereIn('supplier_id', $request->id)->delete(); 
                return $this->sendResponse('Record(s) has been deleted successfully', 200);
            }else{
                return $this->sendError('Record(s) did not deleted, please try again', 422);
            }
        }
        else
        {
            return $this->sendError('No record(s) found for delete, please try again', 422);
        }    
    }

    public function destory_contacts(Request $request)
    {
        try
        {

            if(!empty($request->ids))
            {    
                if(SupplierContact::destroy($request->ids))
                {
                    $supplier_contacts = SupplierContact::where('supplier_id', $request->supplier_id)->pluck('is_primary', 'id')->toArray();

                    $set_primary = true;
            
                    $first_id = "";

                    if(!empty($supplier_contacts))
                    {    
                        $first_id = array_key_first($supplier_contacts);
                        
                        foreach($supplier_contacts as $id => $is_primary)
                        {
                            if($is_primary == 1)
                            {
                                $set_primary = false;
                                break(1);
                            }    
                        } 
                    }   

                    if($set_primary == true && !empty($first_id))
                    {
                        SupplierContact::where('id', $first_id)
                                        ->update(array('is_primary' => 1));
                    }

                    return $this->sendResponse('Supplier contact(s) has been deleted successfully', 200);
                } 
                else
                {
                    return $this->sendError('Somthing went wrong, please try again', 422);
                }   
            }
        }
        catch(Exception $ex)
        {
            return $this->sendError($ex->getMessage(), 400);
        }
        
    }

    public function SendEmail(Request $request)
    {
        // check id and delete record(s)
        if(!empty($request->id))
        {   
            //get supplier primary contact data
            $supplier_contact_data=SupplierContact::whereIn('supplier_id', $request->id)->where('is_primary','1')->get(); 
            
            //get reference data           
            $reference_data = References::orderBy('id', 'desc')->take(3)->get();            
            foreach($supplier_contact_data as $supplier_data)
            {
                $supplier_name=$supplier_data->name; 
                $supplier_email=$supplier_data->email;  
                $emailData=array('toName'=>$supplier_name,'toEmail'=>$supplier_email,'subject'=>'Welcome To Poundshop','template'=>'emails.welcome_supplier','reference_data'=>$reference_data);
                $result=event(new SendMail($emailData)); // send mail to user for welcome
                
                //store in supplier reference welcome send mail
                $final_data=array();
                foreach($reference_data as $row)
                {
                    $final_data[]=array(
                        'supplier_id'=>$supplier_data->supplier_id,
                        'supplier_name'=>$row->supplier_name,
                        'contact_person'=>$row->contact_person,
                        'contact_no'=>$row->contact_no,
                        'contact_email'=>$row->contact_email,
                        'attachment_name'=>$row->attachment_name,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>$request->user()->id
                    );
                }

                $supplier_ref_model=new SupplierReferences;
                $data=$supplier_ref_model->insert($final_data);

                return $this->sendResponse('Mail send successfully to supplier contact', 200);

            }
        }
        else
        {
            return $this->sendError('Mail send failed, please try again', 422);
        }    
    }
    
    public function supplierContacts(\App\Http\Requests\Api\PO\SupplierContactsRequest $request){
        try{
            $suppliers=SupplierMaster::find($request->supplier_id);
            $data=view('components._supplier-contacts',compact('suppliers'))->render();
            return $this->sendResponse('supplier contacts', 200,$data);
        } catch (Exception $ex) {
            return $this->sendError('Bad Request Error', 400);
        }
    }

    public function save_default_contacts(CreateRequest $request)
    {
        try{
            
            $resp1 = $resp2 = 0;    

            if(!empty($request->supplier_id) && !empty($request->contact_id))
            {
                $resp1 = SupplierContact::where('supplier_id', $request->supplier_id)
                               ->where('id', '!=', $request->contact_id)
                               ->update(array('is_primary' => 0)); 

                $resp2 = SupplierContact::where('supplier_id', $request->supplier_id)
                               ->where('id', '=', $request->contact_id)
                               ->update(array('is_primary' => 1));

                if($resp1 == true && $resp2 == true)
                {
                    return $this->sendResponse('Contact is successfully set as primary', 200);
                }
                else
                {
                    return $this->sendError('Something went wrong', 422);
                }                   
            }    

        }
        catch (Exception $ex) {
            return $this->sendError('Bad Request Error', 400);
        }
    }
}