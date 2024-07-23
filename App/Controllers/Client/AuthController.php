<?php

namespace App\Controllers\Client;

use App\Helpers\AuthHelper;
use App\Models\User;
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
    // Thực hiện đăng ký
    public static function registerAction()
    {
        // bắt lỗi validate


        // lấy dữ liệu người dùng nhập vào
        $username=$_POST['username'];
        $password=$_POST['password'];
        $hash_password=password_hash($password,PASSWORD_DEFAULT);
        $email=$_POST['email'];
        $name=$_POST['name'];

        // đưa dữ liệu vào mảng, lưu ý "key" phải trùng tên cột trong cơ sở dữ liệu
        $data=[
            'name' => $name,
            'username' => $username,
            'password' => $hash_password,
            'email' => $email,

        ];

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