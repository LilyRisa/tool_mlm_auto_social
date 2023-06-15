<?php

define('LIB',__DIR__.'/../lib');

function check_connection(){
    $host = 'www.google.com';
    $port = 80;
    $timeout = 5;
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($socket)  return true;
    return false;
}
if(!function_exists('getFilePaths')){
    function getFilePaths($folderPath, $mime = null) {
        $filePaths = array();
        $files = scandir($folderPath);
    
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
    
            $filePath = $folderPath . '/' . $file;
    
            if (is_file($filePath)) {
                if(!empty($mime)){
                    $mime_file = explode('.', $filePath);
                    $mime_file = $mime_file[count($mime_file) - 1];
                    if($mime_file == $mime) $filePaths[] = $filePath;
                }else{
                    $filePaths[] = $filePath;
                }
                
            } elseif (is_dir($filePath)) {
                $filePaths = array_merge($filePaths, getFilePaths($filePath));
            }
        }
    
        return $filePaths;
    }
}


function editvideo($inputVideo, $inputAudio, $path_excute){
    // $exce;
    $filename = basename($inputVideo);
    $outputVideo = $path_excute. "/".$filename;

    $flipFilter = "hflip";
    $flipCommand = "-vf \"$flipFilter\"";

    // Tắt âm thanh của video gốc và xuất video tạm thời
    $tmpVideo = $path_excute. "/".basename($inputVideo)."_tmp_video.mp4";
    $muteCommand = "-an";
    $ffmpegVideoCommand = LIB."/ffmpeg/ffmpeg.exe -hide_banner -loglevel panic -i \"{$inputVideo}\" {$flipCommand} {$muteCommand} -c:v libx264 -y \"{$tmpVideo}\"";

    exec($ffmpegVideoCommand);

    // Chèn nhạc vào video tạm thời
    $audioCommand = "-i \"{$inputAudio}\" -c:a aac";
    $ffmpegAudioCommand =  LIB."/ffmpeg/ffmpeg.exe -hide_banner -loglevel panic -i \"{$tmpVideo}\" {$audioCommand} -c:v copy -map 0:v:0 -map 1:a:0 -shortest -y \"{$outputVideo}\"";
    // exec($ffmpegAudioCommand);

    $process = proc_open($ffmpegAudioCommand, [
        0 => ['pipe', 'r'],  // Nhập chuẩn cho quy trình (stdin)
        1 => ['pipe', 'w'],  // Xuất chuẩn của quy trình (stdout)
        2 => ['pipe', 'w'],  // Xuất chuẩn lỗi của quy trình (stderr)
    ], $pipes);

    // Kiểm tra nếu quy trình tạo thành công
    if (is_resource($process)) {
        // Đọc dữ liệu từ quy trình
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);

        // Đóng quy trình và các đường ống (pipes)
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        // Hiển thị kết quả
        echo "Output:\n{$output}\n";
        echo "Error:\n{$error}\n";
    } else {
        echo "Failed to create process.";
    }
    // Xóa video tạm thời
    @unlink($tmpVideo);
}


function request($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();

    // Thiết lập URL và các tùy chọn khác
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // Thiết lập header
    $curlHeaders = [];
    foreach ($headers as $key => $value) {
        $curlHeaders[] = "$key: $value";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

    // Thiết lập dữ liệu gửi đi (nếu có)
    if ($method !== 'GET' && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    // Gửi request và lấy kết quả
    $response = curl_exec($ch);

    // Xử lý lỗi nếu có
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("Curl request error: $error");
    }

    // Đóng curl
    curl_close($ch);

    // Trả về phản hồi
    return $response;
}
if(!function_exists('upload_fb')){
function upload_fb($folder, $page_id){
    $desc = @file_get_contents(PRESET.'/description.txt');
    $desc = explode(PHP_EOL, $desc);
    $desc = $desc[array_rand($desc)];
    $arr_file = getFilePaths($folder);
    $arr_video_uploaded = @file_get_contents(PRESET.'/uploaded.txt');
    $arr_video_uploaded = explode(PHP_EOL, $arr_video_uploaded);

    foreach($arr_file as $video){
        if(in_array($video, $arr_video_uploaded)) continue;
        upload_reel_page($video, $desc, $page_id);
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
}

function count_file($folder){
    $files = scandir($folder);

    $fileCount = 0;
    foreach ($files as $file) {
        // Loại bỏ các thư mục và tệp tin ẩn (bắt đầu bằng dấu chấm)
        if (!is_dir($file) && !in_array($file, ['.', '..']) && $file[0] !== '.') {
            $fileCount++;
        }
    }
    return $fileCount;
}

function env($key, $value = null){
    return getenv($key, true) ? getenv($key, true) : $value;
}