<?php

function upload_youtube_short($videoPath, $accessToken)
{
    // $videoPath = 'path/to/your/video.mp4';
    $clientId = env("GOOGLE_CLIENT_ID");
    $clientSecret = env("GOOGLE_CLIENT_SECRET");
    $accessToken = $accessToken;

    $client = new \Google\Client();
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->setAccessToken($accessToken);
    $client->setScopes([
        'https://www.googleapis.com/auth/youtube.upload',
        'https://www.googleapis.com/auth/youtube.force-ssl',
        'https://www.googleapis.com/auth/userinfo.email'
    ]);

    $youtube = new \Google_Service_YouTube($client);

    // Tạo một đối tượng Video để tải lên
    $video = new \Google_Service_YouTube_Video();
    // var_dump($video);
    // var_dump($youtube);
    $snippet = new \Google_Service_YouTube_VideoSnippet();
    $desc = @file_get_contents(PRESET.'/description.txt');
    $desc = explode(PHP_EOL, $desc);
    $desc = $desc[array_rand($desc)];
    $title = str_replace('#tienichcongminh', '', $desc);
    $snippet->setTitle($title);
    $snippet->setDescription($desc.' #short #tienichcongminh');
    $snippet->setTags(['tienichcongminh']);
    $video->setSnippet($snippet);

    // Đặt thuộc tính status của video
    $status = new \Google_Service_YouTube_VideoStatus();
    $status->privacyStatus = 'public';
    $video->setStatus($status);

    // Tải lên video
    $chunkSizeBytes = 1 * 1024 * 1024; // Kích thước chunk (1MB)
    $client->setDefer(true);

    $insertRequest = $youtube->videos->insert('snippet,status', $video);
    
    $media = new \Google_Http_MediaFileUpload(
        $client,
        $insertRequest,
        'video/*',
        null,
        true,
        $chunkSizeBytes
    );
    $media->setFileSize(filesize($videoPath));

    $status = false;
    $handle = fopen($videoPath, 'rb');
    while (!$status && !feof($handle)) {
        $chunk = fread($handle, $chunkSizeBytes);
        $status = $media->nextChunk($chunk);
    }
    fclose($handle);

    $client->setDefer(false);

    // Kiểm tra kết quả
    if ($status->status['uploadStatus'] === 'uploaded') {
        return true;
    }
    return false;
}


function exchangeCodeForAccessToken($code)
{
    $clientId = env("GOOGLE_CLIENT_ID");
    $clientSecret = env("GOOGLE_CLIENT_SECRET");
    // $client = new \Google\Client();
    // $client->setClientId($clientId);
    // $client->setClientSecret($clientSecret);
    // $client->setRedirectUri($redirect);

    $data = request('https://www.googleapis.com/oauth2/v4/token', 'POST', http_build_query([

            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => 'https://congminh.name.vn/access.php',
            'grant_type' => 'authorization_code',
        
        ]));
    $data = json_decode($data);
    if(isset($data->access_token)) return ['token' => $data->access_token, 'refesh' => $data->refresh_token];
    return null;
}

function get_access_token($page_id){
    // $data = @file_get_contents(PRESET.'/google_refesh.txt');
    $data = json_decode(request('https://congminh.name.vn/tool/index.php?type=get_value', 'POST', http_build_query([
        'page_id' => $page_id,
        'type' => 'youtube_page'
    ])));
    if(empty($data)) return null;
    $data = $data[0]->content;
    
    $clientId = env("GOOGLE_CLIENT_ID");
    $clientSecret = env("GOOGLE_CLIENT_SECRET");

    $data = request('https://oauth2.googleapis.com/token', 'POST', http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $data,
            'grant_type' => 'refresh_token',
    ]));
    $data = json_decode($data);
    if(isset($data->access_token)) return $data->access_token;
    return null;
}

function gen_url_getaccesstoken()
{
    $clientId = env("GOOGLE_CLIENT_ID");
    $clientSecret = env("GOOGLE_CLIENT_SECRET");
    $client = new \Google\Client();
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri('https://congminh.name.vn/access.php');
    $client->setScopes([
        'https://www.googleapis.com/auth/youtube.upload',
        'https://www.googleapis.com/auth/youtube.force-ssl',
        'https://www.googleapis.com/auth/userinfo.email',
    ]);
    $client->setAccessType('offline');
    // $authUrl = 'https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=online&client_id=585734117891-mlmfgcudv95l4h527p2tq429i7cdkkig.apps.googleusercontent.com&redirect_uri=https%3A%2F%2Fcongminh.name.vn%2Faccess.php&state&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fyoutube.upload%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fyoutube.force-ssl%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&approval_prompt=auto&access_type=offline'
    // Tạo URL cho quy trình xác thực OAuth 2.0
    $authUrl = $client->createAuthUrl();
    $authUrl = str_replace('approval_prompt=auto', 'prompt=consent', $authUrl);
    // Chuyển hướng người dùng đến URL xác thực
    return $authUrl;
}
