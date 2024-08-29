<?php
if (!function_exists('CropLink'))
{
    function CropLinkX($url)
    {
        $url_bitly = "https://api-ssl.bitly.com/v4/shorten";
        // $authorization = "Authorization: Bearer 44ad08df84fb6f6c084b5cd9dbdc8906b3e10926"; // Administrador
        $authorization = "Authorization: Bearer ca40c912c8ffdc76c0a2e642d45fcb792c7dc2e4"; // klaxen Mail
        $post = json_encode([
            'long_url' => $url
        ]);

        $ch = curl_init($url_bitly);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result);
        return $data->link;
    }

    function CropLink($url)
    {
        return $url;
    }
}
