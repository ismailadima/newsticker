<?php
/**
 * @author     Muhammad Arisandi (sands)
 * @datetime   2020
 * @email      muhammad.arisandi@mncgroup.com
 * @perpose    GlobalHelpers
 */

namespace App\Helpers;

use App\Category;
use App\DeleteSchedule;
use App\Unit;
use Illuminate\Support\Facades\Auth;

use DB;

class NewstickerHelpers
{
    public static function fixMSWord($string) {
        $map = Array(
            '33' => '!', '34' => '"', '35' => '#', '36' => '$', '37' => '%', '38' => '&', '39' => "'", '40' => '(', '41' => ')', '42' => '*',
            '43' => '+', '44' => ',', '45' => '-', '46' => '.', '47' => '/', '48' => '0', '49' => '1', '50' => '2', '51' => '3', '52' => '4',
            '53' => '5', '54' => '6', '55' => '7', '56' => '8', '57' => '9', '58' => ':', '59' => ';', '60' => '<', '61' => '=', '62' => '>',
            '63' => '?', '64' => '@', '65' => 'A', '66' => 'B', '67' => 'C', '68' => 'D', '69' => 'E', '70' => 'F', '71' => 'G', '72' => 'H',
            '73' => 'I', '74' => 'J', '75' => 'K', '76' => 'L', '77' => 'M', '78' => 'N', '79' => 'O', '80' => 'P', '81' => 'Q', '82' => 'R',
            '83' => 'S', '84' => 'T', '85' => 'U', '86' => 'V', '87' => 'W', '88' => 'X', '89' => 'Y', '90' => 'Z', '91' => '[', '92' => '\\',
            '93' => ']', '94' => '^', '95' => '_', '96' => '`', '97' => 'a', '98' => 'b', '99' => 'c', '100'=> 'd', '101'=> 'e', '102'=> 'f',
            '103'=> 'g', '104'=> 'h', '105'=> 'i', '106'=> 'j', '107'=> 'k', '108'=> 'l', '109'=> 'm', '110'=> 'n', '111'=> 'o', '112'=> 'p',
            '113'=> 'q', '114'=> 'r', '115'=> 's', '116'=> 't', '117'=> 'u', '118'=> 'v', '119'=> 'w', '120'=> 'x', '121'=> 'y', '122'=> 'z',
            '123'=> '{', '124'=> '|', '125'=> '}', '126'=> '~', '127'=> ' ', '128'=> '&#8364;', '129'=> ' ', '130'=> ',', '131'=> ' ', '132'=> '"',
            '133'=> '.', '134'=> ' ', '135'=> ' ', '136'=> '^', '137'=> ' ', '138'=> ' ', '139'=> '<', '140'=> ' ', '141'=> ' ', '142'=> ' ',
            '143'=> ' ', '144'=> ' ', '145'=> "'", '146'=> "'", '147'=> '"', '148'=> '"', '149'=> '.', '150'=> '-', '151'=> '-', '152'=> '~',
            '153'=> ' ', '154'=> ' ', '155'=> '>', '156'=> ' ', '157'=> ' ', '158'=> ' ', '159'=> ' ', '160'=> ' ', '161'=> '¡', '162'=> '¢',
            '163'=> '£', '164'=> '¤', '165'=> '¥', '166'=> '¦', '167'=> '§', '168'=> '¨', '169'=> '©', '170'=> 'ª', '171'=> '«', '172'=> '¬',
            '173'=> '­', '174'=> '®', '175'=> '¯', '176'=> '°', '177'=> '±', '178'=> '²', '179'=> '³', '180'=> '´', '181'=> 'µ', '182'=> '¶',
            '183'=> '·', '184'=> '¸', '185'=> '¹', '186'=> 'º', '187'=> '»', '188'=> '¼', '189'=> '½', '190'=> '¾', '191'=> '¿', '192'=> 'À',
            '193'=> 'Á', '194'=> 'Â', '195'=> 'Ã', '196'=> 'Ä', '197'=> 'Å', '198'=> 'Æ', '199'=> 'Ç', '200'=> 'È', '201'=> 'É', '202'=> 'Ê',
            '203'=> 'Ë', '204'=> 'Ì', '205'=> 'Í', '206'=> 'Î', '207'=> 'Ï', '208'=> 'Ð', '209'=> 'Ñ', '210'=> 'Ò', '211'=> 'Ó', '212'=> 'Ô',
            '213'=> 'Õ', '214'=> 'Ö', '215'=> '×', '216'=> 'Ø', '217'=> 'Ù', '218'=> 'Ú', '219'=> 'Û', '220'=> 'Ü', '221'=> 'Ý', '222'=> 'Þ',
            '223'=> 'ß', '224'=> 'à', '225'=> 'á', '226'=> 'â', '227'=> 'ã', '228'=> 'ä', '229'=> 'å', '230'=> 'æ', '231'=> 'ç', '232'=> 'è',
            '233'=> 'é', '234'=> 'ê', '235'=> 'ë', '236'=> 'ì', '237'=> 'í', '238'=> 'î', '239'=> 'ï', '240'=> 'ð', '241'=> 'ñ', '242'=> 'ò',
            '243'=> 'ó', '244'=> 'ô', '245'=> 'õ', '246'=> 'ö', '247'=> '÷', '248'=> 'ø', '249'=> 'ù', '250'=> 'ú', '251'=> 'û', '252'=> 'ü',
            '253'=> 'ý', '254'=> 'þ', '255'=> 'ÿ'
        );

        $search = Array();
        $replace = Array();

        foreach ($map as $s => $r) {
            $search[] = chr((int)$s);
            $replace[] = $r;
        }

        return str_replace($search, $replace, $string);
    }

