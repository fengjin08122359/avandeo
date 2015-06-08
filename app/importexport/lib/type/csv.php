<?php
class importexport_type_csv implements importexport_interface_type{

    public function __construct(){
        $this->issupzh = setlocale(LC_ALL, "zh_CN");
        $this->charset = kernel::single('base_charset');
    }

    /*
     * 将导出的数组转换为csv格式，
     * 约定：在每次转换后最后在此函数换行(循环调用此函数进行写文件)
     *       在将转换后的字符串写到文件中则不进行换行操作
     *
     * @params $data array 需要导入的数组，一维数组
     * @return $rs string 转换后csv格式的字符串
     * */
    public function arrToExportType($data){
        $rs = '';
        if( is_array($data) ){
            foreach( (array)$data as $val ){
                $exportData[] = '"'.implode('","',$val).'"';
            }
        }else{
            $exportData[0] = $data;
        }
        $rs = implode("\n",(array)$exportData)."\n";
        return $rs;
    }


    /**
     * 获取文件中每行数据
     * 
     * @param $handle 打开的文件句柄
     * @param $contents 获取到的数据
     * @param $line 行数 
     */
    public function fgethandle(&$handle,&$contents,$line){
        if($this->issupzh){
            $row = fgetcsv($handle);
        }else{
            $row = $this->_fgetcsv($handle);
        }

        if ( !$row ) return false;

        foreach( $row as $num => $col )
        {
            if ($line==0 && $num==0) {
                // 判断下文档的字符集.
                if (!$this->charset->is_utf8($col)){
                    $this->is_utf8 = false;
                }else{
                    $this->is_utf8 = true;
                    if ($col_tmp = $this->charset->replace_utf8bom($col)){
                        // 替换两个双引号
                        $col = substr($col_tmp, 1, -1);
                    }
                }
            }
            if (!$this->is_utf8){
                $contents[$line][$num] = $this->charset->local2utf( (string) $col);
            }
            else{
                $contents[$line][$num] = (string) $col;
            }
        }
        return true;
    }

    //设置导出http头
    public function export_header($filename){
        header("Cache-Control: public");
        header("Content-Type: application/force-download");
        header("Accept-Ranges: bytes");
        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            $iefilename = preg_replace('/\./', '%2e', $filename, substr_count($filename, '.') - 1);
            header("Content-Disposition: attachment; filename=\"$iefilename\"");
        } else {
            header("Content-Disposition: attachment; filename=\"$filename\"");
        }
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
    }

    function _fgetcsv(& $handle, $length = null, $d = ',', $e = '"') {
         $d = preg_quote($d);
         $e = preg_quote($e);
         $_line = "";
         $eof=false;
         while ($eof != true) {
             $_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
             $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
             if ($itemcnt % 2 == 0)
                 $eof = true;
         }
         $_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
         $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
         preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
         $_csv_data = $_csv_matches[1];
         for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
             $_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1' , $_csv_data[$_csv_i]);
             $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
         }
         return empty ($_line) ? false : $_csv_data;
    }
}
