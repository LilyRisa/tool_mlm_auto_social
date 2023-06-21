<?php
require 'loaded.php';
$argv = $_SERVER['argv'];

// $path = $argv[1];
// $audio = $argv[2];
// $exce = $argv[3];

if(count($argv) == 1){
    while(true){

        echo "===============\n";
        echo "=     Tool    =\n";
        echo "=             =\n";
        echo "===============\n";
        echo "1. Convert video\n";
        echo "2. Get access token facebook\n";
        echo "3. Chon page upload\n";
        echo "4. Upload facebook reel\n";
        echo "5. Login google\n";
        echo "0. exit\n";

        $key = readline("Chon: ");
        if($key == 1){
            echo "===================\n";
            echo "=       Tool      =\n";
            echo "=  Convert video  =\n";
            echo "===================\n";
            $param1 = readline("Nhap duong dan video goc: ");
            $param2 = readline("Nhap duong dan chua audio: ");
            $param3 = readline("Nhap duong dan xuat file: ");
            convert($param1, $param2, $param3);
        }else if($key == 2){
            echo "===================\n";
            echo "=       Tool      =\n";
            echo "=  login facebook =\n";
            echo "===================\n";
            login_fb();
        }else if($key == 3){
            echo "===================\n";
            echo "=       Tool      =\n";
            echo "=  page facebook  =\n";
            echo "===================\n";
            echo "\n";
            $data = show_page();
            echo "==== Chon page: ===\n";
            foreach($data as $key => $page){
                echo "($key) {$page->name} ({$page->id})\n";
            }
            $key = readline("Nhap key: ");
            $page_select = $data[$key];
            $result = request('https://congminh.name.vn/tool/index.php?type=insert_value', 'POST', http_build_query([
                'page_id' => $page_select->id,
                'content' => json_encode($page_select),
                'type' => 'facebook_page'
            ]));
        }else if($key == 4){
            echo "===================\n";
            echo "=       Tool      =\n";
            echo "= Upload facebook =\n";
            echo "===================\n";
            echo "\n";
            $folder = readline("Nhap duong dan video: ");
            $page_id = readline("Nhap page_id: ");
            $desc = readline("Nhap duong dan file description: ");
            if($desc == '') $desc = null;
            upload_fb($folder, $page_id, $desc);
        }else if($key == 5){
            echo "===================\n";
            echo "=       Tool      =\n";
            echo "=   login google  =\n";
            echo "===================\n";
            echo "\n";
            echo "Mo url nay tren trinh duyet\n";
            echo gen_url_getaccesstoken();
            echo "\n";
            $url_code = readline("Nhap url tra ve: ");
            preg_match('/code=([^&]+)/', $url_code, $matches);
            if(isset($matches[1])){
                $page_id = readline("Nhap ten kenh: ");
                $token = exchangeCodeForAccessToken(urldecode($matches[1]));
                // @file_put_contents(PRESET.'/google_refesh.txt',$token['refesh']);
                request('https://congminh.name.vn/tool/index.php?type=insert_value', 'POST', http_build_query([
                    'page_id' => $page_id,
                    'content' => $token['refesh'],
                    'type' => 'youtube_page'
                ]));
            }
            echo "Set token success\n";
        }else if($key == 0){
            break;
        }
    }
}else{
    $type = $argv[1];
    if($type == '--convert' || $type == '-c'){

        convert($argv[2], $argv[3], $argv[4]);
    
    }else if($type == '--login-facebook' || $type == '-lf'){
        $page_id = $argv[2];
        login_fb();
    
    
    }else if($type == '--show-page-facebook' || $type == '-spf'){
        $data = show_page();
        echo "==== Chon page: ===\n";
        foreach($data as $key => $page){
            echo "($key) {$page->name} ({$page->id})\n";
        }
    
        $key = readline("Nhap key: ");
        $page_select = $data[$key];
        // @file_put_contents(PRESET.'/access_token_page.txt', json_encode($page_select));
        $check = request('https://congminh.name.vn/tool/index.php?type=insert_value', 'POST', http_build_query([
            'page_id' => $page->id,
            'content' => json_encode($page_select),
            'type' => 'facebook_page'
        ]));
        echo "Lu du lieu page thanh cong !\n";
    }else if($type == '--cron-upload-facebook' || $type == '-cuf'){
        $folder = $argv[2];
        $page_id = $argv[3];
        $desc = null;
        if(isset($argv[4])){
            $desc = $argv[4];
        }

        upload_fb($folder, $page_id, $desc);
        
    }else if($type == '--login-instagram' || $type == '-li'){
        login_ig();
        
    }else if($type == '--upload-youtube' || $type == '-uy'){
        $page_id = $argv[3];
        $desc_text = null;
        if(isset($argv[4])){
            if($argv[4] != null){
                $desc_text = $argv[4];
            }
        }
        $access_token = get_access_token($page_id);
        if(empty($access_token)) throw new \Exception('lOGIN LAI GOOGLE DE LAY DU LIEU DANG NHAP MOI NHAT!');
        $folder = $argv[2];
        $arr_file = getFilePaths($folder);
        $arr_video_uploaded = @file_get_contents(PRESET.'/uploaded_yt.txt');
        $arr_video_uploaded = explode(PHP_EOL, $arr_video_uploaded);
        foreach($arr_file as $video){
            if(in_array($video, $arr_video_uploaded)) continue;
            if(upload_youtube_short($video, $access_token, $desc_text)){
                $fileHandle = fopen(PRESET.'/uploaded_yt.txt', 'a');
                if ($fileHandle) {
                    // Di chuyển con trỏ tệp đến cuối file
                    fseek($fileHandle, 0, SEEK_END);
                    // Ghi dữ liệu vào vị trí cuối file
                    fwrite($fileHandle, PHP_EOL.$video);
                    // Đóng file
                    fclose($fileHandle);
                }
                echo "Tai len thanh cong!\n";
                break;
            }
        }
        
    }
}

?>