    public static  function convert_smart_quotes($string)
    {
        $search = array(chr(145),
                        chr(146),
                        chr(147),
                        chr(148),
                        chr(151));

        $replace = array("'",
                         "'",
                         '"',
                         '"',
                         '-');

        return str_replace($search, $replace, $string);
    }

    public static function replaceMsWordEncodedQuotes($text = null)
    {
        $quotes = array(
            "\xC2\xAB"     => '"', // « (U+00AB) in UTF-8
            "\xC2\xBB"     => '"', // » (U+00BB) in UTF-8
            "\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
            "\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
            "\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
            "\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
            "\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
            "\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
            "\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
            "\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
            "\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
            "\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
        );
        $text = strtr($text, $quotes);

        return $text;
    }

    public static function cleanTextInput($text = null)
    {
        $ms_encode_replace = self::replaceMsWordEncodedQuotes($text);

        $filetxt_regex = preg_replace("/[\x93|\x94]/", "\x22", $ms_encode_replace);
        $filetxt_regex = preg_replace("/[\xEF|\xBB|\xBF]/", "", $filetxt_regex);
        $filetxt_regex = preg_replace("/[\x95|\x96]/", "\x2d", $filetxt_regex); //untuk strip panjang
        //kutip2an handle
        $filetxt_regex = self::convert_smart_quotes($filetxt_regex);

        $content_clean = htmlspecialchars($filetxt_regex, ENT_QUOTES);

        //content remove new line (enter/ \r / \n)
        // $content_clean = trim(str_replace(array("\r\n","\r")," ",$content_clean));

        //clear new line on first and last string
        $content_clean = trim($content_clean);

        return $content_clean;
    }

    public static function decodeHtmlSpecialChars($text = null)
    {
        $content_original = htmlspecialchars_decode($text, ENT_QUOTES);
        return $content_original;
    }


    //Split content kedalam array untuk kebutuhan Edit data pada mode per baris/line
    public static function splitContents($newsticker = null) //model Newsticker
    {
        $category_id = $newsticker->category_id;
        $unit_id = $newsticker->unit_id;
        $content = '';
        $content2 = '';

        if (!empty($newsticker->content)){
            $content = NewstickerHelpers::decodeHtmlSpecialChars($newsticker->content);
        }

        if (!empty($newsticker->content2)){
            $content2 = NewstickerHelpers::decodeHtmlSpecialChars($newsticker->content2);
        }

        $content_split_arr = [
            'content' => [],
            'content2' => []
        ];

        switch($unit_id){
            case Unit::UNIT_MNCTV :
                $content_split_arr = self::splitContentsMnc($content, $content2, $category_id);
            break;

            case Unit::UNIT_RCTI :
                $content_split_arr = self::splitContentsRcti($content, $content2, $category_id);
            break;

            case Unit::UNIT_GTV : 
                $content_split_arr = self::splitContentsGtv($content, $content2, $category_id);
            break;

            case Unit::UNIT_INEWS : 
                $content_split_arr = self::splitContentsInews($content);
            break;

            case Unit::UNIT_MPI : 
                $content_split_arr = self::splitContentNewsCategory($content);
            break;

        }

        return $content_split_arr;
    }

