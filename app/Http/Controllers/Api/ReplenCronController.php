<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Products;
use App\Replen;
use App\LocationAssign;
use App\WareHouse;
use App\LocationAssignTrans;
use App\Cron;
use App\Setting;
class ReplenCronController extends Controller
{
	//replen qty product wise in warehouse
    public function processReplan(Request $request,$product_id='')
    {
    	if(empty($product_id))
    	{
    		$product_id=isset($request->id)?$request->id:'';
    	}
    	
    	if(!empty($product_id))
    	{
    		$warehouse_id=$this->get_warehouse_id();
    		$return_data=$this->replen_process($product_id,$warehouse_id);
    		return $return_data;
    	} 
    	else
    	{
    		//get all warehouse and run for all warehouse
    		$all_warehouse=$this->get_all_warehouse();    		
    		if(!empty($all_warehouse) && !empty($all_warehouse->toArray()))
    		{
    			foreach($all_warehouse as $row)
    			{
					$this->CRON_NAME = 'CRON_' . time();   // CRON NAME
					$this->CRON_TITLE = 'REPLEN_CRON';
    				//check if cron already run for the warehouse in last 30 min
    				$cron_data=Cron::select('id','start_time','end_time','is_cron_failed')->where('warehouse_id',$row->id)->orderBy('id', 'desc')->first();

    				if(!empty($cron_data) && !empty($cron_data->toArray()))
    				{
    					$start_time=$cron_data->start_time;
						$termination_time=date("Y-m-d H:i:s", strtotime($start_time . "+30 minutes"));
						$current_date_time=date("Y-m-d H:i:s", strtotime("now"));

    					//check if there is start time and end time there
    					if(!empty($cron_data->start_time) && !empty($cron_data->end_time))
    					{    						
    						if($termination_time<$current_date_time) //if termination date time passed away
    						{
	    						//cron start code
								$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, '',$row->id,'');	
	    						$return_data=$this->replen_process('',$row->id);		

	    						if($return_data==1 && !empty($cron_id))
	    						{
	    							$this->cron_start_end_update($cron_id);
	    						}
	    					}
	    					
    					}
    					else if(!empty($cron_data->start_time) && !empty($cron_data->is_cron_failed))
    					{
    						if($termination_time<$current_date_time) //if termination date time passed away
    						{
	    						//cron start code
								$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, '',$row->id,'');	
	    						$return_data=$this->replen_process('',$row->id);		

	    						if($return_data==1 && !empty($cron_id))
	    						{
	    							$this->cron_start_end_update($cron_id);
	    						}
	    					}
    					}
    					else if(!empty($cron_data->start_time) && empty($cron_data->end_time) && empty($cron_data->is_cron_failed))
    					{
    						if($termination_time<$current_date_time) //if termination date time passed away
    						{
    							//update the entry and start the new cron    							
    							$this->cron_start_end_update($cron_data->id,'','','','','1');	

    							//cron start code
								$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, '',$row->id,'');	
	    						$return_data=$this->replen_process('',$row->id);		

	    						if($return_data==1 && !empty($cron_id))
	    						{
	    							$this->cron_start_end_update($cron_id);
	    						}
    						}
    					}
    					else if(empty($cron_data->start_time) && empty($cron_data->end_time))
    					{
    						//update cron entry
    						$cron_id=$cron_data->id;
							$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, '',$row->id,'',date('Y-m-d H:i:s'));	
    						$return_data=$this->replen_process('',$row->id);		

    						if($return_data==1 && !empty($cron_id))
    						{
    							$this->cron_start_end_update($cron_id);
    						}
    					}
    				}    				
    				else
    				{
						//cron start code
						$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, '',$row->id,'');	
						$return_data=$this->replen_process('',$row->id);								
						if($return_data==1 && !empty($cron_id))
						{
							$this->cron_start_end_update($cron_id);
						}    					
    				}
    			}
    		}
    	}   	   	
    }

    public function cron_start_end_update($cron_id=NULL,$cron_type='', $cron_name='', $store_id='',$warehouse_id='',$is_cron_failed='',$start_time='')
    {
        try
        {
            if(!empty($cron_id))
            {
                $cron_up = Cron::find($cron_id);
                if(!empty($is_cron_failed))
                {
                	$cron_up->is_cron_failed='1';
                }
                else
                {
                	$cron_up->end_time = date('Y-m-d H:i:s');
                }

                if(!empty($start_time))
                {
                	$cron_up->start_time = $start_time;	
                }
                
                $cron_up->save();
            }
            else
            {
                $newc_cron=new Cron;
                $cron_data=array(
                	'warehouse_id'=>$warehouse_id,
                    'store_id' => 0,
                    'cron_name' => $cron_name,
                    'cron_type' => $cron_type,
                    'start_time' => date('Y-m-d H:i:s')
                );          
                //insert cron data call
                $insertedId=$newc_cron->insertGetId($cron_data);                
                return $insertedId;//return cron id
            }
        }
        catch(Exception $ex)
        {
            return $this->sendError($ex->getMessage(), 400);
        }       
    }


    public function replen_process($product_id='',$warehouse_id)
    {
    	$product= new Products;
    	$product_list=$product->get_replen_counter_data($product_id,$warehouse_id);    	    	    	
    	//dd($product_list);
    	$today_date=date('Y-m-d'); 

    	$default_season_about_to_start=0;
    	$settingObj= new Setting();
    	$default_season_about_to_start_arr=$settingObj->getColumnVal('default_season_start');
    	if(!empty($default_season_about_to_start_arr) && !empty($default_season_about_to_start_arr->toArray()))
    	{
    		$default_season_about_to_start=!empty($default_season_about_to_start_arr[0]->column_val)?$default_season_about_to_start_arr[0]->column_val:0;
    	}

    	$default_promotion_about_to_start=0;
    	$default_promotion_about_to_start_arr=$settingObj->getColumnVal('default_promotion_start');    	
    	if(!empty($default_promotion_about_to_start_arr) && !empty($default_promotion_about_to_start_arr->toArray()))
    	{
    		$default_promotion_about_to_start=!empty($default_promotion_about_to_start_arr[0]->column_val)?$default_promotion_about_to_start_arr[0]->column_val:0;
    	}
    	
		if(!empty($product_list) && !empty($product_list->toArray()))
		{
			foreach($product_list as $row)
			{		
				$movable_qty=0;
				$qty_move_require=1;
				$product_id=$row->id;
				$ros=$row->ros;
				$total_in_pick=$row->total_in_pick;
				$total_in_bulk=$row->total_in_bulk;
				$total_in_warehouse=$row->total_in_warehouse;
				$total_reserved=$row->total_reserved;
				$qty_stock_hold=$row->qty_stock_hold;
				$pick_location_array=array();
				$bulk_location_array=array();
				$all_pick_location_aisle=array();
				$all_bulk_location_aisle=array();
				$default_location_bbd=array();
				$default_location_lowest_qty=array();
				$lowest_bbd='';
				$priority=0;
				$pick_count=0;
				$bulk_count=0;
				$status=0;
				$biggest_pick_bbd='';

				if(!empty($row->all_pick_assign_id))
				{
					$total_asign_id_ar=explode(',',$row->all_pick_assign_id);
					$total_pick_qty_ar=explode(',',$row->all_pick_aisle_qty);
					$pick_ar=array();
					if(!empty($total_asign_id_ar))
					{
						$i=0;
						foreach($total_asign_id_ar as $row_n)
						{
							if(!in_array($row_n,$pick_ar))
							{
								array_push($pick_ar,$row_n);
								$pick_count+=!empty($total_pick_qty_ar[$i])?$total_pick_qty_ar[$i]:0;
							}
							$i++;
						}
					}
					$total_in_pick=$pick_count;					
				}

				if(!empty($row->all_bulk_assign_id))
				{
					$total_bulk_id_ar=explode(',',$row->all_bulk_assign_id);
					$total_bulk_qty_ar=explode(',',$row->all_bulk_aisle_qty);					
					$bulk_ar=array();					
					if(!empty($total_bulk_id_ar))
					{
						$i=0;
						foreach($total_bulk_id_ar as $row_n)
						{
							if(!in_array($row_n,$bulk_ar))
							{
								array_push($bulk_ar,$row_n);
								$bulk_count+=!empty($total_bulk_qty_ar[$i])?$total_bulk_qty_ar[$i]:0;
							}
							$i++;
						}
					}
					$total_in_bulk=$bulk_count;					
				}

				//need to dispatch today
				if(isset($row->qty_dispatch_today))
				{
					$dispatch_today=$row->qty_dispatch_today;
				}
				else
				{
					//as of now we are taking this from location table but in future we need to take this from product order master
					$dispatch_today=$total_reserved;	
				}				 

				//if dispatch today it means we need to add those qty as well
				if(!empty($dispatch_today)) 
				{
					$movable_qty=$qty_stock_hold+$dispatch_today;
				}
				else
				{
					$movable_qty=$qty_stock_hold;	
				}	

				//check if there is require of moving any qty today or not by comparing the difference between the pick location qty and qty require for movable if there is negative value then we don't need to move anything for that product

				if($movable_qty<$total_in_pick)
				{
					$qty_move_require=0;					
				}
				else
				{
					$movable_qty=$movable_qty-$total_in_pick;
				}
								

				$store_data=array();
				$store_data['product_id']=$product_id;
				$store_data['warehouse_id']=$warehouse_id;

				//check if there is qty in warehouse in stock hold and in bulk
				if(!empty($total_in_warehouse) && !empty($qty_stock_hold) && !empty($total_in_bulk) && !empty($qty_move_require))
				{
					$status='7';//by default day stock hodling status
					//get location,bbd,loc_assign
					if(!empty($row->all_pick_assign_id))
					{
						$all_pick_assign_array=explode(',',$row->all_pick_assign_id);
						$all_pick_location_array=explode(',',$row->all_pick_aisle_id);
						$all_pick_aisle_array=explode(',',$row->all_pick_aisle);
						$all_pick_bbd_array=explode(',',$row->all_pick_bbd);
						$all_pick_qty_array=explode(',',$row->all_pick_aisle_qty);	

						$pick_exist_id_data_array=array();
						if(!empty($all_pick_assign_array))
						{
							$j=0;
							foreach($all_pick_assign_array as $arr)
							{
								if(!in_array($arr,$pick_exist_id_data_array))
								{
									$pick_exist_id_data_array[]=$arr;
									$pick_location_array[$j]['assign_id']=$arr;
									$pick_location_array[$j]['location_id']=$all_pick_location_array[$j];
									$pick_location_array[$j]['asile']=$all_pick_aisle_array[$j];
									$pick_location_array[$j]['avail_qty']=$all_pick_qty_array[$j];

									//check unique pick aisle
									if(!in_array($all_pick_aisle_array[$j],$all_pick_location_aisle))
									{
										array_push($all_pick_location_aisle,$all_pick_aisle_array[$j]);
									}

									if(!empty($all_pick_bbd_array))
									{
										$i=0;
										foreach($all_pick_bbd_array as $newr)
										{
											$new_arr=explode('||',$newr);							
											if(!empty($newr) && $arr==$new_arr[0])
											{
												$pick_location_array[$j]['detail'][$i]['loc_ass_id']=$new_arr[0];
												// $pick_location_array[$j]['detail'][$i]['case_detail_id']=$new_arr[1];
												$pick_location_array[$j]['detail'][$i]['qty']=$new_arr[1];
												$pick_location_array[$j]['detail'][$i]['best_before_date']=$new_arr[2];

												if(!empty($biggest_pick_bbd))
												{
													if($biggest_pick_bbd<$new_arr[2])
													{
														$biggest_pick_bbd=$new_arr[2];		
													}
												}
												else
												{
													$biggest_pick_bbd=$new_arr[2];
												}
											}
											$i++;
										}
									}
								}
								$j++;
							}
						}						
					}					
					
					//get location,bbd,loc_assign
					if(!empty($row->all_bulk_assign_id))
					{
						$all_bulk_assign_array=explode(',',$row->all_bulk_assign_id);
						$all_bulk_location_array=explode(',',$row->all_bulk_aisle_id);
						$all_bulk_aisle_array=explode(',',$row->all_bulk_aisle);
						$all_bulk_bbd_array=explode(',',$row->all_bulk_bbd);		
						$all_bulk_qty_array=explode(',',$row->all_bulk_aisle_qty);					
						$bulk_exist_id_data_array=array();
						
						if(!empty($all_bulk_assign_array))
						{
							$j=0;
							$different_aisle_exist=0;
							foreach($all_bulk_assign_array as $arr)
							{
								if(!in_array($arr,$bulk_exist_id_data_array))
								{
									$bulk_exist_id_data_array[]=$arr;
									$bulk_location_array[$j]['assign_id']=$arr;
									$bulk_location_array[$j]['location_id']=$all_bulk_location_array[$j];
									$bulk_location_array[$j]['asile']=$all_bulk_aisle_array[$j];
									$bulk_location_array[$j]['avail_qty']=!empty($all_bulk_qty_array[$j])?$all_bulk_qty_array[$j]:0;

									//get location from bbd then smallest qty and then different aisle
									if(!empty($all_bulk_bbd_array))
									{
										$i=0;
										foreach($all_bulk_bbd_array as $newr)
										{
											$new_arr=explode('||',$newr);							
											if(!empty($newr) && $arr==$new_arr[0])
											{
												$bulk_location_array[$j]['detail'][$i]['loc_ass_id']=$new_arr[0];
												// $bulk_location_array[$j]['detail'][$i]['case_detail_id']=$new_arr[1];
												$bulk_location_array[$j]['detail'][$i]['qty']=!empty($new_arr[1])?$new_arr[1]:0;
												$bulk_location_array[$j]['detail'][$i]['best_before_date']=$new_arr[2];
												
												if($today_date<=$new_arr[2])
												{
													if(!empty($default_location_bbd))
													{
														if($default_location_bbd[0]['best_before_date']>$new_arr[2])
														{
															unset($default_location_bbd);
															$default_location_bbd[0]['location_id']=$all_bulk_location_array[$j];
															$default_location_bbd[0]['assign_id']=$arr;
															$default_location_bbd[0]['asile']=$all_bulk_aisle_array[$j];
															$default_location_bbd[0]['avail_qty']=!empty($all_bulk_qty_array[$j])?$all_bulk_qty_array[$j]:0;
															$default_location_bbd[0]['best_before_date']=$new_arr[2];	
														}
														else if($default_location_bbd[0]['best_before_date']==$new_arr[2])
														{
															$counter=count($default_location_bbd);
															$default_location_bbd[$counter]['location_id']=$all_bulk_location_array[$j];			
															$default_location_bbd[$counter]['assign_id']=$arr;
															$default_location_bbd[$counter]['asile']=$all_bulk_aisle_array[$j];
															$default_location_bbd[$counter]['avail_qty']=!empty($all_bulk_qty_array[$j])?$all_bulk_qty_array[$j]:0;
															$default_location_bbd[$counter]['best_before_date']=$new_arr[2];
														}																	
													}
													else
													{
														$default_location_bbd[0]['location_id']=$all_bulk_location_array[$j];
														$default_location_bbd[0]['assign_id']=$arr;
														$default_location_bbd[0]['asile']=$all_bulk_aisle_array[$j];
														$default_location_bbd[0]['avail_qty']=!empty($all_bulk_qty_array[$j])?$all_bulk_qty_array[$j]:0;
														$default_location_bbd[0]['best_before_date']=$new_arr[2];
													}
												}
											}
											$i++;
										}
									}

									//get default location from qty Basis and different aisle
									//$default_location_bbd=array();
									if(empty($default_location_bbd))
									{										
										if(!empty($default_location_lowest_qty))
										{		
											if(!in_array($all_bulk_aisle_array[$j],$all_pick_location_aisle))
											{
												if($default_location_lowest_qty['avail_qty']>$all_bulk_qty_array[$j])
												{
													$default_location_lowest_qty['location_id']=$all_bulk_location_array[$j];
													$default_location_lowest_qty['assign_id']=$arr;
													$default_location_lowest_qty['asile']=$all_bulk_aisle_array[$j];
													$default_location_lowest_qty['avail_qty']=$all_bulk_qty_array[$j];	
													$different_aisle_exist=1;				
												}
												else if(empty($different_aisle_exist))
												{
													$default_location_lowest_qty['location_id']=$all_bulk_location_array[$j];
													$default_location_lowest_qty['assign_id']=$arr;
													$default_location_lowest_qty['asile']=$all_bulk_aisle_array[$j];
													$default_location_lowest_qty['avail_qty']=$all_bulk_qty_array[$j];	
													$different_aisle_exist=1;	
												}
											}
											else if(empty($different_aisle_exist))
											{
												$default_location_lowest_qty['location_id']=$all_bulk_location_array[$j];
												$default_location_lowest_qty['assign_id']=$arr;
												$default_location_lowest_qty['asile']=$all_bulk_aisle_array[$j];
												$default_location_lowest_qty['avail_qty']=$all_bulk_qty_array[$j];		
											}											
										}
										else 
										{
											if(!in_array($all_bulk_aisle_array[$j],$all_pick_location_aisle))
											{
												$default_location_lowest_qty['location_id']=$all_bulk_location_array[$j];
												$default_location_lowest_qty['assign_id']=$arr;
												$default_location_lowest_qty['asile']=$all_bulk_aisle_array[$j];
												$default_location_lowest_qty['avail_qty']=$all_bulk_qty_array[$j];	
												$different_aisle_exist=1;							
											}
											else
											{
												$default_location_lowest_qty['location_id']=$all_bulk_location_array[$j];
												$default_location_lowest_qty['assign_id']=$arr;
												$default_location_lowest_qty['asile']=$all_bulk_aisle_array[$j];
												$default_location_lowest_qty['avail_qty']=$all_bulk_qty_array[$j];	
											}				
										}
									}
								}
								$j++;
							}
						}						
					}					

					if(!empty($default_location_bbd))
					{
						$default_location_lowest_qty=array();
						$different_aisle_exist=0;
						foreach($default_location_bbd as $row_de)
						{
							if(!empty($default_location_lowest_qty))
							{
								if(!in_array($row_de['asile'],$all_pick_location_aisle))
								{
									if($default_location_lowest_qty['avail_qty']>$row_de['avail_qty'])
									{
										$counter=count($default_location_lowest_qty);
										$default_location_lowest_qty['location_id']=$row_de['location_id'];
										$default_location_lowest_qty['assign_id']=$row_de['assign_id'];
										$default_location_lowest_qty['asile']=$row_de['asile'];
										$default_location_lowest_qty['avail_qty']=$row_de['avail_qty'];
										$different_aisle_exist=1;	
									}
									else if(empty($different_aisle_exist))
									{
										$counter=count($default_location_lowest_qty);
										$default_location_lowest_qty['location_id']=$row_de['location_id'];
										$default_location_lowest_qty['assign_id']=$row_de['assign_id'];
										$default_location_lowest_qty['asile']=$row_de['asile'];
										$default_location_lowest_qty['avail_qty']=$row_de['avail_qty'];
										$different_aisle_exist=1;	
									}
								}
								else if(empty($different_aisle_exist))
								{
									$default_location_lowest_qty['location_id']=$row_de['location_id'];
									$default_location_lowest_qty['assign_id']=$row_de['assign_id'];
									$default_location_lowest_qty['asile']=$row_de['asile'];
									$default_location_lowest_qty['avail_qty']=$row_de['avail_qty'];	
								}
							}
							else 
							{
								if(!in_array($row_de['asile'],$all_pick_location_aisle))
								{
									$default_location_lowest_qty['location_id']=$row_de['location_id'];
									$default_location_lowest_qty['assign_id']=$row_de['assign_id'];
									$default_location_lowest_qty['asile']=$row_de['asile'];
									$default_location_lowest_qty['avail_qty']=$row_de['avail_qty'];	
									$different_aisle_exist=1;	
								}
								else
								{
									$default_location_lowest_qty['location_id']=$row_de['location_id'];
									$default_location_lowest_qty['assign_id']=$row_de['assign_id'];
									$default_location_lowest_qty['asile']=$row_de['asile'];
									$default_location_lowest_qty['avail_qty']=$row_de['avail_qty'];
								}
							}
						}
					}
					
					//store default location
					if(!empty($default_location_lowest_qty))
					{
						$store_data['default_location']=$default_location_lowest_qty['location_id'];
						$store_data['replan_qty']=$default_location_lowest_qty['avail_qty'];	

						//get cases section
						$loc_assign= new LocationAssign;
						$cases_details_array=$loc_assign->get_replen_cases_data($product_id,$warehouse_id,$default_location_lowest_qty['location_id']);
						$total_case_qty=0;
						$all_cases=array();
						$total_qty=0;
						if(!empty($cases_details_array) && !empty($cases_details_array->toArray()))
						{
							$total_qty=count($cases_details_array->toArray());
							$cases_details_array_data=$cases_details_array->toArray();
							
							if($total_qty==1)
							{
								$all_cases[0]=array(
									'qty_per_box'=>$cases_details_array_data[0]['qty_per_box'],
									'total_boxes'=>$cases_details_array_data[0]['total_boxes'],
									'qty'=>$cases_details_array_data[0]['qty'],
								);	
							}
							else
							{
								foreach($cases_details_array as $row_a)
								{
									$total_case_qty+=$row_a['qty'];
									$all_cases[$row_a['qty_per_box']]=array(
										'qty_per_box'=>$row_a['qty_per_box'],
										'total_boxes'=>$row_a['total_boxes'],
										'qty'=>$row_a['qty'],
									);
								}
								arsort($all_cases);
							}
						}

						//handle 2 primary case
						$max_qty_move=$movable_qty;
						$final_qty=0;			
						
						//check if only case bulk location available
						if($total_qty==1 && !empty($max_qty_move))
						{
							$loc_qty=$all_cases[0]['qty'];
							if(!empty($max_qty_move) && !empty($all_cases[0]['qty_per_box']) && $max_qty_move%$all_cases[0]['qty_per_box']==0) //complete devide
							{								
								//if bulk have greater value then required
								if($max_qty_move<=$total_in_bulk) //Scenario 1
								{
									$final_qty=$max_qty_move;
								}
								else
								{
									//if bulk has less value then required
									$final_qty=$total_in_bulk; //Scenario 9
								}
							}
							else 
							{
								if($loc_qty>$max_qty_move) //check if default location have greater then required
								{
									$multipler=$max_qty_move/$all_cases[0]['qty_per_box'];	
									$final_mult=(int) floor($multipler); //round down
									if(!empty($final_mult))
									{
										//Scenario 2
										$final_qty=$final_mult*$all_cases[0]['qty_per_box'];
									}
									else
									{
										//Scenario 8
										$final_mult=1;//default taking atleast one
										$final_qty=$final_mult*$all_cases[0]['qty_per_box'];
									}
								}
								else if($max_qty_move<=$total_in_bulk) //if loc has less but bulk have
								{
									$final_qty=$max_qty_move;//Scenario 3
								}
								else if($max_qty_move>=$total_in_bulk) //if bulk have less
								{
									$final_qty=$total_in_bulk;//Scenario 4
								}
							}
						}
						else if(!empty($max_qty_move))
						{
							if($max_qty_move>=$total_case_qty && $max_qty_move<=$total_in_bulk)
							{
								$final_qty=$max_qty_move;//Scenario 6					
							}
							else if($max_qty_move>=$total_case_qty && $max_qty_move>=$total_in_bulk)
							{
								$final_qty=$total_in_bulk;	//Scenario 7		
							}							
						}												
						
						//maximum qty to move with multiple cases		
						if(empty($final_qty) && !empty($all_cases) && $max_qty_move<=$total_case_qty)
						{
							$counter_start=0;														
							foreach($all_cases as $row_ns)
							{
								if($max_qty_move<=$row_ns['qty'] && empty($counter_start))
								{
									$multiplier=$max_qty_move/$row_ns['qty_per_box'];
									$final_mult=(int) floor($multiplier); //round down
									$final_qty=$final_mult*$row_ns['qty_per_box'];//Scenario 10
									break;
								}
								else
								{
									//Scenario 5
									if(empty($final_qty))
									{
										$final_qty=$row_ns['qty'];
									}
									else
									{
										if($final_qty<$max_qty_move)
										{
											$pending_qty=$max_qty_move-$final_qty;
											
											$multiplier=$pending_qty/$row_ns['qty_per_box'];
											$final_mult=(int) floor($multiplier); //round down
											
											$qty_teq=$final_mult*$row_ns['qty_per_box'];
											$final_qty+=$qty_teq;	
											
										}
									}
									
									$counter_start=1;

									if($final_qty==$max_qty_move)
									{
										break;
									}
								}
							}
						}

						if(!empty($final_qty))
						{
							$store_data['replan_qty']=$final_qty;	//store final replan qty
						}
						
						/*********************** Priority Logic**********************/

						//set priority
						$one_day_stock_req=$ros*1; //1 day stock holding
						$two_day_stock_req=$ros*2;
						$dispatch_logic_applied=0;

						///Order Dispatch Logic for Priority
						$current_date=date('Y-m-d');
						$next_season_date=date('Y-m-d', strtotime($current_date. ' + '.$default_season_about_to_start.' days'));
						$next_season_date=date('Y-m-d', strtotime($next_season_date));
						$current_date=date('Y-m-d', strtotime($current_date));
						$seasonal_start_date='';
						$seasonal_end_date='';
						$is_seasonal_and_between_date='';

						if(!empty($row->seasonal_from_date))
						{
							$seasonal_start_date = date('Y-m-d', strtotime($row->seasonal_from_date));
						}

						if(!empty($row->seasonal_to_date))
						{
							$seasonal_end_date = date('Y-m-d', strtotime($row->seasonal_to_date));
						}						

						///Seasonal & Non Seasonal Product Logic
						if(empty($dispatch_logic_applied))
						{
							//Rule 7
							if(empty($row->is_seasonal) || (!empty($row->is_seasonal) && ((($current_date >= $seasonal_start_date) && ($current_date <= $seasonal_end_date)) || ($next_season_date==$seasonal_start_date))))
							{
								//to check if the seasonal product exist between the season is start
								$is_seasonal_and_between_date=1;
								if(empty($total_in_pick) || $total_in_pick==0) 
								{
									//rule 8
									$priority=8;//priority3
								}
								else if(!empty($total_in_pick) && $one_day_stock_req>$total_in_pick) 
								{
									//rule 9
									$priority=8;//priority3	
								}
								else if(!empty($total_in_pick) && (($total_in_pick >= $two_day_stock_req) && ($total_in_pick <= $qty_stock_hold)))
								{
									//rule 10
									$priority=12;//priority5
								}
								else if(($qty_stock_hold==$total_in_pick) || $total_in_pick>$qty_stock_hold)
								{
									//rule 11
									$priority=0;	
								}	

								//manage status 
								if(!empty($row->is_seasonal) && empty($priority) && empty($dispatch_logic_applied))
								{
									$status=6;//priority6
								}							
							}							

							//rule 12
							if(!empty($row->is_seasonal) && empty($is_seasonal_and_between_date))
							{
								$priority=0;
							}
						}

						if(!empty($row->is_promotional))
						{
							$current_date_time=date('Y-m-d H:i:s');
					    	$next_season_date=strtotime($current_date_time. ' + 1 Hour');
					    	$next_season_day_time=strtotime($current_date_time. ' + '.$default_promotion_about_to_start.' days');
					    	$current_date_time=strtotime($current_date_time);		
					    	$promotion_start_at=strtotime($row->promotion_start_at);

							if((($promotion_start_at >= $current_date_time) && ($promotion_start_at <= $next_season_date)) && $one_day_stock_req>=$total_in_pick)
							{
								$priority=4;//priority1
							}
							else if((($promotion_start_at >= $current_date_time) && ($promotion_start_at <= $next_season_day_time)) && $one_day_stock_req>=$total_in_pick)
							{
								$priority=6;//priority2	
							}

							//manage status 
							if(!empty($row->is_promotional) && !empty($priority))
							{
								$status=5;
							}
						}	

						//ulimate case for dated product
						if(!empty($biggest_pick_bbd))
						{
							//check in entire warehouse that the product have any qty in bulk location which has lesser date of bbd							
							$locAssignObj= new LocationAssign;
							$bul_location_data=$locAssignObj->get_replen_bulk_data($product_id,$warehouse_id,$biggest_pick_bbd);
							$upsqty=0;
							if(!empty($bul_location_data) && !empty($bul_location_data->toArray()))
							{
								foreach($bul_location_data as $jsroq)
								{
									$upsqty+=$jsroq->qty;
								}
							}
							
							//if bulk qty is greater then the replen qty then replace all the thing
							if(!empty($upsqty) && ($upsqty>$store_data['replan_qty']))
							{
								$store_data['replan_qty']=	$upsqty; //overright qty
								$priority=6;//priority 2
								$status=3;//short dated
							}
							else if($upsqty==$store_data['replan_qty'])
							{
								$priority=6;//priority 2
								$status=3;//short dated
							}							
						}
						
						$store_data['priority']=$priority;//No replen this time	
						$store_data['replen_status']=$status;//No replen this time
						$store_data['cron_replan_priority']=$priority;//No replen this time	
						$store_data['cron_replan_qty']=$store_data['replan_qty'];//No replen this time
						$store_data['status']='1';//No replen this time						
						//store the replen data
						$this->store_replen_data($store_data);		
					}
					else
					{
						$store_data['priority']=0;
						$store_data['replen_status']=0;
						$store_data['default_location']='0';
						$store_data['replan_qty']='0';	
						$store_data['cron_replan_priority']=0;//No replen this time	
						$store_data['cron_replan_qty']=0;//No replen this time
						$store_data['status']='0';//No replen this time						
						$this->store_replen_data($store_data);		
					}
				}
				else
				{
					$store_data['priority']=0;
					$store_data['replen_status']=0;
					$store_data['default_location']='0';
					$store_data['replan_qty']='0';	
					$store_data['cron_replan_priority']=0;//No replen this time	
					$store_data['cron_replan_qty']=0;//No replen this time
					$store_data['status']='0';//No replen this time		
					$this->store_replen_data($store_data);	
				}
			}			
		}
		return 1; 
    }

    public function get_warehouse_id()
    {
    	$default_ware=0;
        $warehouse_obj=new WareHouse;
        $default_warehouse_data=$warehouse_obj->select('id')->where('is_default',1)->get();
        if(!empty($default_warehouse_data) && !empty($default_warehouse_data->toArray()))
        {
            $default_ware=isset($default_warehouse_data[0]->id)?$default_warehouse_data[0]->id:'';
        }

        return $default_ware;
    }

    public function get_all_warehouse()
    {
        $warehouse_obj=new WareHouse;
        $default_warehouse_data=$warehouse_obj->select('id')->get();
        return $default_warehouse_data;
    }

    //store replan data
    public function store_replen_data($store_data)
    {
    	if(!empty($store_data))
    	{
    		$product_id=$store_data['product_id'];
    		$warehouse_id=$store_data['warehouse_id'];

    		//check if product entry is already exist or not
    		$replen_data=Replen::where('product_id',$product_id)->where('warehouse_id',$warehouse_id)->get();
    		if(!empty($replen_data) && !empty($replen_data->toArray()))
    		{
    			//update case
    			$replen_id=$replen_data[0]->id;
    			Replen::where('id', $replen_id)->update($store_data);
    		}
    		else 
    		{
    			//insert case    			    			
    			$Replen_c=new Replen;
    			$Replen_c->insert($store_data); 
    		}    		
    	}
    }    
}