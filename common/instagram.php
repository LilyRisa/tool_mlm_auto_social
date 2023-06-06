<?php

function login_ig(){
    $fb = new \Facebook\Facebook([
        'app_id' => env("FACEBOOK_APP_ID"),
        'app_secret' => env("FACEBOOK_APP_SECRET"),
        'default_graph_version' => env("FACEBOOK_APP_VER"),
      ]);
      $helper = $fb->getRedirectLoginHelper();
      $permissions = ['ads_management', "business_management", "instagram_basic", "instagram_content_publish", "pages_manage_engagement"];
      $loginUrl = $helper->getLoginUrl('https://congminh.name.vn/access.php', $permissions);
    echo "Vui long truy cap vào URL sau de đang nhap vao instagram:\n\n";
    echo $loginUrl."&response_type=token";
    echo "\n\n";
    echo "Sau khi dang nhap, instagram se chuyen huong ban den mot trang web không ton tai, nhung trong URL cua trang đo chua access token.\n";
    echo "Hay sao chep toan bo URL do và dan vao day:\n";
    $accessToken = readline("URL: ");
    // $accessToken = explode('access_token=', $accessToken);
    preg_match('/access_token=([^&]+)/', $accessToken, $matches);
    if (isset($matches[1])) {
        
        @file_put_contents(PRESET.'/access_token.txt', $matches[1]);
      } else {
        echo "Không thể lấy access token.\n";
      }

}