    private static function splitContentsMnc($content = null, $content2 = null, $category_id = null)
    {
        $content_split_arr = [
            'content' => [],
            'content2' => []
        ];

        if($category_id == Category::CAT_NEWS){
            $content_split_arr = self::splitContentNewsCategory($content, $content2);
        }else{
            //delimeter ##1 , ##1 ada di akhir, jarak 5 spasi sebelum dan sesudah konten
            $split_content = explode("##1", $content);
            foreach($split_content as $split){
                // $split = trim($split);
                // if(!empty($split) || $split != ''){
                //     $content_split_arr['content'][] = $split;
                // }

                //v2 agar di split konten ada pagar nya, sands, 
                if(!empty($split) || $split != ''){
                    $content_split_arr['content'][] = "##1".$split;
                }
            }


            if(!empty($content2)){
                $split_content = explode("##1", $content2);
                foreach($split_content as $split){
                    // $split = trim($split);
                    // if(!empty($split) || $split != ''){
                    //     $content_split_arr['content2'][] = $split;
                    // }
                        //v2 agar di split konten ada pagar nya, sands, 
                    if(!empty($split) || $split != ''){
                        $content_split_arr['content2'][] = "##1".$split;
                    }
                }
            }

            // set ##1 diakhir data
            //v2 agar di split konten ada pagar nya, sands, 
            $last_index_1 = count( $content_split_arr['content'])-1;
            $last_index_2 = count( $content_split_arr['content2'])-1;
            if($last_index_1 >= 0){ //jika tidak kosong
                $content_split_arr['content'][$last_index_1] = $content_split_arr['content'][$last_index_1]."##1"; 
            }

            if($last_index_2 >= 0){ //jika tidak kosong
                $content_split_arr['content2'][$last_index_2] = $content_split_arr['content2'][$last_index_2]."##1"; 
            }
        }

        return $content_split_arr;
    }

    private static function splitContentsRcti($content = null, $content2 = null, $category_id = null)
    {
        $content_split_arr = [
            'content' => [],
            'content2' => []
        ];

        if($category_id == Category::CAT_NEWS || $category_id == Category::CAT_SERGAP_NEWS){
            $content_split_arr = self::splitContentNewsCategory($content, $content2);
        }else{
            //delimeter nya NEW LINE / Enter
            $split_content = preg_split('/\r\n|\r|\n/', $content);
            foreach($split_content as $split){
                $split = ltrim($split);
                if(!empty($split) || $split != ''){
                    $content_split_arr['content'][] = $split;
                }
            }

            if(!empty($content2)){
                $split_content = preg_split('/\r\n|\r|\n/', $content2);
                foreach($split_content as $split){
                    $split = ltrim($split);
                    if(!empty($split) || $split != ''){
                        $content_split_arr['content2'][] = $split;
                    }
                }
            }
        }

        return $content_split_arr;
    }

    private static function splitContentsGtv($content = null, $content2 = null, $category_id = null)
    {
        $content_split_arr = [
            'content' => [],
            'content2' => []
        ];

        if($category_id == Category::CAT_NEWS){
            $content_split_arr = self::splitContentNewsCategory($content, $content2);
        }else{
                //delimeter nya ##2
            $split_content = explode("##2", $content);
            foreach($split_content as $split){
                $split = trim($split);
                if(!empty($split) || $split != ''){
                    //$content_split_arr['content'][] = $split;

                    //v2 agar di split konten ada pagar nya, sands, 
                    $content_split_arr['content'][] = $split."##2";
                }
            }

            if(!empty($content2)){
                $split_content = explode("##2", $content2);
                foreach($split_content as $split){
                    $split = trim($split);
                    if(!empty($split) || $split != ''){
                        // $content_split_arr['content2'][] = $split;

                        //v2 agar di split konten ada pagar nya, sands, 
                        $content_split_arr['content2'][] = $split."##2";
                    }
                }
            }

            //remove ##2 on last line
            $last_index_1 = count($content_split_arr['content'])-1;
            $last_index_2 = count($content_split_arr['content2'])-1;
            if($last_index_1 >= 0){ //jika tidak kosong
                $line_last = trim(str_replace("##2", "", $content_split_arr['content'][$last_index_1]));
                $content_split_arr['content'][$last_index_1] = $line_last;
            }

            if($last_index_2 >= 0){ //jika tidak kosong
                $line_last = trim(str_replace("##2", "", $content_split_arr['content2'][$last_index_2]));
                $content_split_arr['content2'][$last_index_2] = $line_last;
            }
        }
        

        return $content_split_arr;
    }

