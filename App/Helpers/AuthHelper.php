<?php

namespace App\Helpers;

use App\Models\User;

class AuthHelper
{
    public static function register($data)
    {
        $user = new User();
        // bắt lỗi tồn tại username

        $is_exist = $user->getOneUserByUsername($data['username']);

        if ($is_exist) {
            NotificationHelper::error('rexist_register', 'Tên Đăng nhập đã tồn tại');
            return false;
        }
        $result  = $user->createUser($data);

        if ($result) {
            NotificationHelper::success('register', 'Đăng Ký thàng công');
            return true;
        }

        NotificationHelper::error('register', 'Đăng Ký thất bại');
        return false;
    }

    public static function login($data)
    {
        //kiem tra co ton tai username trong database hay khong => neu khong: thong bao, tra ve false
        $user = new User();
        // bắt lỗi tồn tại username

        $is_exist = $user->getOneUserByUsername($data['username']);

        if (!$is_exist) {
            NotificationHelper::error('rexist_username', 'Tên Đăng nhập không tồn tại');
            return false;
        }
        // ne co kt password co trung khong => neu khong: thong bao, tra ve false
        // password nguoi dung nhap:$data['password']
        // password trong database: $is_exit['password']

        if (!password_verify($data['password'], $is_exist['password'])) {
            NotificationHelper::error('password', 'Mật khẩu khing chính sác');
            return false;
        }

        //ne co kt status == 0 =>: thong bao, tra ve false
        if ($is_exist['status'] == 0) {
            NotificationHelper::error('status', 'Tài khoãn đã bị khoá');
            return false;
        }

        // ne co kiem tra remember => luu session/cookie => thong bao thanh cong tra ve true

        if ($data['remember']) {
            // lưu cookie
            self::updateCookie($is_exist['id']);
        } else {
            self::updateSession($is_exist['id']);
            //luu session
        }

        NotificationHelper::success('login', 'Đăng nhập thành công');
        
        return true;
    }

    public static function updateCookie(int $id)
    {
        $user = new User();
        $result = $user->getOneUser($id);
        if ($result) {
            // chuyển array thanh string json lưu vào trong cookie  user 
            $user_data = json_encode($result);

            // lưu cookie 
            setcookie('user', $user_data, time() + 3600 * 24 * 30 * 12, '/');

            $_SESSION['user'] = $result;
        }
    }
    public static function updateSession(int $id)
    {
        $user = new User();
        $result = $user->getOneUser($id);
        if ($result) {
           
            //lưu session
            $_SESSION['user'] = $result;
        }
    }

    public static function checkLogin(){
        if(isset($_COOKIE['user'])){
            $user = $_COOKIE['user'];
            $user_data = json_decode($user);
            $_SESSION['user']=(array) $user_data;
            return true;
        }
        if(isset($_SESSION['user'])){
            return true;
        }
        return false;
    }
}
