<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Common\CreateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Imagine;
use Intervention\Image\ImageManagerStatic as Image;
use Gumlet\ImageResize;
use App\BookingQcChecklist;
use PDF;

class BookingQcController extends Controller
{
    function __construct()
    {
            CreateRequest::$roles_array = [
               
            ];
    }
    public function store(CreateRequest $request)
    {

        $qcBookObj=\App\BookingQcChecklist::where('booking_id',$request->book_id)->where('qc_list_id',$request->qc_id)->where('product_id',$request->product_id)->first();
        if($qcBookObj)
        {
            $qcPoint=\App\BookingQcCheckListPoint::where('qc_check_list_id',$qcBookObj->id)->where('qc_option_id',$request->qc_option_id)->first();
            if($qcPoint)
            {
                $qcPoint->comments=$request->comments;
                 if($request->file('image'.$request->qc_option_id))
                {
                    $qcPoint->image=$this->saveImage($request);
                }
                $qcPoint->modified_by=$request->user->id;
                $qcPoint->is_checked=(isset($request->is_checked))? $request->is_checked : 0;
                if($qcPoint->save())
                {
                   return $this->sendResponse("Record updated successfully", 200);
                }
                else
                {
                    return $this->sendResponse("Something went wrong", 200);
                }
            }
            else
            {
                $qcObj=new \App\BookingQcCheckListPoint;
                $qcObj->is_checked=(isset($request->is_checked))? $request->is_checked : 0;
                $qcObj->qc_check_list_id=$qcBookObj->id;
                $qcObj->qc_option_id=$request->qc_option_id;
                $qcObj->option_caption=$request->option_caption;
                $qcObj->comments=$request->comments;
                if($request->file('image'.$request->qc_option_id))
                {
                    $qcObj->image=$this->saveImage($request);
                }
                $qcObj->created_by=$request->user->id;
                $qcObj->modified_by=$request->user->id;
                if($qcObj->save())
                {
                    return $this->sendResponse("Record updated successfully", 200);
                }
                else
                {
                    return $this->sendResponse("Something went wrong", 200);
                }
            }
        }
        else
        {
            $qcBookObj=new \App\BookingQcChecklist;
            $qcBookObj->booking_id=$request->book_id;
            $qcBookObj->product_id=$request->product_id;
            $qcBookObj->qc_list_id=$request->qc_id;
            $qcBookObj->created_by=$request->user->id;
            $qcBookObj->modified_by=$request->user->id;
           
            if($qcBookObj->save())
            {

                $qcObj=new \App\BookingQcCheckListPoint;
                $qcObj->is_checked=(isset($request->is_checked))? $request->is_checked : 0;
                $qcObj->qc_check_list_id=$qcBookObj->id;
                $qcObj->qc_option_id=$request->qc_option_id;
                $qcObj->option_caption=$request->option_caption;
                $qcObj->comments=$request->comments;
                if($request->file('image'.$request->qc_option_id))
                {
                    $qcObj->image=$this->saveImage($request);
                }
                $qcObj->created_by=$request->user->id;
                $qcObj->modified_by=$request->user->id;
                if($qcObj->save())
                {
                    return $this->sendResponse("Record updated successfully", 200);
                }
                else
                {
                     return $this->sendResponse("Something went wrong", 200);
                }
            }
        }
    }
    function saveImage(Request $request)
    {
        $file=$request->file('image'.$request->qc_option_id);
    	if ($file) {
            if($request->qc_option_id)
        	{
        		$qcObj=\App\BookingQcCheckListPoint::find($request->qc_option_id);
        	}
           else
           {
           	$qcObj=new \App\BookingQcCheckListPoint;
           }
            $folder     = 'booking-qc-points';
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0777, true);
            }
            $uploadedFile =$file;
            $extension    = strtolower($uploadedFile->getClientOriginalExtension());
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $orientation = exif_read_data($uploadedFile);
            }
            $name = time() . '' . $uploadedFile->getClientOriginalName();
            $path = Storage::putFileAs(('booking-qc-points'), $uploadedFile, $name);
            if (!empty($path)) {
                $folder = 'booking-qc-points';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }
                $folder = 'booking-qc-points/thumbnail/';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }

                $thumbName1   = explode('/', $path);
                $thumbName    = $thumbName1[1];
                $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'booking-qc-points/' . $thumbName;

                $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'booking-qc-points/thumbnail/' . $thumbName;
                //   echo $thumbPath;exit;
                $image     = new ImageResize($originalPath);
                $image->resize(100, 100, true);
                $image->save($thumbPath);

                
                if (isset($bookingObj->image) && !empty($bookingObj->image)) {
                    Storage::delete($bookingObj->image);
                    $thumbName = explode('/', $bookingObj->image)[1];
                    Storage::delete('booking-qc-points/thumbnail/' . $thumbName);
                }
                return $path;
            }
        }
        else {
            return NULL;
        }
    }

    public function removeBookingQcCHecklistImage(Request $request)
    {
        try {
            $bookingObj=\App\BookingQcCheckListPoint::find($request->id);
            if (isset($bookingObj->image) && !empty($bookingObj->image)) {
                 Storage::delete($bookingObj->image);
                $thumbName = explode('/', $bookingObj->image)[1];
                //echo $thumbName;exit;
                Storage::delete('booking-qc-points/thumbnail/'.$thumbName);
            }
            if($bookingObj->update(['image'=>NULL]))
            {
                return $this->sendResponse("Image deleted successfully", 200);
               
            }
            else
            {
                return $this->sendResponse("Something went wrong", 200);
            }
        } 
        catch (Exception $e) {
            return $this->sendError(trans('messages.bad_request '), 400); 
        }
    }

    
}
