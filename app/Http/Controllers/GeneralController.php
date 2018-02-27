<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\City;
use App\User;
use App\Region;
use App\Department;
use App\StaffType;
use App\Designation;

class GeneralController extends Controller
{
    public function checkPermission($permission){
      $return=Auth::User()->hasPermission($permission);
      return $return;
    }
    public function index()
    {
      $permissions=array('city','region','department','staffType','designation');
      //$permissions=array('');
      // if($this->checkPermission(3)){
      //   array_push($permissions,'city');
      // }
      // if($this->checkPermission(4)){
      //   array_push($permissions,'region');
      // }
      // if($this->checkPermission(5)){
      //   array_push($permissions,'department');
      // }
      // if($this->checkPermission(6)){
      //   array_push($permissions,'staffType');
      // }
      // if($this->checkPermission(7)){
      //   array_push($permissions,'designation');
      // }
      return view('generalManagement',array('permissions'=>$permissions));
    }

    public function returnAll()
    {
        $city=City::all();
        $region=Region::all();
        $department=Department::all();
        $staffType=StaffType::all();
        $designation=Designation::all();
        $return=array('city'=>$city,'region'=>$region,'department'=>$department,'staffType'=>$staffType,'designation'=>$designation);
        return $return;
    }

    //City Managemnet
    public function cities()
    {
        return City::all();
    }

    public function createCity(Request $request)
    {
        $city=$request->input('city');
        try{
          $createdCity=City::create(['name'=>$city]);
        }catch(\Exception $e){
          $return=array('return'=>1,'data'=>$e);
          return $return;
        }
        $return=array('return'=>0,'data'=>$createdCity);
        return $return;
    }

    public function editCity(Request $request)
    {
      $id=$request->input('id');
      $city=$request->input('city');
      try{
        City::where('id',$id)->update(['name'=>$city]);
      }catch(\Exception $e){
        $return=array('return'=>1,'data'=>$e);
        return $return;
      }
      $return=array('return'=>0);
      return $return;
    }
    //City Managemnet

    //Region Managemnet
    public function regions()
    {
        return Region::all();
    }

    public function createRegion(Request $request)
    {
        $region=$request->input('region');
        try{
          $createdRegion=Region::create(['name'=>$region]);
        }catch(\Exception $e){
          $return=array('return'=>1,'data'=>$e);
          return $return;
        }
        $return=array('return'=>0,'data'=>$createdRegion);
        return $return;
    }

    public function editRegion(Request $request)
    {
      $id=$request->input('id');
      $region=$request->input('region');
      try{
        Region::where('id',$id)->update(['name'=>$region]);
      }catch(\Exception $e){
        $return=array('return'=>1,'data'=>$e);
        return $return;
      }
      $return=array('return'=>0);
      return $return;
    }
    //Region Managemnet

    //Department Managemnet
    public function departments()
    {
        return Department::all();
    }

    public function createDepartment(Request $request)
    {
        $department=$request->input('department');
        try{
          $createdDepartment=Department::create(['name'=>$department]);
        }catch(\Exception $e){
          $return=array('return'=>1,'data'=>$e);
          return $return;
        }
        $return=array('return'=>0,'data'=>$createdDepartment);
        return $return;
    }

    public function editDepartment(Request $request)
    {
      $id=$request->input('id');
      $department=$request->input('department');
      try{
        Department::where('id',$id)->update(['name'=>$department]);
      }catch(\Exception $e){
        $return=array('return'=>1,'data'=>$e);
        return $return;
      }
      $return=array('return'=>0);
      return $return;
    }
    //Department Managemnet

    //StaffType Managemnet
    public function staffType()
    {
        return StaffType::all();
    }

    public function createStaffType(Request $request)
    {
        $staffType=$request->input('staffType');
        try{
          $createdStaffType=StaffType::create(['type'=>$staffType]);
        }catch(\Exception $e){
          $return=array('return'=>1,'data'=>$e);
          return $return;
        }
        $return=array('return'=>0,'data'=>$createdStaffType);
        return $return;
    }

    public function editStaffType(Request $request)
    {
      $id=$request->input('id');
      $staffType=$request->input('staffType');
      try{
        StaffType::where('id',$id)->update(['type'=>$staffType]);
      }catch(\Exception $e){
        $return=array('return'=>1,'data'=>$e);
        return $return;
      }
      $return=array('return'=>0);
      return $return;
    }
    //StaffType Managemnet
    //Designation Managemnet
    public function designations()
    {
        return Designation::all();
    }

    public function createDesignation(Request $request)
    {
        $designation=$request->input('designation');
        try{
          $createdDesignation=Designation::create(['designation'=>$designation]);
        }catch(\Exception $e){
          $return=array('return'=>1,'data'=>$e);
          return $return;
        }
        $return=array('return'=>0,'data'=>$createdDesignation);
        return $return;
    }

    public function editDesignation(Request $request)
    {
      $id=$request->input('id');
      $designation=$request->input('designation');
      try{
        Designation::where('id',$id)->update(['designation'=>$designation]);
      }catch(\Exception $e){
        $return=array('return'=>1,'data'=>$e);
        return $return;
      }
      $return=array('return'=>0);
      return $return;
    }
    //Designation Managemnet
}
