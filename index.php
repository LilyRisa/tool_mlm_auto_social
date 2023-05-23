<?php
require 'loaded.php';
$argv = $_SERVER['argv'];
$type = $argv[1];

// $path = $argv[1];
// $audio = $argv[2];
// $exce = $argv[3];

if($type == '--convert' || $type == '-c'){

    convert($argv[2], $argv[3], $argv[4]);

}else if($type == '--login-facebook' || $type == '-lf'){
    $page_id = $argv[2];
    login_fb();


}else if($type == '--upload-facebook' || $type == '-uf'){
    $data = show_page();
    echo "==== Chon page: ===\n";
    foreach($data as $key => $page){
        echo "($key) {$page->name} ({$page->id})\n";
    }

    $key = readline("Nhap key: ");
    $page_select = $data[$key];
    @file_put_contents(PRESET.'/access_token_page.txt', json_encode($page_select));
}else if($type == '--cron-upload-facebook' || $type == '-cuf'){
    $folder = $argv[2];

    $arr_file = getFilePaths($folder);
    $arr_video_uploaded = @file_get_contents(PRESET.'/uploaded.txt');
    $arr_video_uploaded = explode(PHP_EOL, $arr_video_uploaded);

    foreach($arr_file as $video){
        if(in_array($video, $arr_video_uploaded)) continue;
        upload_reel_page($video, isset($argv[3]) ? $argv[3] : 'Thế giới tiện ích đồ da dụng giá tốt');
        $fileHandle = fopen(PRESET.'/uploaded.txt', 'a');
        if ($fileHandle) {
            // Di chuyển con trỏ tệp đến cuối file
            fseek($fileHandle, 0, SEEK_END);
            // Ghi dữ liệu vào vị trí cuối file
            fwrite($fileHandle, PHP_EOL.$video);
            // Đóng file
            fclose($fileHandle);
        } 
        break;
    }
}







?>