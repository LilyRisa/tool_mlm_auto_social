<?php

function login_fb(){
    $fb = new \Facebook\Facebook([
        'app_id' => env("FACEBOOK_APP_ID"),
        'app_secret' => env("FACEBOOK_APP_SECRET"),
        'default_graph_version' => env("FACEBOOK_APP_VER"),
      ]);
      $helper = $fb->getRedirectLoginHelper();
      $permissions = ['pages_read_engagement', "pages_show_list", "pages_manage_posts"];
      $loginUrl = $helper->getLoginUrl('https://congminh.name.vn/access.php', $permissions);
    echo "Vui long truy cap vào URL sau de đang nhap vao Facebook:\n\n";
    echo $loginUrl."&response_type=token";
    echo "\n\n";
    echo "Sau khi dang nhap, Facebook se chuyen huong ban den mot trang web không ton tai, nhung trong URL cua trang đo chua access token.\n";
    echo "Hay sao chep toan bo URL do và dan vao day:\n";
    $accessToken = readline("URL: ");
    // $accessToken = explode('access_token=', $accessToken);
    preg_match('/access_token=([^&]+)/', $accessToken, $matches);
    if (isset($matches[1])) {
        
        // @file_put_contents(PRESET.'/access_token.txt', $matches[1]);
        request('https://congminh.name.vn/tool/index.php?type=update_access_token', 'POST', http_build_query([
            'access_token' => $matches[1],
        ]));
      } else {
        echo "Không thể lấy access token.\n";
      }

}

function show_page(){
    // file_get_contents
    // $access_token = @file_get_contents(PRESET.'/access_token.txt');
    $access_token = json_decode(request('https://congminh.name.vn/tool/index.php?type=access_token', 'GET'));
    $access_token = $access_token->content;
    // echo $access_token;
    $data = request("https://graph.facebook.com/v15.0/me/accounts?access_token=".$access_token, 'GET');

    $data = json_decode($data);
    $_SESSION['page'] = !empty($data->data)? $data->data : [];
    return !empty($data->data)? $data->data : [];
}

function upload_reel_page($path_video, $desc, $page_id){
    $page = json_decode(@file_get_contents(PRESET.'/access_token_page.txt'));

    $page = json_decode(request('https://congminh.name.vn/tool/index.php?type=get_value', 'POST', http_build_query([
        'page_id' => $page_id,
        'type' => 'facebook_page'
    ])));
    $page = !empty($page[0]) ? json_decode($page[0]->content) : null;
    if(empty($page)) return false;

    $access_token = $page->access_token;
    $id = $page->id;

    $data = request('https://graph.facebook.com/v15.0/'.$id.'/video_reels?upload_phase=start&access_token='.$access_token, 'POST');

    $data = json_decode($data);
    if(empty($data->upload_url)) return false; 
    $video_id = $data->video_id;
    $upload_url = $data->upload_url;

    $file_data = file_get_contents($path_video);
    $fileSize = filesize($path_video);
    $headers = ['Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="@my_video_file.jpg"',
            'file_size' => $fileSize, 
            'offset' => 0,
            'Authorization' => "OAuth $access_token"
        ];
    
    $data = request($upload_url, 'POST', $file_data, $headers);
    echo $data."\n";
    $data = json_decode($data);
    if($data->success){
        $data = request('https://graph.facebook.com/v15.0/'.$id.'/video_reels?video_id='.$video_id.'&upload_phase=finish&video_state=PUBLISHED&description='.urlencode($desc).'&access_token='.$access_token, 'POST');
        echo $data."\n";
        $data = json_decode($data);
        return $data->success;
    }

    return false;

}