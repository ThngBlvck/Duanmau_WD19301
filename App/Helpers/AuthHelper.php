<?php

namespace App\Helpers;

use App\Models\User;

class AuthHelper
{
    public static function register($data)
    {
        $user = new User();
        // bat ton tai username

        $is_exist = $user -> getOneUserByUsername($data['username']);

        if( $is_exist){
            NotificationHelper::error('rexist_register','Tên Đăng nhập đã tồn tại');
            return false;

        }
        $result  = $user ->createUser($data);

        if( $result){
            NotificationHelper::success('register','Đăng Ký thàng công');
            return true;
        }

        NotificationHelper::error('register','Đăng Ký thất bại');
        return false;

    }
}
