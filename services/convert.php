<?php

function convert($path_video, $path_audio, $path_excute){
    $files_video = getFilePaths($path_video);
    $files_audio = getFilePaths($path_audio, 'mp3');
    $save = PRESET.'/convert.txt';
    $ls = file_get_contents($save);
    $ls = explode(PHP_EOL, $ls);    
    foreach ($files_video as $file) {
        if(in_array($file, $ls)) continue;
        $rand_audio = $files_audio[array_rand($files_audio)];
        echo "Thực thi file: ".basename($file)."\n";
        editvideo($file, $rand_audio, $path_excute);
        $fileHandle = fopen($save, 'a');
        if ($fileHandle) {
            // Di chuyển con trỏ tệp đến cuối file
            fseek($fileHandle, 0, SEEK_END);
        
            // Ghi dữ liệu vào vị trí cuối file
            fwrite($fileHandle, PHP_EOL.$file);
        
            // Đóng file
            fclose($fileHandle);
        
            echo "Đã ghi dữ liệu vào dòng cuối cùng của file.";
        } else {
            echo "Không thể mở file.";
        }  
    }
}