    private static function splitContentNewsCategory($content = null, $content2 = null)
    {
        $content_split_arr = [
            'content' => [],
            'content2' => []
        ];

        //delimeter ## , diikuti angka bebas ##1 contoh
        //NOTE : di tiap baris hastag ditampilkan
        $pos_all_hashtag = GlobalHelpers::strpos_all($content, "##");
        if(!empty($pos_all_hashtag)){
            $split_content  = preg_split("/##[1-9]/", $content);
            foreach($split_content as $index => $split){
                if(!empty($split) || $split != ''){
                    $split = trim($split);
                    $index = !empty($pos_all_hashtag[$index]) ? $pos_all_hashtag[$index] : false;
                    $delimeter_this = $index != false ? substr($content, $index, 3) : "";
                    $content_split_arr['content'][] = $split." ".$delimeter_this;
                }
            }
        }

        return $content_split_arr;
    }

    private static function splitContentsInews(
        $content = null, 
        $content2 = null
    ){
        $content_split_arr = [
            'content' => [],
            'content2' => []
        ];

        //delimeter nya NEW LINE / Enter
        $split_content = preg_split('/\r\n|\r|\n/', $content);
        foreach($split_content as $split){
            $split = trim($split);
            if(!empty($split) || $split != ''){
                $content_split_arr['content'][] = $split;
            }
        }

        if(!empty($content2)){
            $split_content = preg_split('/\r\n|\r|\n/', $content2);
            foreach($split_content as $split){
                $split = trim($split);
                if(!empty($split) || $split != ''){
                    $content_split_arr['content2'][] = $split;
                }
            }
        }

        return $content_split_arr;
    }






    //Merge Hasil Split Array menjadi format masing2 unit
    public static function mergeContents($newsticker, $contents_split, $is_delete_last_line = false) //(Newsticker Model, Array Split)
    {
        $category_id = $newsticker->category_id;
        $unit_id = $newsticker->unit_id;
        $text_content = "";

        switch($unit_id) {
            //delimeter ##1 , ##1 ada di akhir, jarak 5 spasi sebelum dan sesudah konten
            case Unit::UNIT_MNCTV : 
                $text_content = self::mergeContentsMnc($contents_split, $is_delete_last_line, $category_id);
            break;

            case Unit::UNIT_RCTI : 
                $text_content = self::mergeContentsRcti($contents_split, $is_delete_last_line, $category_id);
            break;

            case Unit::UNIT_GTV : 
                $text_content = self::mergeContentsGtv($contents_split, $is_delete_last_line, $category_id);
            break;

            case Unit::UNIT_INEWS : 
                $text_content = self::mergeContentsNewsCategory($contents_split);
            break;

            case Unit::UNIT_MPI :
                $text_content = self::mergeContentsNewsCategory($contents_split);
            break;
        }

        return $text_content;
    }

    private static function mergeContentsMnc($contents_split = null, $is_delete_last_line = null, $category_id = null)
    {
        $text_content = null;

        if($category_id == Category::CAT_NEWS){
            $text_content = self::mergeContentsNewsCategory($contents_split, $is_delete_last_line);
        }else{
            // $last_index = count($contents_split)-1;
            // $text_content = "##1";
            // foreach($contents_split as $key => $content){
            //     $text_content  .=  "     ".$content."     ##1"; 
            // }

            //v2 agar di split konten ada pagar nya, sands, 
            $last_index = count($contents_split)-1;
            foreach($contents_split as $key => $content){
                $content = trim($content);
                if($last_index != $key){
                    $text_content  .= $content."     "; 
                }else{
                    $text_content  .= $content;
                }
            }

            if($is_delete_last_line){
                $text_content .= "     ##1";
            }
        }

        return $text_content;
    }

    private static function mergeContentsRcti($contents_split = null, $is_delete_last_line = null, $category_id = null)
    {
        $text_content = null;

        if($category_id == Category::CAT_NEWS){
            $text_content = self::mergeContentsNewsCategory($contents_split, $is_delete_last_line);
        }else{
            //delimeter nya NEW LINE / Enter
            $last_index = count($contents_split)-1;
            foreach($contents_split as $key => $content){
                $text_content  .= ($last_index == $key) ? $content : $content."\r\n \r\n"; 
            }   
        }
       
        return $text_content;
    }

