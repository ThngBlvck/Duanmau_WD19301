<?php

namespace App\Controllers\Client;

use App\Helpers\AuthHelper;
use App\Helpers\NotificationHelper;
use App\Views\Client\Components\Notification;
use App\Views\Client\Layouts\Footer;
use App\Views\Client\Layouts\Header;
use App\Views\Client\Pages\Auth\Login;
use App\Views\Client\Pages\Auth\Register;

class AuthController{

    // hàm hiển thị giao diện form regiter
    public static function register(){  
        // Hiển thị header
        Header::render();


        // hien thi thong bao
        Notification::render();

        //huy session thong bao
        NotificationHelper::unset();
        // Hiển thị form đăng ký
        Register::render();
        // Hiển thị footer
        Footer::render();
    }
    public static function registerAction(){  
        

        $result = AuthHelper::register($data);
        if($result){
            header('location: /');
        }else{
            header('location: /register');
        }
    }
    public static function login(){
        // Hiển thị header
        Header::render();
        // Hiển thị form đăng nhập
        Login::render();
        // Hiển thị footer
        Footer::render();
    }
}