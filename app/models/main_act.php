<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(E_ERROR);

class Main_act extends CI_Model {

    function get_uraian($query, $select, $db = "") {
        if ($db == "")
            $db = $this->db;
        $db->reconnect();
        $data = $db->query($query);
        if ($data->num_rows() > 0) {
            $row = $data->row();
            return $row->$select;
        } else {
            return "";
        }
        return 1;
    }

    function get_result(&$query, $db = "") {
        if ($db == "")
            $db = $this->db;
        $db->reconnect();
        $data = $db->query($query);
        if ($data) {
            if ($data->num_rows() > 0) {
                $query = $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    function get_combobox($query, $key, $value, $empty = FALSE, &$disable = "", $db = "") {
        if ($db == "")
            $db = $this->db;
        $db->reconnect();
        $combobox = array();
        $data = $db->query($query);
        if ($empty)
            $combobox[""] = "&nbsp;";
        if ($data->num_rows() > 0) {
            $kodedis = "";
            $arrdis = array();
            foreach ($data->result_array() as $row) {
                if (is_array($disable)) {
                    if ($kodedis == $row[$disable[0]]) {
                        if (!array_key_exists($row[$key], $combobox))
                            $combobox[$row[$key]] = str_replace("'", "\'", "&nbsp; &nbsp;&nbsp;" . $row[$value]);
                    }else {
                        if (!array_key_exists($row[$disable[0]], $combobox))
                            $combobox[$row[$disable[0]]] = $row[$disable[1]];
                        if (!array_key_exists($row[$key], $combobox))
                            $combobox[$row[$key]] = str_replace("'", "\'", "&nbsp; &nbsp;&nbsp;" . $row[$value]);
                    }
                    $kodedis = $row[$disable[0]];
                    if (!in_array($kodedis, $arrdis))
                        $arrdis[] = $kodedis;
                }else {
                    $combobox[$row[$key]] = str_replace("'", "\'", $row[$value]);
                }
            }
            $disable = $arrdis;
        }
        return $combobox;
    }

    function post_to_query($array, $except = "") {
        $data = array();
        foreach ($array as $a => $b) {
            if (is_array($except)) {
                if (!in_array($a, $except))
                    $data[$a] = $b;
            }else {
                $data[$a] = $b;
            }
        }
        return $data;
    }

    function clean_sql($data) {
        $data = str_replace("'", "''", $data);

        return $data;
    }

    function send_mail($subject, $body, $trader_id) {
        $this->load->library('email');
        $this->email->set_newline("\r\n");
        $this->email->from("sireka@pom.go.id", "Sistem Registrasi Iklan BPOM");
        // $this->email->to("zona@edi-indonesia.co.id");
        $to = $this->get_uraian("SELECT email FROM t_user WHERE trader_id = $trader_id LIMIT 1", "email");
        if ($to == "")
            return false;
        $to = str_replace("  ", " ", str_replace(";", ", ", $to));
        $this->email->to($to);
        $this->email->reply_to('sireka@pom.go.id', 'Badan POM');
        $this->email->bcc("sireka@pom.go.id, uqo_86@yahoo.com");
        $this->email->subject($subject);
        $this->email->message($body);
        $hasil = $this->email->send();
        // print_r($hasil); die();
        return $hasil;
    }

    function send_json($json, $env) {
        if ($env == 1) {
            $wsdl = 'http://10.242.17.238/index.php/api/simponi/index/format/json';
            $wsdl = 'http://10.241.15.52/index.php/api/simponi/index/format/json';
        } else {
            $wsdl = 'http://wsdev.simponi.kemenkeu.go.id/simponi_ws_dev/index.php/api/simponi/index/format/json';
            $wsdl = 'http://10.242.230.92/simponi_ws_dev/index.php/api/simponi/index/format/json';
        }
        $wsdl = 'http://10.242.17.238/index.php/api/simponi/index/format/json';
        $options = array('http' => array('header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => http_build_query($json)));
        $context = stream_context_create($options);
        if ($result = file_get_contents($wsdl, false, $context)) {
            return $result;
        } else {
            return "Time Out";
        }
    }

    function set_user_log($userid, $ip, $action, $result, $data = "", $id = "", $process_id = "") {
        $id = (double) $id;
        $data = str_replace("'", "''", $data);
        $query = "CALL SetUserLog('$userid', '$ip', '$action', '$result', '$data', $id, $process_id)";
        $hasil = $this->get_result($query);
        if (!$hasil)
            return "Fatal Error (User Log)";
        $row = $query->row_array();
        if ($row["result"] == "ERROR")
            return $row["result"] . " - " . $row["message"] . " (02)";
        return "OK";
    }

    function array_xml($array, &$xml) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (is_int($key))
                    $key = "item";
                $label = $xml->addChild($key);
                $this->array_xml($value, $label);
            }else {
                $xml->addChild($key, $value);
            }
        }
    }

    function json_xml($json, $tag) {
        $json = json_decode($json, TRUE);
        $xml = new SimpleXMLElement($tag);
        // array_walk_recursive(array_flip($json), array($xml, 'addChild'));
        $this->array_xml($json, $xml);
        return $xml->asXML();
    }

    function xml_json($xml) {
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        return $json;
    }

    function get_ip($ip) {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'Unknown';
        if ($ip != "")
            $ipaddress = $ip . " / " . $ipaddress;
        return $ipaddress;
    }

    function get_process_id() {
        $mt = microtime(true);
        $micro = sprintf("%06d", ($mt - floor($mt)) * 1000000);
        return date("ymdHis") . $micro;
    }

}

?>