    private static function mergeContentsGtv($contents_split = null, $is_delete_last_line = null, $category_id = null)
    {
        $text_content = null;

        if($category_id == Category::CAT_NEWS){
            $text_content = self::mergeContentsNewsCategory($contents_split, $is_delete_last_line);
        }else{
            //delimeter ##2
            $count_content_split = count($contents_split);
            $last_index = $count_content_split-1;
            // foreach($contents_split as $key => $content){
            //     $text_content  .= ($last_index == $key && $count_content_split > 1) ? $content : $content." ##2\r\n"; 
            // }

            //v2 agar di split konten ada pagar nya, sands, 
            foreach($contents_split as $key => $content){
                $text_content  .= ($last_index == $key && $count_content_split > 1) ? $content : $content."\r\n"; 
            }

            if($is_delete_last_line){
                $check_last_line  = substr($text_content, -3, 3);
                if($check_last_line == "##2"){
                    $text_content = substr($text_content, 0, -3);
                }
            } 
        }

        return $text_content;
    }

    private static function mergeContentsNewsCategory($contents_split = null, $is_delete_last_line = null)
    {
        $text_content = '';
        $last_index = count($contents_split)-1;

        foreach($contents_split as $key => $content){
            $content = trim($content);
            if($last_index != $key){
                $text_content  .= $content." "; 
            }else{
                $text_content  .= $content;
            }
        }

        return $text_content;
    }

    //Merge Hasil Split Array menjadi format masing2 unit
    //(Newsticker Model, Array Split)
    public static function mergeContentsV2(
        $newsticker = null, 
        $contents_split = null, 
        $code_format = null, 
        $pemisah_line = null
    ){
        $category_id = $newsticker->category_id;
        $unit_id = $newsticker->unit_id;
        $text_content = "";

        switch($unit_id) {
            case Unit::UNIT_INEWS : 
                $text_content = self::mergeContentsInewsV2($contents_split, $code_format, $pemisah_line);
            break;
        }

        return $text_content;
    }

    private static function mergeContentsInewsV2($contents_split, $code_format, $pemisah_line)
    {
        $text_content = null;
      
        //delimeter nya NEW LINE / Enter
        $last_index = count($contents_split)-1;
        foreach($contents_split as $key => $content){
            $content = trim($content);
            if($key == 0){
                if($last_index == $key){
                    $text_content .= $code_format." ".$content." ";
                }else{
                    $text_content .= $code_format." ".$content." ".$pemisah_line." ";
                }
            }

            if($key > 0){
                if($last_index == $key){
                    $text_content .= $content." ";
                }else{
                    $text_content .= $content." ".$pemisah_line." ";
                }
            }
        }   

        return $text_content;
    }

    public static function mergeContentsInewsNoFormat($contents_split)
    {
        $text_content = null;
        
        //delimeter nya NEW LINE / Enter
        $last_index = count($contents_split)-1;
        foreach($contents_split as $key => $content){
            $content = trim($content);
            if($last_index == $key){
                $text_content .= $content;
            }else{
                $text_content .= $content."\r\n";
            }
        }   

        return $text_content;
    }



    ///// START DELETE FEATURE
    public static function buildDeletedContents($newsticker = null)
    {
        $data = $newsticker->deletedTime;
        $deletedData = array('content1' => [],'content2' => []);
        if(!empty($data)){
            foreach($data as $value){
                if($value->type_content == '1'){
                    $deletedData['content1'][] = array('id' =>$value->id, 'time_deleted' =>$value->time_deleted,'is_deleted' => $value->is_deleted);
                } else {
                    $deletedData['content2'][] = array('id' =>$value->id, 'time_deleted' =>$value->time_deleted,'is_deleted' => $value->is_deleted);
                }
            }
        } 
        
        return $deletedData;
    }

    public static function splitContentsforDelete($newsticker = null,$content = null){
        $category_id = $newsticker->category_id;
        $unit_id = $newsticker->unit_id;

        switch($unit_id){
            case Unit::UNIT_MNCTV :
                $content_split_arr = self::splitContentsMnc($content, null, $category_id);
            break;

            case Unit::UNIT_RCTI :
                $content_split_arr = self::splitContentsRcti($content, null, $category_id);
            break;

            case Unit::UNIT_GTV : 
                $content_split_arr = self::splitContentsGtv($content, null, $category_id);
            break;

            case Unit::UNIT_INEWS : 
                $content_split_arr = self::splitContentNewsCategory($content);
            break;

            case Unit::UNIT_MPI : 
                $content_split_arr = self::splitContentNewsCategory($content);
            break;

        }

        return $content_split_arr;
    }
    ///// END DELETE FEATURE


}
?>