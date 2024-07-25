<?php

namespace App\Controllers\Client;

use App\Helpers\AuthHelper;

use App\Helpers\NotificationHelper;
use App\Validations\AuthValidation;
// use App\Views\Admin\Pages\Category\Edit;
use App\Views\Client\Components\Notification;
use App\Views\Client\Layouts\Footer;
use App\Views\Client\Layouts\Header;
use App\Views\Client\Pages\Auth\Login;
use App\Views\Client\Pages\Auth\Register;
use App\Views\Client\Pages\Auth\Edit;

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
    //    bắt lỗi validate
    // Kiểm tra thỏa mãn không?
    // nếu có: tiếp tục chạy lệnh ở dưới
    // nếu không thỏa (lỗi): thông báo và chuyển về trang đăng ký

    $is_valid=AuthValidation::register();

    if(!$is_valid){
        NotificationHelper::error('register_valid', 'Đăng ký thất bại');
        header('location: /register');
        exit();
    }

        // Lấy dữ liệu người dùng nhập
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hash_password = password_hash($password,PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $name = $_POST['name'];

        // đưa dữ liệu vào mảng, lưu ý "key" trùng với tên cột trong cơ sở dữ liệu
        $data =[
            'username' => $username,
            'password' => $hash_password,
            'email' => $email,
            'name' => $name,
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
        Notification::render();
        NotificationHelper::unset();
        // Hiển thị form đăng nhập
        Login::render();
        // Hiển thị footer
        Footer::render();
    }
    public static function loginAction()
    {
        // bat loi
        $is_valid = AuthValidation::login();

        if(!$is_valid){
            NotificationHelper::error('login','Đăng nhập thất bại!');
            header('Location: /login');
            exit();
        }

        $data = [
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'remember' => isset($_POST['member']),

        ];

        $result = AuthHelper::login($data);
        if ($result) {
            // NotificationHelper::success('login', 'Đăng nhập thành công');
            header('Location: /');
        } else {
            // NotificationHelper::error('login', 'Đăng nhập thất bại');
            header('Location: /login');
        }
    }

    public static function logout(){
        AuthHelper::logout();
        NotificationHelper::success('logout', 'Đăng xuất thành công');
        header('Location: /');
    }

    public static function edit($id){
        $result=AuthHelper::edit($id);
        if(!$result){
            if(isset($_SESSION['error']['login'])){
                header('Location: /login');
                exit;
            }
            if(isset($_SESSION['error']['user_id'])){
                $data =$_SESSION['user'];
                $user_id = $data['id'];
                header("Location: /users/$user_id");
                exit;
            }
        }
        $data =$_SESSION['user'];
        header::render();
        Notification::render();
        NotificationHelper::unset();
        // giao dien thong tin user
        Edit::render($data);
        
        Footer::render();
    }

    public static function update($id){
        $is_valid=AuthValidation::edit();
        if(!$is_valid){
            NotificationHelper::error('update_user','Cập nhật thông tin thất bại');
            header("Location: /users/$id");
            exit();
        }
        $data = [
            'email' => $_POST['email'],
            'name' => $_POST['name'],  
        ];
        // Kiểm tra có upload hình ảnh hay không, nếu có: Kiểm tra có hợp lệ không?
        $is_upload=AuthValidation::uploadAvatar();
        if($is_upload){
            $data['avatar'] =$is_upload;
        }
        
        // gọi helper để update 
        $result=AuthHelper::update($id,$data);
        // Kiểm tra kết quả trả về và chuyển hướng
        header("Location: /users/$id");
    }
}