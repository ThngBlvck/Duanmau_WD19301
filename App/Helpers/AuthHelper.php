<?php

namespace App\Helpers;

use App\Models\User; 

class AuthHelper{
    public static function register($data){
        
        $user= new User();
        // bắt lỗi tồn tại username
        $is_exist=$user->getOneUserByUsername($data['username']);
        if($is_exist){
            return false;
        }
        
        $result= $user->createUser($data);

        if($result){
            return true;
        }
        return false;
    }
}